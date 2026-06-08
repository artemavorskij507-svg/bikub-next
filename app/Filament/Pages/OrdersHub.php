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
        return ['total' => Order::count(), 'submitted' => Order::withStatus(OrderStatus::Submitted)->count(), 'active' => Order::whereIn('status', [OrderStatus::Accepted->value, OrderStatus::InProgress->value])->count(), 'completed' => Order::withStatus(OrderStatus::Completed)->count(), 'cancelled' => Order::withStatus(OrderStatus::Cancelled)->count()];
    }
}
