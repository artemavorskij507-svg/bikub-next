<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void { Schema::create('worker_documents', function(Blueprint $t){$t->id();$t->foreignId('worker_application_id')->nullable()->constrained()->cascadeOnDelete();$t->foreignId('worker_profile_id')->nullable()->constrained()->cascadeOnDelete();$t->foreignId('user_id')->nullable()->constrained()->nullOnDelete();$t->string('document_type')->index();$t->string('status')->default('pending')->index();$t->string('file_path')->nullable();$t->string('original_filename')->nullable();$t->string('mime_type')->nullable();$t->unsignedBigInteger('size_bytes')->nullable();$t->timestamp('reviewed_at')->nullable();$t->foreignId('reviewed_by_user_id')->nullable()->constrained('users')->nullOnDelete();$t->text('rejection_reason')->nullable();$t->json('metadata')->nullable();$t->timestamps();});}
 public function down(): void { Schema::dropIfExists('worker_documents'); }
};
