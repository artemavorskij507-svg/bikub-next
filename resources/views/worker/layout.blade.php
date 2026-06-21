<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="bkb-worker-html">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#071120">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <title>@yield('title', 'Worker Cockpit') — BiKuBe</title>
    <link rel="stylesheet" href="{{ asset('css/theme-palette.css') }}">
    <script>window.BKB_THEME_SURFACE='worker'</script>
    <script src="{{ asset('js/theme-palette.js') }}" defer></script>
    <style>
        :root{color-scheme:dark;--bg:#071120;--bg2:#0b1728;--panel:rgba(10,23,39,.88);--panel2:rgba(14,31,52,.78);--line:rgba(148,163,184,.16);--line2:rgba(148,163,184,.1);--text:#f4f8fb;--muted:#91a7bd;--green:#34e69a;--blue:#55d9ff;--amber:#f5bd54;--danger:#fb7185;--brand-rgb:52,230,154;--brand-a:#25c889;--brand-b:#0c7c5b}
        html.bkb-theme-palette-enabled{--green:var(--bkb-accent);--brand-rgb:var(--bkb-accent-rgb);--brand-a:var(--bkb-accent);--brand-b:var(--bkb-accent-2)}
        *{box-sizing:border-box}html,body{min-height:100%;margin:0;max-width:100%;overflow-x:hidden}body{background:radial-gradient(circle at 82% 6%,rgba(var(--brand-rgb),.16),transparent 30%),radial-gradient(circle at 12% 30%,rgba(85,217,255,.11),transparent 32%),linear-gradient(145deg,#071120,#081827 60%,#040b14);color:var(--text);font:15px/1.5 Inter,ui-sans-serif,system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;-webkit-font-smoothing:antialiased}a{color:inherit}:focus-visible{outline:2px solid rgba(var(--brand-rgb),.72);outline-offset:3px}
        .worker-shell{display:grid;grid-template-columns:72px minmax(0,1fr);min-height:100dvh}.worker-sidebar{position:sticky;top:0;height:100dvh;display:flex;flex-direction:column;border-right:1px solid var(--line);background:rgba(5,14,25,.86);backdrop-filter:blur(18px);overflow:hidden;z-index:30}.worker-brand{display:grid;place-items:center;min-height:76px;padding:.85rem;border-bottom:1px solid var(--line);text-decoration:none}.worker-mark{display:grid;width:42px;height:42px;place-items:center;border-radius:14px;background:linear-gradient(135deg,var(--brand-a),var(--brand-b));color:#02130d;font-weight:950;box-shadow:0 0 0 1px rgba(var(--brand-rgb),.24),0 18px 42px rgba(var(--brand-rgb),.13)}.worker-brand-label,.worker-brand>span:not(.worker-mark){position:absolute;left:-999px}.worker-nav{display:grid;gap:.42rem;padding:.75rem .55rem;overflow:auto}.worker-nav a{position:relative;display:grid;place-items:center;min-width:0;min-height:52px;border:1px solid transparent;border-radius:16px;text-decoration:none;color:#d7e4f2;transition:background .16s ease,border-color .16s ease,transform .16s ease,color .16s ease}.worker-nav a:hover,.worker-nav a:focus-visible{border-color:rgba(var(--brand-rgb),.22);background:rgba(var(--brand-rgb),.07);transform:translateY(-1px)}.worker-nav a.is-active{border-color:rgba(var(--brand-rgb),.32);background:rgba(var(--brand-rgb),.12);color:var(--green);box-shadow:inset 0 0 0 1px rgba(var(--brand-rgb),.06)}.worker-nav a.is-active:before{content:"";position:absolute;left:-.56rem;top:14px;bottom:14px;width:3px;border-radius:999px;background:var(--green);box-shadow:0 0 14px rgba(var(--brand-rgb),.9)}.worker-nav-icon{font-size:1.08rem;line-height:1;flex-shrink:0}.worker-nav-text{position:absolute;left:calc(100% + .45rem);top:50%;transform:translateY(-50%) translateX(-4px);min-width:148px;border:1px solid var(--line);border-radius:14px;background:rgba(5,14,25,.94);box-shadow:0 18px 48px rgba(0,0,0,.28);padding:.55rem .7rem;opacity:0;pointer-events:none;transition:opacity .14s ease,transform .14s ease;z-index:60}.worker-nav a:hover .worker-nav-text,.worker-nav a:focus-visible .worker-nav-text{opacity:1;transform:translateY(-50%) translateX(0)}.worker-nav b{display:block;font-size:.82rem}.worker-nav small{display:block;color:var(--muted);font-size:.68rem}.worker-user{margin-top:auto;border-top:1px solid var(--line);padding:.7rem .45rem;color:var(--muted);font-size:.7rem;text-align:center;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}.worker-user strong{color:var(--text);display:block}.worker-user br{display:none}
        .worker-main{min-width:0;display:flex;flex-direction:column}.worker-topbar{position:sticky;top:0;z-index:20;display:flex;align-items:center;justify-content:space-between;gap:1rem;min-height:76px;border-bottom:1px solid var(--line);padding:.85rem 1.2rem;background:rgba(7,17,32,.72);backdrop-filter:blur(18px)}.worker-topbar h1{margin:0;font-size:1.05rem}.worker-topbar p{margin:.12rem 0 0;color:var(--muted);font-size:.8rem}.worker-topbar-right{display:flex;align-items:center;gap:.75rem}.worker-chip{display:inline-flex;align-items:center;gap:.42rem;padding:.34rem .72rem;border-radius:999px;border:1px solid rgba(var(--brand-rgb),.22);background:rgba(var(--brand-rgb),.06);font-size:.7rem;font-weight:900;color:var(--green);letter-spacing:.05em;white-space:nowrap}.worker-chip-dot{width:.45rem;height:.45rem;border-radius:999px;background:var(--green);box-shadow:0 0 7px rgba(var(--brand-rgb),.7)}.worker-content{min-width:0;padding:1.2rem;min-height:calc(100dvh - 76px);opacity:1;transform:none;transition:opacity .14s ease,transform .14s ease}body.worker-is-leaving .worker-content{opacity:.36;transform:translateY(4px)}.worker-app-loader{position:fixed;inset:0;z-index:90;pointer-events:none;opacity:0;background:radial-gradient(circle at 50% 22%,rgba(var(--brand-rgb),.13),transparent 30%),rgba(3,10,18,.12);transition:opacity .12s ease}.worker-app-loader:after{content:"";position:absolute;top:0;left:0;height:2px;width:42vw;background:linear-gradient(90deg,transparent,var(--green),transparent);transform:translateX(-55vw)}body.worker-is-leaving .worker-app-loader{opacity:1}body.worker-is-leaving .worker-app-loader:after{animation:worker-load .55s ease-out infinite}@keyframes worker-load{to{transform:translateX(112vw)}}
        .worker-alert{width:min(100% - 2rem,76rem);margin:1rem auto 0;border:1px solid rgba(var(--brand-rgb),.25);border-radius:12px;background:rgba(13,54,45,.74);padding:.8rem 1rem;color:#dffcf0}.worker-error{border-color:rgba(251,113,133,.34);background:rgba(72,22,34,.74);color:#ffd9df}.worker-bottom{display:none}.worker-tablet-nav{display:none}.worker-card,.card{border:1px solid var(--line);border-radius:18px;background:var(--panel);box-shadow:0 18px 48px rgba(0,0,0,.22);padding:1rem}.muted{color:var(--muted)}.worker-btn,.btn{display:inline-flex;align-items:center;justify-content:center;gap:.45rem;min-height:44px;border:1px solid var(--line);border-radius:12px;background:rgba(14,30,49,.86);color:var(--text);padding:.65rem .95rem;font-weight:850;text-decoration:none;cursor:pointer;font-family:inherit}.worker-btn.is-primary,.btn.primary{border-color:rgba(var(--brand-rgb),.48);background:linear-gradient(135deg,var(--brand-a),var(--brand-b));color:#fff}.worker-btn.is-danger{border-color:rgba(251,113,133,.42);background:rgba(98,29,43,.72);color:#ffd9df}.worker-btn:disabled,.btn:disabled{opacity:.52;cursor:not-allowed}.worker-page-head{display:flex;justify-content:space-between;gap:1rem;align-items:flex-start;margin-bottom:1rem}.worker-page-head h1{margin:0;font-size:clamp(1.8rem,3vw,2.5rem);line-height:1}.worker-hero-eyebrow{margin:0;color:var(--green);font-size:.68rem;font-weight:950;letter-spacing:.12em;text-transform:uppercase}.status-pill{display:inline-flex;align-items:center;gap:.45rem;border:1px solid var(--line);border-radius:999px;padding:.28rem .62rem;font-size:.72rem;font-weight:900;text-transform:uppercase}.status-pill.ok{border-color:rgba(var(--brand-rgb),.32);background:rgba(var(--brand-rgb),.09);color:var(--green)}.status-pill.warn{border-color:rgba(245,189,84,.32);background:rgba(245,189,84,.08);color:var(--amber)}.status-pill.danger{border-color:rgba(251,113,133,.32);background:rgba(251,113,133,.08);color:var(--danger)}.grid{display:grid;gap:1rem}.kv{display:grid;grid-template-columns:minmax(120px,.42fr) 1fr;gap:.75rem;padding:.68rem 0;border-bottom:1px solid var(--line2)}.kv:last-child{border-bottom:0}.actions{display:flex;flex-wrap:wrap;gap:.6rem}.skeleton{position:relative;overflow:hidden;background:rgba(148,163,184,.08);border-radius:12px;min-height:1rem}.skeleton:after{content:"";position:absolute;inset:0;transform:translateX(-100%);background:linear-gradient(90deg,transparent,rgba(255,255,255,.08),transparent);animation:worker-shimmer 1.4s infinite}@keyframes worker-shimmer{100%{transform:translateX(100%)}}
        .worker-switch{display:flex;gap:.28rem;border:1px solid var(--line);border-radius:999px;background:rgba(255,255,255,.035);padding:.25rem;overflow:auto}.worker-switch a,.worker-switch button{min-height:40px;border:0;border-radius:999px;background:transparent;color:var(--muted);font:inherit;font-size:.78rem;font-weight:950;text-decoration:none;padding:.45rem .85rem;white-space:nowrap}.worker-switch .is-active,.worker-switch [aria-selected="true"]{background:rgba(var(--brand-rgb),.16);color:var(--text);box-shadow:inset 0 0 0 1px rgba(var(--brand-rgb),.2)}
        @media(min-width:1051px){.worker-shell{grid-template-columns:72px minmax(0,1fr)}.worker-sidebar{width:72px}.worker-sidebar:hover,.worker-sidebar:focus-within{width:248px;box-shadow:24px 0 80px rgba(0,0,0,.28)}.worker-sidebar:hover .worker-brand,.worker-sidebar:focus-within .worker-brand{display:flex;justify-content:flex-start;gap:.75rem}.worker-sidebar:hover .worker-brand>span:not(.worker-mark),.worker-sidebar:focus-within .worker-brand>span:not(.worker-mark){position:static}.worker-sidebar:hover .worker-nav a,.worker-sidebar:focus-within .worker-nav a{display:flex;justify-content:flex-start;padding:.72rem .78rem}.worker-sidebar:hover .worker-nav-text,.worker-sidebar:focus-within .worker-nav-text{position:static;transform:none;opacity:1;box-shadow:none;border:0;background:transparent;padding:0;min-width:0}.worker-sidebar:hover .worker-user,.worker-sidebar:focus-within .worker-user{text-align:left;padding:1rem;white-space:normal}.worker-sidebar:hover .worker-user br,.worker-sidebar:focus-within .worker-user br{display:block}}
        @media(max-width:1050px){.worker-shell{grid-template-columns:1fr}.worker-sidebar{display:none}.worker-content{padding:1rem}.worker-tablet-nav{position:sticky;top:75px;z-index:19;display:flex;gap:.35rem;overflow:auto;border-bottom:1px solid var(--line);background:rgba(7,17,32,.7);backdrop-filter:blur(16px);padding:.48rem 1rem}.worker-tablet-nav a{display:flex;align-items:center;gap:.35rem;min-height:40px;border:1px solid transparent;border-radius:999px;color:#d9e7f6;text-decoration:none;font-size:.78rem;font-weight:900;padding:.45rem .82rem;white-space:nowrap}.worker-tablet-nav a.is-active{border-color:rgba(var(--brand-rgb),.28);background:rgba(var(--brand-rgb),.12);color:var(--green)}.worker-bottom{display:none}.worker-bottom a{position:relative;border-radius:16px;padding:.55rem .35rem;color:#d9e7f6;text-align:center;text-decoration:none;font-size:.68rem;font-weight:850;display:flex;flex-direction:column;align-items:center;gap:.18rem;line-height:1;transition:background .15s ease,color .15s ease,transform .15s ease}.worker-bottom a .bnav-icon{font-size:1.15rem}.worker-bottom a.is-active{background:rgba(var(--brand-rgb),.12);color:var(--green);transform:translateY(-2px)}.worker-bottom a.is-active:before{content:"";position:absolute;top:.22rem;width:20px;height:2px;border-radius:999px;background:var(--green);box-shadow:0 0 10px rgba(var(--brand-rgb),.8)}}
        @media(max-width:720px){.worker-topbar{align-items:flex-start;min-height:auto;padding:.85rem;flex-direction:column}.worker-topbar-right{width:100%;justify-content:space-between}.worker-tablet-nav{display:none}.worker-content{padding:.85rem .75rem 6.45rem}.worker-bottom{position:fixed;z-index:40;left:0;right:0;bottom:0;display:grid;grid-template-columns:repeat(5,1fr);gap:.35rem;border-top:1px solid var(--line);background:rgba(5,14,25,.94);backdrop-filter:blur(16px);padding:.55rem calc(.55rem + env(safe-area-inset-right)) calc(.55rem + env(safe-area-inset-bottom)) calc(.55rem + env(safe-area-inset-left))}.worker-page-head{display:block}.kv{grid-template-columns:1fr}.worker-card{border-radius:15px}}
        @media(max-width:430px){.worker-topbar h1{font-size:1rem}.worker-topbar p{font-size:.74rem}.worker-chip{padding:.3rem .58rem}.worker-bottom{gap:.22rem}.worker-bottom a{font-size:.62rem;padding:.5rem .2rem}}
        @media(prefers-reduced-motion:reduce){*,*:before,*:after{animation:none!important;transition:none!important;scroll-behavior:auto!important}}
    </style>
    @stack('styles')
</head>
@php
    $routeName = Route::currentRouteName();
    $nav = [
        ['route' => 'worker.dashboard',          'match' => 'worker.dashboard',          'icon' => '🏠', 'label' => 'Dashboard',     'hint' => 'Readiness and current work'],
        ['route' => 'worker.orders.index',       'match' => 'worker.orders.*',           'icon' => '📦', 'label' => 'Orders',        'hint' => 'Assignments and delivery steps'],
        ['route' => 'worker.schedule.index',     'match' => 'worker.schedule.*',         'icon' => '🗓', 'label' => 'Schedule',      'hint' => 'Availability and shifts'],
        ['route' => 'worker.wallet.index',       'match' => 'worker.wallet.*',           'icon' => '💳', 'label' => 'Wallet',        'hint' => 'Earnings and payout readiness'],
        ['route' => 'worker.notifications.index','match' => 'worker.notifications.*',    'icon' => '🔔', 'label' => 'Notifications', 'hint' => 'Operational alerts'],
        ['route' => 'worker.profile.index',      'match' => 'worker.profile.*',          'icon' => '👤', 'label' => 'Profile',       'hint' => 'Compliance and contact details'],
        ['route' => 'worker.support.index',      'match' => 'worker.support.*',          'icon' => '🛟', 'label' => 'Support',       'hint' => 'Help and emergency support'],
    ];
    $mobileNav = [
        ['route' => 'worker.dashboard',    'match' => 'worker.dashboard',   'icon' => '🏠', 'label' => 'Home'],
        ['route' => 'worker.orders.index', 'match' => 'worker.orders.*',    'icon' => '📦', 'label' => 'Orders'],
        ['route' => 'worker.wallet.index', 'match' => 'worker.wallet.*',    'icon' => '💳', 'label' => 'Wallet'],
        ['route' => 'worker.support.index','match' => 'worker.support.*',   'icon' => '🛟', 'label' => 'Help'],
        ['route' => 'worker.profile.index','match' => 'worker.profile.*',   'icon' => '👤', 'label' => 'More'],
    ];
@endphp
<body class="@yield('body-class')">
<div class="worker-shell">
    <aside class="worker-sidebar" aria-label="Worker cockpit navigation">
        <a class="worker-brand" href="{{ route('worker.dashboard') }}">
            <span class="worker-mark">BKB</span>
            <span><strong>BiKuBe Worker</strong><span>Operations cockpit</span></span>
        </a>
        <nav class="worker-nav">
            @foreach($nav as $item)
                @if(Route::has($item['route']))
                    <a href="{{ route($item['route']) }}" @class(['is-active' => request()->routeIs($item['match'])]) @if(request()->routeIs($item['match'])) aria-current="page" @endif>
                        <span class="worker-nav-icon" aria-hidden="true">{{ $item['icon'] }}</span>
                        <span class="worker-nav-text"><b>{{ $item['label'] }}</b><small>{{ $item['hint'] }}</small></span>
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
                <p>Readiness, assignments, GPS status and payout visibility.</p>
            </div>
            <div class="worker-topbar-right">
                <span class="worker-chip"><span class="worker-chip-dot"></span> Narvik operations</span>
                <x-theme-palette.picker surface="worker" />
            </div>
        </header>
        <nav class="worker-tablet-nav" aria-label="Worker tablet navigation">
            @foreach($nav as $item)
                @if(Route::has($item['route']))
                    <a href="{{ route($item['route']) }}" @class(['is-active' => request()->routeIs($item['match'])]) @if(request()->routeIs($item['match'])) aria-current="page" @endif>
                        <span aria-hidden="true">{{ $item['icon'] }}</span><span>{{ $item['label'] }}</span>
                    </a>
                @endif
            @endforeach
        </nav>
        @if(session('status'))<div class="worker-alert" role="status">{{ session('status') }}</div>@endif
        @php($__workerErrors = $errors ?? null)
        @if($__workerErrors && $__workerErrors->any())<div class="worker-alert worker-error" role="alert">{{ collect($__workerErrors->all())->join(' ') }}</div>@endif
        <main class="worker-content" id="worker-content">@yield('content')</main>
    </section>
</div>
<nav class="worker-bottom" aria-label="Worker mobile navigation">
    @foreach($mobileNav as $item)
        @if(Route::has($item['route']))
            <a href="{{ route($item['route']) }}" @class(['is-active' => request()->routeIs($item['match'])]) @if(request()->routeIs($item['match'])) aria-current="page" @endif>
                <span class="bnav-icon" aria-hidden="true">{{ $item['icon'] }}</span><span>{{ $item['label'] }}</span>
            </a>
        @endif
    @endforeach
</nav>
<div class="worker-app-loader" aria-hidden="true"></div>
<script>
window.BiKuBeWorkerAudio = window.BiKuBeWorkerAudio || (() => {
    const paths = { confirmation:null, 'support.notification':null, 'chat.message.sent':null, 'chat.message.received':null };
    const cache = new Map();
    return {
        configure(event, path) { paths[event] = path; },
        paths() { return { ...paths }; },
        async play(event) {
            const path = paths[event];
            if (! path) return false;
            const audio = cache.get(event) || new Audio(path);
            cache.set(event, audio);
            audio.currentTime = 0;
            await audio.play();
            return true;
        }
    };
})();
document.addEventListener('click', (event) => {
    const link = event.target.closest('a[href]');
    if (! link || event.defaultPrevented || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return;
    if (link.target || link.hasAttribute('download')) return;
    const url = new URL(link.href, window.location.href);
    if (url.origin !== window.location.origin || url.href === window.location.href) return;
    document.body.classList.add('worker-is-leaving');
}, { capture: true });
window.addEventListener('pageshow', () => document.body.classList.remove('worker-is-leaving'));
</script>
@stack('scripts')
</body>
</html>
