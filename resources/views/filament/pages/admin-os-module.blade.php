@php
    $module = $this->getModuleDescriptor();
    $dashboardUrl = route('filament.admin.pages.dashboard');
@endphp

<x-filament-panels::page>
    <x-admin-os.page-shell
        class="bkb-admin-shell"
        eyebrow="BiKuBe OS module"
        :title="$module['label'] ?? __('bikube.common.module')"
        :subtitle="$module['purpose'] ?? __('bikube.common.module_placeholder')"
        :status="$module['status'] ?? __('bikube.common.skeleton_only')"
        :primary-href="$dashboardUrl"
        primary-label="Back to Operations Command Center"
    >
        <section class="bkb-business-grid">
            <x-admin-os.readiness-card
                title="Business context"
                :value="$module['owning_context'] ?? __('bikube.common.unassigned')"
                tone="review"
                body="This module remains inside the BiKuBe operating system and must expose real workflow state only."
            />
            <x-admin-os.readiness-card
                title="Real now"
                :value="count($module['safe_actions'] ?? [])"
                tone="ready"
                body="Implemented capabilities are listed below; no fake operational data is invented."
            />
            <x-admin-os.readiness-card
                title="Blocked / deferred"
                :value="count($module['blocked_actions'] ?? [])"
                tone="blocked"
                body="Unavailable actions stay explicit until the real backend or provider exists."
            />
        </section>

        <x-admin-os.action-matrix :items="[
            [
                'name' => 'Use this module',
                'status' => 'Real route',
                'tone' => 'ready',
                'requirement' => 'The page route and access policy are wired.',
                'blocker' => 'No blocker for viewing the module.',
                'url' => request()->getPathInfo(),
                'action' => 'Stay here',
            ],
            [
                'name' => 'Production action',
                'status' => 'Gated',
                'tone' => 'blocked',
                'requirement' => 'Domain service, authorization and audit trail must exist.',
                'blocker' => ($module['blocked_actions'][0] ?? __('bikube.common.no_production_action_wired_yet')),
                'action' => 'Disabled',
            ],
            [
                'name' => 'Next product pass',
                'status' => 'Planned',
                'tone' => 'review',
                'requirement' => 'Implement only the next business workflow, not abstract placeholders.',
                'blocker' => ($module['next_steps'][0] ?? 'Define the next concrete business workflow.'),
                'action' => 'Planned',
            ],
        ]" />

        <section class="bkb-os-two-column">
            <article class="bkb-os-command-panel">
                <div class="bkb-section-heading">
                    <div>
                        <p class="bkb-kicker">{{ __('bikube.common.what_is_real_now') }}</p>
                        <h2>Operational truth</h2>
                    </div>
                </div>
                <ul class="bkb-check-list">
                    @forelse (($module['safe_actions'] ?? []) as $action)
                        <li>{{ $action }}</li>
                    @empty
                        <li>{{ __('bikube.common.navigation_route_and_skeleton_are_real') }}</li>
                    @endforelse
                </ul>
            </article>

            <article class="bkb-os-command-panel is-blocked">
                <div class="bkb-section-heading">
                    <div>
                        <p class="bkb-kicker">{{ __('bikube.common.not_implemented_yet') }}</p>
                        <h2>Honest blockers</h2>
                    </div>
                </div>
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
                    <p class="bkb-kicker">{{ __('bikube.common.implementation_checklist') }}</p>
                    <h2 id="bkb-next-steps-title">{{ __('bikube.common.next_production_safe_steps') }}</h2>
                </div>
                <span class="bkb-status-badge">{{ __('bikube.common.needs_real_module_pass') }}</span>
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
    </x-admin-os.page-shell>
</x-filament-panels::page>
