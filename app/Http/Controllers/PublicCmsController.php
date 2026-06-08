<?php

namespace App\Http\Controllers;

use App\Models\CmsPage;
use App\Models\ServicePage;
use App\Models\ServiceScenario;
use App\Support\SeoMetadataResolver;
use Illuminate\Contracts\View\View;

class PublicCmsController extends Controller
{
    public function page(string $slug, SeoMetadataResolver $seo): View
    {
        $locale = app()->getLocale();
        $page = CmsPage::publiclyVisible()->where('slug', $slug)->where('locale', $locale)->firstOrFail();
        $path = "/p/{$slug}";

        return view('public.cms.page', compact('page'))->with('seo', $seo->resolve($page, $path, $locale));
    }

    public function servicePage(string $serviceSlug, SeoMetadataResolver $seo): View
    {
        $locale = app()->getLocale();
        $page = ServicePage::publiclyVisible()->where('service_slug', $serviceSlug)->where('locale', $locale)->first();
        $path = "/services/{$serviceSlug}";

        if ($page) {
            return view('public.cms.service-page', compact('page'))->with('seo', $seo->resolve($page, $path, $locale));
        }

        $scenario = ServiceScenario::active()->with(['category', 'fields' => fn ($query) => $query->active()])->where('slug', $serviceSlug)->firstOrFail();

        return view('public.services.scenario', compact('scenario'))->with('seo', [
            'title' => $scenario->title,
            'description' => $scenario->subtitle ?: $scenario->description,
            'canonical' => url($path),
            'og_title' => $scenario->title,
            'og_description' => $scenario->subtitle ?: $scenario->description,
        ]);
    }
}
