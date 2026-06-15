<?php
namespace App\Services\Finance\Payouts;
use App\Contracts\Payouts\PayoutProviderInterface;
use App\Data\Payouts\PayoutProviderResult;
use App\Models\WorkerSettlementEntry;
class DisabledPayoutProvider implements PayoutProviderInterface {
    public function providerKey():string{return 'disabled';}
    public function isConfigured():bool{return false;}
    public function createPayoutInstruction(WorkerSettlementEntry $entry):PayoutProviderResult{return PayoutProviderResult::blocked('Payout provider is not configured.');}
    public function markPayoutPaid(WorkerSettlementEntry $entry,array $providerPayload=[]):PayoutProviderResult{return PayoutProviderResult::blocked('Payout provider is not configured.');}
    public function cancelPayout(WorkerSettlementEntry $entry,string $reason):PayoutProviderResult{return PayoutProviderResult::blocked('Payout provider is not configured.');}
}
