<?php
namespace App\Services\Finance;
use App\Models\{Order,WorkerSettlementEntry};
class PayoutReadinessService {
    public function __construct(private PayoutProviderManager $providers,private WorkerSettlementRuleReviewService $reviews){}
    public function forOrder(Order $order):array{
        $entry=$order->workerSettlementEntries()->latest()->first();$blockers=[];
        if(!$entry)$blockers[]='Settlement entry is required.';
        $ruleId=data_get($entry?->metadata,'worker_settlement_rule_id');$rule=$ruleId?\App\Models\WorkerSettlementRule::find($ruleId):null;
        if(!$rule||$rule->status!=='active')$blockers[]='Settlement rule is not active and approved.';
        elseif(!$this->reviews->getRuleReviewReadiness($rule)['ready'])$blockers=array_merge($blockers,$this->reviews->getRuleReviewReadiness($rule)['blockers']);
        if(!$order->completionProofs()->where('status','accepted')->exists())$blockers[]='Customer completion confirmation is required.';
        if(!$order->billingDocuments()->where('status','issued')->exists())$blockers[]='Issued invoice is required.';
        if(!$order->paymentRecords()->where('status','captured')->exists())$blockers[]='Payment is not captured.';
        if($order->completionProofs()->where('status','disputed')->exists())$blockers[]='Open completion dispute blocks payout.';
        if(!$this->providers->resolve()->isConfigured())$blockers[]='Payout provider is not configured.';
        if($entry?->status==='paid')$blockers[]='Settlement is already marked paid.';
        return ['entry'=>$entry,'rule'=>$rule,'provider'=>$this->providers->status(),'ready'=>empty($blockers),'blockers'=>array_values(array_unique($blockers)),'next_action'=>empty($blockers)?'Prepare payout instruction.':$blockers[0]];
    }
    public function forEntry(WorkerSettlementEntry $entry):array{return $this->forOrder($entry->order);}
}
