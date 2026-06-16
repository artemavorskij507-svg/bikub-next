<?php

namespace App\Services\PublicSite;

/**
 * Maps PageDataBuilder output (DB sections/items) onto the delivery $page shape.
 * Only replaces keys that have actual DB content; leaves static fallback intact.
 */
class DeliveryPageAssembler
{
    public function assemble(array $builderData, array $staticPage): array
    {
        if (empty($builderData) || ($builderData['_source'] ?? '') !== 'db') {
            return $staticPage;
        }

        $page = $staticPage;

        foreach ($builderData['sections_list'] as $section) {
            $segment = $section['config']['segment'] ?? null;

            switch ($section['type']) {
                case 'hero_slider':
                    if ($segment && !empty($section['items']) && isset($page['segments'][$segment])) {
                        $slides = $this->mapItems($section['items'], 'slide');
                        if (!empty($slides)) {
                            $page['segments'][$segment]['slides'] = $slides;
                        }
                    }
                    break;

                case 'product_grid':
                    if ($segment && !empty($section['items']) && isset($page['segments'][$segment])) {
                        $products = $this->mapItems($section['items'], 'product');
                        if (!empty($products)) {
                            $page['segments'][$segment]['products'] = $products;
                        }
                    }
                    break;

                case 'promo_strip':
                    if ($segment && !empty($section['items']) && isset($page['segments'][$segment])) {
                        $promos = $this->mapItems($section['items'], 'promo');
                        if (!empty($promos)) {
                            $page['segments'][$segment]['promos'] = $promos;
                        }
                    }
                    break;

                case 'store_strip':
                    if ($segment && !empty($section['items']) && isset($page['segments'][$segment])) {
                        $stores = $this->mapItems($section['items'], 'store');
                        if (!empty($stores)) {
                            $page['segments'][$segment]['stores'] = $stores;
                        }
                    }
                    break;

                case 'feature_strip':
                    $benefits = $this->mapItems($section['items'], 'benefit');
                    if (!empty($benefits)) {
                        $page['benefits'] = $benefits;
                    }
                    break;
            }
        }

        return $page;
    }

    private function mapItems(array $items, string $type): array
    {
        $filtered = array_filter($items, fn ($i) => ($i['type'] ?? '') === $type);

        return array_values(array_map(function ($item) use ($type): array {
            return match ($type) {
                'slide' => [
                    'eyebrow' => $item['subtitle'] ?: 'BiKuBe Levering',
                    'title'   => $item['title'],
                    'image'   => $this->assetPath($item['image']),
                    'active'  => true,
                ],
                'product' => [
                    'title'     => $item['title'],
                    'subtitle'  => $item['subtitle'],
                    'price'     => $item['body'] ?: '',
                    'old_price' => null,
                    'badge'     => $item['badge'] ?: '',
                    'image'     => $this->assetPath($item['image']),
                ],
                'promo' => [
                    'title'    => $item['title'],
                    'subtitle' => $item['subtitle'],
                    'image'    => $this->assetPath($item['image']),
                ],
                'store' => [
                    'name'  => $item['title'],
                    'logo'  => $this->assetPath($item['image']),
                    'label' => $item['safety_label'] ?: 'Pickup example',
                ],
                'benefit' => [
                    'title'    => $item['title'],
                    'subtitle' => $item['subtitle'],
                    'icon'     => $item['icon'] ?: 'gift',
                ],
                default => [],
            };
        }, $filtered));
    }

    private function assetPath(?string $path): string
    {
        if (empty($path)) {
            return '';
        }

        if (str_starts_with($path, 'http') || str_starts_with($path, '/')) {
            return $path;
        }

        return asset($path);
    }
}
