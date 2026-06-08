<?php

namespace App\Filament\Pages;

use App\Filament\Resources\CmsPages\CmsPageResource;
use App\Filament\Resources\SeoMetadata\SeoMetadataResource;
use App\Filament\Resources\ServicePages\ServicePageResource;
use App\Models\CmsPage;
use App\Models\SeoMetadata;
use App\Models\ServicePage;
use Throwable;

class ContentCms extends AdminOsModulePage
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'CMS & SEO';

    protected static string|\UnitEnum|null $navigationGroup = 'Content';

    protected static ?int $navigationSort = 10;

    protected static ?string $title = 'CMS & SEO';

    protected string $view = 'filament.pages.content-cms';

    public function getModuleKey(): string
    {
        return 'content';
    }

    /**
     * @return array<int, array<string, string>>
     */
    public function getContentFoundation(): array
    {
        return [
            $this->status('Media Library', $this->packageVersion('spatie/laravel-medialibrary'), 'Core media package is installed for future CMS images, documents and proof files.', 'installed'),
            $this->status('Filament Media Library plugin', $this->packageVersion('filament/spatie-laravel-media-library-plugin'), 'Filament form components are installed; no media models are wired yet.', 'installed'),
            $this->status('Translatable content', $this->packageVersion('spatie/laravel-translatable'), 'Translation foundation exists for multilingual fields.', 'installed'),
            $this->status('Sitemap', 'command available', 'Run php artisan bikube:generate-sitemap to generate public/sitemap.xml from published records.', 'works'),
            $this->status('Settings plugin', $this->packageVersion('filament/spatie-laravel-settings-plugin'), 'Settings plugin is installed for future platform configuration screens.', 'installed'),
            $this->status('CMS domain', 'works', 'CMS pages, service pages and publication states are backed by real database tables.', 'works'),
            $this->status('SEO metadata', 'works', 'Public CMS views resolve owner metadata first, then path metadata, then safe page fields.', 'works'),
            $this->status('Public rendering', 'works', 'Published-only CMS and service routes are active. Drafts and archived records return 404.', 'works'),
        ];
    }

    public function getContentCounts(): array
    {
        try {
            return [
                'cms_pages' => CmsPage::count(),
                'service_pages' => ServicePage::count(),
                'seo_metadata' => SeoMetadata::count(),
                'draft' => CmsPage::where('status', 'draft')->count() + ServicePage::where('status', 'draft')->count(),
                'published' => CmsPage::where('status', 'published')->count() + ServicePage::where('status', 'published')->count(),
                'archived' => CmsPage::where('status', 'archived')->count() + ServicePage::where('status', 'archived')->count(),
            ];
        } catch (Throwable) {
            return array_fill_keys(['cms_pages', 'service_pages', 'seo_metadata', 'draft', 'published', 'archived'], null);
        }
    }

    public function getResourceLinks(): array
    {
        return [
            ['label' => 'CMS pages', 'detail' => 'Landing, legal and information pages.', 'url' => CmsPageResource::getUrl()],
            ['label' => 'Service pages', 'detail' => 'Scenario-aware public service content.', 'url' => ServicePageResource::getUrl()],
            ['label' => 'SEO metadata', 'detail' => 'Path and owner metadata records.', 'url' => SeoMetadataResource::getUrl()],
        ];
    }
}
