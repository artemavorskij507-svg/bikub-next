<?php
namespace App\Services\Finance\Payouts;
use App\Contracts\Payouts\PayoutProviderInterface;
use App\Data\Payouts\PayoutProviderResult;
use App\Models\WorkerSettlementEntry;
class ManualBankReviewPayoutProvider implements PayoutProviderInterface {
 public function providerKey():string{return 'manual_bank_review';}
 public function isConfigured():bool{return false;}
 public function createPayoutInstruction(WorkerSettlementEntry $entry):PayoutProviderResult{return PayoutProviderResult::blocked('Manual payout evidence workflow is not implemented.');}
 public function markPayoutPaid(WorkerSettlementEntry $entry,array $providerPayload=[]):PayoutProviderResult{return PayoutProviderResult::blocked('Manual payout evidence workflow is not implemented.');}
 public function cancelPayout(WorkerSettlementEntry $entry,string $reason):PayoutProviderResult{return PayoutProviderResult::blocked('Manual payout evidence workflow is not implemented.');}
}
