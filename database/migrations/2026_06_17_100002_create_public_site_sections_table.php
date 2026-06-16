<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('public_site_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_id')->constrained('public_site_pages')->cascadeOnDelete();
            $table->string('section_type', 64)->index();
            $table->json('title')->nullable();
            $table->json('subtitle')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true)->index();
            $table->json('config')->nullable();
            $table->timestamps();

            $table->index(['page_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('public_site_sections');
    }
};
