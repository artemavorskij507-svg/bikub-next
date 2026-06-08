<?php

namespace App\Console\Commands;

use App\Models\CmsPage;
use App\Models\ServicePage;
use Illuminate\Console\Command;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class GenerateSitemap extends Command
{
    protected $signature = 'bikube:generate-sitemap';

    protected $description = 'Generate a sitemap from publicly visible BiKuBe CMS records';

    public function handle(): int
    {
        $sitemap = Sitemap::create()->add(Url::create(url('/')));
        $count = 0;

        CmsPage::publiclyVisible()->orderBy('id')->each(function (CmsPage $page) use ($sitemap, &$count): void {
            $sitemap->add(Url::create(route('public.cms.page', ['slug' => $page->slug]))->setLastModificationDate($page->updated_at));
            $count++;
        });

        ServicePage::publiclyVisible()->orderBy('id')->each(function (ServicePage $page) use ($sitemap, &$count): void {
            $sitemap->add(Url::create(route('public.cms.service-page', ['serviceSlug' => $page->service_slug]))->setLastModificationDate($page->updated_at));
            $count++;
        });

        $sitemap->writeToFile(public_path('sitemap.xml'));
        $this->info("Generated public/sitemap.xml with homepage and {$count} published CMS URL(s).");

        return self::SUCCESS;
    }
}
