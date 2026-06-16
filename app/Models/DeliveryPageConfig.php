<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryPageConfig extends Model
{
    protected $table = 'delivery_page_configs';

    protected $fillable = ['key', 'value'];

    protected $casts = ['value' => 'array'];

    public static function get(string $key, mixed $default = null): mixed
    {
        return static::where('key', $key)->value('value') ?? $default;
    }

    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
    }
}
