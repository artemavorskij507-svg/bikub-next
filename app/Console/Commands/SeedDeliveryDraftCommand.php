<?php

namespace App\Console\Commands;

use App\Models\PublicSitePage;
use App\Models\PublicSiteSection;
use App\Models\PublicSiteSectionItem;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SeedDeliveryDraftCommand extends Command
{
    protected $signature   = 'public-site:seed-delivery-draft';
    protected $description = 'Create a draft PublicSitePage for /category/delivery seeded from safe static data.';

    public function handle(): int
    {
        if (PublicSitePage::where('route_path', '/category/delivery')->exists()) {
            $this->info('Draft already exists for /category/delivery — skipping.');
            return self::SUCCESS;
        }

        DB::transaction(function () {
            $page = PublicSitePage::create([
                'template_key'          => 'commerce_delivery',
                'route_path'            => '/category/delivery',
                'linked_category_slug'  => 'delivery',
                'publish_status'        => 'draft',
                'published_at'          => null,
            ]);

            $this->seedSegment($page, 'products', 0);
            $this->seedSegment($page, 'meals',    3);
            $this->seedSegment($page, 'bulky',    6);
            $this->seedFeatureStrip($page, 9);
        });

        $page = PublicSitePage::where('route_path', '/category/delivery')->first();
        $this->info("Created draft PublicSitePage id={$page->id} for /category/delivery.");
        $this->line('Status: draft (not published). Use the admin CMS to review and publish.');

        return self::SUCCESS;
    }

    private function seedSegment(PublicSitePage $page, string $segment, int $baseOrder): void
    {
        $labels = [
            'products' => ['eyebrow' => 'BiKuBe Grocery',  'title' => 'Ferske varer, pakket med omsorg'],
            'meals'    => ['eyebrow' => 'BiKuBe Mat',       'title' => 'Varm restaurantmat, levert'],
            'bulky'    => ['eyebrow' => 'BiKuBe Cargo',     'title' => 'Storlevering av hjemmeartikler'],
        ];
        $imgBase = "images/bikube/delivery/segments/{$segment}";

        // hero_slider
        $slider = PublicSiteSection::create([
            'page_id'      => $page->id,
            'section_type' => 'hero_slider',
            'title'        => ['nb' => $labels[$segment]['title'], 'en' => $labels[$segment]['title']],
            'sort_order'   => $baseOrder,
            'is_active'    => true,
            'config'       => ['segment' => $segment],
        ]);

        foreach (range(1, 5) as $i) {
            PublicSiteSectionItem::create([
                'section_id'   => $slider->id,
                'item_type'    => 'slide',
                'title'        => ['nb' => $labels[$segment]['title'], 'en' => $labels[$segment]['title']],
                'subtitle'     => ['nb' => $labels[$segment]['eyebrow'], 'en' => $labels[$segment]['eyebrow']],
                'image_path'   => "{$imgBase}/{$i}.png",
                'sort_order'   => $i,
                'is_active'    => true,
            ]);
        }

        // promo_strip
        $promoData = $this->promos($segment, $imgBase);
        $promoSection = PublicSiteSection::create([
            'page_id'      => $page->id,
            'section_type' => 'promo_strip',
            'sort_order'   => $baseOrder + 1,
            'is_active'    => true,
            'config'       => ['segment' => $segment],
        ]);
        foreach ($promoData as $idx => $promo) {
            PublicSiteSectionItem::create([
                'section_id'  => $promoSection->id,
                'item_type'   => 'promo',
                'title'       => ['nb' => $promo['title'], 'en' => $promo['title']],
                'subtitle'    => ['nb' => $promo['subtitle'], 'en' => $promo['subtitle']],
                'image_path'  => $promo['image'],
                'sort_order'  => $idx + 1,
                'is_active'   => true,
            ]);
        }

        // store_strip
        $storeData = $this->stores($segment, $imgBase);
        $storeSection = PublicSiteSection::create([
            'page_id'      => $page->id,
            'section_type' => 'store_strip',
            'sort_order'   => $baseOrder + 2,
            'is_active'    => true,
            'config'       => ['segment' => $segment],
        ]);
        foreach ($storeData as $idx => $store) {
            PublicSiteSectionItem::create([
                'section_id'   => $storeSection->id,
                'item_type'    => 'store',
                'title'        => ['nb' => $store['name'], 'en' => $store['name']],
                'image_path'   => $store['logo'],
                'safety_label' => $store['label'],
                'sort_order'   => $idx + 1,
                'is_active'    => true,
            ]);
        }
    }

    private function seedFeatureStrip(PublicSitePage $page, int $order): void
    {
        $section = PublicSiteSection::create([
            'page_id'      => $page->id,
            'section_type' => 'feature_strip',
            'title'        => ['nb' => 'Fordeler', 'en' => 'Benefits'],
            'sort_order'   => $order,
            'is_active'    => true,
            'config'       => [],
        ]);

        $benefits = [
            ['title' => 'Secure payment',    'subtitle' => 'Payment coming soon', 'icon' => 'lock'],
            ['title' => 'Careful packing',   'subtitle' => 'Freshness preserved', 'icon' => 'gift'],
            ['title' => 'Support 24/7',      'subtitle' => 'Always online',       'icon' => 'phone'],
            ['title' => 'Bonuses and offers','subtitle' => 'Useful promotions',   'icon' => 'spark'],
        ];

        foreach ($benefits as $idx => $b) {
            PublicSiteSectionItem::create([
                'section_id' => $section->id,
                'item_type'  => 'benefit',
                'title'      => ['nb' => $b['title'], 'en' => $b['title']],
                'subtitle'   => ['nb' => $b['subtitle'], 'en' => $b['subtitle']],
                'icon'       => $b['icon'],
                'sort_order' => $idx + 1,
                'is_active'  => true,
            ]);
        }
    }

    private function promos(string $segment, string $imgBase): array
    {
        return match ($segment) {
            'products' => [
                ['title' => 'Gratis levering',       'subtitle' => 'For første dagligvarebestilling', 'image' => 'images/bikube/delivery/promo-baner2.png'],
                ['title' => 'Sunn kurv',              'subtitle' => 'Grønnsaker, meieri og snacks',    'image' => "{$imgBase}/2.png"],
                ['title' => 'Familiens ukeshandel',   'subtitle' => 'Én bestilling for hele uken',     'image' => "{$imgBase}/5.png"],
            ],
            'meals' => [
                ['title' => 'Middag i kveld',   'subtitle' => 'Ferdigmat og restaurantmat',           'image' => "{$imgBase}/1.png"],
                ['title' => 'Kokkens utvalg',   'subtitle' => 'Utvalgte varme retter i nærheten',     'image' => "{$imgBase}/3.png"],
                ['title' => 'Rask lunsj',        'subtitle' => 'Send bestilling — dispatcher bekrefter ETA', 'image' => "{$imgBase}/4.png"],
            ],
            'bulky' => [
                ['title' => 'Hjemmeomsorg',   'subtitle' => 'Store varer og bulksupport',         'image' => "{$imgBase}/1.png"],
                ['title' => 'Hvitvare-rute',  'subtitle' => 'Innbæring og oppsettbestilling',     'image' => "{$imgBase}/3.png"],
                ['title' => 'Kontorflytt',    'subtitle' => 'Bokser, skrivebord og utstyr',       'image' => "{$imgBase}/5.png"],
            ],
            default => [],
        };
    }

    private function stores(string $segment, string $imgBase): array
    {
        return match ($segment) {
            'products' => [
                ['name' => 'MENY',      'logo' => 'images/bikube/delivery/stores/meny.png',      'label' => 'Pickup example'],
                ['name' => 'KIWI',      'logo' => 'images/bikube/delivery/stores/kiwi.jpg',      'label' => 'Pickup example'],
                ['name' => 'REMA 1000', 'logo' => 'images/bikube/delivery/stores/rema1000.svg',  'label' => 'Pickup example'],
                ['name' => 'Coop Mega', 'logo' => 'images/bikube/delivery/stores/coopmega.svg',  'label' => 'Pickup example'],
                ['name' => 'SPAR',      'logo' => 'images/bikube/delivery/stores/spar.svg',      'label' => 'Pickup example'],
                ['name' => 'Joker',     'logo' => 'images/bikube/delivery/stores/joker.svg',     'label' => 'Pickup example'],
            ],
            'meals' => [
                ['name' => 'Partner setup', 'logo' => "{$imgBase}/1.png", 'label' => 'Confirm pickup in order form'],
                ['name' => 'Partner setup', 'logo' => "{$imgBase}/2.png", 'label' => 'Confirm pickup in order form'],
                ['name' => 'Partner setup', 'logo' => "{$imgBase}/3.png", 'label' => 'Confirm pickup in order form'],
                ['name' => 'Partner setup', 'logo' => "{$imgBase}/4.png", 'label' => 'Confirm pickup in order form'],
                ['name' => 'Partner setup', 'logo' => "{$imgBase}/5.png", 'label' => 'Confirm pickup in order form'],
            ],
            'bulky' => [
                ['name' => 'Partner setup', 'logo' => "{$imgBase}/1.png", 'label' => 'Confirm pickup in order form'],
                ['name' => 'Partner setup', 'logo' => "{$imgBase}/2.png", 'label' => 'Confirm pickup in order form'],
                ['name' => 'Partner setup', 'logo' => "{$imgBase}/3.png", 'label' => 'Confirm pickup in order form'],
                ['name' => 'Partner setup', 'logo' => "{$imgBase}/4.png", 'label' => 'Confirm pickup in order form'],
                ['name' => 'Partner setup', 'logo' => "{$imgBase}/6.png", 'label' => 'Confirm pickup in order form'],
            ],
            default => [],
        };
    }
}
