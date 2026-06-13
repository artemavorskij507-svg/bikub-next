<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ThemePaletteEvent extends Model
{
    public $timestamps = false;
    protected $fillable = ['user_id', 'actor_id', 'event_type', 'from_hex', 'to_hex', 'metadata', 'created_at'];
    protected $casts = ['metadata' => 'array', 'created_at' => 'datetime'];
}
