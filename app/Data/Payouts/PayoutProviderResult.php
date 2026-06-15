<?php
namespace App\Data\Payouts;
class PayoutProviderResult {
    public function __construct(public readonly bool $successful,public readonly string $message,public readonly ?string $providerReference=null,public readonly array $metadata=[]){}
    public static function blocked(string $message):self{return new self(false,$message);}
}
