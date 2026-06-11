<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PricingRule extends Model
{
    protected $fillable = ['service_scenario_id', 'scenario_key', 'name', 'code', 'type', 'status', 'currency', 'base_amount', 'per_unit_amount', 'unit_key', 'min_amount', 'max_amount', 'conditions', 'sort_order', 'starts_at', 'ends_at'];

    protected function casts(): array
    {
        return ['base_amount' => 'decimal:2', 'per_unit_amount' => 'decimal:2', 'min_amount' => 'decimal:2', 'max_amount' => 'decimal:2', 'conditions' => 'array', 'starts_at' => 'datetime', 'ends_at' => 'datetime'];
    }

    public function scenario(): BelongsTo { return $this->belongsTo(ServiceScenario::class, 'service_scenario_id'); }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active')->where(fn ($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', now()))->where(fn ($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>=', now()));
    }
}
