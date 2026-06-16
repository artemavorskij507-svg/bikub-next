@php
    $cards = $this->getBusinessReadinessCards();
    $blockers = $this->getLaunchBlockers();
    $rows = $this->getModuleReadinessRows();
    $technical = $this->getTechnicalReadiness();
@endphp

<x-filament-panels::page>
    <main class="bkb-admin-shell bkb-business-shell bkb-system-readiness" aria-labelledby="bkb-readiness-title">
        <section class="bkb-ops-hero bkb-surface bkb-business-hero">
            <div class="bkb-ops-hero__copy">
                <a class="bkb-back-link" href="{{ route('filament.admin.pages.dashboard', absolute: false) }}">Back to Operations Command Center</a>
                <p class="bkb-kicker">BiKuBe Next / Readiness</p>
                <h1 id="bkb-readiness-title">Готовность системы BiKuBe</h1>
                <p class="bkb-hero__subtitle">Проверка того, что бизнес-процессы, платежи, исполнители, заказы и техническая безопасность готовы к запуску. Технические governance-модули ниже свернуты и не являются лицом продукта.</p>
                <div class="bkb-status-row">
                    <span class="bkb-status-badge bkb-status-badge--safe">Business-first readiness</span>
                    <span class="bkb-status-badge">No fake launch claims</span>
                    <span class="bkb-status-badge">Technical controls demoted</span>
                </div>
            </div>

            <aside class="bkb-os-card bkb-os-card--blocked">
                <span class="bkb-card-eyebrow">Launch blockers</span>
                <h2>What still blocks Narvik pilot launch</h2>
                <ul class="bkb-blocked-list">
                    @foreach ($blockers as $blocker)
                        <li>{{ $blocker }}</li>
                    @endforeach
                </ul>
            </aside>
        </section>

        <section class="bkb-business-grid" aria-label="Business readiness cards">
            @foreach ($cards as $card)
                <article class="bkb-business-card bkb-business-card--{{ $card['tone'] }}">
                    <span>{{ $card['label'] }}</span>
                    <strong>{{ $card['count'] }}</strong>
                    <p>{{ $card['detail'] }}</p>
                    @if ($card['url'])
                        <a href="{{ $card['url'] }}">Open route</a>
                    @endif
                </article>
            @endforeach
        </section>

        <section class="bkb-table-wrap bkb-readiness-table">
            <h2>Module readiness</h2>
            <table class="bkb-ops-table">
                <thead>
                    <tr>
                        <th>Area</th>
                        <th>Status</th>
                        <th>Business meaning</th>
                        <th>Next action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($rows as $row)
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

        <details class="bkb-technical-readiness">
            <summary>
                <span>Security Technical Readiness</span>
                <strong>Scanner, retention, reviewer access, audit exports and incidents</strong>
            </summary>

            <div class="bkb-runtime-grid">
                @foreach ($technical as $item)
                    <article class="bkb-runtime-card bkb-runtime-card--review">
                        <span>{{ $item['label'] }}</span>
                        <strong>{{ $item['status'] }}</strong>
                        <p>{{ $item['detail'] }}</p>
                        @if ($item['url'])
                            <a class="bkb-card-link" href="{{ $item['url'] }}">Open technical route</a>
                        @endif
                    </article>
                @endforeach
            </div>
        </details>
    </main>
</x-filament-panels::page>
