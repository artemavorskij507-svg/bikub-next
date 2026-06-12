<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
class SupportMessage extends Model implements HasMedia {
 use InteractsWithMedia,LogsActivity,SoftDeletes;
 protected $fillable=['support_ticket_id','author_id','author_type','message_type','body','visibility','is_system','metadata'];
 protected function casts():array{return ['is_system'=>'boolean','metadata'=>'array'];}
 public function ticket(){return $this->belongsTo(SupportTicket::class,'support_ticket_id');} public function author(){return $this->belongsTo(User::class,'author_id');}
 public function registerMediaCollections():void{$this->addMediaCollection('support_message_attachments')->useDisk('local');}
 public function getActivitylogOptions():LogOptions{return LogOptions::defaults()->logOnly(['message_type','visibility','is_system'])->logOnlyDirty();}
}
