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

    public static function group(): string
    {
        return 'map';
    }
}
