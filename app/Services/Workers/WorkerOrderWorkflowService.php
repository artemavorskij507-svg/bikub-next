<?php

namespace App\Services\Workers;

use App\Enums\OrderStatus;
use App\Models\{Order, User};
use App\Services\Orders\OrderEngine;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class WorkerOrderWorkflowService
{
    public function acceptAssignment(User $worker, Order $order): void
    {
        $this->transition($worker, $order, 'worker.accepted', OrderStatus::Accepted);
        $order->activeDispatchAssignment()?->update(['status' => 'accepted', 'accepted_at' => now()]);
    }
    public function startOrder(User $worker, Order $order): void { $this->transition($worker, $order, 'worker.started', OrderStatus::InProgress); }
    public function markArrivedPickup(User $worker, Order $order): void { $this->milestone($worker, $order, 'worker.arrived_pickup', 'worker.started'); }
    public function markPickedUp(User $worker, Order $order): void { $this->milestone($worker, $order, 'worker.picked_up', 'worker.arrived_pickup'); }
    public function markArrivedDropoff(User $worker, Order $order): void { $this->milestone($worker, $order, 'worker.arrived_dropoff', 'worker.picked_up'); }
    public function completeOrder(User $worker, Order $order): void
    {
        $this->requireEvent($order, 'worker.arrived_dropoff');
        $this->transition($worker, $order, 'worker.completed', OrderStatus::Completed);
    }

    public function nextAction(Order $order): ?array
    {
        return match ($order->status) {
            OrderStatus::Submitted => ['key' => 'accept', 'label' => 'Accept assignment'],
            OrderStatus::Accepted => ['key' => 'start', 'label' => 'Start order'],
            OrderStatus::InProgress => ! $this->hasEvent($order, 'worker.arrived_pickup')
                ? ['key' => 'arrived-pickup', 'label' => 'Arrived at pickup']
                : (! $this->hasEvent($order, 'worker.picked_up')
                    ? ['key' => 'picked-up', 'label' => 'Confirm pickup']
                    : (! $this->hasEvent($order, 'worker.arrived_dropoff')
                        ? ['key' => 'arrived-dropoff', 'label' => 'Arrived at drop-off']
                        : ['key' => null, 'label' => 'Submit completion proof before final completion'])),
            default => null,
        };
    }

    public function assertOwnership(User $worker, Order $order): void
    {
        if ($order->activeDispatchAssignment()?->assigned_user_id !== $worker->id) {
            throw ValidationException::withMessages(['order' => 'This order is not assigned to you.']);
        }
    }

    private function transition(User $worker, Order $order, string $event, OrderStatus $next): void
    {
        $this->assertOwnership($worker, $order);

        DB::transaction(function () use ($worker, $order, $event, $next) {
            $from = $order->status->value;

            app(OrderEngine::class)->transitionTo(
                $order,
                $next,
                $event,
                [
                    ...($next === OrderStatus::Accepted ? ['accepted_at' => now()] : []),
                    ...($next === OrderStatus::Completed ? ['completed_at' => now()] : []),
                ],
            );

            $order->refresh();
            $order->dispatchEvents()->create(['dispatch_assignment_id' => $order->activeDispatchAssignment()?->id, 'actor_type' => User::class, 'actor_id' => $worker->id, 'event_type' => $event, 'from_status' => $from, 'to_status' => $next->value]);
        });
    }

    private function milestone(User $worker, Order $order, string $event, string $requiredEvent): void
    {
        $this->assertOwnership($worker, $order);
        if ($order->status !== OrderStatus::InProgress) throw ValidationException::withMessages(['status' => 'Start the order before recording delivery milestones.']);
        $this->requireEvent($order, $requiredEvent);
        if ($this->hasEvent($order, $event)) throw ValidationException::withMessages(['status' => 'This delivery milestone is already recorded.']);
        $order->events()->create(['actor_type' => User::class, 'actor_id' => $worker->id, 'event_type' => $event, 'from_status' => $order->status->value, 'to_status' => $order->status->value, 'created_at' => now()]);
        $order->dispatchEvents()->create(['dispatch_assignment_id' => $order->activeDispatchAssignment()?->id, 'actor_type' => User::class, 'actor_id' => $worker->id, 'event_type' => $event]);
    }

    private function hasEvent(Order $order, string $event): bool { return $order->events()->where('event_type', $event)->exists(); }
    private function requireEvent(Order $order, string $event): void
    {
        if (! $this->hasEvent($order, $event)) throw ValidationException::withMessages(['status' => 'Complete the previous delivery step first.']);
    }
}
