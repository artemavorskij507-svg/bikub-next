@php($brand = rescue(fn () => app(\App\Settings\PlatformSettings::class)->public_brand_name, 'BiKuBe', report: false))
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') · {{ $brand }}</title>
    <link rel="stylesheet" href="{{ asset('css/theme-palette.css') }}">
    <link rel="stylesheet" href="{{ asset('css/account-public-shell.css') }}">
    <script>window.BKB_THEME_SURFACE = 'account'</script>
    <script src="{{ asset('js/theme-palette.js') }}" defer></script>
</head>
<body class="shell-body">

<header class="shell-header">
    <a class="shell-brand" href="/">{{ $brand }}<span>.</span></a>
    <span style="font-size:.72rem;font-weight:900;letter-spacing:.08em;text-transform:uppercase;opacity:.52;padding:.18rem .55rem;border:1px solid currentColor;border-radius:6px">Customer account</span>
    <nav aria-label="Account navigation">
        <a href="{{ route('account.dashboard') }}">Overview</a>
        <a href="{{ route('account.orders.index') }}">Orders</a>
        <a href="{{ route('account.billing.index') }}">Billing</a>
        <a href="{{ route('account.support.index') }}">Support</a>
        <x-theme-palette.picker surface="account" />
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="shell-quiet">Sign out</button>
        </form>
    </nav>
</header>

@if(session('status'))
    <div style="width:min(100% - 2rem,76rem);margin:1rem auto;padding:.75rem 1rem;border-radius:8px;border:1px solid rgba(37,220,145,.28);background:rgba(10,50,35,.7);color:#d0ffe8">
        {{ session('status') }}
    </div>
@endif

@if($errors->any())
    <div style="width:min(100% - 2rem,76rem);margin:1rem auto;padding:.75rem 1rem;border-radius:8px;border:1px solid rgba(252,165,165,.28);background:rgba(72,22,34,.7);color:#ffd9df">
        {{ collect($errors->all())->join(' · ') }}
    </div>
@endif

<main class="shell-main">
    @yield('content')
</main>

<footer class="shell-footer">
    {{ $brand }} · Narvik operations
</footer>

@stack('scripts')
</body>
</html>
