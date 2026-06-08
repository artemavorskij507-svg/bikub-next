@php
    $module = $this->getModuleDescriptor();
    $dashboardUrl = route('filament.admin.pages.dashboard');
@endphp

<x-filament-panels::page>
    <main class="bkb-admin-shell bkb-module-page" aria-labelledby="bkb-module-title">
        <section class="bkb-module-hero bkb-surface">
            <div>
                <a class="bkb-back-link" href="{{ $dashboardUrl }}">
                    {{ __('bikube.common.back_to_admin') }}
                </a>

                <p class="bkb-kicker">
                    {{ __('bikube.admin.module_foundation') }}
                </p>

                <h1 id="bkb-module-title">
                    {{ $module['label'] ?? __('bikube.common.module') }}
                </h1>

                <p class="bkb-hero__subtitle">
                    {{ $module['purpose'] ?? __('bikube.common.module_placeholder') }}
                </p>
            </div>

            <aside class="bkb-module-status" aria-label="{{ __('bikube.common.module_status') }}">
                <span>{{ __('bikube.common.current_state') }}</span>

                <strong>{{ $module['status'] ?? __('bikube.common.skeleton_only') }}</strong>

                <p>{{ __('bikube.common.no_fake_operational_data') }}</p>
            </aside>
        </section>

        <section class="bkb-module-layout">
            <article class="bkb-os-card bkb-os-card--context">
                <span class="bkb-card-eyebrow">
                    {{ __('bikube.common.owning_context') }}
                </span>

                <h2>{{ $module['owning_context'] ?? __('bikube.common.unassigned') }}</h2>

                <p>{{ __('bikube.common.bounded_context_shell_notice') }}</p>
            </article>

            <article class="bkb-os-card">
                <span class="bkb-card-eyebrow">
                    {{ __('bikube.common.what_is_real_now') }}
                </span>

                <h2>{{ __('bikube.common.safe_foundation_only') }}</h2>

                <ul class="bkb-check-list">
                    @forelse (($module['safe_actions'] ?? []) as $action)
                        <li>{{ $action }}</li>
                    @empty
                        <li>{{ __('bikube.common.navigation_route_and_skeleton_are_real') }}</li>
                    @endforelse
                </ul>
            </article>

            <article class="bkb-os-card bkb-os-card--blocked">
                <span class="bkb-card-eyebrow">
                    {{ __('bikube.common.not_implemented_yet') }}
                </span>

                <h2>{{ __('bikube.common.blocked_until_domain_work') }}</h2>

                <ul class="bkb-blocked-list">
                    @foreach (($module['blocked_actions'] ?? [__('bikube.common.no_production_action_wired_yet')]) as $action)
                        <li>{{ $action }}</li>
                    @endforeach
                </ul>
            </article>
        </section>

        <section class="bkb-roadmap-card bkb-surface" aria-labelledby="bkb-next-steps-title">
            <div class="bkb-roadmap-card__header">
                <div>
                    <p class="bkb-kicker">
                        {{ __('bikube.common.implementation_checklist') }}
                    </p>

                    <h2 id="bkb-next-steps-title">
                        {{ __('bikube.common.next_production_safe_steps') }}
                    </h2>
                </div>

                <span class="bkb-status-badge">
                    {{ __('bikube.common.needs_real_module_pass') }}
                </span>
            </div>

            <ol class="bkb-step-list">
                @foreach (($module['next_steps'] ?? []) as $step)
                    <li>
                        <span>{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                        <p>{{ $step }}</p>
                    </li>
                @endforeach
            </ol>
        </section>

        <section class="bkb-honesty-panel" aria-label="{{ __('bikube.common.approval_boundaries') }}">
            <h2>{{ __('bikube.common.approval_boundaries') }}</h2>

            <p>{{ __('bikube.common.approval_boundaries_notice') }}</p>
        </section>
    </main>
</x-filament-panels::page>