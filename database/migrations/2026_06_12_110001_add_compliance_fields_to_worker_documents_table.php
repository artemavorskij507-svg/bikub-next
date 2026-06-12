<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('worker_documents', function (Blueprint $table): void {
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamp('retention_until')->nullable();
            $table->string('compliance_status')->nullable()->index();
            $table->string('risk_level')->nullable()->index();
        });
    }

    public function down(): void
    {
        Schema::table('worker_documents', function (Blueprint $table): void {
            $table->dropIndex(['compliance_status']);
            $table->dropIndex(['risk_level']);
            $table->dropColumn(['approved_at', 'rejected_at', 'retention_until', 'compliance_status', 'risk_level']);
        });
    }
};
