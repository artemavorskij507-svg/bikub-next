<?php
namespace App\Services\Finance;
use App\Contracts\Payouts\PayoutProviderInterface;
use App\Services\Finance\Payouts\DisabledPayoutProvider;
class PayoutProviderManager { public function resolve():PayoutProviderInterface{return app(DisabledPayoutProvider::class);} public function status():array{$p=$this->resolve();return ['key'=>$p->providerKey(),'configured'=>$p->isConfigured(),'reason'=>'Payout provider is not configured.'];} }
