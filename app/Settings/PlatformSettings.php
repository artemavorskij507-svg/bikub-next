<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class PlatformSettings extends Settings
{
    public string $platform_name;
    public string $public_brand_name;
    public string $default_locale;
    public string $launch_region;
    public ?string $support_email;
    public ?string $support_phone;
    public ?string $maintenance_message;

    public static function group(): string
    {
        return 'platform';
    }
}
