<?php

namespace App\Filament\Widgets;

use App\Enums\OrderStatus;
use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class GLFMaTStatsOverview extends BaseWidget
{
    /**
     * GLF MaT is currently the only active restaurant partner, so requests
     * are scoped by scenario key. Once a second restaurant partner is
     * onboarded, this must move to a real partner_id / restaurant_id column
     * (see docs/GLF_MAT_MODEL_ARCHITECTURE.md).
     */
    private const SCENARIO_KEYS = ['delivery.meals', 'restaurant.booking'];

    protected function getColumns(): int|array
    {
        // Explicit grid (matches the 3→2→1 breakpoint rhythm used by the
        // rest of the dashboard) instead of relying on Filament's default.
        return [
            'default' => 1,
            'sm' => 2,
            'lg' => 3,
        ];
    }

    protected function getStats(): array
    {
        $base = Order::whereIn('service_scenario_key', self::SCENARIO_KEYS);
        $today = Carbon::now()->startOfDay();

        $total = (clone $base)->count();
        $todayCount = (clone $base)->whereDate('created_at', $today)->count();
        $pending = (clone $base)->where('status', OrderStatus::Submitted)->count();
        $delivery = (clone $base)->where('service_scenario_key', 'delivery.meals')->count();
        $booking = (clone $base)->where('service_scenario_key', 'restaurant.booking')->count();
        $completedToday = (clone $base)
            ->where('status', OrderStatus::Completed)
            ->whereDate('completed_at', $today)
            ->count();

        return [
            Stat::make('Усього запитів GLF MaT', $total)
                ->description('Доставка + бронювання, з моменту запуску')
                ->descriptionIcon('heroicon-m-archive-box')
                ->color('gray'),

            Stat::make('Запитів сьогодні', $todayCount)
                ->description('Нові заявки за сьогодні')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('info'),

            Stat::make('Очікують підтвердження', $pending)
                ->description('Потребують ручного підтвердження')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Заявки на доставку', $delivery)
                ->description('Сценарій delivery.meals')
                ->descriptionIcon('heroicon-m-truck')
                ->color('primary'),

            Stat::make('Заявки на бронювання', $booking)
                ->description('Сценарій restaurant.booking')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('primary'),

            Stat::make('Виконано сьогодні', $completedToday)
                ->description('Успішно завершені сьогодні')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
        ];
    }
}
