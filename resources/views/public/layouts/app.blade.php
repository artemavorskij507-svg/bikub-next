<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $seo['title'] ?? 'BiKuBe' }}</title>
    @if (! empty($seo['description']))<meta name="description" content="{{ $seo['description'] }}">@endif
    @if (! empty($seo['canonical']))<link rel="canonical" href="{{ $seo['canonical'] }}">@endif
    @if (! empty($seo['robots']))<meta name="robots" content="{{ $seo['robots'] }}">@endif
    @if (! empty($seo['og_title']))<meta property="og:title" content="{{ $seo['og_title'] }}">@endif
    @if (! empty($seo['og_description']))<meta property="og:description" content="{{ $seo['og_description'] }}">@endif
    @if (! empty($seo['og_image']))<meta property="og:image" content="{{ $seo['og_image'] }}">@endif
    <link rel="stylesheet" href="{{ asset('css/theme-palette.css') }}">
    <script>window.BKB_THEME_SURFACE='public'</script>
    <script src="{{ asset('js/theme-palette.js') }}" defer></script>
    <style>
        :root{color-scheme:dark;--bg:#061019;--panel:#0b1924;--line:#1d3341;--text:#f2f7f8;--muted:#9db0bb;--accent:#25dc91}
        *{box-sizing:border-box}body{margin:0;background:radial-gradient(circle at 80% 0,#103329 0,transparent 30%),var(--bg);color:var(--text);font-family:Inter,ui-sans-serif,system-ui,sans-serif;line-height:1.65}
        a{color:inherit}.public-shell{width:min(100% - 2rem,76rem);margin:auto}.public-header{display:flex;justify-content:space-between;align-items:center;min-height:4.5rem;border-bottom:1px solid var(--line)}
        .brand{font-weight:900;letter-spacing:.01em;text-decoration:none}.brand span{color:var(--accent)}.public-main{padding:clamp(3rem,8vw,7rem) 0}
        .content-hero{max-width:58rem}.eyebrow{color:var(--accent);font-size:.78rem;font-weight:850;letter-spacing:.12em;text-transform:uppercase}h1{font-size:clamp(2.4rem,7vw,5.6rem);line-height:1.02;margin:.7rem 0 1rem;letter-spacing:0}.subtitle{max-width:48rem;color:#c5d3da;font-size:clamp(1.05rem,2vw,1.35rem)}
        .published{margin-top:1.5rem;color:var(--muted);font-size:.85rem}.content-body{max-width:58rem;margin-top:3rem;padding-top:2rem;border-top:1px solid var(--line);color:#d9e4e8;font-size:1.06rem;white-space:pre-wrap}
        .public-footer{padding:2rem 0 3rem;border-top:1px solid var(--line);color:var(--muted);font-size:.85rem}
        .public-action,button{display:inline-flex;border:0;border-radius:.65rem;background:var(--accent);color:#03130d;padding:.85rem 1.15rem;font-weight:850;text-decoration:none;cursor:pointer}.request-form{display:grid;gap:1rem;max-width:42rem;margin-top:2rem}.request-form label{display:grid;gap:.4rem;font-weight:700}.request-form input,.request-form textarea{width:100%;border:1px solid var(--line);border-radius:.65rem;background:#07151f;color:var(--text);padding:.8rem}.request-form textarea{min-height:9rem}.form-errors{color:#ff9c9c}
    </style>
</head>
<body>
    <header class="public-shell public-header">
        <a class="brand" href="/">BiKuBe<span>.</span></a>
        <span>Local services operating system for Norway</span><x-locale-switcher />
        @auth<x-theme-palette.picker surface="public" />@endauth
    </header>
    <main class="public-shell public-main">@yield('content')</main>
    <footer class="public-shell public-footer">BiKuBe Next · Narvik first</footer>
</body>
</html>
