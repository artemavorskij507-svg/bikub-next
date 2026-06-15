@php
    $runtimeChecks = $this->getRuntimeChecks();
    $readinessGates = $this->getReadinessGates();
    $moduleRoutes = $this->getModuleRoutes();
    $actions = $this->getActionClassification();
    $foundationModules = $this->getOperationalFoundationModules();
    $dashboardUrl = route('filament.admin.pages.dashboard');

    $stateLabels = [
        'ready' => __('bikube.status.ready'),
        'safe' => __('bikube.operational_states.safe'),
        'review' => __('bikube.operational_states.review'),
        'blocked' => __('bikube.status.blocked'),
    ];
@endphp

<x-filament-panels::page>
    <main class="bkb-admin-shell bkb-ops-cockpit" aria-labelledby="bkb-ops-title">
        <section class="bkb-ops-hero bkb-surface">
            <div class="bkb-ops-hero__copy">
                <a class="bkb-back-link" href="{{ $dashboardUrl }}">{{ __('bikube.common.back_to_admin') }}</a>
                <p class="bkb-kicker">{{ __('bikube.operations.kicker') }}</p>
                <h1 id="bkb-ops-title">{{ __('bikube.operations.title') }}</h1>
                <p class="bkb-hero__subtitle">
                    {{ __('bikube.operations.description') }}
                </p>

                <div class="bkb-status-row" aria-label="{{ __('bikube.operations.status_label') }}">
                    <span class="bkb-status-badge bkb-status-badge--safe">{{ __('bikube.operations.skeleton_foundation') }}</span>
                    <span class="bkb-status-badge">{{ __('bikube.operations.no_legacy_data') }}</span>
                    <span class="bkb-status-badge">{{ __('bikube.operations.no_fake_data') }}</span>
                </div>
            </div>

            <aside class="bkb-ops-summary" aria-label="{{ __('bikube.operations.foundation_summary') }}">
                <div>
                    <span>{{ __('bikube.operations.operational_posture') }}</span>
                    <strong>{{ __('bikube.operations.foundation_only') }}</strong>
                    <p>{{ __('bikube.operations.foundation_only_detail') }}</p>
                </div>
                <div>
                    <span>{{ __('bikube.operations.launch_city') }}</span>
                    <strong>{{ __('bikube.operations.narvik_first') }}</strong>
                    <p>{{ __('bikube.operations.narvik_detail') }}</p>
                </div>
                <div>
                    <span>{{ __('bikube.operations.data_policy') }}</span>
                    <strong>{{ __('bikube.operations.honest_state') }}</strong>
                    <p>{{ __('bikube.operations.honest_state_detail') }}</p>
                </div>
            </aside>
        </section>

        <section class="bkb-ops-runtime" aria-labelledby="bkb-runtime-title">
            <div class="bkb-section-heading">
                <p class="bkb-kicker">{{ __('bikube.operations.runtime_evidence') }}</p>
                <h2 id="bkb-runtime-title">{{ __('bikube.operations.current_foundation_checks') }}</h2>
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
                <p class="bkb-kicker">{{ __('bikube.operations.installed_modules') }}</p>
                <h2 id="bkb-foundation-title">{{ __('bikube.operations.module_foundation_wiring') }}</h2>
            </div>

            <div class="bkb-runtime-grid">
                @foreach ($foundationModules as $module)
                    <article class="bkb-runtime-card bkb-runtime-card--{{ strtolower(str_replace(' ', '-', $module['tone'])) }}">
                        <span>{{ $module['label'] }}</span>
                        <strong>{{ $module['tone'] }}</strong>
                        <p>{{ $module['state'] }} - {{ $module['detail'] }}</p>
                        @if ($module['url'])
                            <a class="bkb-card-link" href="{{ $module['url'] }}">{{ __('bikube.common.open_real_route') }}</a>
                        @endif
                    </article>
                @endforeach
            </div>
        </section>
        <section class="bkb-ops-board">
            <div class="bkb-ops-board__main">
                <div class="bkb-section-heading">
                    <p class="bkb-kicker">{{ __('bikube.operations.readiness_gates') }}</p>
                    <h2>{{ __('bikube.operations.real_operations_blockers') }}</h2>
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

            <aside class="bkb-ops-board__rail" aria-label="{{ __('bikube.operations.operations_rail') }}">
                <section class="bkb-os-card">
                    <span class="bkb-card-eyebrow">{{ __('bikube.operations.admin_routes') }}</span>
                    <h2>{{ __('bikube.operations.route_map') }}</h2>
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
                    <span class="bkb-card-eyebrow">{{ __('bikube.operations.action_honesty') }}</span>
                    <h2>{{ __('bikube.operations.action_classification') }}</h2>
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
