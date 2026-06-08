<?php

namespace Tests\Feature;

use App\Models\CmsPage;
use App\Models\SeoMetadata;
use App\Models\ServicePage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicCmsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        if (! extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('pdo_sqlite is required by the current isolated test database configuration.');
        }

        parent::setUp();
    }

    public function test_only_published_cms_pages_are_public(): void
    {
        CmsPage::create(['type' => 'info', 'slug' => 'draft', 'locale' => 'en', 'title' => 'Draft', 'status' => 'draft']);
        CmsPage::create(['type' => 'info', 'slug' => 'archived', 'locale' => 'en', 'title' => 'Archived', 'status' => 'archived']);
        CmsPage::create(['type' => 'info', 'slug' => 'published', 'locale' => 'en', 'title' => 'Published', 'status' => 'published']);

        $this->get('/p/draft')->assertNotFound();
        $this->get('/p/archived')->assertNotFound();
        $this->get('/p/published')->assertOk()->assertSee('Published');
    }

    public function test_published_service_page_is_public(): void
    {
        ServicePage::create(['service_slug' => 'delivery', 'locale' => 'en', 'title' => 'Delivery', 'status' => 'published']);

        $this->get('/services/delivery')->assertOk()->assertSee('Delivery');
    }

    public function test_seo_metadata_is_rendered(): void
    {
        $page = CmsPage::create(['type' => 'info', 'slug' => 'seo', 'locale' => 'en', 'title' => 'Page', 'status' => 'published']);
        SeoMetadata::create(['owner_type' => $page->getMorphClass(), 'owner_id' => $page->id, 'locale' => 'en', 'seo_title' => 'SEO title', 'seo_description' => 'SEO description']);

        $this->get('/p/seo')->assertOk()->assertSee('<title>SEO title</title>', false)->assertSee('SEO description');
    }

    public function test_sitemap_command_creates_file(): void
    {
        $path = public_path('sitemap.xml');
        @unlink($path);

        $this->artisan('bikube:generate-sitemap')->assertSuccessful();
        $this->assertFileExists($path);
        @unlink($path);
    }
}
