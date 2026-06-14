<?php

namespace App\Services\Orders;

use App\Models\Order;
use App\Settings\OperationsSettings;

class OrderHealthService
{
    public function evaluate(Order $order): array
    {
        $operations = rescue(fn () => app(OperationsSettings::class), null, report: false);
        $assignment = $order->activeDispatchAssignment();
        $openSupport = $order->supportTickets->whereNotIn('status', ['resolved', 'closed']);
        $items = [];
        $add = function (string $severity, string $label, string $reason, string $action) use (&$items): void {
            $items[] = compact('severity', 'label', 'reason', 'action');
        };
        if (! $order->customer_id) $add('warning', 'Customer ownership missing', 'No account owns this order.', 'Link a verified customer account.');
        if (! $order->latestPriceQuote()) $add('warning', 'Price quote missing', 'No persisted quote exists.', 'Review pricing inputs and create a quote.');
        if (! ($operations?->payment_provider_enabled ?? false)) $add('blocked', 'Payment provider not connected', 'Payment actions are disabled globally.', 'Connect an approved payment adapter.');
        if (! $assignment) $add('warning', 'No active assignment', 'No worker is assigned to this order.', 'Review dispatch eligibility.');
        if ($openSupport->isNotEmpty()) $add($openSupport->where('priority', 'urgent')->isNotEmpty() ? 'critical' : 'warning', 'Open support issue', $openSupport->count().' open ticket(s) require review.', 'Open latest support ticket.');
        if ($order->workerLocationPings->isEmpty()) $add('warning', 'No real GPS ping', 'No verified worker location exists for this order.', 'Worker must send location from mobile HTTPS.');
        if (! ($operations?->customer_tracking_enabled ?? false)) $add('info', 'Customer tracking disabled', 'Customer GPS visibility is disabled by policy.', 'Keep tracking internal until policy enables it.');
        return $items;
    }
}
