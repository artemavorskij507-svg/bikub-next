<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class WorkerPayoutReviewEvidence extends Model{
 use SoftDeletes;
 protected $table='worker_payout_review_evidence';
 protected $fillable=['worker_payout_review_id','worker_id','uploaded_by_id','status','evidence_type','original_filename','stored_disk','stored_path','mime_type','size_bytes','sha256_hash','visibility','reviewer_note','rejection_reason','metadata','scan_status','scan_engine','scan_signature','scanned_at','scan_error','quarantined_at','quarantine_reason','retention_until','scheduled_deletion_at','deleted_by_retention_at','download_override_required','download_override_reason','download_override_by_id','download_override_at'];
 protected $hidden=['stored_path','reviewer_note','metadata'];
 protected function casts():array{return ['metadata'=>'array','scanned_at'=>'datetime','quarantined_at'=>'datetime','retention_until'=>'date','scheduled_deletion_at'=>'datetime','deleted_by_retention_at'=>'datetime','download_override_required'=>'boolean','download_override_at'=>'datetime'];}
 public function review(){return $this->belongsTo(WorkerPayoutReview::class,'worker_payout_review_id');}
 public function events(){return $this->hasMany(WorkerPayoutReviewEvidenceEvent::class,'evidence_id')->latest('created_at');}
}
