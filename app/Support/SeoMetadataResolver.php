<?php

namespace App\Support;

use App\Models\SeoMetadata;
use Illuminate\Database\Eloquent\Model;

class SeoMetadataResolver
{
    public function resolve(Model $page, string $path, string $locale): array
    {
        $metadata = SeoMetadata::query()
            ->where('owner_type', $page->getMorphClass())
            ->where('owner_id', $page->getKey())
            ->where('locale', $locale)
            ->first()
            ?? SeoMetadata::query()->where('path', $path)->where('locale', $locale)->first();

        $title = $metadata?->seo_title ?: $page->title;
        $description = $metadata?->seo_description ?: ($page->subtitle ?? $page->short_description ?? null);

        return [
            'title' => $title,
            'description' => $description,
            'canonical' => $metadata?->canonical_url ?: url($path),
            'robots' => $metadata?->robots,
            'og_title' => $metadata?->og_title ?: $title,
            'og_description' => $metadata?->og_description ?: $description,
            'og_image' => $metadata?->og_image,
        ];
    }
}
