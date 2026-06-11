<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up():void{Schema::table('worker_documents',function(Blueprint $t){$t->boolean('required')->default(false);$t->boolean('manually_verified')->default(false);$t->timestamp('expires_at')->nullable();$t->text('verification_note')->nullable();});}
 public function down():void{Schema::table('worker_documents',function(Blueprint $t){$t->dropColumn(['required','manually_verified','expires_at','verification_note']);});}
};
