<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('operation_zones', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type')->index();
            $table->string('status')->default('active')->index();
            $table->string('geometry_type');
            $table->json('coordinates');
            $table->unsignedInteger('radius_meters')->nullable();
            $table->string('color')->nullable();
            $table->foreignId('created_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('operation_zone_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operation_zone_id')->constrained()->cascadeOnDelete();
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('event_type')->index();
            $table->text('description')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        foreach ([
            'default_map_layer' => 'standard',
            'enabled_map_layers' => ['standard', 'satellite', 'hybrid', 'terrain'],
            'satellite_provider' => 'esri_world_imagery',
            'terrain_provider' => 'opentopomap',
            'hybrid_provider' => 'esri_world_imagery_reference',
            'map_refresh_seconds' => 12,
            'stale_gps_seconds' => 120,
        ] as $name => $value) {
            DB::table('settings')->insertOrIgnore([
                'group' => 'map',
                'name' => $name,
                'locked' => false,
                'payload' => json_encode($value),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('operation_zone_events');
        Schema::dropIfExists('operation_zones');
    }
};
