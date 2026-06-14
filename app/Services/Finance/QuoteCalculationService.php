<?php

namespace App\Services\Finance;

use App\Models\Order;
use App\Models\OrderPriceQuote;
use App\Models\PricingRule;
use App\Models\User;
use App\Services\Pricing\PricingEngine;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class QuoteCalculationService
{
    public function __construct(private readonly PricingEngine $pricing) {}

    public function calculateForOrder(Order $order, User $actor): OrderPriceQuote
    {
        return $this->persist($order, $actor, 'quote_created');
    }

    public function recalculateForOrder(Order $order, User $actor, string $reason): OrderPriceQuote
    {
        if (blank($reason)) {
            throw ValidationException::withMessages(['reason' => 'A recalculation reason is required.']);
        }

        return $this->persist($order, $actor, 'quote_recalculated', $reason);
    }

    public function previewForOrder(Order $order): array
    {
        $order->loadMissing('scenario');
        $blockers = $this->getQuoteBlockers($order);

        if ($blockers !== []) {
            return ['ready' => false, 'blockers' => $blockers, 'result' => null];
        }

        $result = $this->pricing->calculateForScenario($order->scenario, $order->metadata['intake'] ?? []);

        if ($result['status'] !== 'estimated' || (float) $result['total'] <= 0) {
            return [
                'ready' => false,
                'blockers' => [['code' => 'manual_review', 'reason' => $result['breakdown'][0]['label'] ?? 'Pricing requires manual review.']],
                'result' => $result,
            ];
        }

        return ['ready' => true, 'blockers' => [], 'result' => $result];
    }

    public function getQuoteBlockers(Order $order): array
    {
        $order->loadMissing('scenario');
        if (! $order->scenario) {
            return [['code' => 'missing_scenario', 'reason' => 'The order has no service scenario.']];
        }

        $rules = $this->pricing->getApplicableRules($order->scenario, $order->metadata['intake'] ?? []);
        if ($rules->isEmpty()) {
            return [['code' => 'missing_pricing_rule', 'reason' => 'No active pricing rule matches this service scenario.']];
        }

        if ($rules->contains(fn (PricingRule $rule) => $rule->type === 'manual_review')) {
            return [['code' => 'manual_review_rule', 'reason' => 'The active pricing rule requires operational review.']];
        }

        $intake = $order->metadata['intake'] ?? [];
        $missing = $rules
            ->filter(fn (PricingRule $rule) => $rule->type === 'per_unit' && $rule->unit_key && ! is_numeric($intake[$rule->unit_key] ?? null))
            ->pluck('unit_key')->unique()->values()->all();

        return $missing === []
            ? []
            : [['code' => 'missing_intake_fields', 'reason' => 'Required pricing intake is missing: '.implode(', ', $missing).'.']];
    }

    private function persist(Order $order, User $actor, string $eventType, ?string $reason = null): OrderPriceQuote
    {
        $preview = $this->previewForOrder($order);
        if (! $preview['ready']) {
            $message = $preview['blockers'][0]['reason'];
            $this->recordEvent($order, $actor, 'quote_blocked', $message, ['blockers' => $preview['blockers']]);
            throw ValidationException::withMessages(['quote' => $message]);
        }

        return DB::transaction(function () use ($order, $actor, $eventType, $reason): OrderPriceQuote {
            $quote = $this->pricing->quoteForOrder($order);
            $this->recordEvent($order, $actor, $eventType, $reason, [
                'quote_id' => $quote->id,
                'quote_number' => $quote->quote_number,
                'total' => $quote->total,
                'currency' => $quote->currency,
            ]);
            activity('pricing')->performedOn($quote)->causedBy($actor)->withProperties(['order_id' => $order->id, 'reason' => $reason])->log($eventType);

            return $quote;
        });
    }

    private function recordEvent(Order $order, User $actor, string $type, ?string $note, array $payload = []): void
    {
        $order->events()->create([
            'actor_type' => User::class,
            'actor_id' => $actor->id,
            'event_type' => $type,
            'payload' => $payload ?: null,
            'note' => $note,
            'created_at' => now(),
        ]);
    }
}
