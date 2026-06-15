<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class WorkerSettlementRuleReview extends Model {
    use SoftDeletes;
    protected $fillable=['worker_settlement_rule_id','review_type','status','requested_by_id','reviewer_id','requested_at','reviewed_at','decision_note','evidence_summary','evidence_reference','metadata'];
    protected function casts():array{return ['requested_at'=>'datetime','reviewed_at'=>'datetime','metadata'=>'array'];}
    public function rule(){return $this->belongsTo(WorkerSettlementRule::class,'worker_settlement_rule_id');}
    public function events(){return $this->hasMany(WorkerSettlementRuleReviewEvent::class)->latest('created_at');}
}
