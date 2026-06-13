<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserThemePreference extends Model
{
    protected $fillable = ['user_id', 'hex', 'source', 'updated_by_id'];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function updatedBy(): BelongsTo { return $this->belongsTo(User::class, 'updated_by_id'); }
}
