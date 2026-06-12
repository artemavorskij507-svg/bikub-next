<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;
class WorkerApplication extends Model {
 use LogsActivity;
 protected $fillable=['user_id','name','email','phone','worker_type','status','desired_service_area','vehicle_type','capabilities','experience_notes','compliance_notes','submitted_at','reviewed_at','reviewed_by_user_id','decision_reason','metadata'];
 protected function casts(): array{return ['capabilities'=>'array','metadata'=>'array','submitted_at'=>'datetime','reviewed_at'=>'datetime'];}
 public function user(){return $this->belongsTo(User::class);} public function reviewer(){return $this->belongsTo(User::class,'reviewed_by_user_id');}
 public function documents(){return $this->hasMany(WorkerDocument::class);} public function events(){return $this->hasMany(WorkerApplicationEvent::class);}
 public function invitations(){return $this->hasMany(WorkerAccountInvitation::class);}
 public function getActivitylogOptions():LogOptions{return LogOptions::defaults()->logOnly(['user_id','status','reviewed_by_user_id','decision_reason'])->logOnlyDirty()->dontSubmitEmptyLogs();}
}
