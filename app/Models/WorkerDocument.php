<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class WorkerDocument extends Model {
 protected $fillable=['worker_application_id','worker_profile_id','user_id','document_type','status','file_path','original_filename','mime_type','size_bytes','reviewed_at','reviewed_by_user_id','rejection_reason','metadata'];
 protected function casts():array{return ['metadata'=>'array','reviewed_at'=>'datetime'];}
 public function application(){return $this->belongsTo(WorkerApplication::class,'worker_application_id');} public function profile(){return $this->belongsTo(WorkerProfile::class,'worker_profile_id');}
}
