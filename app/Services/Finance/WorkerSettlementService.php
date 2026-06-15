<?php

namespace App\Services\Finance;

use App\Models\{Order, User, WorkerSettlementEntry};
use Illuminate\Validation\ValidationException;

class WorkerSettlementService
{
    public function __construct(private WorkerSettlementNumberGenerator $numbers, private WorkerSettlementRuleService $rules) {}

    public function calculateForOrder(Order $order, User $actor): WorkerSettlementEntry { return $this->persist($order, $actor, 'created', null); }
    public function recalculateForOrder(Order $order, User $actor, string $reason): WorkerSettlementEntry { throw_if(blank($reason), ValidationException::withMessages(['reason' => 'Recalculation reason is required.'])); return $this->persist($order, $actor, 'recalculated', $reason); }

    public function getPayoutBlockers(Order $order): array
    {
        $blockers = [];
        $assignment = $order->activeDispatchAssignment();
        $invoice = $order->billingDocuments()->where('status', 'issued')->latest()->first();
        $proof = $order->completionProofs()->latest()->first();
        if (! $assignment) $blockers[] = 'Worker assignment is required.';
        if (! $invoice) $blockers[] = 'Issued invoice is required.';
        if (! $proof || $proof->status !== 'accepted') $blockers[] = $proof?->status === 'disputed' ? 'Customer disputed the completion proof.' : 'Customer completion confirmation is required.';
        if (! $order->paymentRecords()->where('status', 'captured')->exists()) $blockers[] = 'Payment is not captured.';
        if ($assignment?->assignedUser) $blockers = [...$blockers, ...$this->rules->getRuleBlockers($order, $assignment->assignedUser)];
        return array_values(array_unique($blockers));
    }

    public function getSettlementReadiness(Order $order): array
    {
        $entry = $order->workerSettlementEntries()->latest()->first();
        $assignment = $order->activeDispatchAssignment();
        $rule = $assignment?->assignedUser ? $this->rules->findApplicableRule($order, $assignment->assignedUser) : null;
        $blockers = $this->getPayoutBlockers($order);
        return ['entry' => $entry, 'rule' => $rule, 'ready' => empty($blockers), 'blockers' => $blockers, 'reason' => $blockers[0] ?? null];
    }

    public function getWorkerEarningsSummary(User $worker): array
    {
        $query = WorkerSettlementEntry::where('worker_id', $worker->id);
        return ['entries' => $query->latest()->get(), 'ready_amount' => (float) (clone $query)->where('status', 'ready')->sum('worker_amount'), 'paid_amount' => (float) (clone $query)->where('status', 'paid')->sum('worker_amount'), 'blocked_count' => (clone $query)->whereIn('status', ['blocked', 'pending_capture'])->count()];
    }

    public function canApproveSettlement(WorkerSettlementEntry $entry, User $actor): array { return ['allowed' => $entry->status === 'ready' && $actor->can('admin.finance.manage'), 'reason' => $entry->status !== 'ready' ? 'Settlement is not ready.' : 'Finance permission is required.']; }
    public function canMarkPaid(WorkerSettlementEntry $entry, User $actor): array { return ['allowed' => false, 'reason' => 'Payout provider/manual payout workflow is not configured.']; }

    private function persist(Order $order, User $actor, string $event, ?string $reason): WorkerSettlementEntry
    {
        $assignment = $order->activeDispatchAssignment();
        $invoice = $order->billingDocuments()->where('status', 'issued')->latest()->first();
        $proof = $order->completionProofs()->latest()->first();
        $payment = $order->paymentRecords()->where('status', 'captured')->latest()->first();
        $rule = $assignment?->assignedUser ? $this->rules->findApplicableRule($order, $assignment->assignedUser) : null;
        $amounts = $rule ? $this->rules->calculateAmounts($order, $rule) : ['gross_amount' => $invoice?->total_amount, 'worker_amount' => null, 'platform_fee_amount' => null, 'currency' => $invoice?->currency ?? $order->currency ?? 'NOK', 'calculation_basis' => 'Blocked until an explicit active legal/tax-approved worker settlement rule is configured.'];
        $blockers = $this->getPayoutBlockers($order);
        $status = empty($blockers) ? 'ready' : ($payment ? 'blocked' : 'pending_capture');
        $entry = WorkerSettlementEntry::updateOrCreate(['order_id' => $order->id], ['entry_number' => WorkerSettlementEntry::where('order_id', $order->id)->value('entry_number') ?? $this->numbers->generate(), 'worker_id' => $assignment?->assigned_user_id, 'worker_profile_id' => $assignment?->assignedUser?->workerProfile?->id, 'dispatch_assignment_id' => $assignment?->id, 'billing_document_id' => $invoice?->id, 'payment_record_id' => $payment?->id, 'completion_proof_id' => $proof?->id, 'status' => $status, ...$amounts, 'blocker_reason' => implode(' ', $blockers), 'ready_at' => empty($blockers) ? now() : null, 'metadata' => ['worker_settlement_rule_id' => $rule?->id], 'created_by_id' => $actor->id, 'updated_by_id' => $actor->id]);
        $entry->events()->create(['order_id' => $order->id, 'worker_id' => $entry->worker_id, 'actor_id' => $actor->id, 'event_type' => $event, 'to_value' => $status, 'description' => $reason ?? 'Settlement ledger calculated from persisted order evidence; payout blockers remain enforced.', 'metadata' => ['worker_settlement_rule_id' => $rule?->id], 'created_at' => now()]);
        return $entry;
    }
}
