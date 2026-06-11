<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('worker_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('display_name')->nullable();
            $table->string('worker_type')->default('courier')->index();
            $table->string('status')->default('pending')->index();
            $table->string('phone')->nullable();
            $table->string('vehicle_type')->nullable();
            $table->string('service_area')->nullable();
            foreach (['can_deliver', 'can_move', 'can_handle_eco', 'can_do_handyman', 'can_tow', 'can_run_errands'] as $capability) {
                $table->boolean($capability)->default(false);
            }
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('rejected_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('worker_profiles');
    }
};
