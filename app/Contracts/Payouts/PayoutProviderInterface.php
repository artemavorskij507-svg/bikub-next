<?php
namespace App\Contracts\Payouts;
use App\Data\Payouts\PayoutProviderResult;
use App\Models\WorkerSettlementEntry;
interface PayoutProviderInterface {
    public function providerKey():string;
    public function isConfigured():bool;
    public function createPayoutInstruction(WorkerSettlementEntry $entry):PayoutProviderResult;
    public function markPayoutPaid(WorkerSettlementEntry $entry,array $providerPayload=[]):PayoutProviderResult;
    public function cancelPayout(WorkerSettlementEntry $entry,string $reason):PayoutProviderResult;
}
