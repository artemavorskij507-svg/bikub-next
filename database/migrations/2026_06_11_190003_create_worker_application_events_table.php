<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void { Schema::create('worker_application_events', function(Blueprint $t){$t->id();$t->foreignId('worker_application_id')->constrained()->cascadeOnDelete();$t->string('actor_type')->nullable();$t->unsignedBigInteger('actor_id')->nullable();$t->string('event_type')->index();$t->string('from_status')->nullable();$t->string('to_status')->nullable();$t->json('payload')->nullable();$t->text('note')->nullable();$t->timestamp('created_at')->useCurrent()->index();});}
 public function down(): void { Schema::dropIfExists('worker_application_events'); }
};
