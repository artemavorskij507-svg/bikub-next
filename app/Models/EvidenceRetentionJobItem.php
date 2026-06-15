<?php
namespace App\Models;use Illuminate\Database\Eloquent\Model;
class EvidenceRetentionJobItem extends Model{protected $fillable=['evidence_retention_job_id','evidence_id','status','reason','metadata'];protected function casts():array{return ['metadata'=>'array'];}public function evidence(){return $this->belongsTo(WorkerPayoutReviewEvidence::class,'evidence_id');}}
