<?php

use Filament\Support\Icons\Heroicon;

return [
    'locales' => ['nb', 'en', 'ru', 'uk'],    
    'gate' => null,
    'ignore_groups' => [],
    'navigation_sort' => null,
    'navigation_group' => 'System',
    'prefix_tabs' => [
        'bikube' => 'BiKuBe',
        'admin' => 'Admin',
        'checkout' => 'Checkout',
        'account' => 'Account',
        'lk' => 'LK',
        'partner' => 'Partner',
        'public' => 'Public',
        'orders' => 'Orders',
        'sys' => 'System',
        'acc' => 'Accounting',
    ],
    'widget' => [
        'enabled' => false,
        'gate' => null,
        'sort' => null,
    ],
    'navigation_icon' => Heroicon::OutlinedLanguage,
];

