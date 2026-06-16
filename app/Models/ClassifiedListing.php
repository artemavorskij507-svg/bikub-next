<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClassifiedListing extends Model
{
    use SoftDeletes;

    public const STATUS_DRAFT = 'draft';
    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_ARCHIVED = 'archived';

    protected $fillable = [
        'classified_category_id',
        'user_id',
        'listing_number',
        'title',
        'slug',
        'description',
        'price_amount',
        'currency',
        'condition',
        'location',
        'contact_name',
        'contact_email',
        'contact_phone',
        'status',
        'is_featured',
        'image_path',
        'published_at',
        'expires_at',
        'moderated_at',
        'moderated_by_id',
        'moderation_note',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'price_amount' => 'decimal:2',
            'is_featured' => 'boolean',
            'published_at' => 'datetime',
            'expires_at' => 'datetime',
            'moderated_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ClassifiedCategory::class, 'classified_category_id');
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function moderator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderated_by_id');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('status', self::STATUS_APPROVED)
            ->whereNotNull('published_at')
            ->where(function (Builder $query) {
                $query->whereNull('expires_at')->orWhere('expires_at', '>', now());
            });
    }

    public function scopeVisibleToPublic(Builder $query): Builder
    {
        return $query->published();
    }

    public function formattedPrice(): string
    {
        if ($this->price_amount === null) {
            return __('bikube.classifieds.price_on_request');
        }

        return number_format((float) $this->price_amount, 0, ',', ' ') . ' ' . $this->currency;
    }
}
