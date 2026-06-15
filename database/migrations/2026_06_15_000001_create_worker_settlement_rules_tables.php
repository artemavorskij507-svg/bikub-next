<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('worker_settlement_rules', function (Blueprint $table) {
            $table->id();
            $table->string('rule_number')->unique();
            $table->string('name');
            $table->string('status')->default('draft')->index();
            $table->string('service_scenario_key')->nullable()->index();
            $table->string('worker_role')->nullable()->index();
            $table->string('calculation_type');
            $table->decimal('worker_share_percent', 5, 2)->nullable();
            $table->decimal('platform_fee_percent', 5, 2)->nullable();
            $table->decimal('fixed_worker_amount', 12, 2)->nullable();
            $table->string('currency', 3)->default('NOK');
            $table->decimal('min_order_amount', 12, 2)->nullable();
            $table->decimal('max_order_amount', 12, 2)->nullable();
            $table->string('legal_review_status')->default('required')->index();
            $table->string('tax_review_status')->default('required')->index();
            $table->date('effective_from')->nullable();
            $table->date('effective_until')->nullable();
            $table->foreignId('approved_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('rejected_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('rejected_at')->nullable();
            $table->text('approval_note')->nullable();
            $table->json('metadata')->nullable();
            $table->foreignId('created_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('worker_settlement_rule_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('worker_settlement_rule_id')->constrained()->cascadeOnDelete();
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('event_type')->index();
            $table->string('from_value')->nullable();
            $table->string('to_value')->nullable();
            $table->text('description')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('worker_settlement_rule_events');
        Schema::dropIfExists('worker_settlement_rules');
    }
};
