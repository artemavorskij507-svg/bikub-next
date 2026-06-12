<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;
class SupportTicketAssignment extends Model {
 use LogsActivity; protected $fillable=['support_ticket_id','assigned_to_id','assigned_by_id','status','assigned_at','released_at','release_reason'];
 protected function casts():array{return ['assigned_at'=>'datetime','released_at'=>'datetime'];}
 public function ticket(){return $this->belongsTo(SupportTicket::class,'support_ticket_id');} public function assignee(){return $this->belongsTo(User::class,'assigned_to_id');} public function assignedBy(){return $this->belongsTo(User::class,'assigned_by_id');}
 public function getActivitylogOptions():LogOptions{return LogOptions::defaults()->logOnly(['status','assigned_to_id','released_at'])->logOnlyDirty();}
}
