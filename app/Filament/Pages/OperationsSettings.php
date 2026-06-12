<?php

namespace App\Filament\Pages;

use App\Settings\OperationsSettings as OperationsSettingsData;
use Filament\Forms\Components\Toggle;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class OperationsSettings extends SettingsPage
{
    protected static string $settings = OperationsSettingsData::class;
    protected static string|\UnitEnum|null $navigationGroup = 'Operations';
    protected static ?string $navigationLabel = 'Operations Settings';
    protected static ?int $navigationSort = 30;

    public static function canAccess(): bool { return auth()->user()?->can('admin.system.manage') ?? false; }
    public static function getNavigationLabel(): string { return __('bikube.settings.operations'); }
    public function getTitle(): string { return __('bikube.settings.operations'); }

    public function form(Schema $schema): Schema
    {
        return $schema->components([Section::make('Operational gates')->schema([
            Toggle::make('dispatch_enabled')->helperText('Controls the real dispatch workflow.'),
            Toggle::make('gps_tracking_enabled')->helperText('Worker browser pings only; customer tracking remains separate.'),
            Toggle::make('payment_provider_enabled')->disabled()->helperText('Payment provider adapter is not connected yet.'),
            Toggle::make('customer_tracking_enabled')->disabled()->helperText('Customer tracking is not exposed yet.'),
            Toggle::make('manual_review_required_default')->helperText('Use manual review when no confident automatic rule exists.'),
        ])]);
    }
}
