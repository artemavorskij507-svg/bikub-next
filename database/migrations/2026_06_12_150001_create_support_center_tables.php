<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('support_tickets', function (Blueprint $table): void {
            $table->id();
            $table->string('ticket_number')->unique();
            $table->string('status')->default('open')->index();
            $table->string('priority')->default('normal')->index();
            $table->string('category')->default('other')->index();
            $table->string('subject');
            $table->text('summary')->nullable();
            $table->string('source')->default('admin');
            $table->string('visibility')->default('internal');
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('worker_profile_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('worker_document_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('dispatch_assignment_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('created_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('assigned_to_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('resolved_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamp('last_message_at')->nullable()->index();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('support_messages', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('support_ticket_id')->constrained()->cascadeOnDelete();
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('author_type');
            $table->string('message_type');
            $table->text('body');
            $table->string('visibility')->default('internal');
            $table->boolean('is_system')->default(false);
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('support_ticket_events', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('support_ticket_id')->constrained()->cascadeOnDelete();
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('event_type')->index();
            $table->string('from_value')->nullable();
            $table->string('to_value')->nullable();
            $table->text('description')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('created_at')->useCurrent()->index();
        });

        Schema::create('support_ticket_assignments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('support_ticket_id')->constrained()->cascadeOnDelete();
            $table->foreignId('assigned_to_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('assigned_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status')->default('active')->index();
            $table->timestamp('assigned_at');
            $table->timestamp('released_at')->nullable();
            $table->text('release_reason')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('support_ticket_assignments');
        Schema::dropIfExists('support_ticket_events');
        Schema::dropIfExists('support_messages');
        Schema::dropIfExists('support_tickets');
    }
};
