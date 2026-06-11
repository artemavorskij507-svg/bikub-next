<?php
namespace App\Models; use Illuminate\Database\Eloquent\Model;
class WorkerAccountInvitation extends Model {
 protected $fillable=['worker_application_id','invited_user_id','invited_by_user_id','email','token_hash','status','expires_at','accepted_at','cancelled_at','metadata'];
 protected function casts():array{return ['expires_at'=>'datetime','accepted_at'=>'datetime','cancelled_at'=>'datetime','metadata'=>'array'];}
 public function application(){return $this->belongsTo(WorkerApplication::class,'worker_application_id');} public function invitedUser(){return $this->belongsTo(User::class,'invited_user_id');} public function invitedBy(){return $this->belongsTo(User::class,'invited_by_user_id');}
 public function isPending():bool{return $this->status==='pending'&&!$this->isExpired();} public function isExpired():bool{return $this->expires_at?->isPast()??false;} public function markAccepted(User $u):void{$this->update(['status'=>'accepted','invited_user_id'=>$u->id,'accepted_at'=>now()]);} public function markCancelled():void{$this->update(['status'=>'cancelled','cancelled_at'=>now()]);}
}
