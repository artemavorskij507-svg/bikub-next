<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class SupportTicket extends Model implements HasMedia
{
    use InteractsWithMedia, LogsActivity, SoftDeletes;

    protected $fillable = ['ticket_number', 'status', 'priority', 'category', 'subject', 'summary', 'source', 'visibility', 'order_id', 'customer_id', 'worker_profile_id', 'worker_document_id', 'dispatch_assignment_id', 'created_by_id', 'assigned_to_id', 'resolved_by_id', 'resolved_at', 'closed_at', 'last_message_at', 'metadata'];
    protected function casts(): array { return ['resolved_at'=>'datetime','closed_at'=>'datetime','last_message_at'=>'datetime','metadata'=>'array']; }
    public function order(){return $this->belongsTo(Order::class);}
    public function customer(){return $this->belongsTo(User::class,'customer_id');}
    public function workerProfile(){return $this->belongsTo(WorkerProfile::class);}
    public function workerDocument(){return $this->belongsTo(WorkerDocument::class);}
    public function dispatchAssignment(){return $this->belongsTo(DispatchAssignment::class);}
    public function creator(){return $this->belongsTo(User::class,'created_by_id');}
    public function assignee(){return $this->belongsTo(User::class,'assigned_to_id');}
    public function resolver(){return $this->belongsTo(User::class,'resolved_by_id');}
    public function messages(){return $this->hasMany(SupportMessage::class);}
    public function events(){return $this->hasMany(SupportTicketEvent::class)->orderByDesc('created_at');}
    public function assignments(){return $this->hasMany(SupportTicketAssignment::class)->orderByDesc('assigned_at');}
    public function registerMediaCollections():void{$this->addMediaCollection('support_ticket_attachments')->useDisk('local');}
    public function getActivitylogOptions():LogOptions{return LogOptions::defaults()->logOnly(['status','priority','assigned_to_id','resolved_at','closed_at'])->logOnlyDirty();}
}
