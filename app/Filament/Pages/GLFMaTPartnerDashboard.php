<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\GLFMaTStatsOverview;
use App\Models\Order;
use BackedEnum;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Carbon;
use UnitEnum;

class GLFMaTPartnerDashboard extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationLabel = 'GLF MaT Partner Module';
    protected static string|\UnitEnum|null $navigationGroup = 'Partners';
    protected static ?int $navigationSort = 10;
    protected static ?string $title = 'GLF MaT Partner Module';
    protected string $view = 'filament.pages.glf-mat-partner-dashboard';

    public function getTitle(): string
    {
        return 'GLF MaT Partner Module';
    }

    public function getHeading(): string|Htmlable
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
