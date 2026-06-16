@php
    $snapshot = $this->getBusinessSnapshot();
    $pipeline = $this->getOrderPipeline();
    $corridor = $this->getDeliveryCorridor();
    $actions = $this->getBusinessCorridorActions();
    $readiness = $this->getLaunchReadiness();
@endphp

<x-filament-panels::page>
    <main class="bkb-admin-shell bkb-business-shell" aria-labelledby="bkb-command-title">
        <section class="bkb-ops-hero bkb-surface bkb-business-hero bkb-command-hero">
            <div class="bkb-ops-hero__copy">
                <p class="bkb-kicker">{{ __('bikube.admin_shell.kicker') }}</p>
                <h1 id="bkb-command-title">{{ __('bikube.admin_shell.title') }}</h1>
                <p class="bkb-hero__subtitle">{{ __('bikube.admin_shell.subtitle') }}</p>
                <div class="bkb-status-row">
                    <span class="bkb-status-badge bkb-status-badge--safe">{{ __('bikube.admin_shell.business_shell') }}</span>
                    <span class="bkb-status-badge">{{ __('bikube.admin_shell.no_fake_gps') }}</span>
                    <span class="bkb-status-badge">{{ __('bikube.admin_shell.no_fake_payments') }}</span>
                </div>
                <div class="bkb-command-rail" aria-label="{{ __('bikube.admin_shell.status_rail') }}">
                    <div><span>{{ __('bikube.admin_shell.orders') }}</span><strong>{{ $snapshot['orders_waiting_dispatch'] }}</strong></div>
                    <div><span>{{ __('bikube.admin_shell.dispatch') }}</span><strong>{{ $snapshot['unassigned_orders'] }}</strong></div>
                    <div><span>{{ __('bikube.admin_shell.workers') }}</span><strong>{{ $snapshot['eligible_workers'] }}</strong></div>
                    <div><span>{{ __('bikube.admin_shell.finance') }}</span><strong>{{ $snapshot['blocked_payments'] }}</strong></div>
                    <div><span>{{ __('bikube.admin_shell.support') }}</span><strong>{{ $snapshot['open_support_tickets'] }}</strong></div>
                </div>
            </div>

            <aside class="bkb-command-visual" aria-label="{{ __('bikube.admin_shell.visual_label') }}">
                <div class="bkb-command-map" aria-hidden="true">
                    <i></i><i></i><i></i><i></i><i></i>
                </div>
                <div class="bkb-command-visual__stats">
                    <div><span>{{ __('bikube.admin_shell.orders_today') }}</span><strong>{{ $snapshot['orders_today'] }}</strong></div>
                    <div><span>{{ __('bikube.admin_shell.waiting_dispatch') }}</span><strong>{{ $snapshot['orders_waiting_dispatch'] }}</strong></div>
                    <div><span>{{ __('bikube.admin_shell.active_workers') }}</span><strong>{{ $snapshot['active_workers'] }}</strong></div>
                </div>
            </aside>
        </section>

        <section class="bkb-business-grid bkb-business-grid--snapshot" aria-label="Business snapshot">
            <article class="bkb-business-card"><span>{{ __('bikube.admin_shell.order_pipeline') }}</span><strong>{{ $snapshot['orders_waiting_dispatch'] }}</strong><p>{{ __('bikube.admin_shell.order_pipeline_body') }}</p><a href="{{ route('filament.admin.pages.orders-hub', absolute: false) }}">{{ __('bikube.admin_shell.open_orders_hub') }}</a></article>
            <article class="bkb-business-card"><span>{{ __('bikube.admin_shell.dispatch_queue') }}</span><strong>{{ $snapshot['unassigned_orders'] }}</strong><p>{{ __('bikube.admin_shell.dispatch_queue_body') }}</p><a href="{{ route('filament.admin.pages.dispatch-center', absolute: false) }}">{{ __('bikube.admin_shell.open_dispatch_center') }}</a></article>
            <article class="bkb-business-card"><span>{{ __('bikube.admin_shell.assigned_jobs') }}</span><strong>{{ $snapshot['assigned_jobs'] }}</strong><p>{{ __('bikube.admin_shell.assigned_jobs_body') }}</p><a href="{{ route('filament.admin.pages.live-operations-map', absolute: false) }}">{{ __('bikube.admin_shell.open_live_map') }}</a></article>
            <article class="bkb-business-card"><span>{{ __('bikube.admin_shell.worker_availability') }}</span><strong>{{ $snapshot['eligible_workers'] }}</strong><p>{{ __('bikube.admin_shell.worker_availability_body') }}</p><a href="{{ route('filament.admin.pages.people-workforce', absolute: false) }}">{{ __('bikube.admin_shell.open_workforce') }}</a></article>
            <article class="bkb-business-card"><span>{{ __('bikube.admin_shell.finance_blockers') }}</span><strong>{{ $snapshot['unpaid_invoices'] + $snapshot['blocked_payments'] }}</strong><p>{{ __('bikube.admin_shell.finance_blockers_body') }}</p><a href="{{ route('filament.admin.pages.finance-control', absolute: false) }}">{{ __('bikube.admin_shell.open_finance') }}</a></article>
            <article class="bkb-business-card"><span>{{ __('bikube.admin_shell.support_issues') }}</span><strong>{{ $snapshot['open_support_tickets'] }}</strong><p>{{ __('bikube.admin_shell.support_issues_body') }}</p><a href="{{ route('filament.admin.pages.support-center', absolute: false) }}">{{ __('bikube.admin_shell.open_support') }}</a></article>
        </section>

        <section class="bkb-os-section-grid">
            <article class="bkb-os-command-panel">
                <div class="bkb-section-heading">
                    <div>
                        <p class="bkb-kicker">Order pipeline</p>
                        <h2>From request to completion</h2>
                    </div>
                </div>
                <div class="bkb-os-pipeline" aria-label="Order pipeline">
                    @foreach ([
                        'created' => 'Created',
                        'waiting_dispatch' => 'Waiting dispatch',
                        'assigned' => 'Assigned',
                        'in_progress' => 'In progress',
                        'completed' => 'Completed',
                        'blocked' => 'Blocked',
                    ] as $key => $label)
                        <div class="is-{{ $key }}">
                            <span>{{ $label }}</span>
                            <strong>{{ $pipeline[$key] }}</strong>
                        </div>
                    @endforeach
                </div>
            </article>

            <article class="bkb-os-command-panel">
                <div class="bkb-section-heading">
                    <div>
                        <p class="bkb-kicker">Delivery business corridor</p>
                        <h2>Real operational chain</h2>
                    </div>
                </div>
                <div class="bkb-delivery-corridor" aria-label="Delivery business corridor">
                    @foreach ($corridor as $step)
                        <div class="is-{{ $step['tone'] }}">
                            <span>Step {{ $loop->iteration }}</span>
                            <strong>{{ $step['step'] }}</strong>
                            <p>{{ $step['blocker'] }}</p>
                            @if ($step['url'])
                                <a href="{{ $step['url'] }}">{{ $step['action'] }}</a>
                            @else
                                <button type="button" disabled title="{{ $step['blocker'] }}">{{ $step['action'] }}</button>
                            @endif
                        </div>
                    @endforeach
                </div>
            </article>
        </section>

        <section class="bkb-ops-board">
            <div class="bkb-ops-board__main">
                <div class="bkb-section-heading">
                    <div>
                        <p class="bkb-kicker">Business corridor</p>
                        <h2>Real routes for the daily operator flow</h2>
                    </div>
                </div>

                <div class="bkb-business-actions">
                    @foreach ($actions as $action)
                        <a class="bkb-action-row" href="{{ $action['url'] }}">
                            <span>Open</span>
                            <strong>{{ $action['label'] }}</strong>
                            <p>{{ $action['description'] }}</p>
                        </a>
                    @endforeach
                </div>
            </div>

            <aside class="bkb-ops-board__rail">
                <section class="bkb-os-card bkb-os-card--blocked">
                    <span class="bkb-card-eyebrow">Launch blockers</span>
                    <h2>Production is still gated honestly</h2>
                    <ul class="bkb-blocked-list">
                        <li>Payment provider is not connected.</li>
                        <li>Payout provider is disabled.</li>
                        <li>Live GPS requires HTTPS and mobile permission UAT.</li>
                        <li>External email/SMS is not configured.</li>
                    </ul>
                </section>
            </aside>
        </section>

        <section class="bkb-table-wrap bkb-readiness-table">
            <h2>Launch readiness by business area</h2>
            <table class="bkb-ops-table">
                <thead>
                    <tr><th>Area</th><th>Status</th><th>Business meaning</th><th>Next action</th></tr>
                </thead>
                <tbody>
                    @foreach ($readiness as $row)
                        <tr>
                            <td>{{ $row['area'] }}</td>
                            <td><span class="bkb-status-chip">{{ $row['status'] }}</span></td>
                            <td>{{ $row['meaning'] }}</td>
                            <td>@if ($row['url'])<a href="{{ $row['url'] }}">{{ $row['action'] }}</a>@else{{ $row['action'] }}@endif</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </section>
    </main>
</x-filament-panels::page>
