@php
    use Illuminate\Support\Facades\Route;

    $currentRoute = Route::currentRouteName();

    $dashboard = [
        'label' => 'BiKuBe',
        'short' => 'Panel',
        'route' => 'filament.admin.pages.dashboard',
    ];

    $groups = [
        [
            'label' => 'Operations',
            'route' => 'filament.admin.pages.operations-command-center',
            'items' => [
                ['label' => 'Operations Command Center', 'description' => 'Business overview and launch readiness.', 'route' => 'filament.admin.pages.dashboard'],
                ['label' => 'Orders Hub', 'description' => 'Order lifecycle, blockers and action queue.', 'route' => 'filament.admin.pages.orders-hub'],
                ['label' => 'Dispatch Center', 'description' => 'Assignment and dispatch operations.', 'route' => 'filament.admin.pages.dispatch-center'],
                ['label' => 'Live Map', 'description' => 'Real worker GPS only; no fake markers.', 'route' => 'filament.admin.pages.live-operations-map'],
                ['label' => 'Today's Work', 'description' => 'Kanban-style operational order board.', 'route' => 'filament.admin.pages.order-board'],
                ['label' => 'Problems / Exceptions', 'description' => 'Support and operational incidents.', 'route' => 'filament.admin.pages.support-center'],
            ],
        ],
        [
            'label' => 'Orders',
            'route' => 'filament.admin.resources.orders.index',
            'items' => [
                ['label' => 'Orders', 'description' => 'All customer orders.', 'route' => 'filament.admin.resources.orders.index'],
                ['label' => 'Delivery Orders', 'description' => 'Delivery service order intake.', 'route' => 'filament.admin.pages.orders-hub'],
                ['label' => 'Quotes', 'description' => 'Pricing rules and quote readiness.', 'route' => 'filament.admin.resources.pricing-rules.index'],
                ['label' => 'Invoices', 'description' => 'Billing documents and payment state.', 'route' => 'filament.admin.resources.billing-documents.index'],
            ],
        ],
        [
            'label' => 'Workforce',
            'route' => 'filament.admin.pages.people-workforce',
            'items' => [
                ['label' => 'Workers', 'description' => 'Worker profiles and availability.', 'route' => 'filament.admin.resources.worker-profiles.index'],
                ['label' => 'Worker Applications', 'description' => 'Applicants waiting for review.', 'route' => 'filament.admin.resources.worker-applications.index'],
                ['label' => 'Worker Documents', 'description' => 'Private worker compliance documents.', 'route' => 'filament.admin.resources.worker-documents.index'],
                ['label' => 'Worker Payout Profiles', 'description' => 'Masked payout readiness profiles.', 'route' => 'filament.admin.resources.worker-payout-profiles.index'],
                ['label' => 'Worker Payout Reviews', 'description' => 'Identity, tax and payout review queue.', 'route' => 'filament.admin.resources.worker-payout-reviews.index'],
            ],
        ],
        [
            'label' => 'Customers',
            'route' => 'account.dashboard',
            'items' => [
                ['label' => 'Customers', 'description' => 'Authenticated customer account route.', 'route' => 'account.dashboard'],
                ['label' => 'Account Orders', 'description' => 'Customer order visibility.', 'route' => 'account.orders.index'],
                ['label' => 'Support Requests', 'description' => 'Customer support portal.', 'route' => 'account.support.index'],
            ],
        ],
        [
            'label' => 'Finance',
            'route' => 'filament.admin.pages.finance-control',
            'items' => [
                ['label' => 'Finance Control', 'description' => 'Billing, payments and settlement blockers.', 'route' => 'filament.admin.pages.finance-control'],
                ['label' => 'Billing Documents', 'description' => 'Invoices and billing records.', 'route' => 'filament.admin.resources.billing-documents.index'],
                ['label' => 'Payment Records', 'description' => 'Payment provider readiness and records.', 'route' => 'filament.admin.resources.payment-records.index'],
                ['label' => 'Worker Settlement Entries', 'description' => 'Settlement ledger and payout blockers.', 'route' => 'filament.admin.resources.worker-settlement-entries.index'],
                ['label' => 'Worker Settlement Rules', 'description' => 'Approved settlement rules only.', 'route' => 'filament.admin.resources.worker-settlement-rules.index'],
                ['label' => 'Payout Provider Settings', 'description' => 'Disabled payout provider readiness.', 'route' => 'filament.admin.pages.payout-provider-settings'],
            ],
        ],
        [
            'label' => 'Support',
            'route' => 'filament.admin.pages.support-center',
            'items' => [
                ['label' => 'Support Center', 'description' => 'Operational support cockpit.', 'route' => 'filament.admin.pages.support-center'],
                ['label' => 'Support Tickets', 'description' => 'Ticket resource and messages.', 'route' => 'filament.admin.resources.support-tickets.index'],
            ],
        ],
        [
            'label' => 'Services',
            'route' => 'filament.admin.pages.services-catalog',
            'items' => [
                ['label' => 'Delivery', 'description' => 'Delivery service catalog readiness.', 'route' => 'filament.admin.pages.services-catalog'],
                ['label' => 'Moving', 'description' => 'Large item and moving service readiness.', 'route' => 'filament.admin.resources.service-scenarios.index'],
                ['label' => 'Handyman', 'description' => 'Assistant and local task scenarios.', 'route' => 'filament.admin.resources.service-scenarios.index'],
                ['label' => 'Classifieds', 'description' => 'Deferred service category boundary.', 'route' => 'filament.admin.resources.service-categories.index'],
                ['label' => 'Service Scenarios', 'description' => 'Public checkout scenarios and fields.', 'route' => 'filament.admin.resources.service-scenarios.index'],
            ],
        ],
        [
            'label' => 'Content',
            'route' => 'filament.admin.pages.translation-manager',
            'items' => [
                ['label' => 'Translation Manager', 'description' => 'Four-language Admin OS localization.', 'route' => 'filament.admin.pages.translation-manager'],
                ['label' => 'Public Pages', 'description' => 'CMS pages for public content.', 'route' => 'filament.admin.resources.cms-pages.index'],
                ['label' => 'Service Content', 'description' => 'Service pages and SEO metadata.', 'route' => 'filament.admin.resources.service-pages.index'],
            ],
        ],
        [
            'label' => 'System',
            'route' => 'filament.admin.pages.system-security',
            'items' => [
                ['label' => 'System Readiness', 'description' => 'Business launch blockers and readiness.', 'route' => 'filament.admin.pages.system-security'],
                ['label' => 'Operations Settings', 'description' => 'Operational gates and map policy.', 'route' => 'filament.admin.pages.operations-settings'],
                ['label' => 'API Keys', 'description' => 'Payment/provider key readiness.', 'route' => 'filament.admin.pages.payment-provider-settings'],
                ['label' => 'Logs', 'description' => 'Audit log and technical evidence.', 'route' => 'filament.admin.pages.audit-log'],
                ['label' => 'Security Technical Readiness', 'description' => 'Reviewer, scanner, retention and incident tools.', 'route' => 'filament.admin.pages.security-governance'],
                ['label' => 'Scanner & Private Evidence', 'description' => 'Private evidence scan gate.', 'route' => 'filament.admin.pages.security-file-scanner'],
            ],
        ],
    ];

    $groups = array_values(array_filter($groups, fn (array $group): bool => Route::has($group['route'])));
@endphp

<nav class="bkb-top-module-nav" aria-label="BiKuBe Admin OS module switcher">
    <div class="bkb-top-module-nav__bar">
        @if (Route::has($dashboard['route']))
            @php($isDashboardActive = $currentRoute === $dashboard['route'])
            <a href="{{ route($dashboard['route'], absolute: false) }}" @class(['bkb-top-module-nav__link','bkb-top-module-nav__link--dashboard','bkb-top-module-nav__link--active' => $isDashboardActive]) @if ($isDashboardActive) aria-current="page" @endif>
                <span>{{ $dashboard['short'] }}</span>
            </a>
        @endif

        @foreach ($groups as $group)
            @php($isGroupActive = $currentRoute === $group['route'] || collect($group['items'])->contains(fn ($item) => $currentRoute === $item['route']))
            <div @class(['bkb-top-module-nav__group','bkb-top-module-nav__group--active' => $isGroupActive])>
                <button class="bkb-top-module-nav__trigger" type="button" aria-haspopup="true">
                    <span>{{ $group['label'] }}</span>
                    <span class="bkb-top-module-nav__chevron" aria-hidden="true">&dtrif;</span>
                </button>
                <div class="bkb-top-module-nav__dropdown" role="menu">
                    @foreach ($group['items'] as $item)
                        @continue(! Route::has($item['route']))
                        @php($isItemActive = $currentRoute === $item['route'])
                        <a href="{{ route($item['route'], absolute: false) }}" @class(['bkb-top-module-nav__dropdown-link','bkb-top-module-nav__dropdown-link--active' => $isItemActive]) @if ($isItemActive) aria-current="page" @endif role="menuitem">
                            <strong>{{ $item['label'] }}</strong>
                            <small>{{ $item['description'] }}</small>
                        </a>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</nav>
