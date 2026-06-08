@php
    $items = $this->getContentFoundation();
    $counts = $this->getContentCounts();
    $resources = $this->getResourceLinks();
    $dashboardUrl = route('filament.admin.pages.dashboard');
@endphp

<x-filament-panels::page>
    <main class="bkb-admin-shell bkb-status-cockpit" aria-labelledby="bkb-content-title">
        <section class="bkb-module-hero bkb-surface">
            <div>
                <a class="bkb-back-link" href="{{ $dashboardUrl }}">Back to Admin OS</a>
                <p class="bkb-kicker">BiKuBe Next / Content</p>
                <h1 id="bkb-content-title">CMS & SEO</h1>
                <p class="bkb-hero__subtitle">Real CMS records, publication workflow and SEO metadata management. Public rendering remains intentionally separate.</p>
            </div>
            <aside class="bkb-module-status">
                <span>Current state</span>
                <strong>CMS foundation operational</strong>
                <p>All counts below come from the BiKuBe Next database. No demo content is seeded.</p>
            </aside>
        </section>

        <section class="bkb-foundation-strip" aria-label="Content record counts">
            @foreach ([
                'CMS pages' => $counts['cms_pages'],
                'Service pages' => $counts['service_pages'],
                'SEO metadata' => $counts['seo_metadata'],
                'Draft' => $counts['draft'],
                'Published' => $counts['published'],
                'Archived' => $counts['archived'],
            ] as $label => $count)
                <article>
                    <span>{{ $label }}</span>
                    <strong>{{ $count ?? 'Unavailable' }}</strong>
                </article>
            @endforeach
        </section>

        <section class="bkb-ops-runtime">
            <div class="bkb-section-heading">
                <p class="bkb-kicker">Working resources</p>
                <h2>Manage real content records</h2>
            </div>
            <div class="bkb-runtime-grid">
                @foreach ($resources as $resource)
                    <article class="bkb-runtime-card bkb-runtime-card--works">
                        <span>Content resource</span>
                        <strong>{{ $resource['label'] }}</strong>
                        <p>{{ $resource['detail'] }}</p>
                        <a class="bkb-card-link" href="{{ $resource['url'] }}">Open resource</a>
                    </article>
                @endforeach
            </div>
        </section>

        <section class="bkb-ops-runtime">
            <div class="bkb-section-heading">
                <p class="bkb-kicker">Installed capabilities</p>
                <h2>Content stack readiness</h2>
            </div>
            <div class="bkb-runtime-grid">
                @foreach ($items as $item)
                    <article class="bkb-runtime-card bkb-runtime-card--{{ $item['tone'] }}">
                        <span>{{ $item['label'] }}</span>
                        <strong>{{ $item['state'] }}</strong>
                        <p>{{ $item['detail'] }}</p>
                    </article>
                @endforeach
            </div>
        </section>

        <section class="bkb-honesty-panel">
            <div>
                <span>Not wired yet</span>
                <strong>Media attachment workflow and structured data</strong>
            </div>
            <p>Public rendering and sitemap generation are wired. Media attachment ownership and JSON-LD remain intentionally unimplemented.</p>
        </section>

        <section class="bkb-honesty-panel">
            <div>
                <span>Public routes</span>
                <strong>/p/{slug} · /services/{service_slug}</strong>
            </div>
            <p>Only published records with a current publication date resolve. Run <code>php artisan bikube:generate-sitemap</code> after publishing changes.</p>
        </section>
    </main>
</x-filament-panels::page>
