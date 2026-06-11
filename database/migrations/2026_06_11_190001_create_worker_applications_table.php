<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void { Schema::create('worker_applications', function(Blueprint $t){$t->id();$t->foreignId('user_id')->nullable()->constrained()->nullOnDelete();$t->string('name');$t->string('email')->index();$t->string('phone')->nullable();$t->string('worker_type')->default('courier')->index();$t->string('status')->default('submitted')->index();$t->string('desired_service_area')->nullable();$t->string('vehicle_type')->nullable();$t->json('capabilities')->nullable();$t->text('experience_notes')->nullable();$t->text('compliance_notes')->nullable();$t->timestamp('submitted_at')->nullable();$t->timestamp('reviewed_at')->nullable();$t->foreignId('reviewed_by_user_id')->nullable()->constrained('users')->nullOnDelete();$t->text('decision_reason')->nullable();$t->json('metadata')->nullable();$t->timestamps();});}
 public function down(): void { Schema::dropIfExists('worker_applications'); }
};
