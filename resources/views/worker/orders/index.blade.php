@extends('worker.layout')
@section('title', 'Мои заказы')
@section('content')
@php
    $statusLabels = ['submitted'=>'Ожидает','accepted'=>'Принят','in_progress'=>'В работе','completed'=>'Выполнен','cancelled'=>'Отменён'];
    $variantFor = function ($order) {
        $st = $order->status?->value ?? (string) $order->status;
        $assignmentStatus = ($order->activeDispatchAssignment() ?? $order->dispatchAssignments->first())?->status;
        return match (true) {
            in_array($st, ['accepted', 'in_progress'], true) || $assignmentStatus === 'accepted' => 'active',
            $st === 'submitted' || $assignmentStatus === 'assigned' => 'assigned',
            $st === 'completed' => 'completed',
            $st === 'cancelled' => 'cancelled',
            default => 'history',
        };
    };
    $ordersByPriority = $orders->sortBy(fn($order) => match ($variantFor($order)) {'active'=>0,'assigned'=>1,'completed'=>2,'cancelled'=>3,default=>4})->values();
    $activeOrders = $ordersByPriority->filter(fn($order) => $variantFor($order) === 'active')->values();
    $assignedOrders = $ordersByPriority->filter(fn($order) => $variantFor($order) === 'assigned')->values();
    $historyOrders = $ordersByPriority->filter(fn($order) => in_array($variantFor($order), ['completed','cancelled','history'], true))->values();
    $completedToday = $orders->filter(fn($order) => ($order->status?->value ?? null) === 'completed' && ($order->completed_at?->isToday() || $order->updated_at?->isToday()))->count();
    $completedRealIncome = $orders->filter(fn($order) => ($order->status?->value ?? null) === 'completed')->sum(fn($order) => (float) ($order->priceQuotes->first()?->total_nok ?? 0));
    $query = trim((string) request('q', ''));
    $tab = request('tab', 'all');
    $visibleOrders = $ordersByPriority->filter(function ($order) use ($variantFor, $tab, $query) {
        $variant = $variantFor($order);
        if ($tab === 'active' && $variant !== 'active') return false;
        if ($tab === 'assigned' && $variant !== 'assigned') return false;
        if ($tab === 'history' && ! in_array($variant, ['completed','cancelled','history'], true)) return false;
        if ($query !== '') {
            $intake = $order->metadata['intake'] ?? [];
            $hay = Str::lower($order->order_number.' '.($order->scenario?->title ?? '').' '.($intake['pickup_address'] ?? '').' '.($intake['dropoff_address'] ?? ''));
            if (! Str::contains($hay, Str::lower($query))) return false;
        }
        if (request('status') && request('status') !== $variant) return false;
        if (request('service') === 'delivery' && ! Str::startsWith($order->service_scenario_key, 'delivery.')) return false;
        return true;
    })->values();
@endphp

<style>
@media(min-width:1180px){.worker-sidebar{width:92px!important;overflow:hidden!important}.worker-sidebar small,.worker-sidebar p:not(.worker-brand-kicker),.worker-sidebar .worker-brand-title,.worker-sidebar .nav-copy,.worker-sidebar .sidebar-copy{display:none!important}.worker-sidebar a{justify-content:center!important;font-size:0!important;gap:0!important}.worker-sidebar a span:not(:first-child),.worker-sidebar a div:not(:first-child){display:none!important}.worker-sidebar a span:first-child,.worker-sidebar a div:first-child{font-size:1rem!important}.worker-sidebar [class*=label],.worker-sidebar [class*=copy],.worker-sidebar [class*=title]{display:none!important}.worker-shell,.worker-content{--orders-sidebar-note:collapsed}}
.ov2-page{min-width:0;display:grid;gap:1rem;padding-bottom:78px}.ov2-mobile-head{display:flex;align-items:center;justify-content:space-between;gap:.8rem}.ov2-kicker{margin:0;color:var(--muted);font-size:.68rem;font-weight:950;letter-spacing:.12em;text-transform:uppercase}.ov2-title{margin:.1rem 0 0;font-size:clamp(1.75rem,7vw,2.55rem);line-height:.95}.ov2-status-pack{display:flex;gap:.45rem;align-items:center;flex-wrap:wrap;justify-content:flex-end}.ov2-stats{display:grid;grid-template-columns:1fr 1fr;gap:.55rem}.ov2-stat{border:1px solid var(--line);border-radius:18px;background:linear-gradient(180deg,var(--panel),rgba(14,30,49,.78));padding:.8rem;min-height:78px}.ov2-stat-label{margin:0 0 .45rem;color:var(--muted);font-size:.66rem;font-weight:950;text-transform:uppercase;letter-spacing:.08em}.ov2-stat-value{margin:0;color:var(--text);font-size:1.6rem;line-height:1;font-weight:950}.ov2-stat-note{margin:.35rem 0 0;color:var(--muted);font-size:.72rem}.ov2-tabs{display:grid;grid-template-columns:repeat(4,1fr);gap:.35rem;border:1px solid var(--line);border-radius:999px;background:rgba(255,255,255,.035);padding:.25rem;overflow:auto}.ov2-tab{min-height:40px;display:grid;place-items:center;border-radius:999px;color:var(--muted);font-weight:950;text-decoration:none;white-space:nowrap;font-size:.78rem}.ov2-tab.is-active{background:rgba(var(--brand-rgb),.16);color:var(--text);box-shadow:inset 0 0 0 1px rgba(var(--brand-rgb),.2)}.ov2-filters{display:grid;grid-template-columns:1fr;gap:.5rem}.ov2-input,.ov2-select{width:100%;min-height:46px;border:1px solid var(--line);border-radius:14px;background:rgba(14,30,49,.8);color:var(--text);padding:.68rem .8rem;font:inherit}.ov2-layout{display:grid;grid-template-columns:1fr;gap:1rem}.ov2-main{min-width:0;display:grid;gap:.75rem}.ov2-section-title{margin:.15rem 0 0;color:var(--muted);font-size:.7rem;font-weight:950;text-transform:uppercase;letter-spacing:.12em}.ov2-order-card{position:relative;min-width:0;border:1px solid var(--line);border-radius:24px;background:linear-gradient(180deg,var(--panel),rgba(14,30,49,.86));overflow:hidden;box-shadow:0 18px 54px rgba(0,0,0,.2)}.ov2-order-card:before{content:"";position:absolute;inset:0;background:radial-gradient(circle at 18% 0,rgba(var(--brand-rgb),.13),transparent 34%);pointer-events:none}.ov2-order-card--active{border-color:rgba(var(--brand-rgb),.34)}.ov2-card-topline{position:relative;z-index:1;display:flex;align-items:center;justify-content:space-between;gap:.75rem;padding:.85rem .9rem;border-bottom:1px solid var(--line2)}.ov2-card-eyebrow{color:var(--muted);font-size:.68rem;font-weight:950;letter-spacing:.12em;text-transform:uppercase}.ov2-card-body{position:relative;z-index:1;display:grid;gap:.85rem;padding:.85rem}.ov2-route-pane{order:2;min-height:150px;border:1px solid var(--line2);border-radius:20px;overflow:hidden;background:rgba(255,255,255,.03)}.ov2-route-empty,.ov2-real-map{position:relative;min-height:150px;height:100%;display:grid;align-content:end;gap:.25rem;padding:1rem;color:var(--muted)}.ov2-route-empty strong,.ov2-real-map p{margin:0;color:var(--text);font-weight:950}.ov2-route-empty small,.ov2-real-map small{font-size:.74rem;max-width:230px}.ov2-fake-grid{position:absolute;inset:0;background:linear-gradient(90deg,rgba(255,255,255,.045) 1px,transparent 1px),linear-gradient(0deg,rgba(255,255,255,.04) 1px,transparent 1px),radial-gradient(circle at 70% 20%,rgba(var(--brand-rgb),.18),transparent 30%);background-size:34px 34px,34px 34px,100% 100%;opacity:.7}.ov2-route-dot{position:absolute;width:12px;height:12px;border-radius:999px;background:var(--green);box-shadow:0 0 0 8px rgba(var(--brand-rgb),.12)}.ov2-route-dot--a{left:22%;top:28%}.ov2-route-dot--b{right:24%;bottom:30%}.ov2-real-map{background:linear-gradient(135deg,rgba(var(--brand-rgb),.12),rgba(255,255,255,.04))}.ov2-leaflet-preview{position:absolute;inset:0;z-index:0}.ov2-real-map p,.ov2-real-map small{position:relative;z-index:1;text-shadow:0 2px 18px rgba(0,0,0,.7)}.ov2-map-pin{position:absolute;color:var(--green);font-size:1rem}.ov2-map-pin--pickup{left:22%;bottom:24%}.ov2-map-pin--drop{right:20%;top:18%}.ov2-order-core{order:1;display:grid;gap:.85rem}.ov2-identity-row{display:flex;gap:.8rem;align-items:center}.ov2-order-icon{width:48px;height:48px;border-radius:18px;display:grid;place-items:center;background:rgba(var(--brand-rgb),.1);border:1px solid rgba(var(--brand-rgb),.2);font-size:1.35rem;flex:0 0 auto}.ov2-order-number{margin:0;color:var(--text);font-size:1.12rem;font-weight:950}.ov2-order-service{margin:.16rem 0 0;color:var(--muted);font-size:.86rem}.ov2-timeline{display:grid;gap:.55rem;border-left:1px solid var(--line2);margin-left:.5rem;padding-left:1rem}.ov2-timeline-row{display:grid;grid-template-columns:16px minmax(0,1fr);gap:.5rem;align-items:start}.ov2-pin{margin-left:-1.47rem;color:var(--green);font-size:.7rem}.ov2-timeline-row span:not(.ov2-pin){display:block;color:var(--muted);font-size:.62rem;font-weight:950;letter-spacing:.09em}.ov2-timeline-row strong{display:block;color:#d7e4f2;font-size:.88rem;line-height:1.32}.ov2-meta-strip{display:flex;gap:.55rem;flex-wrap:wrap;color:var(--muted);font-size:.73rem}.ov2-next-pane{order:3;display:grid;gap:.35rem;padding-top:.8rem;border-top:1px solid var(--line2)}.ov2-price{margin:0;color:var(--text);font-size:1.38rem;font-weight:950}.ov2-paystate,.ov2-muted{margin:0;color:var(--muted);font-size:.78rem}.ov2-primary-cta{margin-top:.45rem;width:100%;min-height:48px}.ov2-widgets{display:none}.ov2-widget{border:1px solid var(--line);border-radius:22px;background:var(--panel);padding:1rem}.ov2-widget-title{margin:0 0 .75rem;color:var(--text);font-weight:950}.ov2-kv{display:flex;justify-content:space-between;gap:1rem;padding:.58rem 0;border-bottom:1px solid var(--line2);color:var(--muted);font-size:.82rem}.ov2-kv:last-child{border-bottom:0}.ov2-kv strong{color:var(--text)}.ov2-actions{display:grid;gap:.55rem}.ov2-action-link{display:flex;align-items:center;justify-content:space-between;border:1px solid var(--line2);border-radius:14px;background:rgba(255,255,255,.035);padding:.75rem .85rem;text-decoration:none;font-weight:900}.ov2-empty{border:1px dashed var(--line);border-radius:22px;background:var(--panel);padding:2rem;text-align:center;color:var(--muted)}
@media(min-width:680px){.ov2-page{padding-bottom:0}.ov2-stats{grid-template-columns:repeat(4,1fr)}.ov2-filters{grid-template-columns:minmax(220px,1fr) 150px 150px 110px}.ov2-card-body{grid-template-columns:minmax(0,1fr) 230px;align-items:stretch}.ov2-route-pane{order:2}.ov2-order-core{order:1}.ov2-next-pane{grid-column:1 / -1;grid-template-columns:1fr 1fr auto;align-items:center}.ov2-primary-cta{width:auto;min-width:190px;margin-top:0}.ov2-paystate{align-self:end}}
@media(min-width:980px){.ov2-layout{grid-template-columns:minmax(0,1fr) 300px}.ov2-widgets{display:grid;gap:.75rem;align-content:start;position:sticky;top:92px}.ov2-card-body{grid-template-columns:minmax(0,1fr) 220px}.ov2-route-pane{grid-column:1 / -1;order:1;min-height:190px}.ov2-order-core{order:2}.ov2-next-pane{order:3;grid-column:auto;border-top:0;border-left:1px solid var(--line2);padding:.2rem 0 .2rem 1rem;align-content:end;grid-template-columns:1fr}.ov2-stats{gap:.75rem}.ov2-stat{min-height:104px;padding:1rem}.ov2-mobile-head{margin-top:.2rem}}
@media(min-width:1240px){.ov2-page{gap:1.05rem}.ov2-card-body{grid-template-columns:minmax(0,1fr) 220px}.ov2-route-pane{min-height:210px}.ov2-order-card--active .ov2-card-body{min-height:250px}}
@media(max-width:820px){.worker-sidebar{display:none!important}.worker-content{padding-left:0!important}.worker-shell{grid-template-columns:1fr!important}}
@media(max-width:430px){.ov2-tabs{grid-template-columns:repeat(3,minmax(90px,1fr));overflow:auto}.ov2-tab:first-child{display:none}.ov2-stat{border-radius:16px;padding:.7rem}.ov2-stat-value{font-size:1.45rem}.ov2-card-topline{padding:.75rem}.ov2-card-body{padding:.75rem}.ov2-route-pane{min-height:132px}.ov2-route-empty,.ov2-real-map{min-height:132px}.ov2-order-number{font-size:1rem}.ov2-primary-cta{min-height:50px}}
</style>

<div class="ov2-page">
    <header class="ov2-mobile-head">
        <div>
            <p class="ov2-kicker">Worker orders</p>
            <h1 class="ov2-title">Мои заказы</h1>
        </div>
        <div class="ov2-status-pack">
            <span class="worker-chip"><span class="worker-chip-dot"></span> {{ auth()->user()?->workerAvailability?->status === 'offline' ? 'Offline' : 'Online' }}</span>
            <span class="worker-chip">{{ auth()->user()?->name ?? 'Worker' }}</span>
        </div>
    </header>

    <section class="ov2-stats" aria-label="Orders statistics">
        <div class="ov2-stat"><p class="ov2-stat-label">Active</p><p class="ov2-stat-value">{{ $activeOrders->count() }}</p><p class="ov2-stat-note">current work</p></div>
        <div class="ov2-stat"><p class="ov2-stat-label">Assigned</p><p class="ov2-stat-value">{{ $assignedOrders->count() }}</p><p class="ov2-stat-note">waiting action</p></div>
        <div class="ov2-stat"><p class="ov2-stat-label">Today</p><p class="ov2-stat-value">{{ $completedToday }}</p><p class="ov2-stat-note">completed real orders</p></div>
        <div class="ov2-stat"><p class="ov2-stat-label">Income</p><p class="ov2-stat-value">{{ $completedRealIncome > 0 ? number_format($completedRealIncome, 0, '.', ' ') : '—' }}</p><p class="ov2-stat-note">NOK confirmed</p></div>
    </section>

    <nav class="ov2-tabs" aria-label="Order status tabs">
        @foreach(['all'=>'All','active'=>'Active','assigned'=>'Assigned','history'=>'History'] as $key=>$label)
            <a class="ov2-tab {{ $tab === $key ? 'is-active' : '' }}" href="{{ request()->fullUrlWithQuery(['tab'=>$key]) }}">{{ $label }}</a>
        @endforeach
    </nav>

    <form class="ov2-filters" method="GET" action="{{ route('worker.orders.index') }}">
        <input type="hidden" name="tab" value="{{ $tab }}">
        <input class="ov2-input" name="q" value="{{ $query }}" placeholder="Search order, address, service...">
        <select class="ov2-select" name="status" aria-label="Status filter"><option value="">Status: all</option><option value="active" @selected(request('status')==='active')>Active</option><option value="assigned" @selected(request('status')==='assigned')>Assigned</option><option value="completed" @selected(request('status')==='completed')>Completed</option></select>
        <select class="ov2-select" name="service" aria-label="Service filter"><option value="">Service: all</option><option value="delivery" @selected(request('service')==='delivery')>Delivery</option></select>
        <button class="worker-btn" type="submit">Apply</button>
    </form>

    <div class="ov2-layout">
        <main class="ov2-main">
            @if($visibleOrders->isNotEmpty())
                @foreach($visibleOrders as $order)
                    @php $variant = $variantFor($order); @endphp
                    @if($loop->first && $variant === 'active')<p class="ov2-section-title">Active order</p>@endif
                    @if($loop->first && $variant === 'assigned')<p class="ov2-section-title">Assigned orders</p>@endif
                    @include('worker.orders.partials.order-card', ['order' => $order, 'variant' => $loop->first && $variant === 'active' ? 'hero' : $variant, 'statusLabels' => $statusLabels])
                @endforeach
            @else
                <section class="ov2-empty"><h2>No orders in this view</h2><p>Dispatch has not assigned matching real orders to this account. No demo orders are shown.</p></section>
            @endif
        </main>

        <aside class="ov2-widgets" aria-label="Orders widgets">
            <section class="ov2-widget"><p class="ov2-widget-title">Today summary</p><div class="ov2-kv"><span>Active</span><strong>{{ $activeOrders->count() }}</strong></div><div class="ov2-kv"><span>Assigned</span><strong>{{ $assignedOrders->count() }}</strong></div><div class="ov2-kv"><span>Completed today</span><strong>{{ $completedToday }}</strong></div><div class="ov2-kv"><span>Income</span><strong>{{ $completedRealIncome > 0 ? number_format($completedRealIncome, 0, '.', ' ').' NOK' : '—' }}</strong></div></section>
            <section class="ov2-widget"><p class="ov2-widget-title">Quick actions</p><div class="ov2-actions"><a class="ov2-action-link" href="{{ route('worker.dashboard') }}"><span>Dashboard</span><span>→</span></a>@if($activeOrders->first())<a class="ov2-action-link" href="{{ route('worker.orders.show', $activeOrders->first()) }}"><span>Current Job</span><span>→</span></a>@endif<a class="ov2-action-link" href="{{ route('worker.support.index') }}"><span>Support</span><span>→</span></a><a class="ov2-action-link" href="{{ route('worker.wallet.index') }}"><span>Wallet</span><span>→</span></a></div></section>
            <section class="ov2-widget"><p class="ov2-widget-title">Calendar</p><p class="ov2-muted">No per-day workload markers are shown because only real order dates may create calendar dots.</p></section>
        </aside>
    </div>
</div>

<script>
window.BiKuBeAudioManager = window.BiKuBeAudioManager || (() => {
    const paths = {
        'worker.online': null,
        'worker.offline': null,
        'order.assigned': null,
        'order.accepted': null,
        'order.completed': null,
        'chat.message.sent': null,
        'chat.message.received': null,
        'support.notification': null,
    };
    const cache = new Map();
    const configure = (event, path) => { paths[event] = path; };
    const play = async (event) => {
        const path = paths[event];
        if (! path) return false;
        const audio = cache.get(event) || new Audio(path);
        cache.set(event, audio);
        audio.currentTime = 0;
        await audio.play();
        return true;
    };
    return { configure, play, paths: () => ({ ...paths }) };
})();
(function(){
    if (! window.L) return;
    document.querySelectorAll('.ov2-leaflet-preview').forEach((el) => {
        if (el.dataset.ready) return;
        const p = [Number(el.dataset.pickupLat), Number(el.dataset.pickupLng)];
        const d = [Number(el.dataset.dropoffLat), Number(el.dataset.dropoffLng)];
        if (!p.every(Number.isFinite) || !d.every(Number.isFinite)) return;
        el.dataset.ready = '1';
        const map = L.map(el, { zoomControl:false, attributionControl:false, dragging:false, scrollWheelZoom:false, doubleClickZoom:false, boxZoom:false, keyboard:false, tap:false });
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 18 }).addTo(map);
        L.marker(p).addTo(map);
        L.marker(d).addTo(map);
        map.fitBounds([p,d], { padding: [20,20], maxZoom: 14 });
        setTimeout(() => map.invalidateSize(true), 80);
        setTimeout(() => map.invalidateSize(true), 350);
    });
})();
</script>
@endsection
