<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class WorkerApplicationEvent extends Model {
 public $timestamps=false; protected $fillable=['worker_application_id','actor_type','actor_id','event_type','from_status','to_status','payload','note','created_at'];
 protected function casts():array{return ['payload'=>'array','created_at'=>'datetime'];} public function application(){return $this->belongsTo(WorkerApplication::class,'worker_application_id');}
}
