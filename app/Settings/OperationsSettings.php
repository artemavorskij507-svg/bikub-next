<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class OperationsSettings extends Settings
{
    public bool $dispatch_enabled;
    public bool $gps_tracking_enabled;
    public bool $payment_provider_enabled;
    public bool $customer_tracking_enabled;
    public bool $manual_review_required_default;

    public static function group(): string
    {
        return 'operations';
    }
}
