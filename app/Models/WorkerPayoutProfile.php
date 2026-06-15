<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class WorkerPayoutProfile extends Model {
 use SoftDeletes;
 protected $fillable=['worker_id','worker_profile_id','status','country','currency','payout_method','account_holder_name','encrypted_bank_account','bank_account_last_four','encrypted_iban','iban_last_four','encrypted_swift_bic','swift_bic_last_four','encrypted_vipps_phone','vipps_phone_last_four','tax_profile_status','identity_profile_status','submitted_at','approved_at','rejected_at','approved_by_id','rejected_by_id','review_note','rejection_reason','metadata'];
 protected $hidden=['encrypted_bank_account','encrypted_iban','encrypted_swift_bic','encrypted_vipps_phone'];
 protected function casts():array{return ['submitted_at'=>'datetime','approved_at'=>'datetime','rejected_at'=>'datetime','metadata'=>'array'];}
 public function worker(){return $this->belongsTo(User::class,'worker_id');}public function workerProfile(){return $this->belongsTo(WorkerProfile::class);}public function events(){return $this->hasMany(WorkerPayoutProfileEvent::class)->latest('created_at');}
}
