<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class WorkerApplication extends Model {
 protected $fillable=['user_id','name','email','phone','worker_type','status','desired_service_area','vehicle_type','capabilities','experience_notes','compliance_notes','submitted_at','reviewed_at','reviewed_by_user_id','decision_reason','metadata'];
 protected function casts(): array{return ['capabilities'=>'array','metadata'=>'array','submitted_at'=>'datetime','reviewed_at'=>'datetime'];}
 public function user(){return $this->belongsTo(User::class);} public function reviewer(){return $this->belongsTo(User::class,'reviewed_by_user_id');}
 public function documents(){return $this->hasMany(WorkerDocument::class);} public function events(){return $this->hasMany(WorkerApplicationEvent::class);}
}
