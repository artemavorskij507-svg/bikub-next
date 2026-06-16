<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PublicSiteSectionItem extends Model
{
    public const ITEM_TYPES = [
        'slide'   => 'Hero Slide',
        'product' => 'Product Card',
        'promo'   => 'Promo Banner',
        'store'   => 'Store / Partner Card',
        'step'    => 'How It Works Step',
        'faq'     => 'FAQ Item',
        'benefit' => 'Benefit / Feature',
        'cta'     => 'CTA Button',
        'service' => 'Service Card',
    ];

    public const ALLOWED_SAFETY_LABELS = [
        'Partner setup',
        'Pickup example',
        'ETA after dispatcher confirms',
        'Payment coming soon',
        'GPS after pickup',
        'Manual confirmation',
        'Rating after launch',
    ];

    protected $fillable = [
        'section_id',
        'item_type',
        'title',
        'subtitle',
        'body',
        'cta_label',
        'cta_route',
        'image_path',
        'mobile_image_path',
        'icon',
        'badge',
        'linked_scenario_slug',
        'safety_label',
        'payload',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'title'     => 'array',
            'subtitle'  => 'array',
            'body'      => 'array',
            'cta_label' => 'array',
            'is_active' => 'boolean',
            'payload'   => 'array',
        ];
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(PublicSiteSection::class, 'section_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order');
    }

    public function getLocaleField(string $field, ?string $locale = null): string
    {
        $locale ??= app()->getLocale();
        $data = $this->{$field} ?? [];

        return $data[$locale]
            ?? $data[config('bikube_locales.fallback', 'en')]
            ?? $data['nb']
            ?? '';
    }
}
