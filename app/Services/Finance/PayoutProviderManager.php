<?php
namespace App\Services\Finance;
use App\Contracts\Payouts\PayoutProviderInterface;
use App\Services\Finance\Payouts\DisabledPayoutProvider;
use App\Services\Finance\Payouts\ManualBankReviewPayoutProvider;
use App\Settings\PayoutSettings;
class PayoutProviderManager { public function resolve():PayoutProviderInterface{$s=app(PayoutSettings::class);return $s->payout_provider_key==='manual_bank_review'?app(ManualBankReviewPayoutProvider::class):app(DisabledPayoutProvider::class);} public function status():array{$s=app(PayoutSettings::class);$p=$this->resolve();return ['key'=>$p->providerKey(),'environment'=>$s->payout_environment,'configured'=>$p->isConfigured(),'outbound_enabled'=>$s->payout_outbound_enabled,'manual_review_enabled'=>$s->payout_manual_review_enabled,'approval_required'=>$s->payout_approval_required,'reason'=>$p->providerKey()==='manual_bank_review'?'Manual payout evidence workflow is not implemented.':'Payout provider is not configured.'];} }
