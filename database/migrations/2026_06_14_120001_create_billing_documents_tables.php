<?php
use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up():void{
  Schema::create('billing_documents',function(Blueprint $t){$t->id();$t->string('document_number')->unique();$t->string('document_type')->default('invoice')->index();$t->string('status')->default('draft')->index();$t->foreignId('order_id')->nullable()->constrained()->nullOnDelete();$t->foreignId('customer_id')->nullable()->constrained('users')->nullOnDelete();$t->foreignId('order_price_quote_id')->nullable()->constrained()->nullOnDelete();$t->string('currency',3)->default('NOK');$t->decimal('subtotal_amount',12,2)->nullable();$t->decimal('tax_amount',12,2)->nullable();$t->decimal('total_amount',12,2)->nullable();$t->timestamp('issued_at')->nullable();$t->timestamp('due_at')->nullable();$t->timestamp('paid_at')->nullable();$t->timestamp('voided_at')->nullable();$t->json('metadata')->nullable();$t->foreignId('created_by_id')->nullable()->constrained('users')->nullOnDelete();$t->foreignId('updated_by_id')->nullable()->constrained('users')->nullOnDelete();$t->timestamps();$t->softDeletes();});
  Schema::create('billing_document_events',function(Blueprint $t){$t->id();$t->foreignId('billing_document_id')->constrained()->cascadeOnDelete();$t->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();$t->string('event_type')->index();$t->string('from_value')->nullable();$t->string('to_value')->nullable();$t->text('description')->nullable();$t->json('metadata')->nullable();$t->timestamp('created_at');});
 }
 public function down():void{Schema::dropIfExists('billing_document_events');Schema::dropIfExists('billing_documents');}
};
