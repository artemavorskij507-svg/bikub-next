<?php

namespace Database\Seeders;

use App\Models\PricingRule;
use App\Models\ServiceScenario;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BiKuBePricingRuleSeeder extends Seeder
{
    public function run(): void
    {
        $rules = [
            'delivery.groceries' => ['base', 149, null, null],
            'delivery.meals' => ['base', 99, null, null],
            'delivery.bulky' => ['per_unit', 599, 75, 'floor_pickup'],
            'moving.home' => ['per_unit', 990, 650, 'rooms_count'],
            'moving.business' => ['manual_review', null, null, null],
            'eco.disposal' => ['per_unit', 399, 100, 'item_count'],
            'eco.furniture' => ['per_unit', 499, 150, 'item_count'],
            'eco.appliances' => ['per_unit', 499, 150, 'item_count'],
            'handyman.hourly' => ['manual_review', null, null, null],
            'handyman.assembly' => ['manual_review', null, null, null],
            'handyman.repair' => ['manual_review', null, null, null],
            'tow.emergency' => ['manual_review', null, null, null],
            'roadside.assistance' => ['manual_review', null, null, null],
            'personal-task.errand' => ['base', 349, null, null],
            'personal-task.concierge' => ['manual_review', null, null, null],
            'classifieds.delivery' => ['base', 249, null, null],
        ];

        foreach ($rules as $key => [$type, $base, $perUnit, $unitKey]) {
            $scenario = ServiceScenario::where('scenario_key', $key)->first();
            if (! $scenario) continue;
            PricingRule::updateOrCreate(['code' => 'initial-'.Str::slug($key)], [
                'service_scenario_id' => $scenario->id, 'scenario_key' => $key,
                'name' => $type === 'manual_review' ? 'Operational review required' : 'Initial transparent estimate',
                'type' => $type, 'status' => 'active', 'currency' => 'NOK',
                'base_amount' => $base, 'per_unit_amount' => $perUnit, 'unit_key' => $unitKey,
                'conditions' => ['note' => 'Initial configurable operational rule; not a final market price.'],
            ]);
        }
    }
}
