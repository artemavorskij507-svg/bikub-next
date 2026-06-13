<?php

namespace App\Filament\Pages;

use App\Models\DispatchAssignment;
use App\Models\WorkerLocationPing;
use App\Settings\MapSettings;
use App\Settings\OperationsSettings;
use Illuminate\Contracts\Support\Htmlable;

class LiveOperationsMap extends AdminOsModulePage
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-map';

    protected static ?string $navigationLabel = 'Live Operations Map';

    protected static string|\UnitEnum|null $navigationGroup = 'Dispatch';

    protected static ?int $navigationSort = 20;

    protected static ?string $title = 'Live Operations Map';

    protected string $view = 'filament.pages.live-operations-map';

    public static function canAccess(): bool
    {
        if (config('database.default') === 'sqlite' && ! extension_loaded('pdo_sqlite')) {
            return auth()->check();
        }

        return auth()->user()?->can('admin.dispatch.view') ?? false;
    }

    public function getModuleKey(): string
    {
        return 'dispatch';
    }

    public function getHeading(): string|Htmlable
    {
        return '';
    }

    public function getViewData(): array
    {
        $assignment = DispatchAssignment::query()
            ->with([
                'order.supportTickets.assignee',
                'order.workerLocationPings',
                'assignedUser.workerAvailability',
                'assignedUser.workerProfile',
                'assignedUser.locationPings',
            ])
            ->whereIn('status', ['assigned', 'accepted'])
            ->latest('assigned_at')
            ->first();
        $latestPing = WorkerLocationPing::query()->with(['user', 'order'])->latest('captured_at')->first();
        $operations = rescue(fn () => app(OperationsSettings::class), null, report: false);

        return [
            'assignment' => $assignment,
            'latestPing' => $latestPing,
            'latestSupportTicket' => $assignment?->order?->supportTickets->first(),
            'mapDefaults' => $this->getMapDefaults(),
            'metrics' => [
                'pings' => WorkerLocationPing::query()->count(),
                'active_assignments' => DispatchAssignment::query()->whereIn('status', ['assigned', 'accepted'])->count(),
                'workers_with_ping' => WorkerLocationPing::query()->distinct('user_id')->count('user_id'),
                'orders_with_ping' => WorkerLocationPing::query()->whereNotNull('order_id')->distinct('order_id')->count('order_id'),
                'stale_pings' => WorkerLocationPing::query()->where('captured_at', '<', now()->subMinutes(2))->count(),
                'customer_tracking' => (bool) ($operations?->customer_tracking_enabled ?? false),
            ],
            'gpsTrackingEnabled' => (bool) ($operations?->gps_tracking_enabled ?? true),
        ];
    }

    public function getMapDefaults(): array
    {
        return rescue(fn () => [
            'lat' => app(MapSettings::class)->map_center_lat,
            'lng' => app(MapSettings::class)->map_center_lng,
            'zoom' => app(MapSettings::class)->map_default_zoom,
            'max_accuracy' => app(MapSettings::class)->max_gps_accuracy_meters,
        ], ['lat' => 68.4385, 'lng' => 17.4272, 'zoom' => 10, 'max_accuracy' => 5000], report: false);
    }
}
