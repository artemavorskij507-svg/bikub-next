<?php

namespace App\Filament\Pages;

use App\Settings\ThemePaletteSettings as Settings;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ThemePaletteSettings extends SettingsPage
{
    protected static string $settings = Settings::class;
    protected static string|\UnitEnum|null $navigationGroup = 'System';
    protected static ?string $navigationLabel = 'Theme Palette';
    protected static ?string $slug = 'theme-palette-settings';
    protected static ?int $navigationSort = 34;

    public static function canAccess(): bool { return auth()->user()?->can('admin.system.manage') ?? false; }

    public function form(Schema $schema): Schema
    {
        $roles = collect(['owner','admin','dispatcher','finance','support','content_manager','workforce_manager','security_manager','worker'])->mapWithKeys(fn ($role)=>[$role=>str($role)->replace('_',' ')->title()])->all();
        return $schema->components([
            Section::make('Palette policy')->description('Controls who may personalize the BiKuBe operational accent.')->columns(2)->schema([
                Toggle::make('enabled')->label('Enable theme palette'),
                TextInput::make('default_hex')->label('Default HEX')->required()->regex('/^#[0-9A-Fa-f]{6}$/')->helperText('Strict #RRGGBB format.'),
                Select::make('access_mode')->options(['allow'=>'Allow listed roles','deny'=>'Deny listed roles'])->required(),
                CheckboxList::make('allowed_roles')->options($roles)->columns(3)->columnSpanFull(),
            ]),
            Section::make('Surfaces')->columns(4)->schema([
                Toggle::make('apply_admin')->label('Admin OS'), Toggle::make('apply_account')->label('Account'),
                Toggle::make('apply_worker')->label('Worker'), Toggle::make('apply_public')->label('Public default'),
                Toggle::make('allow_custom_hex')->label('Custom HEX'), Toggle::make('allow_presets')->label('Presets'),
            ]),
        ]);
    }
}
