<x-filament-panels::page>
    @php($counts = $this->getOrderCounts())
    <main class="bkb-admin-shell"><section class="bkb-ops-hero bkb-surface"><div class="bkb-ops-hero__copy"><p class="bkb-kicker">BiKuBe Next / Orders</p><h1>Orders Hub</h1><p class="bkb-hero__subtitle">Real order requests received through active service scenarios. Payment and dispatch remain disconnected.</p><div class="bkb-status-row"><span class="bkb-status-badge bkb-status-badge--safe">Order Engine works</span><span class="bkb-status-badge">No fake orders</span></div></div></section>
    <section class="bkb-runtime-grid">@foreach($counts as $label => $count)<article class="bkb-runtime-card"><span>{{ ucfirst($label) }}</span><strong>{{ $count }}</strong><p>Real database count.</p></article>@endforeach</section>
    <section class="bkb-os-card"><h2>Order operations</h2><p>Review real submitted requests. Assignment, payment capture and dispatch are not implemented.</p><a class="bkb-card-link" href="{{ route('filament.admin.resources.orders.index') }}">Open real orders</a></section></main>
</x-filament-panels::page>
