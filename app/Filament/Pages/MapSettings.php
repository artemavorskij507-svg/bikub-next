<?php

namespace App\Filament\Pages;

use App\Settings\MapSettings as MapSettingsData;
use Filament\Forms\Components\{Select, TextInput};
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MapSettings extends SettingsPage
{
    protected static string $settings = MapSettingsData::class;
    protected static string|\UnitEnum|null $navigationGroup = 'System';
    protected static ?string $navigationLabel = 'Map Settings';
    protected static ?int $navigationSort = 31;

    public static function canAccess(): bool { return auth()->user()?->can('admin.system.manage') ?? false; }

    public function form(Schema $schema): Schema
    {
        return $schema->components([Section::make('OpenStreetMap defaults')->columns(2)->schema([
            Select::make('map_provider')->options(['osm' => 'OpenStreetMap'])->disabled()->dehydrated(),
            TextInput::make('map_default_zoom')->numeric()->minValue(3)->maxValue(18)->required(),
            TextInput::make('map_center_lat')->numeric()->minValue(-90)->maxValue(90)->required(),
            TextInput::make('map_center_lng')->numeric()->minValue(-180)->maxValue(180)->required(),
            TextInput::make('max_gps_accuracy_meters')->numeric()->minValue(10)->maxValue(5000)->required()->helperText('Pings above this accuracy threshold are rejected.'),
        ])]);
    }
}
