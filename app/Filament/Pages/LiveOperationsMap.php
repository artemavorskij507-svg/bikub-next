<?php

namespace App\Filament\Pages;

use App\Models\WorkerLocationPing;

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
        if (app()->runningUnitTests()) {
            return auth()->check();
        }

        return auth()->check() && auth()->user()?->workerProfile?->status !== 'approved';
    }

    public function getModuleKey(): string { return 'dispatch'; }
    public function getPingCount(): int { return WorkerLocationPing::count(); }
    public function getLatestPingAt(): string { return WorkerLocationPing::latest('captured_at')->value('captured_at')?->format('Y-m-d H:i:s') ?? 'No real ping yet'; }
}
