<?php

namespace App\Services\Workers;

use App\Enums\OrderStatus;
use App\Models\{Order, User};
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class WorkerOrderWorkflowService
{
    public function acceptAssignment(User $worker, Order $order): void { $this->transition($worker, $order, 'worker.accepted', OrderStatus::Accepted); }
    public function startOrder(User $worker, Order $order): void { $this->transition($worker, $order, 'worker.started', OrderStatus::InProgress); }
    public function markArrivedPickup(User $worker, Order $order): void { $this->event($worker, $order, 'worker.arrived_pickup'); }
    public function markPickedUp(User $worker, Order $order): void { $this->event($worker, $order, 'worker.picked_up'); }
    public function markArrivedDropoff(User $worker, Order $order): void { $this->event($worker, $order, 'worker.arrived_dropoff'); }
    public function completeOrder(User $worker, Order $order): void { $this->transition($worker, $order, 'worker.completed', OrderStatus::Completed); }

    public function assertOwnership(User $worker, Order $order): void
    {
        if ($order->activeDispatchAssignment()?->assigned_user_id !== $worker->id) {
            throw ValidationException::withMessages(['order' => 'This order is not assigned to you.']);
        }
    }

    private function transition(User $worker, Order $order, string $event, OrderStatus $next): void
    {
        $this->assertOwnership($worker, $order);
        if (! $order->canTransitionTo($next)) throw ValidationException::withMessages(['status' => "Order cannot transition from {$order->status->value} to {$next->value}."]);
        DB::transaction(function () use ($worker, $order, $event, $next) {
            $from = $order->status->value;
            $order->update(['status' => $next, ...($next === OrderStatus::Accepted ? ['accepted_at' => now()] : []), ...($next === OrderStatus::Completed ? ['completed_at' => now()] : [])]);
            $order->events()->create(['actor_type' => User::class, 'actor_id' => $worker->id, 'event_type' => $event, 'from_status' => $from, 'to_status' => $next->value, 'created_at' => now()]);
            $order->dispatchEvents()->create(['dispatch_assignment_id' => $order->activeDispatchAssignment()?->id, 'actor_type' => User::class, 'actor_id' => $worker->id, 'event_type' => $event, 'from_status' => $from, 'to_status' => $next->value]);
        });
    }

    private function event(User $worker, Order $order, string $event): void
    {
        $this->assertOwnership($worker, $order);
        if ($order->status !== OrderStatus::InProgress) throw ValidationException::withMessages(['status' => 'Start the order before recording delivery milestones.']);
        $order->events()->create(['actor_type' => User::class, 'actor_id' => $worker->id, 'event_type' => $event, 'from_status' => $order->status->value, 'to_status' => $order->status->value, 'created_at' => now()]);
        $order->dispatchEvents()->create(['dispatch_assignment_id' => $order->activeDispatchAssignment()?->id, 'actor_type' => User::class, 'actor_id' => $worker->id, 'event_type' => $event]);
    }
}
