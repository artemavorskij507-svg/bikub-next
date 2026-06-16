<?php

namespace App\Services\PublicSite;

use App\Models\PublicSitePage;
use App\Models\PublicSiteSection;
use App\Models\PublicSiteSectionItem;

class PageDataBuilder
{
    public function __construct(private ContentSafetyValidator $safety) {}

    public function forRoute(string $routePath, bool $previewDraft = false): ?array
    {
        $query = PublicSitePage::with([
            'activeSections.activeItems',
        ])->where('route_path', $routePath);

        $page = $previewDraft
            ? $query->whereIn('publish_status', ['draft', 'published'])->first()
            : $query->published()->first();

        if (! $page) {
            return null;
        }

        return $this->build($page);
    }

    public function forId(int $id): ?array
    {
        $page = PublicSitePage::with(['activeSections.activeItems'])->find($id);

        return $page ? $this->build($page) : null;
    }

    public function build(PublicSitePage $page): array
    {
        $locale = app()->getLocale();

        $sections = [];
        foreach ($page->activeSections as $section) {
            $sections[$section->section_type][] = $this->buildSection($section, $locale);
        }

        return [
            '_source'       => 'db',
            '_page_id'      => $page->id,
            '_template'     => $page->template_key,
            '_route'        => $page->route_path,
            '_locale'       => $locale,
            'sections'      => $sections,
            'sections_list' => $page->activeSections->map(fn ($s) => $this->buildSection($s, $locale))->all(),
        ];
    }

    private function buildSection(PublicSiteSection $section, string $locale): array
    {
        return [
            'id'      => $section->id,
            'type'    => $section->section_type,
            'title'   => $this->resolveLocale($section->title, $locale),
            'subtitle'=> $this->resolveLocale($section->subtitle, $locale),
            'config'  => $section->config ?? [],
            'items'   => $section->activeItems->map(fn ($item) => $this->buildItem($item, $locale))->all(),
        ];
    }

    private function buildItem(PublicSiteSectionItem $item, string $locale): array
    {
        return [
            'id'            => $item->id,
            'type'          => $item->item_type,
            'title'         => $this->safety->sanitizeText($this->resolveLocale($item->title, $locale)),
            'subtitle'      => $this->safety->sanitizeText($this->resolveLocale($item->subtitle, $locale)),
            'body'          => $this->safety->sanitizeText($this->resolveLocale($item->body, $locale)),
            'cta_label'     => $this->safety->sanitizeText($this->resolveLocale($item->cta_label, $locale)),
            'cta_route'     => $this->safeCtaRoute($item->cta_route),
            'image'         => $item->image_path,
            'mobile_image'  => $item->mobile_image_path,
            'icon'          => $item->icon,
            'badge'         => $item->badge,
            'safety_label'  => $item->safety_label,
            'scenario_slug' => $item->linked_scenario_slug,
            'payload'       => $item->payload ?? [],
        ];
    }

    private function resolveLocale(?array $data, string $locale): string
    {
        if (empty($data)) {
            return '';
        }

        return $data[$locale]
            ?? $data[config('bikube_locales.fallback', 'en')]
            ?? $data['nb']
            ?? '';
    }

    private function safeCtaRoute(?string $route): string
    {
        if (empty($route)) {
            return '';
        }

        // Allow named routes (no scheme, no double slash after host)
        if (str_starts_with($route, '/')) {
            return $route;
        }

        // Allow named routes resolvable in the app
        if (! str_contains($route, '://') && ! str_contains($route, '//')) {
            try {
                return route($route);
            } catch (\Throwable) {
                return '';
            }
        }

        return '';
    }
}
