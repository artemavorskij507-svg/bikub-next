<?php
namespace App\Models;use Illuminate\Database\Eloquent\Model;
class SecurityGovernanceNotification extends Model{protected $fillable=['notification_number','type','severity','status','subject','message','related_type','related_id','assigned_to_id','created_by_id','acknowledged_by_id','resolved_by_id','acknowledged_at','resolved_at','metadata'];protected function casts():array{return ['acknowledged_at'=>'datetime','resolved_at'=>'datetime','metadata'=>'array'];}public function events(){return $this->hasMany(SecurityGovernanceNotificationEvent::class)->latest('created_at');}}
