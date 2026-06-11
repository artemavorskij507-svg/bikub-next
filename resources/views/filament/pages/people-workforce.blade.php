@php
    $items = $this->getPeopleFoundation();
    $dashboardUrl = route('filament.admin.pages.dashboard');
    $counts = $this->getWorkerCounts();
@endphp

<x-filament-panels::page>
    <main class="bkb-admin-shell bkb-status-cockpit" aria-labelledby="bkb-people-title">
        <section class="bkb-module-hero bkb-surface">
            <div>
                <a class="bkb-back-link" href="{{ $dashboardUrl }}">Back to Admin OS</a>
                <p class="bkb-kicker">BiKuBe Next / People</p>
                <h1 id="bkb-people-title">People & Workforce</h1>
                <p class="bkb-hero__subtitle">Identity, RBAC and workforce-readiness foundation. This page does not invent worker records or operational counts.</p>
            </div>
            <aside class="bkb-module-status">
                <span>Current state</span>
                <strong>RBAC packages installed</strong>
                <p>Roles, permissions and workforce domain setup still require a real implementation pass.</p>
            </aside>
        </section>

        <section class="bkb-ops-runtime">
            <div class="bkb-foundation-strip">
                @foreach(['users'=>'Total users','applications'=>'Applications','submitted'=>'Submitted applications','profiles'=>'Worker profiles','approved'=>'Approved workers','online'=>'Online / available','eligible'=>'Dispatch eligible','documents'=>'Documents pending'] as $key=>$label)
                    <article><span>{{ $label }}</span><strong>{{ $counts[$key] }}</strong></article>
                @endforeach
            </div>
            <p><a class="bkb-card-link" href="{{ \App\Filament\Resources\WorkerProfiles\WorkerProfileResource::getUrl() }}">Open real Worker Profiles</a></p>
            <p><a class="bkb-card-link" href="{{ \App\Filament\Resources\WorkerApplications\WorkerApplicationResource::getUrl() }}">Review Worker Applications</a></p>
            <div class="bkb-section-heading">
                <p class="bkb-kicker">People foundation</p>
                <h2>Auth and RBAC readiness</h2>
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
    </main>
</x-filament-panels::page>
