@php
    $modules = $this->getAdminModules();
    $proof = $this->getProof();
    $moduleRoutes = [
        'operations' => route('filament.admin.pages.operations-command-center'),
        'dispatch' => route('filament.admin.pages.dispatch-center'),
        'orders' => route('filament.admin.pages.orders-hub'),
        'people' => route('filament.admin.pages.people-workforce'),
        'services' => route('filament.admin.pages.services-catalog'),
        'finance' => route('filament.admin.pages.finance-control'),
        'support' => route('filament.admin.pages.support-center'),
        'content' => route('filament.admin.pages.content-cms'),
        'system' => route('filament.admin.pages.system-security'),
    ];

    $moduleVisuals = [
        'operations' => ['index' => '01', 'accent' => 'emerald', 'label' => 'Command'],
        'dispatch' => ['index' => '02', 'accent' => 'sky', 'label' => 'Assign'],
        'orders' => ['index' => '03', 'accent' => 'violet', 'label' => 'Lifecycle'],
        'people' => ['index' => '04', 'accent' => 'cyan', 'label' => 'Workforce'],
        'services' => ['index' => '05', 'accent' => 'lime', 'label' => 'Scenarios'],
        'finance' => ['index' => '06', 'accent' => 'amber', 'label' => 'Ledger'],
        'support' => ['index' => '07', 'accent' => 'rose', 'label' => 'SLA'],
        'content' => ['index' => '08', 'accent' => 'teal', 'label' => 'SEO'],
        'system' => ['index' => '09', 'accent' => 'slate', 'label' => 'Security'],
    ];
@endphp

<x-filament-panels::page>
    <main class="bkb-admin-shell" aria-labelledby="bkb-admin-title">
        <section class="bkb-hero bkb-surface">
            <div class="bkb-hero__content">
                <p class="bkb-kicker">Narvik-first operations foundation</p>
                <h1 id="bkb-admin-title">BiKuBe Admin OS</h1>
                <p class="bkb-hero__subtitle">
                    Production-grade local services operating system for Norway — Narvik first.
                </p>
                <div class="bkb-status-row" aria-label="Foundation status">
                    <span class="bkb-status-badge bkb-status-badge--safe">Real module foundation</span>
                    <span class="bkb-status-badge">Database-backed counts</span>
                    <span class="bkb-status-badge">No fake actions</span>
                </div>
            </div>

            <div class="bkb-hero__panel" aria-label="Platform foundation">
                <div>
                    <span class="bkb-panel-label">Stack</span>
                    <strong>Laravel 13</strong>
                </div>
                <div>
                    <span class="bkb-panel-label">Admin</span>
                    <strong>Filament 5</strong>
                </div>
                <div>
                    <span class="bkb-panel-label">Database</span>
                    <strong>PostgreSQL</strong>
                </div>
                <div>
                    <span class="bkb-panel-label">Porting</span>
                    <strong>Controlled modules active</strong>
                </div>
            </div>
        </section>

        <section class="bkb-foundation-strip" aria-label="Honest foundation constraints">
            <article>
                <span>Reality</span>
                <strong>Service, intake, order and pricing foundations are connected.</strong>
            </article>
            <article>
                <span>Safety</span>
                <strong>No fake GPS, payment, order or dispatch actions.</strong>
            </article>
            <article>
                <span>Next</span>
                <strong>Dispatch, GPS and payment provider remain disconnected.</strong>
            </article>
        </section>

        <section class="bkb-section-heading"><div><p class="bkb-kicker">Implementation proof</p><h2>Real Admin OS module status</h2></div></section>
        <section class="bkb-module-grid">
            @foreach($proof as $area => $metrics)
                <article class="bkb-module-card">
                    <h3>{{ str($area)->title() }}</h3>
                    <dl class="bkb-module-meta">@foreach($metrics as $label => $value)<div><dt>{{ str($label)->replace('_', ' ')->title() }}</dt><dd>{{ $value }}</dd></div>@endforeach</dl>
                    @if($area === 'services')<a class="bkb-card-link" href="{{ route('filament.admin.pages.services-catalog') }}">Open Service Catalog</a>
                    @elseif($area === 'orders' || $area === 'intake')<a class="bkb-card-link" href="{{ route('filament.admin.pages.orders-hub') }}">Open Orders Hub</a>
                    @elseif($area === 'pricing')<a class="bkb-card-link" href="{{ route('filament.admin.pages.finance-control') }}">Open Finance Control</a>
                    @else<a class="bkb-card-link" href="{{ route('filament.admin.pages.content-cms') }}">Open Content CMS</a>@endif
                </article>
            @endforeach
            <article class="bkb-module-card"><h3>Provider readiness</h3><dl class="bkb-module-meta"><div><dt>Payment provider</dt><dd>Not connected</dd></div><div><dt>Capture / refund</dt><dd>Not available</dd></div><div><dt>Dispatch</dt><dd>Not connected</dd></div><div><dt>GPS tracking</dt><dd>Not connected</dd></div></dl></article>
        </section>

        <section class="bkb-section-heading" aria-labelledby="bkb-modules-title">
            <div>
                <p class="bkb-kicker">Admin OS modules</p>
                <h2 id="bkb-modules-title">Control surfaces ready for real implementation</h2>
            </div>
            <a class="bkb-text-link" href="{{ route('filament.admin.pages.operations-command-center') }}">
                Start with Operations
            </a>
        </section>

        <section class="bkb-module-grid" aria-label="BiKuBe Admin OS module routes">
            @foreach ($modules as $key => $module)
                @php($visual = $moduleVisuals[$key] ?? ['index' => '00', 'accent' => 'emerald', 'label' => 'Module'])
                <article class="bkb-module-card bkb-module-card--{{ $visual['accent'] }}">
                    <div class="bkb-module-card__top">
                        <span class="bkb-module-card__index">{{ $visual['index'] }}</span>
                        <span class="bkb-module-card__tag">{{ $visual['label'] }}</span>
                    </div>

                    <h3>{{ $module['label'] ?? 'Module' }}</h3>
                    <p>{{ $module['purpose'] ?? 'Skeleton module awaiting domain implementation.' }}</p>

                    <dl class="bkb-module-meta">
                        <div>
                            <dt>Status</dt>
                            <dd>{{ $module['status'] ?? 'skeleton only' }}</dd>
                        </div>
                        <div>
                            <dt>Next implementation</dt>
                            <dd>{{ ($module['next_steps'] ?? ['Define domain contract.'])[0] }}</dd>
                        </div>
                    </dl>

                    <a class="bkb-card-link" href="{{ $moduleRoutes[$key] ?? route('filament.admin.pages.dashboard') }}">
                        Open real route
                    </a>
                </article>
            @endforeach
        </section>
    </main>
</x-filament-panels::page>
