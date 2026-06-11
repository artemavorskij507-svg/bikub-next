@php($status = $this->getFinanceStatus())
<x-filament-panels::page>
<main class="bkb-admin-shell bkb-status-cockpit">
    <section class="bkb-module-hero bkb-surface"><div><p class="bkb-kicker">BiKuBe Next / Finance</p><h1>Finance Control</h1><p class="bkb-hero__subtitle">Transparent estimates and payment-readiness without fake provider states.</p></div><aside class="bkb-module-status"><span>Pricing Engine</span><strong>Active</strong><p>Payment provider, reservation, capture, refund and invoicing are not connected.</p></aside></section>
    <section class="bkb-ops-runtime"><div class="bkb-foundation-strip">
        @foreach($status as $label => $value)<article><span>{{ str($label)->replace('_', ' ')->title() }}</span><strong>{{ $label === 'quoted_value' && $value !== null ? number_format((float)$value, 2).' NOK' : ($value ?? 'Unavailable') }}</strong></article>@endforeach
    </div><p><a class="bkb-card-link" href="{{ $this->getPricingRulesUrl() }}">Manage pricing rules</a></p></section>
    <section class="bkb-honesty-panel"><div><span>Not connected</span><strong>Payment provider · invoices · accounting</strong></div><p>No capture, refund, paid or reserved actions are exposed.</p></section>
</main>
</x-filament-panels::page>
