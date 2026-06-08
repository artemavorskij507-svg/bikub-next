@php
    use Illuminate\Support\Facades\Route;

    $currentRoute = Route::currentRouteName();

    $dashboard = [
        'label' => 'BiKuBe Admin OS',
        'short' => __('bikube.admin.dashboard'),
        'route' => 'filament.admin.pages.dashboard',
    ];

    $groups = [
        [
            'label' => __('bikube.admin.operations'),
            'route' => 'filament.admin.pages.operations-command-center',
            'items' => [
                ['label' => 'Operations Command Center', 'description' => 'Readiness cockpit', 'route' => 'filament.admin.pages.operations-command-center'],
            ],
        ],
        [
            'label' => __('bikube.admin.dispatch'),
            'route' => 'filament.admin.pages.dispatch-center',
            'items' => [
                ['label' => 'Dispatch Center', 'description' => 'Assignment foundation', 'route' => 'filament.admin.pages.dispatch-center'],
            ],
        ],
        [
            'label' => __('bikube.admin.orders'),
            'route' => 'filament.admin.pages.orders-hub',
            'items' => [
                ['label' => 'Orders Hub', 'description' => 'Order engine shell', 'route' => 'filament.admin.pages.orders-hub'],
            ],
        ],
        [
            'label' => __('bikube.admin.people'),
            'route' => 'filament.admin.pages.people-workforce',
            'items' => [
                ['label' => 'People & Workforce', 'description' => 'Workers, admins, partners', 'route' => 'filament.admin.pages.people-workforce'],
            ],
        ],
        [
            'label' => __('bikube.admin.services'),
            'route' => 'filament.admin.pages.services-catalog',
            'items' => [
                ['label' => 'Service Catalog', 'description' => 'Scenario registry', 'route' => 'filament.admin.pages.services-catalog'],
            ],
        ],
        [
            'label' => __('bikube.admin.finance'),
            'route' => 'filament.admin.pages.finance-control',
            'items' => [
                ['label' => 'Finance Control', 'description' => 'Wallets and payout shell', 'route' => 'filament.admin.pages.finance-control'],
            ],
        ],
        [
            'label' => __('bikube.admin.support'),
            'route' => 'filament.admin.pages.support-center',
            'items' => [
                ['label' => 'Support Center', 'description' => 'Tickets and incident shell', 'route' => 'filament.admin.pages.support-center'],
            ],
        ],
        [
            'label' => __('bikube.admin.content'),
            'route' => 'filament.admin.pages.content-cms',
            'items' => [
                ['label' => 'CMS & SEO', 'description' => 'Public content foundation', 'route' => 'filament.admin.pages.content-cms'],
            ],
        ],
        [
            'label' => __('bikube.admin.system'),
            'route' => 'filament.admin.pages.system-security',
            'items' => [
                ['label' => 'System & Security', 'description' => 'Security and audit shell', 'route' => 'filament.admin.pages.system-security'],
                ['label' => 'Translation Manager', 'description' => 'Manage admin and product translations', 'route' => 'filament.admin.pages.translation-manager'],
            ],
        ],
    ];

    $groups = array_values(array_filter($groups, fn (array $group): bool => Route::has($group['route'])));
@endphp

<nav class="bkb-top-module-nav" aria-label="BiKuBe Admin OS module switcher">
    <div class="bkb-top-module-nav__bar">
        @if (Route::has($dashboard['route']))
            @php
                $isDashboardActive = $currentRoute === $dashboard['route'];
            @endphp

            <a
                href="{{ route($dashboard['route'], absolute: false) }}"
                @class([
                    'bkb-top-module-nav__link',
                    'bkb-top-module-nav__link--dashboard',
                    'bkb-top-module-nav__link--active' => $isDashboardActive,
                ])
                @if ($isDashboardActive) aria-current="page" @endif
            >
                <span>{{ $dashboard['short'] }}</span>
            </a>
        @endif

        @foreach ($groups as $group)
            @php
                $isGroupActive = $currentRoute === $group['route'];
            @endphp

            <div
                @class([
                    'bkb-top-module-nav__group',
                    'bkb-top-module-nav__group--active' => $isGroupActive,
                ])
            >
                <button class="bkb-top-module-nav__trigger" type="button" aria-haspopup="true">
                    <span>{{ $group['label'] }}</span>
                    <span class="bkb-top-module-nav__chevron" aria-hidden="true">&dtrif;</span>
                </button>

                <div class="bkb-top-module-nav__dropdown" role="menu">
                    @foreach ($group['items'] as $item)
                        @continue(! Route::has($item['route']))

                        @php
                            $isItemActive = $currentRoute === $item['route'];
                        @endphp

                        <a
                            href="{{ route($item['route'], absolute: false) }}"
                            @class([
                                'bkb-top-module-nav__dropdown-link',
                                'bkb-top-module-nav__dropdown-link--active' => $isItemActive,
                            ])
                            @if ($isItemActive) aria-current="page" @endif
                            role="menuitem"
                        >
                            <strong>{{ $item['label'] }}</strong>
                            <small>{{ $item['description'] }}</small>
                        </a>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</nav>