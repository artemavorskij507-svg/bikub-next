@php
    $snapshot = $this->getBusinessSnapshot();
    $pipeline = $this->getOrderPipeline();
    $corridor = $this->getDeliveryCorridor();
    $actions = $this->getBusinessCorridorActions();
    $readiness = $this->getLaunchReadiness();
@endphp

<x-filament-panels::page>
    <main class="bkb-admin-shell bkb-business-shell" aria-labelledby="bkb-command-title">
        <section class="bkb-ops-hero bkb-surface bkb-business-hero">
            <div class="bkb-ops-hero__copy">
                <p class="bkb-kicker">BiKuBe Admin OS</p>
                <h1 id="bkb-command-title">BiKuBe Operations Command Center</h1>
                <p class="bkb-hero__subtitle">Orders, dispatch, workers, finance and support in one Narvik-first operating system. Counts are read from the current runtime; blocked production capabilities stay visible.</p>
                <div class="bkb-status-row">
                    <span class="bkb-status-badge bkb-status-badge--safe">Business shell</span>
                    <span class="bkb-status-badge">No fake GPS</span>
                    <span class="bkb-status-badge">No fake payments</span>
                </div>
            </div>

            <aside class="bkb-ops-summary" aria-label="Live business snapshot">
                <div><span>Orders today</span><strong>{{ $snapshot['orders_today'] }}</strong><p>New customer requests created today.</p></div>
                <div><span>Waiting dispatch</span><strong>{{ $snapshot['orders_waiting_dispatch'] }}</strong><p>Orders that still need operational attention.</p></div>
                <div><span>Active workers</span><strong>{{ $snapshot['active_workers'] }}</strong><p>Workers currently online or available.</p></div>
            </aside>
        </section>

        <section class="bkb-business-grid bkb-business-grid--snapshot" aria-label="Business snapshot">
            <article class="bkb-business-card"><span>Order pipeline</span><strong>{{ $snapshot['orders_waiting_dispatch'] }}</strong><p>Submitted or accepted orders waiting for movement.</p><a href="{{ route('filament.admin.pages.orders-hub', absolute: false) }}">Open Orders Hub</a></article>
            <article class="bkb-business-card"><span>Dispatch queue</span><strong>{{ $snapshot['unassigned_orders'] }}</strong><p>Real unassigned orders from the dispatch engine.</p><a href="{{ route('filament.admin.pages.dispatch-center', absolute: false) }}">Open Dispatch Center</a></article>
            <article class="bkb-business-card"><span>Assigned jobs</span><strong>{{ $snapshot['assigned_jobs'] }}</strong><p>Orders with active dispatch assignments.</p><a href="{{ route('filament.admin.pages.live-operations-map', absolute: false) }}">Open Live Map</a></article>
            <article class="bkb-business-card"><span>Worker availability</span><strong>{{ $snapshot['eligible_workers'] }}</strong><p>Approved workers matching dispatch eligibility.</p><a href="{{ route('filament.admin.pages.people-workforce', absolute: false) }}">Open Workforce</a></article>
            <article class="bkb-business-card"><span>Finance blockers</span><strong>{{ $snapshot['unpaid_invoices'] + $snapshot['blocked_payments'] }}</strong><p>Unpaid invoices and failed or blocked payment records.</p><a href="{{ route('filament.admin.pages.finance-control', absolute: false) }}">Open Finance</a></article>
            <article class="bkb-business-card"><span>Support issues</span><strong>{{ $snapshot['open_support_tickets'] }}</strong><p>Open customer, worker or operational support tickets.</p><a href="{{ route('filament.admin.pages.support-center', absolute: false) }}">Open Support</a></article>
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
