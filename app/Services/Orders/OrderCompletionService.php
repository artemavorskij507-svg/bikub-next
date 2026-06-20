<?php

namespace App\Services\Orders;

use App\Models\{Order, OrderCompletionProof, User};
use App\Services\Support\SupportTicketService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderCompletionService
{
    public function canSubmitProof(Order $order, User $worker): array
    {
        $assigned = $order->dispatchAssignments()->where('assigned_user_id', $worker->id)->whereIn('status', ['assigned', 'accepted'])->exists();
        $duplicate = $order->completionProofs()->whereIn('status', ['submitted', 'accepted'])->exists();
        $arrivedDropoff = $order->events()->where('event_type', 'worker.arrived_dropoff')->exists();
        $submitted = $order->completionProofs()->whereIn('status', ['submitted', 'accepted', 'disputed'])->exists();
        $allowed = $assigned && $order->status->value === 'in_progress' && $arrivedDropoff && ! $duplicate;

        return [
            'allowed' => $allowed,
            'submitted' => $submitted,
            'reason' => ! $assigned
                ? 'Worker is not assigned to this order.'
                : ($order->status->value !== 'in_progress'
                    ? 'Order is not in progress.'
                    : (! $arrivedDropoff
                        ? 'Arrive at drop-off before submitting completion proof.'
                        : ($duplicate ? 'An active completion proof already exists.' : null))),
        ];
    }

    public function submitProof(Order $order, User $worker, array $data): OrderCompletionProof
    {
        $check = $this->canSubmitProof($order, $worker);
        throw_unless($check['allowed'], ValidationException::withMessages(['proof' => $check['reason']]));
        $proof = $order->completionProofs()->create(['dispatch_assignment_id' => $order->activeDispatchAssignment()?->id, 'worker_id' => $worker->id, 'status' => 'submitted', 'proof_type' => 'text', 'worker_note' => $data['worker_note'], 'submitted_at' => now()]);
        $this->event($proof, $worker, 'submitted', 'Worker submitted text completion proof.');
        return $proof;
    }

    public function acceptByCustomer(OrderCompletionProof $proof, User $customer, ?string $note): OrderCompletionProof
    {
        $this->assertCustomer($proof, $customer);
        throw_unless($proof->status === 'submitted', ValidationException::withMessages(['proof' => 'Only submitted proof can be accepted.']));
        $proof->update(['status' => 'accepted', 'customer_note' => $note, 'accepted_at' => now(), 'reviewed_by_customer_id' => $customer->id]);
        $this->event($proof, $customer, 'accepted_by_customer', 'Customer accepted completion proof.');
        return $proof;
    }

    public function disputeByCustomer(OrderCompletionProof $proof, User $customer, string $reason): OrderCompletionProof
    {
        $this->assertCustomer($proof, $customer);
        throw_unless($proof->status === 'submitted', ValidationException::withMessages(['proof' => 'Only submitted proof can be disputed.']));
        return DB::transaction(function () use ($proof, $customer, $reason) {
            $proof->update(['status' => 'disputed', 'customer_note' => $reason, 'disputed_at' => now(), 'reviewed_by_customer_id' => $customer->id]);
            $this->event($proof, $customer, 'disputed_by_customer', 'Customer disputed completion proof.');
            $ticket = app(SupportTicketService::class)->createTicket([
                'subject' => 'Completion dispute: '.$proof->order->order_number, 'summary' => $reason,
                'category' => 'order_completion_dispute', 'priority' => 'high', 'source' => 'account', 'visibility' => 'customer_visible',
                'order_id' => $proof->order_id, 'customer_id' => $customer->id, 'dispatch_assignment_id' => $proof->dispatch_assignment_id,
                'metadata' => ['order_completion_proof_id' => $proof->id],
            ], $customer);
            app(SupportTicketService::class)->addMessage($ticket, ['body' => $reason, 'message_type' => 'public_reply', 'visibility' => 'customer_visible', 'author_type' => 'customer'], $customer);
            $proof->update(['metadata' => [...($proof->metadata ?? []), 'support_ticket_id' => $ticket->id]]);
            return $proof->refresh();
        });
    }

    public function getCompletionState(Order $order): array
    {
        $proof = $order->completionProofs()->with('events')->latest()->first();
        return ['proof' => $proof, 'status' => $proof?->status ?? 'not_submitted', 'payout_blockers' => $this->getPayoutBlockers($order)];
    }

    public function getPayoutBlockers(Order $order): array
    {
        $proof = $order->completionProofs()->latest()->first();
        $blockers = [];
        if (! $proof) $blockers[] = 'Completion proof has not been submitted.';
        elseif ($proof->status === 'disputed') $blockers[] = 'Customer disputed the completion proof.';
        elseif ($proof->status !== 'accepted') $blockers[] = 'Completion proof is not accepted by customer.';
        if (! $order->billingDocuments()->where('status', 'issued')->exists()) $blockers[] = 'Issued invoice is required.';
        if (! $order->paymentRecords()->where('status', 'captured')->exists()) $blockers[] = 'Payment is not captured.';
        if (! $order->dispatchAssignments()->whereIn('status', ['assigned', 'accepted'])->exists()) $blockers[] = 'Worker assignment is required.';
        if ($order->supportTickets()->where('category', 'order_completion_dispute')->whereNotIn('status', ['resolved', 'closed'])->exists()) $blockers[] = 'An open completion dispute exists.';
        return $blockers;
    }

    private function assertCustomer(OrderCompletionProof $proof, User $customer): void
    {
        abort_unless($proof->order()->where('customer_id', $customer->id)->exists(), 404);
    }

    private function event(OrderCompletionProof $proof, User $actor, string $type, string $description): void
    {
        $proof->events()->create(['order_id' => $proof->order_id, 'actor_id' => $actor->id, 'event_type' => $type, 'description' => $description, 'created_at' => now()]);
    }
}
