<?php

namespace App\Filament\Pages;

use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Support\Facades\Schema;

class OrdersHub extends AdminOsModulePage
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationLabel = 'Orders Hub';

    protected static string|\UnitEnum|null $navigationGroup = 'Orders';

    protected static ?int $navigationSort = 10;

    protected static ?string $title = 'Orders Hub';

    public function getModuleKey(): string
    {
        return 'orders';
    }

    public function getOrderCounts(): array
    {
        if (! Schema::hasTable('orders')) return ['total' => 0, 'submitted' => 0, 'active' => 0, 'completed' => 0, 'cancelled' => 0];
        return ['total' => Order::count(), 'submitted' => Order::withStatus(OrderStatus::Submitted)->count(), 'unassigned' => Order::whereIn('status', ['submitted', 'accepted'])->whereDoesntHave('dispatchAssignments', fn ($q) => $q->whereIn('status', ['assigned', 'accepted']))->count(), 'dispatch_ready' => Order::whereHas('dispatchEvents', fn ($q) => $q->where('event_type', 'dispatch.ready'))->count(), 'assigned' => Order::whereHas('dispatchAssignments', fn ($q) => $q->whereIn('status', ['assigned', 'accepted']))->count(), 'completed' => Order::withStatus(OrderStatus::Completed)->count()];
    }

    public function getLatestOrders(): array
    {
        if (! Schema::hasTable('orders')) return [];
        return Order::with(['scenario', 'priceQuotes', 'dispatchEvents'])->withCount('events')->latest()->limit(8)->get()->map(fn (Order $order) => [
            'number' => $order->order_number, 'scenario' => $order->scenario?->title ?? $order->service_scenario_key,
            'contact' => implode(' · ', array_filter([$order->customer_name, $order->customer_email, $order->customer_phone])),
            'status' => $order->status->value, 'payment' => $order->payment_status->value,
            'estimated' => $order->estimated_total, 'quote_status' => $order->latestPriceQuote()?->status ?? 'No quote',
            'quote_total' => $order->latestPriceQuote()?->total, 'events' => $order->events_count,
            'dispatch' => $order->activeDispatchAssignment() ? 'Assigned' : ($order->isDispatchReady() ? 'Ready' : 'Unassigned'),
            'dispatch_event' => $order->dispatchEvents->first()?->event_type ?? 'No dispatch event',
            'url' => \App\Filament\Resources\Orders\OrderResource::getUrl('edit', ['record' => $order]),
        ])->all();
    }
}
