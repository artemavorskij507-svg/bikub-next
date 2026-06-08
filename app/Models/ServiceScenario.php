<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class ServiceScenario extends Model
{
    public const STATUSES = ['draft', 'active', 'paused', 'archived'];

    protected $fillable = [
        'category_id', 'scenario_key', 'slug', 'title', 'subtitle', 'description', 'service_type', 'status',
        'requires_pickup_address', 'requires_dropoff_address', 'requires_worker', 'requires_partner',
        'requires_payment', 'supports_scheduling', 'supports_live_tracking', 'base_price', 'currency',
        'sort_order', 'metadata', 'form_schema',
    ];

    protected function casts(): array
    {
        return [
            'requires_pickup_address' => 'boolean', 'requires_dropoff_address' => 'boolean',
            'requires_worker' => 'boolean', 'requires_partner' => 'boolean', 'requires_payment' => 'boolean',
            'supports_scheduling' => 'boolean', 'supports_live_tracking' => 'boolean',
            'base_price' => 'decimal:2', 'metadata' => 'array', 'form_schema' => 'array',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ServiceCategory::class);
    }

    public function fields(): HasMany
    {
        return $this->hasMany(ServiceScenarioField::class, 'scenario_id')->orderBy('sort_order');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isCheckoutReady(): bool
    {
        return false;
    }
}
