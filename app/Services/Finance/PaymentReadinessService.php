<?php

namespace App\Services\Finance;

use App\Models\Order;
use App\Models\User;
use App\Settings\OperationsSettings;

class PaymentReadinessService
{
    public function getOrderPaymentReadiness(Order $order): array
    {
        $blockers = $this->getPaymentBlockers($order);

        return [
            'ready' => $blockers === [],
            'blockers' => $blockers,
            'warnings' => $order->supportTickets()->where('category', 'payment_issue')->whereNotIn('status', ['resolved', 'closed'])->exists()
                ? ['An open payment support issue requires review.']
                : [],
            'next_safe_action' => $order->latestPriceQuote() ? 'Review quote and provider blockers.' : 'Create or calculate a real price quote.',
            'disabled_reason' => $blockers[0]['reason'] ?? null,
        ];
    }

    public function getFinanceMetrics(): array
    {
        $orders = Order::query();

        return [
            'quoted' => Order::whereHas('priceQuotes')->count(),
            'missing_quote' => Order::whereDoesntHave('priceQuotes')->count(),
            'provider_enabled' => $this->getProviderStatus()['enabled'],
            'ready' => Order::with(['priceQuotes', 'customer', 'supportTickets'])->get()->filter(fn (Order $order) => $this->getOrderPaymentReadiness($order)['ready'])->count(),
            'blocked' => Order::with(['priceQuotes', 'customer', 'supportTickets'])->get()->filter(fn (Order $order) => ! $this->getOrderPaymentReadiness($order)['ready'])->count(),
            'payment_issues' => Order::whereHas('supportTickets', fn ($query) => $query->where('category', 'payment_issue')->whereNotIn('status', ['resolved', 'closed']))->count(),
            'with_owner' => $orders->clone()->whereNotNull('customer_id')->count(),
            'completed_today' => $orders->clone()->whereDate('completed_at', today())->count(),
        ];
    }

    public function getPaymentBlockers(Order $order): array
    {
        $blockers = [];
        $quote = $order->latestPriceQuote();

        if (! $quote) {
            $blockers[] = $this->blocker('missing_quote', 'Quote missing', 'No real price quote exists for this order.');
        } elseif ((float) $quote->total <= 0) {
            $blockers[] = $this->blocker('invalid_quote_total', 'Quote total unavailable', 'The latest quote has no positive total.');
        }

        if (! $order->customer_id) {
            $blockers[] = $this->blocker('missing_customer', 'Customer ownership not linked', 'A verified customer account is required before customer payment visibility.');
        }

        if (! $this->getProviderStatus()['connected']) {
            $blockers[] = $this->blocker('provider_disabled', 'Payment provider not connected', 'Payment provider not connected yet.');
        }

        if ($order->supportTickets()->where('category', 'payment_issue')->whereNotIn('status', ['resolved', 'closed'])->exists()) {
            $blockers[] = $this->blocker('payment_support_issue', 'Open payment support issue', 'Resolve or review the open payment support ticket first.');
        }

        return $blockers;
    }

    public function canCreatePaymentIntent(Order $order, User $actor): array
    {
        return $this->decision($order, $actor, 'admin.finance.payment_actions');
    }

    public function canCapturePayment(Order $order, User $actor): array
    {
        return $this->decision($order, $actor, 'admin.finance.payment_actions');
    }

    public function canRefundPayment(Order $order, User $actor): array
    {
        return $this->decision($order, $actor, 'admin.finance.refunds');
    }

    public function getProviderStatus(): array
    {
        $settings = rescue(fn () => app(OperationsSettings::class), null, report: false);
        $enabled = (bool) ($settings?->payment_provider_enabled ?? false);

        return [
            'enabled' => $enabled,
            'connected' => false,
            'state' => $enabled ? 'Configuration incomplete' : 'Disabled',
            'reason' => 'No payment provider adapter or credential contract is configured.',
        ];
    }

    private function decision(Order $order, User $actor, string $permission): array
    {
        if (! $actor->can($permission)) {
            return ['allowed' => false, 'reason' => 'You do not have permission for this payment action.'];
        }

        $readiness = $this->getOrderPaymentReadiness($order);

        return ['allowed' => $readiness['ready'], 'reason' => $readiness['disabled_reason']];
    }

    private function blocker(string $code, string $label, string $reason): array
    {
        return compact('code', 'label', 'reason');
    }
}
