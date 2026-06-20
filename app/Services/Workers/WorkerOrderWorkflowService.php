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


    public function executionState(User $worker, Order $order, array $proofEligibility = []): array
    {
        $this->assertOwnership($worker, $order);

        $events = $order->events()->pluck('event_type')->all();
        $has = fn (string $event): bool => in_array($event, $events, true);
        $status = $order->status;

        $steps = [
            ['key' => 'accept', 'label' => 'Accept assignment', 'event' => 'worker.accepted', 'route' => 'worker.orders.accept', 'complete' => $status !== OrderStatus::Submitted, 'available' => $status === OrderStatus::Submitted, 'reason' => $status === OrderStatus::Submitted ? null : 'Assignment is already accepted or no longer awaiting acceptance.'],
            ['key' => 'start', 'label' => 'Start job', 'event' => 'worker.started', 'route' => 'worker.orders.start', 'complete' => $status === OrderStatus::InProgress || $has('worker.started'), 'available' => $status === OrderStatus::Accepted, 'reason' => $status === OrderStatus::Accepted ? null : 'Accept the assignment before starting the job.'],
            ['key' => 'arrived-pickup', 'label' => 'Arrived at pickup', 'event' => 'worker.arrived_pickup', 'route' => 'worker.orders.arrived-pickup', 'complete' => $has('worker.arrived_pickup'), 'available' => $status === OrderStatus::InProgress && $has('worker.started') && ! $has('worker.arrived_pickup'), 'reason' => $has('worker.started') ? 'Pickup arrival is already recorded or the job is not in progress.' : 'Start the job before recording pickup arrival.'],
            ['key' => 'picked-up', 'label' => 'Confirm pickup', 'event' => 'worker.picked_up', 'route' => 'worker.orders.picked-up', 'complete' => $has('worker.picked_up'), 'available' => $status === OrderStatus::InProgress && $has('worker.arrived_pickup') && ! $has('worker.picked_up'), 'reason' => $has('worker.arrived_pickup') ? 'Pickup is already confirmed or the job is not in progress.' : 'Record arrival at pickup first.'],
            ['key' => 'arrived-dropoff', 'label' => 'Arrived at drop-off', 'event' => 'worker.arrived_dropoff', 'route' => 'worker.orders.arrived-dropoff', 'complete' => $has('worker.arrived_dropoff'), 'available' => $status === OrderStatus::InProgress && $has('worker.picked_up') && ! $has('worker.arrived_dropoff'), 'reason' => $has('worker.picked_up') ? 'Drop-off arrival is already recorded or the job is not in progress.' : 'Confirm pickup before recording drop-off arrival.'],
            ['key' => 'completion-proof', 'label' => 'Submit completion proof', 'event' => 'completion_proof.submitted', 'route' => 'worker.orders.completion-proof.submit', 'complete' => (bool) ($proofEligibility['submitted'] ?? false), 'available' => (bool) ($proofEligibility['allowed'] ?? false), 'reason' => $proofEligibility['reason'] ?? 'Complete the delivery steps before submitting proof.'],
        ];

        $next = collect($steps)->first(fn ($step) => $step['available'])
            ?? collect($steps)->first(fn ($step) => ! $step['complete']);

        return [
            'steps' => $steps,
            'next_action' => $next,
            'events' => $events,
            'assignment' => $order->activeDispatchAssignment(),
        ];
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
