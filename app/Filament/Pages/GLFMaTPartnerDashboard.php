<?php

namespace App\Filament\Pages;

use App\Models\Order;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;

class GLFMaTPartnerDashboard extends Page
{
    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-shopping-cart';
    }

    public function getTitle(): string
    {
        return 'GLF MaT Partner Module';
    }

    public static function getNavigationLabel(): string
    {
        return 'GLF MaT Partner Module';
    }

    public static function getNavigationSort(): ?int
    {
        return 10;
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Partners';
    }

    public function getHeading(): string
    {
        return 'GLF MaT Partner Module';
    }

    public function getSubheading(): ?string
    {
        return 'Order management, booking requests, delivery coordination';
    }

    public static function canAccess(): bool
    {
        return auth()->check() && (auth()->user()->can('admin.orders.view') || auth()->user()->hasRole('partner'));
    }

    public function getWidgets(): array
    {
        return [
            GLFMaTStatsOverview::class,
        ];
    }
}
