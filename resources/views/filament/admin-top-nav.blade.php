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
                ['label' => __('bikube.modules.operations'), 'description' => __('bikube.descriptions.operations'), 'route' => 'filament.admin.pages.operations-command-center'],
            ],
        ],
        [
            'label' => __('bikube.admin.dispatch'),
            'route' => 'filament.admin.pages.dispatch-center',
            'items' => [
                ['label' => __('bikube.modules.dispatch'), 'description' => __('bikube.descriptions.dispatch'), 'route' => 'filament.admin.pages.dispatch-center'],
                ['label' => __('bikube.modules.live_map'), 'description' => __('bikube.descriptions.live_map'), 'route' => 'filament.admin.pages.live-operations-map'],
            ],
        ],
        [
            'label' => __('bikube.admin.orders'),
            'route' => 'filament.admin.pages.orders-hub',
            'items' => [
                ['label' => __('bikube.modules.orders'), 'description' => __('bikube.descriptions.orders'), 'route' => 'filament.admin.pages.orders-hub'],
            ],
        ],
        [
            'label' => __('bikube.admin.people'),
            'route' => 'filament.admin.pages.people-workforce',
            'items' => [
                ['label' => __('bikube.modules.people'), 'description' => __('bikube.descriptions.people'), 'route' => 'filament.admin.pages.people-workforce'],
            ],
        ],
        [
            'label' => __('bikube.admin.services'),
            'route' => 'filament.admin.pages.services-catalog',
            'items' => [
                ['label' => __('bikube.modules.services'), 'description' => __('bikube.descriptions.services'), 'route' => 'filament.admin.pages.services-catalog'],
            ],
        ],
        [
            'label' => __('bikube.admin.finance'),
            'route' => 'filament.admin.pages.finance-control',
            'items' => [
                ['label' => __('bikube.modules.finance'), 'description' => __('bikube.descriptions.finance'), 'route' => 'filament.admin.pages.finance-control'],
            ],
        ],
        [
            'label' => __('bikube.admin.support'),
            'route' => 'filament.admin.pages.support-center',
            'items' => [
                ['label' => __('bikube.modules.support'), 'description' => __('bikube.descriptions.support'), 'route' => 'filament.admin.pages.support-center'],
            ],
        ],
        [
            'label' => __('bikube.admin.content'),
            'route' => 'filament.admin.pages.content-cms',
            'items' => [
                ['label' => __('bikube.modules.content'), 'description' => __('bikube.descriptions.content'), 'route' => 'filament.admin.pages.content-cms'],
            ],
        ],
        [
            'label' => __('bikube.admin.system'),
            'route' => 'filament.admin.pages.system-security',
            'items' => [
                ['label' => __('bikube.modules.system'), 'description' => __('bikube.descriptions.system'), 'route' => 'filament.admin.pages.system-security'],
                ['label' => __('bikube.modules.translations'), 'description' => __('bikube.descriptions.translations'), 'route' => 'filament.admin.pages.translation-manager'],
            ],
        ],
    ];

    $groups = array_values(array_filter($groups, fn (array $group): bool => Route::has($group['route'])));
@endphp

<nav class="bkb-top-module-nav" aria-label="{{ __('bikube.common.module_switcher') }}">
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
