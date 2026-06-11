<?php

namespace Database\Seeders;

use App\Models\ServiceScenario;
use App\Models\ServiceScenarioField;
use Illuminate\Database\Seeder;

class BiKuBeServiceScenarioFieldSeeder extends Seeder
{
    public function run(): void
    {
        $groups = [
            ['delivery.groceries', [
                ['pickup_address', 'Pickup address', 'address', true],
                ['dropoff_address', 'Drop-off address', 'address', true],
                ['grocery_list', 'Grocery list', 'textarea', true],
                ['preferred_store', 'Preferred store', 'text', false],
                ['delivery_window', 'Preferred delivery window', 'datetime', true],
                ['contact_phone', 'Contact phone', 'phone', true],
            ]],
            ['delivery.meals', [
                ['restaurant_name', 'Restaurant name', 'text', true],
                ['pickup_address', 'Pickup address', 'address', true],
                ['dropoff_address', 'Drop-off address', 'address', true],
                ['order_reference', 'Restaurant order reference', 'text', false],
                ['delivery_window', 'Preferred delivery window', 'datetime', true],
                ['contact_phone', 'Contact phone', 'phone', true],
            ]],
            ['delivery.bulky', [
                ['pickup_address', 'Pickup address', 'address', true],
                ['dropoff_address', 'Drop-off address', 'address', true],
                ['item_description', 'Item description', 'textarea', true],
                ['item_weight_estimate', 'Estimated weight (kg)', 'number', false],
                ['floor_pickup', 'Pickup floor', 'number', false],
                ['floor_dropoff', 'Drop-off floor', 'number', false],
                ['elevator_available', 'Elevator available', 'boolean', false],
                ['preferred_time', 'Preferred time', 'datetime', true],
            ]],
            ['moving.home', [
                ['pickup_address', 'Pickup address', 'address', true],
                ['dropoff_address', 'Drop-off address', 'address', true],
                ['rooms_count', 'Number of rooms', 'number', true],
                ['inventory_notes', 'Inventory notes', 'textarea', true],
                ['floor_pickup', 'Pickup floor', 'number', false],
                ['floor_dropoff', 'Drop-off floor', 'number', false],
                ['elevator_available', 'Elevator available', 'boolean', false],
                ['preferred_date', 'Preferred date', 'date', true],
            ]],
            ['moving.business', [
                ['company_name', 'Company name', 'text', true],
                ['pickup_address', 'Pickup address', 'address', true],
                ['dropoff_address', 'Drop-off address', 'address', true],
                ['workspace_count', 'Number of workspaces', 'number', true],
                ['inventory_notes', 'Inventory notes', 'textarea', true],
                ['preferred_date', 'Preferred date', 'date', true],
            ]],
            [['eco.disposal', 'eco.furniture', 'eco.appliances'], [
                ['pickup_address', 'Pickup address', 'address', true],
                ['item_description', 'Item description', 'textarea', true],
                ['item_count', 'Number of items', 'number', true],
                ['floor', 'Pickup floor', 'number', false],
                ['elevator_available', 'Elevator available', 'boolean', false],
                ['preferred_date', 'Preferred date', 'date', true],
                ['disposal_notes', 'Disposal notes', 'textarea', false],
            ]],
            [['handyman.hourly', 'handyman.assembly', 'handyman.repair'], [
                ['service_address', 'Service address', 'address', true],
                ['task_description', 'Task description', 'textarea', true],
                ['preferred_date', 'Preferred date', 'date', true],
                ['photos_note', 'Photo or access notes', 'textarea', false],
                ['contact_phone', 'Contact phone', 'phone', true],
            ]],
            [['tow.emergency', 'roadside.assistance'], [
                ['vehicle_location', 'Vehicle location', 'address', true],
                ['destination_address', 'Destination address', 'address', false],
                ['vehicle_type', 'Vehicle type', 'text', true],
                ['problem_description', 'Problem description', 'textarea', true],
                ['contact_phone', 'Contact phone', 'phone', true],
                ['urgent', 'Urgent assistance required', 'boolean', false],
            ]],
            [['personal-task.errand', 'personal-task.concierge'], [
                ['task_location', 'Task location', 'address', true],
                ['task_description', 'Task description', 'textarea', true],
                ['preferred_time', 'Preferred time', 'datetime', true],
                ['contact_phone', 'Contact phone', 'phone', true],
            ]],
            ['classifieds.delivery', [
                ['pickup_address', 'Pickup address', 'address', true],
                ['dropoff_address', 'Drop-off address', 'address', true],
                ['item_description', 'Item description', 'textarea', true],
                ['seller_contact', 'Seller contact', 'text', true],
                ['preferred_time', 'Preferred time', 'datetime', true],
            ]],
        ];

        foreach ($groups as [$keys, $fields]) {
            foreach ((array) $keys as $scenarioKey) {
                $scenario = ServiceScenario::where('scenario_key', $scenarioKey)->first();

                if (! $scenario) {
                    continue;
                }

                foreach ($fields as $sort => [$key, $label, $type, $required]) {
                    ServiceScenarioField::updateOrCreate(
                        ['scenario_id' => $scenario->id, 'field_key' => $key],
                        ['label' => $label, 'type' => $type, 'required' => $required, 'sort_order' => $sort, 'is_active' => true],
                    );
                }
            }
        }
    }
}
