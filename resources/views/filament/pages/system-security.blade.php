@php
    $runtime = $this->getRuntimeStatus();
    $modules = $this->getOperationalModules();
    $security = $this->getSecurityFoundation();
    $checklist = $this->getSecurityChecklist();
    $dashboardUrl = route('filament.admin.pages.dashboard');

    /**
     * Translation helper with safe fallback.
     * This prevents raw translation keys from appearing while we migrate the page gradually.
     */
    $t = static function (string $key, string $fallback): string {
        $value = __($key);

        return $value === $key ? $fallback : $value;
    };
@endphp

<x-filament-panels::page>
    <main class="bkb-admin-shell bkb-status-cockpit" aria-labelledby="bkb-system-title">
        <section class="bkb-ops-hero bkb-surface">
            <div class="bkb-ops-hero__copy">
                <a class="bkb-back-link" href="{{ $dashboardUrl }}">
                    {{ $t('bikube.system_security.back_to_admin', 'Back to Admin OS') }}
                </a>

                <p class="bkb-kicker">
                    {{ $t('bikube.system_security.kicker', 'BiKuBe Next / System') }}
                </p>

                <h1 id="bkb-system-title">
                    {{ $t('bikube.system_security.title', 'System & Security') }}
                </h1>

                <p class="bkb-hero__subtitle">
                    {{ $t(
                        'bikube.system_security.description',
                        'Real runtime and package readiness for the Admin OS foundation. This page shows installed, configured and missing setup states only.'
                    ) }}
                </p>

                <div class="bkb-status-row">
                    <span class="bkb-status-badge bkb-status-badge--safe">
                        {{ $t('bikube.system_security.badges.no_old_db', 'No old BiKuBe DB connected') }}
                    </span>

                    <span class="bkb-status-badge">
                        {{ $t('bikube.system_security.badges.no_secrets', 'No secrets printed') }}
                    </span>

                    <span class="bkb-status-badge">
                        {{ $t('bikube.system_security.badges.no_fake_readiness', 'No fake production readiness') }}
                    </span>
                </div>
            </div>

            <aside class="bkb-ops-summary" aria-label="{{ $t('bikube.system_security.runtime_summary', 'Runtime summary') }}">
                @foreach ($runtime as $item)
                    <div>
                        <span>{{ $item['label'] }}</span>
                        <strong>{{ $item['state'] }}</strong>
                        <p>{{ $item['detail'] }}</p>
                    </div>
                @endforeach
            </aside>
        </section>

        <section class="bkb-ops-runtime">
            <div class="bkb-section-heading">
                <p class="bkb-kicker">
                    {{ $t('bikube.system_security.operational_modules', 'Operational modules') }}
                </p>

                <h2>
                    {{ $t('bikube.system_security.installed_foundation_status', 'Installed foundation status') }}
                </h2>
            </div>

            <div class="bkb-runtime-grid">
                @foreach ($modules as $module)
                    <article class="bkb-runtime-card bkb-runtime-card--{{ $module['tone'] }}">
                        <span>{{ $module['label'] }}</span>
                        <strong>{{ $module['state'] }}</strong>
                        <p>{{ $module['detail'] }}</p>

                        @if ($module['url'])
                            <a class="bkb-card-link" href="{{ $module['url'] }}">
                                {{ $t('bikube.system_security.open_real_route', 'Open real route') }}
                            </a>
                        @endif
                    </article>
                @endforeach
            </div>
        </section>

        <section class="bkb-ops-board">
            <div class="bkb-ops-board__main">
                <div class="bkb-section-heading">
                    <p class="bkb-kicker">
                        {{ $t('bikube.system_security.security_foundation', 'Security foundation') }}
                    </p>

                    <h2>
                        {{ $t('bikube.system_security.auth_rbac_audit', 'Auth, RBAC and audit packages') }}
                    </h2>
                </div>

                <div class="bkb-gate-list">
                    @foreach ($security as $item)
                        <article class="bkb-gate-card bkb-gate-card--{{ $item['tone'] }}">
                            <div class="bkb-gate-card__header">
                                <div>
                                    <span>{{ $item['label'] }}</span>
                                    <h3>{{ $item['state'] }}</h3>
                                </div>

                                <strong>{{ strtoupper((string) $item['tone']) }}</strong>
                            </div>

                            <p class="bkb-gate-card__next">{{ $item['detail'] }}</p>
                        </article>
                    @endforeach
                </div>
            </div>

            <aside class="bkb-ops-board__rail">
                <section class="bkb-os-card bkb-os-card--blocked">
                    <span class="bkb-card-eyebrow">
                        {{ $t('bikube.system_security.security_checklist', 'Security checklist') }}
                    </span>

                    <h2>
                        {{ $t('bikube.system_security.production_gates', 'Production gates') }}
                    </h2>

                    <div class="bkb-action-list">
                        @foreach ($checklist as $item)
                            <div class="bkb-action-row bkb-action-row--disabled">
                                <span>{{ strtoupper((string) $item['tone']) }}</span>
                                <strong>{{ $item['label'] }}: {{ $item['state'] }}</strong>
                                <p>{{ $item['detail'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </section>
            </aside>
        </section>
    </main>
</x-filament-panels::page>