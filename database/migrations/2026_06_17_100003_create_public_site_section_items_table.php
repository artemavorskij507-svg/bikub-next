<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('public_site_section_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_id')->constrained('public_site_sections')->cascadeOnDelete();
            $table->string('item_type', 64)->index();
            $table->json('title')->nullable();
            $table->json('subtitle')->nullable();
            $table->json('body')->nullable();
            $table->json('cta_label')->nullable();
            $table->string('cta_route')->nullable();
            $table->string('image_path')->nullable();
            $table->string('mobile_image_path')->nullable();
            $table->string('icon')->nullable();
            $table->string('badge')->nullable();
            $table->string('linked_scenario_slug')->nullable()->index();
            $table->string('safety_label')->nullable();
            $table->json('payload')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();

            $table->index(['section_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('public_site_section_items');
    }
};
