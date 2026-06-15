<?php
namespace App\Settings;
use Spatie\LaravelSettings\Settings;
class PayoutSettings extends Settings {
 public string $payout_provider_key; public string $payout_environment; public bool $payout_outbound_enabled; public bool $payout_manual_review_enabled; public bool $payout_bank_account_collection_enabled; public bool $payout_approval_required; public ?float $payout_minimum_amount; public string $payout_currency; public ?string $payout_provider_notes;
 public static function group():string{return 'payout';}
}
