<?php

namespace App\Http\Controllers;

use App\Models\PublicSitePage;
use App\Services\PublicSite\PageDataBuilder;
use Illuminate\Http\Response;
use Illuminate\View\View;

class PublicSitePreviewController extends Controller
{
    public function __invoke(PublicSitePage $page, PageDataBuilder $builder): View|Response
    {
        abort_unless(auth()->check(), 403, 'Preview requires authentication.');

        $data = $builder->build($page);

        return match ($page->template_key) {
            'commerce_delivery' => view('public.categories.delivery', [
                'builderPageData' => $data,
                'scenario'        => null,
                '_preview'        => true,
            ]),
            default => response(
                "<pre style='font:13px monospace;padding:2rem;background:#020713;color:#94a3b8'>"
                . "Preview for template [{$page->template_key}] not yet wired.\n\n"
                . htmlspecialchars(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))
                . "</pre>"
            ),
        };
    }
}
