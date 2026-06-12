<?php

namespace App\Filament\Pages;

use App\Models\WorkerLocationPing;
use App\Models\DispatchAssignment;

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

    public function getModuleKey(): string { return 'dispatch'; }
    public function getPingCount(): int { return WorkerLocationPing::count(); }
    public function getLatestPingAt(): string { return WorkerLocationPing::latest('captured_at')->value('captured_at')?->format('Y-m-d H:i:s') ?? 'No real ping yet'; }
    public function getOrdersWithPingsCount(): int { return WorkerLocationPing::whereNotNull('order_id')->distinct('order_id')->count('order_id'); }
    public function getActiveAssignmentCount(): int { return DispatchAssignment::whereIn('status', ['assigned', 'accepted'])->count(); }
    public function getCurrentAssignment(): ?DispatchAssignment
    {
        return DispatchAssignment::with(['order', 'assignedUser.workerAvailability'])
            ->whereIn('status', ['assigned', 'accepted'])
            ->latest('assigned_at')
            ->first();
    }
}
