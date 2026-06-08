<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('service_scenarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained('service_categories')->nullOnDelete();
            $table->string('scenario_key')->unique();
            $table->string('slug');
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->text('description')->nullable();
            $table->string('service_type')->index();
            $table->string('status', 32)->default('draft')->index();
            $table->boolean('requires_pickup_address')->default(false);
            $table->boolean('requires_dropoff_address')->default(false);
            $table->boolean('requires_worker')->default(true);
            $table->boolean('requires_partner')->default(false);
            $table->boolean('requires_payment')->default(true);
            $table->boolean('supports_scheduling')->default(false);
            $table->boolean('supports_live_tracking')->default(false);
            $table->decimal('base_price', 12, 2)->nullable();
            $table->string('currency', 3)->default('NOK');
            $table->integer('sort_order')->default(0);
            $table->json('metadata')->nullable();
            $table->json('form_schema')->nullable();
            $table->timestamps();

            $table->unique(['slug', 'service_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_scenarios');
    }
};
