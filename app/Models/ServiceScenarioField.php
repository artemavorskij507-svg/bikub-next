<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class ServiceScenarioField extends Model
{
    protected $fillable = [
        'scenario_id', 'field_key', 'label', 'type', 'required', 'options', 'validation_rules', 'sort_order', 'is_active',
    ];

    protected function casts(): array
    {
        return ['required' => 'boolean', 'options' => 'array', 'validation_rules' => 'array', 'is_active' => 'boolean'];
    }

    public function scenario(): BelongsTo
    {
        return $this->belongsTo(ServiceScenario::class, 'scenario_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
