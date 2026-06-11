@php
    $services = $this->getScenarioModules();
    $counts = $this->getCatalogCounts();
    $links = $this->getResourceLinks();
    $dashboardUrl = route('filament.admin.pages.dashboard');
@endphp

<x-filament-panels::page>
    <main class="bkb-admin-shell bkb-status-cockpit" aria-labelledby="bkb-services-title">
        <section class="bkb-module-hero bkb-surface">
            <div>
                <a class="bkb-back-link" href="{{ $dashboardUrl }}">Back to Admin OS</a>
                <p class="bkb-kicker">BiKuBe Next / Services</p>
                <h1 id="bkb-services-title">Service Catalog</h1>
                <p class="bkb-hero__subtitle">Real service definitions and execution contracts for the Narvik-first platform.</p>
            </div>
            <aside class="bkb-module-status">
                <span>Current state</span>
                <strong>Scenario intake operational</strong>
                <p>Validated public intake creates real submitted orders. Payment and dispatch remain unavailable.</p>
            </aside>
        </section>

        <section class="bkb-ops-runtime">
            <div class="bkb-section-heading">
                <p class="bkb-kicker">Scenario registry</p>
                <h2>{{ count($services) }} BiKuBe service scenarios</h2>
            </div>
            <div class="bkb-foundation-strip">
                @foreach ($counts as $label => $count)<article><span>{{ ucfirst($label) }}</span><strong>{{ $count ?? 'Unavailable' }}</strong></article>@endforeach
            </div>
            <p><a class="bkb-card-link" href="{{ $links['categories'] }}">Manage categories</a> · <a class="bkb-card-link" href="{{ $links['scenarios'] }}">Manage scenarios</a></p>
            <div class="bkb-service-grid">
                @foreach ($services as $service)
                    <article class="bkb-service-card">
                        <span>{{ $service['code'] }}</span>
                        <h3>{{ $service['label'] }}</h3>
                        <p>{{ $service['scope'] }}</p>
                        <dl>
                            <div><dt>Status</dt><dd>{{ $service['status'] }}</dd></div>
                            <div><dt>Configured fields</dt><dd>{{ $service['fields'] }}</dd></div>
                            <div><dt>Payment contract</dt><dd>{{ $service['payment'] }}</dd></div>
                            <div><dt>Tracking</dt><dd>{{ $service['tracking'] }}</dd></div>
                            <div><dt>Pricing readiness</dt><dd>{{ $service['pricing'] }}</dd></div>
                        </dl>
                        <p><a href="{{ $service['edit_url'] }}">Edit scenario</a> · <a href="{{ $service['url'] }}">Public page</a> · <a href="{{ $service['request_url'] }}">Request form</a></p>
                    </article>
                @endforeach
            </div>
        </section>
        <section class="bkb-honesty-panel"><div><span>Works</span><strong>Scenario fields · Order intake</strong></div><p>Public forms use active field definitions. Payment, dispatch and worker assignment are not connected.</p></section>
    </main>
</x-filament-panels::page>
