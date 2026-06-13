<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class ThemePaletteSettings extends Settings
{
    public bool $enabled;
    public string $default_hex;
    public string $access_mode;
    public array $allowed_roles;
    public bool $apply_admin;
    public bool $apply_account;
    public bool $apply_worker;
    public bool $apply_public;
    public bool $allow_custom_hex;
    public bool $allow_presets;

    public static function group(): string
    {
        return 'theme_palette';
    }
}
