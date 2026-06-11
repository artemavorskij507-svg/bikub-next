<?php

namespace App\Services\Pricing;

use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\OrderPriceQuote;
use App\Models\PricingRule;
use App\Models\ServiceScenario;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PricingEngine
{
    public function __construct(private readonly PriceQuoteNumberGenerator $numbers) {}

    public function quoteForOrder(Order $order): OrderPriceQuote
    {
        $order->loadMissing('scenario');
        $result = $this->calculateForScenario($order->scenario, $order->metadata['intake'] ?? []);

        return DB::transaction(function () use ($order, $result): OrderPriceQuote {
            $quote = $order->priceQuotes()->create([
                ...$result,
                'quote_number' => $this->numbers->generate(),
                'calculation_inputs' => $order->metadata['intake'] ?? [],
                'expires_at' => now()->addDays(7),
            ]);

            $updates = ['estimated_total' => $quote->total];
            if ($order->scenario->requires_payment && (float) $quote->total > 0) {
                $updates['payment_status'] = PaymentStatus::Pending;
            } elseif (! $order->scenario->requires_payment) {
                $updates['payment_status'] = PaymentStatus::NotRequired;
            }
            $order->update($updates);

            return $quote;
        });
    }

    public function calculateForScenario(ServiceScenario $scenario, array $intake): array
    {
        $rules = $this->getApplicableRules($scenario, $intake);
        if ($rules->isEmpty() || $rules->contains(fn (PricingRule $rule) => $rule->type === 'manual_review')) {
            return $this->manualReviewResult($scenario, $rules->isEmpty() ? 'No active pricing rule configured.' : 'Pricing requires operational review.');
        }

        $breakdown = [];
        $subtotal = 0.0;
        foreach ($rules as $rule) {
            $amount = (float) ($rule->base_amount ?? 0);
            if ($rule->type === 'per_unit' && $rule->unit_key) {
                $amount += (float) ($rule->per_unit_amount ?? 0) * max(0, (float) ($intake[$rule->unit_key] ?? 0));
            }
            $amount = max((float) ($rule->min_amount ?? 0), $amount);
            if ($rule->max_amount !== null) $amount = min((float) $rule->max_amount, $amount);
            $subtotal += $amount;
            $breakdown[] = ['code' => $rule->code, 'label' => $rule->name, 'amount' => round($amount, 2)];
        }

        return ['status' => $subtotal > 0 ? 'estimated' : 'manual_review_required', 'currency' => $scenario->currency ?: 'NOK', 'subtotal' => $subtotal, 'fees_total' => 0, 'discounts_total' => 0, 'tax_total' => 0, 'total' => $subtotal, 'breakdown' => $breakdown ?: [['label' => 'No active pricing rule configured.', 'amount' => 0]]];
    }

    public function getApplicableRules(ServiceScenario $scenario, array $intake): Collection
    {
        return PricingRule::active()->where(fn ($query) => $query->where('service_scenario_id', $scenario->id)->orWhere('scenario_key', $scenario->scenario_key))->orderBy('sort_order')->get();
    }

    private function manualReviewResult(ServiceScenario $scenario, string $reason): array
    {
        return ['status' => 'manual_review_required', 'currency' => $scenario->currency ?: 'NOK', 'subtotal' => 0, 'fees_total' => 0, 'discounts_total' => 0, 'tax_total' => 0, 'total' => 0, 'breakdown' => [['label' => $reason, 'amount' => 0]]];
    }
}
