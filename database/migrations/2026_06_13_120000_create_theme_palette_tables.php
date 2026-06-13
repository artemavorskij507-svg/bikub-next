<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('user_theme_preferences', function (Blueprint $table) {
            $table->id(); $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('hex', 7); $table->string('source')->nullable();
            $table->foreignId('updated_by_id')->nullable()->constrained('users')->nullOnDelete(); $table->timestamps();
        });
        Schema::create('theme_palette_events', function (Blueprint $table) {
            $table->id(); $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete(); $table->string('event_type');
            $table->string('from_hex',7)->nullable(); $table->string('to_hex',7)->nullable(); $table->json('metadata')->nullable(); $table->timestamp('created_at');
        });
        $defaults = ['enabled'=>true,'default_hex'=>'#9cff3f','access_mode'=>'allow','allowed_roles'=>['owner','admin','dispatcher','finance','support','content_manager','workforce_manager','security_manager','worker'],'apply_admin'=>true,'apply_account'=>true,'apply_worker'=>true,'apply_public'=>false,'allow_custom_hex'=>true,'allow_presets'=>true];
        foreach ($defaults as $name=>$value) DB::table('settings')->updateOrInsert(['group'=>'theme_palette','name'=>$name], ['locked'=>false,'payload'=>json_encode($value),'created_at'=>now(),'updated_at'=>now()]);
    }
    public function down(): void { Schema::dropIfExists('theme_palette_events'); Schema::dropIfExists('user_theme_preferences'); DB::table('settings')->where('group','theme_palette')->delete(); }
};
