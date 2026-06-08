@php
    $runtimeChecks = $this->getRuntimeChecks();
    $readinessGates = $this->getReadinessGates();
    $moduleRoutes = $this->getModuleRoutes();
    $actions = $this->getActionClassification();
    $foundationModules = $this->getOperationalFoundationModules();
    $dashboardUrl = route('filament.admin.pages.dashboard');

    $stateLabels = [
        'ready' => 'Ready',
        'safe' => 'Safe',
        'review' => 'Review',
        'blocked' => 'Blocked',
    ];
@endphp

<x-filament-panels::page>
    <main class="bkb-admin-shell bkb-ops-cockpit" aria-labelledby="bkb-ops-title">
        <section class="bkb-ops-hero bkb-surface">
            <div class="bkb-ops-hero__copy">
                <a class="bkb-back-link" href="{{ $dashboardUrl }}">Back to Admin OS</a>
                <p class="bkb-kicker">BiKuBe Next / Operations</p>
                <h1 id="bkb-ops-title">Operations Command Center</h1>
                <p class="bkb-hero__subtitle">
                    Real launch-readiness cockpit for the Narvik-first platform foundation. This page reports what exists in the current runtime and refuses fake operational data.
                </p>

                <div class="bkb-status-row" aria-label="Operations status">
                    <span class="bkb-status-badge bkb-status-badge--safe">Skeleton foundation</span>
                    <span class="bkb-status-badge">No legacy data connected</span>
                    <span class="bkb-status-badge">No fake GPS, orders or payments</span>
                </div>
            </div>

            <aside class="bkb-ops-summary" aria-label="Foundation summary">
                <div>
                    <span>Operational posture</span>
                    <strong>Foundation only</strong>
                    <p>Ready for real domain module passes, not production dispatch.</p>
                </div>
                <div>
                    <span>Launch city</span>
                    <strong>Narvik first</strong>
                    <p>Norway expectations and compliance boundaries stay visible.</p>
                </div>
                <div>
                    <span>Data policy</span>
                    <strong>Honest state</strong>
                    <p>No placeholder numbers, fake queues or fake maps.</p>
                </div>
            </aside>
        </section>

        <section class="bkb-ops-runtime" aria-labelledby="bkb-runtime-title">
            <div class="bkb-section-heading">
                <p class="bkb-kicker">Runtime evidence</p>
                <h2 id="bkb-runtime-title">Current foundation checks</h2>
            </div>

            <div class="bkb-runtime-grid">
                @foreach ($runtimeChecks as $check)
                    <article class="bkb-runtime-card bkb-runtime-card--{{ $check['status'] }}">
                        <span>{{ $check['label'] }}</span>
                        <strong>{{ $check['value'] }}</strong>
                        <p>{{ $check['detail'] }}</p>
                    </article>
                @endforeach
            </div>
        </section>


        <section class="bkb-ops-runtime" aria-labelledby="bkb-foundation-title">
            <div class="bkb-section-heading">
                <p class="bkb-kicker">Installed operational modules</p>
                <h2 id="bkb-foundation-title">Module foundation wiring</h2>
            </div>

            <div class="bkb-runtime-grid">
                @foreach ($foundationModules as $module)
                    <article class="bkb-runtime-card bkb-runtime-card--{{ strtolower(str_replace(' ', '-', $module['tone'])) }}">
                        <span>{{ $module['label'] }}</span>
                        <strong>{{ $module['tone'] }}</strong>
                        <p>{{ $module['state'] }} - {{ $module['detail'] }}</p>
                        @if ($module['url'])
                            <a class="bkb-card-link" href="{{ $module['url'] }}">Open real route</a>
                        @endif
                    </article>
                @endforeach
            </div>
        </section>
        <section class="bkb-ops-board">
            <div class="bkb-ops-board__main">
                <div class="bkb-section-heading">
                    <p class="bkb-kicker">Readiness gates</p>
                    <h2>What blocks real operations</h2>
                </div>

                <div class="bkb-gate-list">
                    @foreach ($readinessGates as $gate)
                        <article class="bkb-gate-card bkb-gate-card--{{ $gate['status'] }}">
                            <div class="bkb-gate-card__header">
                                <div>
                                    <span>{{ $gate['name'] }}</span>
                                    <h3>{{ $gate['state'] }}</h3>
                                </div>
                                <strong>{{ $stateLabels[$gate['status']] ?? ucfirst($gate['status']) }}</strong>
                            </div>

                            <ul>
                                @foreach ($gate['evidence'] as $item)
                                    <li>{{ $item }}</li>
                                @endforeach
                            </ul>

                            <p class="bkb-gate-card__next">{{ $gate['next'] }}</p>
                        </article>
                    @endforeach
                </div>
            </div>

            <aside class="bkb-ops-board__rail" aria-label="Operations rail">
                <section class="bkb-os-card">
                    <span class="bkb-card-eyebrow">Admin OS routes</span>
                    <h2>Skeleton route map</h2>
                    <div class="bkb-route-list">
                        @foreach ($moduleRoutes as $route)
                            <a href="{{ $route['url'] }}" class="bkb-route-row">
                                <span>{{ $route['label'] }}</span>
                                <strong>{{ $route['status'] }}</strong>
                            </a>
                        @endforeach
                    </div>
                </section>

                <section class="bkb-os-card bkb-os-card--blocked">
                    <span class="bkb-card-eyebrow">Action honesty</span>
                    <h2>Visible action classification</h2>
                    <div class="bkb-action-list">
                        @foreach ($actions as $action)
                            @if ($action['url'])
                                <a class="bkb-action-row bkb-action-row--works" href="{{ $action['url'] }}">
                                    <span>{{ $action['classification'] }}</span>
                                    <strong>{{ $action['label'] }}</strong>
                                    <p>{{ $action['detail'] }}</p>
                                </a>
                            @else
                                <div class="bkb-action-row bkb-action-row--disabled" aria-disabled="true">
                                    <span>{{ $action['classification'] }}</span>
                                    <strong>{{ $action['label'] }}</strong>
                                    <p>{{ $action['detail'] }}</p>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </section>
            </aside>
        </section>
    </main>
</x-filament-panels::page>
