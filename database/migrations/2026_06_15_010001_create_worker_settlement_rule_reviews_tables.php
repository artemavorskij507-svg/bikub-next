<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('worker_settlement_rule_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('worker_settlement_rule_id')->constrained()->cascadeOnDelete();
            $table->string('review_type')->index();
            $table->string('status')->default('requested')->index();
            $table->foreignId('requested_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('reviewer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('requested_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('decision_note')->nullable();
            $table->text('evidence_summary')->nullable();
            $table->string('evidence_reference')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('worker_settlement_rule_review_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('worker_settlement_rule_review_id')->constrained()->cascadeOnDelete();
            $table->foreignId('worker_settlement_rule_id')->constrained()->cascadeOnDelete();
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('event_type')->index();
            $table->text('description')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }
    public function down(): void { Schema::dropIfExists('worker_settlement_rule_review_events'); Schema::dropIfExists('worker_settlement_rule_reviews'); }
};
