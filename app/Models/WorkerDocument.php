<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
class WorkerDocument extends Model implements HasMedia {
 use InteractsWithMedia;
 protected $fillable=['worker_application_id','worker_profile_id','user_id','document_type','status','file_path','original_filename','mime_type','size_bytes','reviewed_at','reviewed_by_user_id','rejection_reason','metadata','required','manually_verified','expires_at','verification_note','approved_at','rejected_at','retention_until','compliance_status','risk_level'];
 protected function casts():array{return ['metadata'=>'array','reviewed_at'=>'datetime','approved_at'=>'datetime','rejected_at'=>'datetime','retention_until'=>'datetime','required'=>'boolean','manually_verified'=>'boolean','expires_at'=>'datetime'];}
 public function application(){return $this->belongsTo(WorkerApplication::class,'worker_application_id');} public function profile(){return $this->belongsTo(WorkerProfile::class,'worker_profile_id');}
 public function supportTickets(){return $this->hasMany(SupportTicket::class)->orderByDesc('created_at');}
 public function registerMediaCollections():void{$this->addMediaCollection('worker_documents')->useDisk('local')->singleFile();}
 public function hasReviewableFile():bool{return $this->hasMedia('worker_documents')||filled($this->file_path);}
}
