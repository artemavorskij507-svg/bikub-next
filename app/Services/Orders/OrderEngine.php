<?php

namespace App\Services\Orders;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\OrderEvent;
use App\Models\ServiceScenario;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderEngine
{
    public function __construct(private readonly OrderNumberGenerator $numbers) {}

    public function createDraftFromScenario(ServiceScenario $scenario, array $payload): Order
    {
        if (! $scenario->isActive()) {
            throw ValidationException::withMessages(['scenario' => 'This service is not accepting requests.']);
        }

        return DB::transaction(function () use ($scenario, $payload): Order {
            $order = Order::create([
                ...$payload,
                'order_number' => $this->numbers->generate(),
                'service_scenario_id' => $scenario->id,
                'service_scenario_key' => $scenario->scenario_key,
                'status' => OrderStatus::Draft,
                'payment_status' => PaymentStatus::NotRequired,
                'currency' => $scenario->currency ?: 'NOK',
            ]);
            $this->recordEvent($order, 'order.created');
            return $order;
        });
    }

    public function submit(Order $order): Order { return $this->transition($order, OrderStatus::Submitted, 'order.submitted', ['submitted_at' => now()]); }
    public function cancel(Order $order, ?string $reason): Order { return $this->transition($order, OrderStatus::Cancelled, 'order.cancelled', ['cancelled_at' => now()], $reason); }

    public function recordEvent(Order $order, string $eventType, array $payload = [], ?string $from = null, ?string $to = null, ?string $note = null): OrderEvent
    {
        return $order->events()->create(['event_type' => $eventType, 'from_status' => $from, 'to_status' => $to, 'payload' => $payload ?: null, 'note' => $note, 'actor_type' => auth()->check() ? get_class(auth()->user()) : null, 'actor_id' => auth()->id()]);
    }

    private function transition(Order $order, OrderStatus $next, string $event, array $attributes = [], ?string $note = null): Order
    {
        if (! $order->canTransitionTo($next)) throw ValidationException::withMessages(['status' => "Order cannot transition from {$order->status->value} to {$next->value}."]);
        return DB::transaction(function () use ($order, $next, $event, $attributes, $note): Order {
            $from = $order->status->value;
            $order->update([...$attributes, 'status' => $next]);
            $this->recordEvent($order, $event, [], $from, $next->value, $note);
            return $order->refresh();
        });
    }
}
