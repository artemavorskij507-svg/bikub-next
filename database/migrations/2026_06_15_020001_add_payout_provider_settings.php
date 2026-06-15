<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
return new class extends Migration {
 public function up():void{foreach(['payout_provider_key'=>'disabled','payout_environment'=>'local_review','payout_outbound_enabled'=>false,'payout_manual_review_enabled'=>false,'payout_bank_account_collection_enabled'=>false,'payout_approval_required'=>true,'payout_minimum_amount'=>null,'payout_currency'=>'NOK','payout_provider_notes'=>null] as $name=>$value)DB::table('settings')->insertOrIgnore(['group'=>'payout','name'=>$name,'locked'=>false,'payload'=>json_encode($value),'created_at'=>now(),'updated_at'=>now()]);}
 public function down():void{DB::table('settings')->where('group','payout')->delete();}
};
