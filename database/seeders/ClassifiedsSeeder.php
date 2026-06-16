<?php

namespace Database\Seeders;

use App\Models\ClassifiedCategory;
use Illuminate\Database\Seeder;

class ClassifiedsSeeder extends Seeder
{
    public function run(): void
    {
        collect([
            ['name' => 'Products', 'slug' => 'products', 'description' => 'Local items for sale in Narvik.', 'icon' => 'box', 'image_path' => 'images/bikube/home/category-classifieds.png', 'sort_order' => 10],
            ['name' => 'Home', 'slug' => 'home', 'description' => 'Furniture, home equipment and everyday household listings.', 'icon' => 'home', 'image_path' => 'images/bikube/home/category-handyman.png', 'sort_order' => 20],
            ['name' => 'Transport', 'slug' => 'transport', 'description' => 'Large items, moving help and local delivery needs.', 'icon' => 'truck', 'image_path' => 'images/bikube/home/category-delivery.png', 'sort_order' => 30],
            ['name' => 'Local help', 'slug' => 'local-help', 'description' => 'Errands, practical help and local assistant requests.', 'icon' => 'hand', 'image_path' => 'images/bikube/home/category-assistant.png', 'sort_order' => 40],
            ['name' => 'Services', 'slug' => 'services', 'description' => 'Local service offers and requests within BiKuBe compliance boundaries.', 'icon' => 'sparkles', 'image_path' => 'images/bikube/home/scenario-assistant.png', 'sort_order' => 50],
        ])->each(function (array $category) {
            ClassifiedCategory::updateOrCreate(
                ['slug' => $category['slug']],
                [...$category, 'is_active' => true],
            );
        });
    }
}
