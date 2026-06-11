<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('worker_location_pings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('worker_profile_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('dispatch_assignment_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->decimal('accuracy_meters', 10, 2)->nullable();
            $table->decimal('heading', 8, 2)->nullable();
            $table->decimal('speed_mps', 10, 3)->nullable();
            $table->string('source')->default('browser');
            $table->timestamp('captured_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->index(['user_id', 'created_at']);
            $table->index('order_id');
            $table->index('dispatch_assignment_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('worker_location_pings');
    }
};
