<?php

namespace App\Filament\Pages;

use Spatie\Activitylog\Models\Activity;

class AuditLog extends AdminOsModulePage
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = 'Audit Log';
    protected static string|\UnitEnum|null $navigationGroup = 'System';
    protected static ?int $navigationSort = 20;
    protected static ?string $title = 'Audit Log';
    protected string $view = 'filament.pages.audit-log';

    public function getModuleKey(): string { return 'system'; }

    public function getActivities()
    {
        return Activity::query()->with(['causer', 'subject'])->latest()->limit(100)->get();
    }
}
