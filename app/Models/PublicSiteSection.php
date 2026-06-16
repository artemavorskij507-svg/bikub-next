<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PublicSiteSection extends Model
{
    public const SECTION_TYPES = [
        'hero'          => 'Hero',
        'hero_slider'   => 'Hero Slider',
        'segment_tabs'  => 'Segment Tabs (Delivery)',
        'product_grid'  => 'Product Grid',
        'promo_strip'   => 'Promo Strip',
        'store_strip'   => 'Store / Partner Strip',
        'feature_strip' => 'Feature Strip',
        'how_it_works'  => 'How It Works',
        'faq'           => 'FAQ',
        'cta_block'     => 'CTA Block',
        'seo_block'     => 'SEO Block',
        'service_cards' => 'Service Cards',
        'coverage_map'  => 'Coverage / Map',
    ];

    protected $fillable = [
        'page_id',
        'section_type',
        'title',
        'subtitle',
        'sort_order',
        'is_active',
        'config',
    ];

    protected function casts(): array
    {
        return [
            'title'     => 'array',
            'subtitle'  => 'array',
            'is_active' => 'boolean',
            'config'    => 'array',
        ];
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(PublicSitePage::class, 'page_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(PublicSiteSectionItem::class, 'section_id');
    }

    public function activeItems(): HasMany
    {
        return $this->hasMany(PublicSiteSectionItem::class, 'section_id')
            ->where('is_active', true)
            ->orderBy('sort_order');
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
