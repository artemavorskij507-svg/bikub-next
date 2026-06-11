<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pricing_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_scenario_id')->nullable()->constrained()->nullOnDelete();
            $table->string('scenario_key')->nullable()->index();
            $table->string('name');
            $table->string('code')->unique();
            $table->string('type');
            $table->string('status')->default('active')->index();
            $table->string('currency', 3)->default('NOK');
            $table->decimal('base_amount', 12, 2)->nullable();
            $table->decimal('per_unit_amount', 12, 2)->nullable();
            $table->string('unit_key')->nullable();
            $table->decimal('min_amount', 12, 2)->nullable();
            $table->decimal('max_amount', 12, 2)->nullable();
            $table->json('conditions')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void { Schema::dropIfExists('pricing_rules'); }
};
