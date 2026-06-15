<?php
namespace App\Services\Finance;
use App\Models\{User,WorkerSettlementRule,WorkerSettlementRuleReview};
use Illuminate\Validation\ValidationException;
class WorkerSettlementRuleReviewService {
    public function requestReview(WorkerSettlementRule $rule,string $type,User $actor,?string $note):WorkerSettlementRuleReview{
        throw_unless(in_array($type,['legal','tax','finance','compliance'],true),ValidationException::withMessages(['review_type'=>'Valid review type is required.']));
        $review=$rule->reviews()->create(['review_type'=>$type,'status'=>'requested','requested_by_id'=>$actor->id,'requested_at'=>now(),'decision_note'=>$note]);
        $this->event($review,$actor,'requested',$note?:ucfirst($type).' review requested.');$this->sync($rule);return $review;
    }
    public function markInReview(WorkerSettlementRuleReview $review,User $actor):WorkerSettlementRuleReview{return $this->transition($review,$actor,'in_review','marked_in_review','Review started.');}
    public function approveReview(WorkerSettlementRuleReview $review,User $actor,string $decisionNote,string $evidenceSummary):WorkerSettlementRuleReview{
        $this->assertApprover($actor);throw_if(blank($decisionNote),ValidationException::withMessages(['decision_note'=>'Decision note is required.']));throw_if(blank($evidenceSummary),ValidationException::withMessages(['evidence_summary'=>'Evidence summary is required.']));
        $review->update(['status'=>'approved','reviewer_id'=>$actor->id,'reviewed_at'=>now(),'decision_note'=>$decisionNote,'evidence_summary'=>$evidenceSummary]);$this->event($review,$actor,'approved',$decisionNote);$this->sync($review->rule);return $review->refresh();
    }
    public function rejectReview(WorkerSettlementRuleReview $review,User $actor,string $reason):WorkerSettlementRuleReview{$this->assertApprover($actor);return $this->transition($review,$actor,'rejected','rejected',$reason);}
    public function cancelReview(WorkerSettlementRuleReview $review,User $actor,string $reason):WorkerSettlementRuleReview{return $this->transition($review,$actor,'cancelled','cancelled',$reason);}
    public function getRuleReviewReadiness(WorkerSettlementRule $rule):array{$latest=$rule->reviews()->latest()->get()->unique('review_type')->keyBy('review_type');$blockers=[];foreach(['legal','tax','finance'] as $type){$review=$latest->get($type);if(!$review||$review->status!=='approved')$blockers[]=ucfirst($type).' review is not approved.';elseif(blank($review->evidence_summary))$blockers[]='Evidence summary is missing.';}return ['reviews'=>$latest,'ready'=>empty($blockers),'blockers'=>array_values(array_unique($blockers))];}
    private function transition(WorkerSettlementRuleReview $review,User $actor,string $status,string $event,string $reason):WorkerSettlementRuleReview{throw_if(blank($reason),ValidationException::withMessages(['reason'=>'Reason is required.']));$review->update(['status'=>$status,'reviewer_id'=>$actor->id,'reviewed_at'=>now(),'decision_note'=>$reason]);$this->event($review,$actor,$event,$reason);$this->sync($review->rule);return $review->refresh();}
    private function sync(WorkerSettlementRule $rule):void{$readiness=$this->getRuleReviewReadiness($rule);$rule->update(['legal_review_status'=>$readiness['reviews']->get('legal')?->status??'required','tax_review_status'=>$readiness['reviews']->get('tax')?->status??'required']);}
    private function event(WorkerSettlementRuleReview $review,User $actor,string $type,string $description):void{$review->events()->create(['worker_settlement_rule_id'=>$review->worker_settlement_rule_id,'actor_id'=>$actor->id,'event_type'=>$type,'description'=>$description,'created_at'=>now()]);$review->rule->events()->create(['actor_id'=>$actor->id,'event_type'=>'review_'.$type,'description'=>$description,'metadata'=>['review_id'=>$review->id,'review_type'=>$review->review_type],'created_at'=>now()]);}
    private function assertApprover(User $actor):void{throw_unless($actor->can('admin.finance.manage'),ValidationException::withMessages(['actor'=>'Finance management permission is required to decide reviews.']));}
}
