<?php
namespace App\Models;use Illuminate\Database\Eloquent\Model;
class EvidenceRetentionJob extends Model{protected $fillable=['job_number','status','mode','candidate_count','deleted_count','failed_count','cutoff_date','requested_by_id','approved_by_id','executed_by_id','requested_at','approved_at','executed_at','failure_reason','notes','metadata'];protected function casts():array{return ['cutoff_date'=>'date','requested_at'=>'datetime','approved_at'=>'datetime','executed_at'=>'datetime','metadata'=>'array'];}public function items(){return $this->hasMany(EvidenceRetentionJobItem::class);}}
