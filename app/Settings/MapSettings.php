<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class MapSettings extends Settings
{
    public string $map_provider;
    public float $map_center_lat;
    public float $map_center_lng;
    public int $map_default_zoom;
    public int $max_gps_accuracy_meters;
    public string $default_map_layer;
    public array $enabled_map_layers;
    public string $satellite_provider;
    public string $terrain_provider;
    public string $hybrid_provider;
    public int $map_refresh_seconds;
    public int $stale_gps_seconds;

    public static function group(): string
    {
        return 'map';
    }
}
