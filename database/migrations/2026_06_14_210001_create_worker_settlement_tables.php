<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('worker_settlement_entries', function (Blueprint $t) {
            $t->id(); $t->string('entry_number')->unique(); $t->foreignId('worker_id')->nullable()->constrained('users')->nullOnDelete(); $t->foreignId('worker_profile_id')->nullable()->constrained()->nullOnDelete(); $t->foreignId('order_id')->nullable()->constrained()->nullOnDelete(); $t->foreignId('dispatch_assignment_id')->nullable()->constrained()->nullOnDelete(); $t->foreignId('billing_document_id')->nullable()->constrained()->nullOnDelete(); $t->foreignId('payment_record_id')->nullable()->constrained()->nullOnDelete(); $t->foreignId('completion_proof_id')->nullable()->constrained('order_completion_proofs')->nullOnDelete(); $t->string('status')->default('blocked')->index(); $t->string('currency',3)->default('NOK'); $t->decimal('gross_amount',12,2)->nullable(); $t->decimal('platform_fee_amount',12,2)->nullable(); $t->decimal('worker_amount',12,2)->nullable(); $t->decimal('tax_amount',12,2)->nullable(); $t->string('calculation_basis')->nullable(); $t->text('blocker_reason')->nullable(); $t->timestamp('ready_at')->nullable(); $t->timestamp('approved_at')->nullable(); $t->timestamp('paid_at')->nullable(); $t->json('metadata')->nullable(); $t->foreignId('created_by_id')->nullable()->constrained('users')->nullOnDelete(); $t->foreignId('updated_by_id')->nullable()->constrained('users')->nullOnDelete(); $t->timestamps(); $t->softDeletes(); $t->unique('order_id');
        });
        Schema::create('worker_settlement_events', function (Blueprint $t) {
            $t->id(); $t->foreignId('settlement_entry_id')->constrained('worker_settlement_entries')->cascadeOnDelete(); $t->foreignId('order_id')->nullable()->constrained()->nullOnDelete(); $t->foreignId('worker_id')->nullable()->constrained('users')->nullOnDelete(); $t->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete(); $t->string('event_type')->index(); $t->string('from_value')->nullable(); $t->string('to_value')->nullable(); $t->text('description')->nullable(); $t->json('metadata')->nullable(); $t->timestamp('created_at')->useCurrent();
        });
    }
    public function down(): void { Schema::dropIfExists('worker_settlement_events'); Schema::dropIfExists('worker_settlement_entries'); }
};
