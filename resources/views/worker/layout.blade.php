<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="bkb-worker-html">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#071120">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <link rel="stylesheet" href="{{ asset('css/theme-palette.css') }}">
    <script>window.BKB_THEME_SURFACE='worker'</script>
    <script src="{{ asset('js/theme-palette.js') }}" defer></script>
    <title>@yield('title', 'Worker Cockpit') — BiKuBe</title>
    <style>
        :root{color-scheme:dark;--bg:#071120;--panel:rgba(11,24,40,.88);--panel2:rgba(15,31,50,.78);--line:rgba(148,163,184,.16);--text:#f3f7fb;--muted:#8fa5bd;--green:#34e69a;--blue:#55d9ff;--amber:#f5bd54;--danger:#fb7185}
        *{box-sizing:border-box}html,body{min-height:100%;margin:0}body{overflow:hidden;background:radial-gradient(circle at 78% 8%,rgba(52,230,154,.16),transparent 28%),radial-gradient(circle at 12% 28%,rgba(85,217,255,.12),transparent 32%),linear-gradient(145deg,#071120,#081827 58%,#040b14);color:var(--text);font:15px/1.5 Inter,ui-sans-serif,system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;-webkit-font-smoothing:antialiased}
        a{color:inherit}.worker-shell{display:flex;width:100%;height:100dvh;overflow:hidden}.worker-sidebar{width:270px;flex:0 0 270px;display:flex;flex-direction:column;border-right:1px solid var(--line);background:rgba(5,14,25,.82);backdrop-filter:blur(18px);box-shadow:18px 0 50px rgba(0,0,0,.25)}.worker-brand{display:flex;align-items:center;gap:.72rem;min-height:76px;padding:1rem;border-bottom:1px solid var(--line);text-decoration:none}.worker-mark{display:grid;width:42px;height:42px;place-items:center;border-radius:13px;background:linear-gradient(135deg,#45efaa,#079263);color:#02130d;font-weight:950;box-shadow:0 0 28px rgba(52,230,154,.32)}.worker-brand strong{display:block;font-size:1rem;font-weight:950}.worker-brand span{display:block;color:var(--green);font-size:.62rem;font-weight:900;letter-spacing:.12em;text-transform:uppercase}.worker-nav{display:grid;gap:.35rem;padding:.85rem;overflow:auto}.worker-nav a{display:grid;gap:.1rem;border:1px solid transparent;border-radius:11px;padding:.78rem .85rem;text-decoration:none;color:#d7e4f2}.worker-nav a:hover,.worker-nav a:focus-visible,.worker-nav a.is-active{border-color:rgba(52,230,154,.28);background:rgba(52,230,154,.08);outline:none}.worker-nav b{font-size:.86rem}.worker-nav small{color:var(--muted);font-size:.7rem}.worker-user{margin-top:auto;border-top:1px solid var(--line);padding:1rem;color:var(--muted);font-size:.8rem}.worker-main{min-width:0;flex:1;display:flex;flex-direction:column}.worker-topbar{display:flex;align-items:center;justify-content:space-between;gap:1rem;min-height:76px;border-bottom:1px solid var(--line);padding:.9rem 1.2rem;background:rgba(7,17,32,.66);backdrop-filter:blur(18px)}.worker-topbar h1{margin:0;font-size:1.02rem}.worker-topbar p{margin:.1rem 0 0;color:var(--muted);font-size:.78rem}.worker-content{height:100%;overflow:auto;padding:1.15rem 1.2rem 6rem;scrollbar-width:thin;scrollbar-color:rgba(148,163,184,.32) transparent}.worker-bottom{display:none}.worker-alert{border:1px solid rgba(52,230,154,.24);border-radius:11px;background:rgba(13,54,45,.72);margin:1rem 1.2rem 0;padding:.75rem 1rem;color:#dffcf0}.worker-error{border-color:rgba(251,113,133,.32);background:rgba(72,22,34,.72);color:#ffd9df}.btn,.worker-btn{display:inline-flex;align-items:center;justify-content:center;min-height:2.65rem;border:1px solid var(--line);border-radius:9px;background:rgba(14,30,49,.86);color:var(--text);padding:.58rem .85rem;font-weight:850;text-decoration:none;cursor:pointer}.btn.primary,.worker-btn.is-primary{border-color:rgba(52,230,154,.48);background:linear-gradient(135deg,#25c889,#0c7c5b);color:#fff}.btn.danger{border-color:rgba(251,113,133,.45);background:rgba(98,29,43,.72);color:#ffd9df}.btn:disabled,.worker-btn:disabled{cursor:not-allowed;opacity:.55}.card,.worker-card{border:1px solid var(--line);border-radius:14px;background:var(--panel);box-shadow:0 18px 48px rgba(0,0,0,.22);padding:1rem}.muted{color:var(--muted)}.grid{display:grid;gap:1rem}.cards{grid-template-columns:repeat(auto-fit,minmax(250px,1fr))}.kv{display:grid;grid-template-columns:minmax(120px,.38fr) 1fr;gap:.75rem;padding:.7rem 0;border-bottom:1px solid var(--line)}.actions{display:flex;flex-wrap:wrap;gap:.55rem}label{display:grid;gap:.35rem;margin:.75rem 0;color:#c9d8e8;font-weight:750}input,select,textarea{width:100%;border:1px solid var(--line);border-radius:9px;background:rgba(4,12,23,.75);color:var(--text);padding:.72rem}.worker-hero{position:relative;overflow:hidden;display:grid;grid-template-columns:minmax(0,1fr) auto;gap:1rem;align-items:center;border:1px solid var(--line);border-radius:18px;background:linear-gradient(145deg,rgba(12,31,50,.96),rgba(5,16,29,.96));padding:1.2rem;box-shadow:0 26px 80px rgba(0,0,0,.28)}.worker-hero:before{position:absolute;inset:0;background:radial-gradient(circle at 78% 22%,rgba(52,230,154,.2),transparent 28%),linear-gradient(90deg,rgba(85,217,255,.07),transparent);content:"";pointer-events:none}.worker-hero>*{position:relative}.worker-hero-eyebrow{margin:0;color:var(--green);font-size:.68rem;font-weight:950;letter-spacing:.12em;text-transform:uppercase}.worker-hero h2{margin:.25rem 0 0;font-size:clamp(1.6rem,4vw,3rem);line-height:1;font-weight:950}.worker-hero p{max-width:52rem;color:#b8c9dc}.worker-kpis{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:.8rem}.worker-kpi span{color:var(--muted);font-size:.68rem;font-weight:950;text-transform:uppercase}.worker-kpi strong{display:block;margin-top:.35rem;font-size:1.55rem}.worker-task-card{display:grid;grid-template-columns:1fr auto;gap:1rem;align-items:center}.worker-status-pill{display:inline-flex;border:1px solid rgba(85,217,255,.28);border-radius:999px;background:rgba(85,217,255,.08);padding:.22rem .55rem;color:#dff7ff;font-size:.72rem;font-weight:900;text-transform:uppercase}.worker-empty{display:grid;place-items:center;min-height:14rem;text-align:center}.worker-empty i{display:grid;width:4.2rem;height:4.2rem;place-items:center;border-radius:999px;background:rgba(148,163,184,.1);font-style:normal;font-size:1.8rem}.worker-page-head{display:flex;justify-content:space-between;gap:1rem;align-items:flex-start;margin-bottom:1rem}.worker-page-head h1{margin:0;font-size:2rem;line-height:1}.worker-page-head p{margin:.3rem 0 0}.worker-order-list{display:grid;gap:.8rem}.worker-order-row{display:grid;grid-template-columns:auto 1fr auto;gap:.85rem;align-items:center;border:1px solid var(--line);border-radius:14px;background:var(--panel);padding:1rem;text-decoration:none}.worker-order-icon{display:grid;width:2.7rem;height:2.7rem;place-items:center;border-radius:12px;background:rgba(52,230,154,.12);color:var(--green);font-weight:950}
        @media(max-width:860px){body{overflow:auto}.worker-shell{display:block;height:auto;min-height:100dvh}.worker-sidebar{display:none}.worker-topbar{position:sticky;top:0;z-index:20}.worker-content{padding:1rem .85rem 5.8rem}.worker-bottom{position:fixed;z-index:30;left:0;right:0;bottom:0;display:grid;grid-template-columns:repeat(4,1fr);gap:.35rem;border-top:1px solid var(--line);background:rgba(5,14,25,.92);backdrop-filter:blur(16px);padding:.55rem}.worker-bottom a{border-radius:10px;padding:.55rem .35rem;color:#d9e7f6;text-align:center;text-decoration:none;font-size:.74rem;font-weight:850}.worker-bottom a.is-active{background:rgba(52,230,154,.1);color:#eafff5}.worker-hero,.worker-task-card,.worker-page-head{grid-template-columns:1fr}.worker-kpis{grid-template-columns:repeat(2,minmax(0,1fr))}.worker-order-row{grid-template-columns:auto 1fr}.worker-order-row .worker-btn{grid-column:1/-1}.kv{grid-template-columns:1fr}}
    </style>
</head>
@php
    $routeName = Route::currentRouteName();
    $nav = [
        ['route' => 'worker.dashboard', 'label' => 'Dashboard', 'hint' => 'Status and daily work'],
        ['route' => 'worker.orders.index', 'label' => 'Assignments', 'hint' => 'Assigned orders'],
        ['route' => 'worker.payout-profile.show', 'label' => 'Payout profile', 'hint' => 'Masked readiness'],
        ['route' => 'worker.payout-reviews.index', 'label' => 'Reviews', 'hint' => 'Tax and identity'],
        ['route' => 'worker.notifications.index', 'label' => 'Notifications', 'hint' => 'Updates and alerts'],
        ['route' => 'worker.profile.index',        'label' => 'Profile',       'hint' => 'Account information'],
        ['route' => 'worker.schedule.index',       'label' => 'Schedule',      'hint' => 'Availability and shifts'],
        ['route' => 'worker.wallet.index',         'label' => 'Finances',      'hint' => 'Earnings and payouts'],
        ['route' => 'worker.support.index', 'label' => 'Support', 'hint' => 'Worker support'],
    ];
@endphp
<body class="@yield('body-class')">
<div class="worker-shell">
    <aside class="worker-sidebar" aria-label="Worker cockpit navigation">
        <a class="worker-brand" href="{{ route('worker.dashboard') }}">
            <span class="worker-mark">BKB</span>
            <span><strong>BiKuBe Worker</strong><span>Courier cockpit</span></span>
        </a>
        <nav class="worker-nav">
            @foreach($nav as $item)
                @if(Route::has($item['route']))
                    <a href="{{ route($item['route']) }}" @class(['is-active' => $routeName === $item['route']])>
                        <b>{{ $item['label'] }}</b>
                        <small>{{ $item['hint'] }}</small>
                    </a>
                @endif
            @endforeach
        </nav>
        <div class="worker-user">
            <strong>{{ auth()->user()?->name ?? 'Worker' }}</strong><br>
            {{ auth()->user()?->email }}
        </div>
    </aside>
    <section class="worker-main">
        <header class="worker-topbar">
            <div>
                <h1>@yield('title', 'Worker Cockpit')</h1>
                <p>Real assignments, presence, GPS permission and payout readiness.</p>
            </div>
            <x-theme-palette.picker surface="worker" />
        </header>
        @if(session('status'))<div class="worker-alert">{{ session('status') }}</div>@endif
        @if($errors->any())<div class="worker-alert worker-error">{{ collect($errors->all())->join(' ') }}</div>@endif
        <main class="worker-content">@yield('content')</main>
    </section>
</div>
<nav class="worker-bottom" aria-label="Worker mobile navigation">
    @foreach(array_slice($nav, 0, 4) as $item)
        @if(Route::has($item['route']))
            <a href="{{ route($item['route']) }}" @class(['is-active' => $routeName === $item['route']])>{{ $item['label'] }}</a>
        @endif
    @endforeach
</nav>
@stack('scripts')
</body>
</html>
