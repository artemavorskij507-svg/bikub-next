<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Model;

class SeoMetadata extends Model
{
    protected $table = 'seo_metadata';

    protected $fillable = [
        'owner_type',
        'owner_id',
        'path',
        'locale',
        'seo_title',
        'seo_description',
        'canonical_url',
        'robots',
        'og_title',
        'og_description',
        'og_image',
    ];

    public function owner(): MorphTo
    {
        return $this->morphTo();
    }
}
