<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PublicSitePage extends Model
{
    public const TEMPLATE_KEYS = [
        'home'              => 'Home',
        'commerce_delivery' => 'Delivery (Commerce)',
        'food_landing'      => 'Food / Meals',
        'moving_landing'    => 'Moving',
        'handyman_landing'  => 'Handyman',
        'eco_landing'       => 'Eco / Recycling',
        'social_landing'    => 'Social Help',
        'personal_landing'  => 'Personal Task',
        'tow_landing'       => 'Tow / Evacuation',
        'classifieds'       => 'Classifieds',
        'it_marketing'      => 'IT & Marketing',
        'glf_mat'           => 'GLF MaT',
        'generic_landing'   => 'Generic Landing',
    ];

    public const PUBLISH_STATUSES = ['draft', 'published', 'archived'];

    protected $fillable = [
        'template_key',
        'route_path',
        'linked_category_slug',
        'publish_status',
        'published_at',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return ['published_at' => 'datetime'];
    }

    public function sections(): HasMany
    {
        return $this->hasMany(PublicSiteSection::class, 'page_id');
    }

    public function activeSections(): HasMany
    {
        return $this->hasMany(PublicSiteSection::class, 'page_id')
            ->where('is_active', true)
            ->orderBy('sort_order');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('publish_status', 'published')
            ->where(fn (Builder $q) => $q->whereNull('published_at')->orWhere('published_at', '<=', now()));
    }

    public function isPublished(): bool
    {
        return $this->publish_status === 'published'
            && ($this->published_at === null || $this->published_at->isPast());
    }
}
