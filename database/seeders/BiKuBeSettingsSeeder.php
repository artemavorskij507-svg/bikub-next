<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BiKuBeSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            'platform' => [
                'platform_name' => 'BiKuBe Next',
                'public_brand_name' => 'BiKuBe',
                'default_locale' => 'en',
                'launch_region' => 'Narvik / Ballangen',
                'support_email' => null,
                'support_phone' => null,
                'maintenance_message' => null,
            ],
            'operations' => [
                'dispatch_enabled' => true,
                'gps_tracking_enabled' => true,
                'payment_provider_enabled' => false,
                'customer_tracking_enabled' => false,
                'manual_review_required_default' => true,
            ],
            'map' => [
                'map_provider' => 'osm',
                'map_center_lat' => 68.4385,
                'map_center_lng' => 17.4272,
                'map_default_zoom' => 10,
                'max_gps_accuracy_meters' => 5000,
                'default_map_layer' => 'standard',
                'enabled_map_layers' => ['standard', 'satellite', 'hybrid', 'terrain'],
                'satellite_provider' => 'esri_world_imagery',
                'terrain_provider' => 'opentopomap',
                'hybrid_provider' => 'esri_world_imagery_reference',
                'map_refresh_seconds' => 12,
                'stale_gps_seconds' => 120,
            ],
        ];

        foreach ($defaults as $group => $settings) {
            foreach ($settings as $name => $value) {
                DB::table('settings')->insertOrIgnore([
                    'group' => $group,
                    'name' => $name,
                    'locked' => false,
                    'payload' => json_encode($value),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
