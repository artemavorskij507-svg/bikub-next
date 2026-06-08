<?php

namespace Database\Seeders;

use App\Models\ServiceCategory;
use App\Models\ServiceScenario;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BiKuBeServiceScenarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'delivery' => 'Delivery', 'moving' => 'Moving', 'eco' => 'Eco & disposal',
            'handyman' => 'Handyman', 'roadside' => 'Tow & roadside',
            'personal-task' => 'Personal tasks', 'classifieds' => 'Classifieds delivery',
        ];

        foreach ($categories as $slug => $title) {
            ServiceCategory::updateOrCreate(['slug' => $slug], [
                'title' => $title,
                'sort_order' => array_search($slug, array_keys($categories), true),
                'is_active' => true,
            ]);
        }

        $definitions = [
            ['delivery.groceries', 'delivery', 'Grocery delivery', 'Delivery', true, true, true, true, true],
            ['delivery.meals', 'delivery', 'Ready food delivery', 'Delivery', true, true, true, true, true],
            ['delivery.bulky', 'delivery', 'Large-item delivery', 'Delivery', true, true, true, true, true],
            ['moving.home', 'moving', 'Home moving', 'Moving', true, true, true, true, false],
            ['moving.business', 'moving', 'Business moving', 'Moving', true, true, true, true, false],
            ['eco.disposal', 'eco', 'Eco disposal', 'Eco', true, false, true, true, false],
            ['eco.furniture', 'eco', 'Furniture disposal', 'Eco', true, false, true, true, false],
            ['eco.appliances', 'eco', 'Appliance disposal', 'Eco', true, false, true, true, false],
            ['handyman.hourly', 'handyman', 'Hourly handyman', 'Handyman', false, false, true, true, false],
            ['handyman.assembly', 'handyman', 'Assembly service', 'Handyman', false, false, true, true, false],
            ['handyman.repair', 'handyman', 'Repair service', 'Handyman', false, false, true, true, false],
            ['tow.emergency', 'roadside', 'Emergency towing', 'Roadside', true, true, true, false, true],
            ['roadside.assistance', 'roadside', 'Roadside assistance', 'Roadside', true, false, true, false, true],
            ['personal-task.errand', 'personal-task', 'Local errand', 'Personal task', false, false, true, true, false],
            ['personal-task.concierge', 'personal-task', 'Concierge support', 'Personal task', false, false, true, true, false],
            ['classifieds.delivery', 'classifieds', 'Classifieds delivery', 'Delivery', true, true, true, true, true],
        ];

        foreach ($definitions as $index => [$key, $categorySlug, $title, $type, $pickup, $dropoff, $worker, $scheduling, $tracking]) {
            ServiceScenario::updateOrCreate(['scenario_key' => $key], [
                'category_id' => ServiceCategory::where('slug', $categorySlug)->value('id'),
                'slug' => Str::of($key)->replace('.', '-')->toString(),
                'title' => $title, 'service_type' => $type, 'status' => 'active',
                'requires_pickup_address' => $pickup, 'requires_dropoff_address' => $dropoff,
                'requires_worker' => $worker,
                'requires_partner' => in_array($key, ['delivery.groceries', 'delivery.meals'], true),
                'requires_payment' => true, 'supports_scheduling' => $scheduling,
                'supports_live_tracking' => $tracking, 'currency' => 'NOK', 'sort_order' => $index,
            ]);
        }
    }
}
