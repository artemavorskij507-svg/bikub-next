<?php

namespace App\Filament\Pages;

use App\Settings\MapSettings as MapSettingsData;
use Filament\Forms\Components\{CheckboxList, Select, TextInput};
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
    public static function getNavigationLabel(): string { return __('bikube.settings.map'); }
    public function getTitle(): string { return __('bikube.settings.map'); }

    public function form(Schema $schema): Schema
    {
        return $schema->components([Section::make('OpenStreetMap defaults')->columns(2)->schema([
            Select::make('map_provider')->options(['osm' => 'OpenStreetMap'])->disabled()->dehydrated(),
            TextInput::make('map_default_zoom')->numeric()->minValue(3)->maxValue(18)->required(),
            TextInput::make('map_center_lat')->numeric()->minValue(-90)->maxValue(90)->required(),
            TextInput::make('map_center_lng')->numeric()->minValue(-180)->maxValue(180)->required(),
            TextInput::make('max_gps_accuracy_meters')->numeric()->minValue(10)->maxValue(5000)->required()->helperText('Pings above this accuracy threshold are rejected.'),
            Select::make('default_map_layer')->options(['standard'=>'Standard','satellite'=>'Satellite','hybrid'=>'Hybrid','terrain'=>'Terrain'])->required(),
            CheckboxList::make('enabled_map_layers')->options(['standard'=>'Standard OSM','satellite'=>'Satellite imagery','hybrid'=>'Hybrid imagery + labels','terrain'=>'Terrain / relief'])->columns(2),
            TextInput::make('satellite_provider')->disabled()->dehydrated()->helperText('Keyless Esri World Imagery tiles. Attribution is shown on the map.'),
            TextInput::make('hybrid_provider')->disabled()->dehydrated()->helperText('Keyless Esri imagery with reference-label overlay.'),
            TextInput::make('terrain_provider')->disabled()->dehydrated()->helperText('Keyless OpenTopoMap tiles.'),
            TextInput::make('map_refresh_seconds')->numeric()->minValue(10)->maxValue(60)->required(),
            TextInput::make('stale_gps_seconds')->numeric()->minValue(30)->maxValue(3600)->required(),
        ])]);
    }
}
