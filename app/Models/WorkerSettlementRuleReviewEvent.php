<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class WorkerSettlementRuleReviewEvent extends Model {
    public $timestamps=false;
    protected $fillable=['worker_settlement_rule_review_id','worker_settlement_rule_id','actor_id','event_type','description','metadata','created_at'];
    protected function casts():array{return ['metadata'=>'array','created_at'=>'datetime'];}
}
