<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class GLFMaTStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $today = Carbon::now()->startOfDay();

        // Orders for GLF MaT (from metadata or hardcoded for now)
        $todayOrders = Order::where('status', 'pending')
            ->whereDate('created_at', $today)
            ->count();

        $pendingConfirmation = Order::where('status', 'pending')
            ->whereDate('created_at', $today)
            ->count();

        $inProgress = Order::whereIn('status', ['assigned', 'at_pickup', 'picked_up', 'at_delivery'])
            ->whereDate('created_at', $today)
            ->count();

        $completed = Order::where('status', 'completed')
            ->whereDate('created_at', $today)
            ->count();

        return [
            Stat::make('Today\'s Orders', $todayOrders)
                ->description('Total orders received today')
                ->descriptionIcon(icon: 'heroicon-m-shopping-cart')
                ->color('info'),

            Stat::make('Pending Confirmation', $pendingConfirmation)
                ->description('Awaiting restaurant approval')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('In Progress', $inProgress)
                ->description('Active deliveries/service')
                ->descriptionIcon('heroicon-m-truck')
                ->color('primary'),

            Stat::make('Completed Today', $completed)
                ->description('Successfully fulfilled')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
        ];
    }
}
