<?php
namespace App\Models;use Illuminate\Database\Eloquent\Model;
class WorkerPayoutReviewEvidenceEvent extends Model{public $timestamps=false;protected $fillable=['evidence_id','worker_payout_review_id','actor_id','event_type','description','metadata','created_at'];protected function casts():array{return ['metadata'=>'array','created_at'=>'datetime'];}}
