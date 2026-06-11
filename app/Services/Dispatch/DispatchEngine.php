<?php

namespace App\Services\Dispatch;

use App\Enums\OrderStatus;
use App\Models\DispatchAssignment;
use App\Models\DispatchEvent;
use App\Models\Order;
use App\Models\User;
use App\Services\Workers\WorkerEligibilityService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DispatchEngine
{
    public function listUnassignedOrders(): Collection
    {
        return Order::whereIn('status', [OrderStatus::Submitted->value, OrderStatus::Accepted->value])
            ->whereDoesntHave('dispatchAssignments', fn ($query) => $query->whereIn('status', ['assigned', 'accepted']))
            ->with(['scenario', 'priceQuotes', 'dispatchEvents'])->latest('submitted_at')->get();
    }

    public function markReadyForDispatch(Order $order, ?string $note = null): DispatchEvent
    {
        $this->assertDispatchable($order);
        if ($order->isDispatchReady()) {
            throw ValidationException::withMessages(['dispatch' => 'Order is already dispatch-ready.']);
        }
        return $this->recordDispatchEvent($order, 'dispatch.ready', [], $note);
    }

    public function assign(Order $order, User $worker, User $assignedBy, ?string $note = null): DispatchAssignment
    {
        $this->assertDispatchable($order);
        if (! app(WorkerEligibilityService::class)->userIsEligible($worker, $order)) {
            throw ValidationException::withMessages(['worker' => 'No eligible workers available.']);
        }
        if ($order->activeDispatchAssignment()) {
            throw ValidationException::withMessages(['dispatch' => 'Order already has an active assignment.']);
        }

        return DB::transaction(function () use ($order, $worker, $assignedBy, $note) {
            $assignment = $order->dispatchAssignments()->create(['assigned_user_id' => $worker->id, 'assigned_by_user_id' => $assignedBy->id, 'status' => 'assigned', 'assignment_type' => 'manual', 'assigned_at' => now(), 'notes' => $note]);
            $this->recordDispatchEvent($order, 'dispatch.assigned', ['assigned_user_id' => $worker->id], $note, $assignment);
            return $assignment;
        });
    }

    public function unassign(Order $order, User $actor, string $reason): DispatchAssignment
    {
        $assignment = $order->activeDispatchAssignment();
        if (! $assignment) throw ValidationException::withMessages(['dispatch' => 'Order has no active assignment.']);
        if (trim($reason) === '') throw ValidationException::withMessages(['reason' => 'Unassignment reason is required.']);

        return DB::transaction(function () use ($order, $actor, $reason, $assignment) {
            $assignment->update(['status' => 'cancelled', 'cancelled_at' => now(), 'cancellation_reason' => $reason]);
            $this->recordDispatchEvent($order, 'dispatch.unassigned', ['assigned_user_id' => $assignment->assigned_user_id, 'actor_user_id' => $actor->id], $reason, $assignment);
            return $assignment->refresh();
        });
    }

    public function recordDispatchEvent(Order $order, string $eventType, array $payload = [], ?string $note = null, ?DispatchAssignment $assignment = null): DispatchEvent
    {
        return $order->dispatchEvents()->create(['dispatch_assignment_id' => $assignment?->id, 'actor_type' => auth()->check() ? get_class(auth()->user()) : null, 'actor_id' => auth()->id(), 'event_type' => $eventType, 'payload' => $payload ?: null, 'note' => $note]);
    }

    public function eligibleWorkers(): Collection
    {
        return User::with(['workerProfile', 'workerAvailability'])->get()->filter(fn (User $user) => $user->isEligibleWorker())->values();
    }

    private function assertDispatchable(Order $order): void
    {
        if (! in_array($order->status, [OrderStatus::Submitted, OrderStatus::Accepted], true)) {
            throw ValidationException::withMessages(['dispatch' => 'Only submitted or accepted orders can enter dispatch.']);
        }
    }
}
