<?php
namespace App\Models;use Illuminate\Database\Eloquent\Model;
class EvidenceRetentionJobEvent extends Model{public $timestamps=false;protected $fillable=['evidence_retention_job_id','actor_id','event_type','description','metadata','created_at'];protected function casts():array{return ['metadata'=>'array','created_at'=>'datetime'];}}
