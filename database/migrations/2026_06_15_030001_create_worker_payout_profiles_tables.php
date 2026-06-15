<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up():void{
  Schema::create('worker_payout_profiles',function(Blueprint $t){$t->id();$t->foreignId('worker_id')->unique()->constrained('users')->cascadeOnDelete();$t->foreignId('worker_profile_id')->nullable()->constrained()->nullOnDelete();$t->string('status')->default('draft')->index();$t->string('country',2)->default('NO');$t->string('currency',3)->default('NOK');$t->string('payout_method')->default('manual_bank_review');$t->string('account_holder_name')->nullable();$t->text('encrypted_bank_account')->nullable();$t->string('bank_account_last_four',4)->nullable();$t->text('encrypted_iban')->nullable();$t->string('iban_last_four',4)->nullable();$t->text('encrypted_swift_bic')->nullable();$t->string('swift_bic_last_four',4)->nullable();$t->text('encrypted_vipps_phone')->nullable();$t->string('vipps_phone_last_four',4)->nullable();$t->string('tax_profile_status')->default('missing');$t->string('identity_profile_status')->default('missing');$t->timestamp('submitted_at')->nullable();$t->timestamp('approved_at')->nullable();$t->timestamp('rejected_at')->nullable();$t->foreignId('approved_by_id')->nullable()->constrained('users')->nullOnDelete();$t->foreignId('rejected_by_id')->nullable()->constrained('users')->nullOnDelete();$t->text('review_note')->nullable();$t->text('rejection_reason')->nullable();$t->json('metadata')->nullable();$t->timestamps();$t->softDeletes();});
  Schema::create('worker_payout_profile_events',function(Blueprint $t){$t->id();$t->foreignId('worker_payout_profile_id')->constrained()->cascadeOnDelete();$t->foreignId('worker_id')->nullable()->constrained('users')->nullOnDelete();$t->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();$t->string('event_type')->index();$t->text('description')->nullable();$t->json('metadata')->nullable();$t->timestamp('created_at')->useCurrent();});
 }
 public function down():void{Schema::dropIfExists('worker_payout_profile_events');Schema::dropIfExists('worker_payout_profiles');}
};
