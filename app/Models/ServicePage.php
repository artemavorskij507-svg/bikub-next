<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class ServicePage extends Model implements HasMedia
{
    use InteractsWithMedia;
    public const STATUSES = ['draft', 'published', 'archived'];

    protected $fillable = [
        'scenario_key',
        'service_slug',
        'locale',
        'title',
        'subtitle',
        'short_description',
        'body',
        'status',
        'published_at',
    ];

    protected function casts(): array
    {
        return ['published_at' => 'datetime'];
    }

    public function isPublished(): bool
    {
        return $this->status === 'published' && ($this->published_at === null || $this->published_at->isPast());
    }

    public function scopePubliclyVisible(Builder $query): Builder
    {
        return $query->where('status', 'published')
            ->where(fn (Builder $query) => $query->whereNull('published_at')->orWhere('published_at', '<=', now()));
    }

    public function seoMetadata(): MorphOne
    {
        return $this->morphOne(SeoMetadata::class, 'owner');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('hero')->useDisk('public')->singleFile();
    }
}
