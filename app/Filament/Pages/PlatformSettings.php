<?php

namespace App\Filament\Pages;

use App\Settings\PlatformSettings as PlatformSettingsData;
use Filament\Forms\Components\{Select, Textarea, TextInput};
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PlatformSettings extends SettingsPage
{
    protected static string $settings = PlatformSettingsData::class;
    protected static string|\UnitEnum|null $navigationGroup = 'System';
    protected static ?string $navigationLabel = 'Platform Settings';
    protected static ?int $navigationSort = 30;

    public static function canAccess(): bool { return auth()->user()?->can('admin.system.manage') ?? false; }

    public function form(Schema $schema): Schema
    {
        return $schema->components([Section::make('Platform identity')->columns(2)->schema([
            TextInput::make('platform_name')->required()->maxLength(120),
            TextInput::make('public_brand_name')->required()->maxLength(120),
            Select::make('default_locale')->options(['en' => 'English', 'nb' => 'Norwegian Bokmal', 'ru' => 'Russian', 'uk' => 'Ukrainian'])->required(),
            TextInput::make('launch_region')->required()->maxLength(160),
            TextInput::make('support_email')->email()->maxLength(255),
            TextInput::make('support_phone')->tel()->maxLength(60),
            Textarea::make('maintenance_message')->columnSpanFull()->maxLength(1000),
        ])]);
    }
}
