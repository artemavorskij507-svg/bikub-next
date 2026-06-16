@extends('layouts.public-shell')

@section('title', __('bikube.classifieds.market.title'))

@section('content')
<section class="mk-page">
    <header class="mk-header">
        <div class="mk-wrap">
            <div class="mk-topbar">
                <a class="mk-logo" href="{{ route('public.classifieds.index') }}" aria-label="BiKuBe Market">
                    <span class="mk-logo-badge">B</span>
                    <span><strong>BiKuBe</strong><em>Market</em></span>
                </a>
                <nav class="mk-nav" aria-label="{{ __('bikube.classifieds.market.nav') }}">
                    <a href="{{ route('public.classifieds.index') }}">{{ __('bikube.classifieds.market.all_ads') }}</a>
                    <a href="#categories">{{ __('bikube.classifieds.market.categories') }}</a>
                    @auth
                        <a href="{{ route('account.classifieds.index') }}">{{ __('bikube.classifieds.market.my_ads') }}</a>
                    @else
                        <a href="{{ route('login') }}">{{ __('bikube.classifieds.market.sign_in') }}</a>
                    @endauth
                </nav>
                <div class="mk-actions">
                    <a class="mk-btn mk-btn-ghost" href="{{ route('account.dashboard') }}">{{ __('bikube.classifieds.market.account') }}</a>
                    <a class="mk-btn mk-btn-primary" href="{{ route('account.classifieds.create') }}">{{ __('bikube.classifieds.market.create_ad') }}</a>
                </div>
            </div>

            <div class="mk-hero">
                <div class="mk-hero-copy">
                    <span class="mk-location">{{ __('bikube.classifieds.market.location_badge') }}</span>
                    <h1>{{ __('bikube.classifieds.market.heading') }}</h1>
                    <p>{{ __('bikube.classifieds.market.subheading') }}</p>
                    <div class="mk-kpis">
                        <article><strong>{{ $listings->total() }}</strong><span>{{ __('bikube.classifieds.market.active_ads') }}</span></article>
                        <article><strong>{{ $categories->count() }}</strong><span>{{ __('bikube.classifieds.market.category_count') }}</span></article>
                        <article><strong>Narvik</strong><span>{{ __('bikube.classifieds.market.first_city') }}</span></article>
                    </div>
                </div>
            </div>

            <form class="mk-search-form" method="GET" action="{{ route('public.classifieds.index') }}">
                <label class="mk-input">
                    <span>{{ __('bikube.classifieds.market.search') }}</span>
                    <input name="q" value="{{ request('q') }}" placeholder="{{ __('bikube.classifieds.market.search_placeholder') }}">
                </label>
                <label class="mk-input">
                    <span>{{ __('bikube.classifieds.market.category') }}</span>
                    <select name="category">
                        <option value="">{{ __('bikube.classifieds.market.any_category') }}</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->slug }}" @selected(request('category') === $category->slug)>{{ trans()->has("bikube.classifieds.categories.{$category->slug}") ? __("bikube.classifieds.categories.{$category->slug}") : $category->name }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="mk-input">
                    <span>{{ __('bikube.classifieds.market.place') }}</span>
                    <input name="location" value="{{ request('location') }}" placeholder="Narvik">
                </label>
                <button class="mk-btn mk-btn-primary mk-btn-search" type="submit">{{ __('bikube.classifieds.market.find') }}</button>
            </form>
        </div>
    </header>

    <main class="mk-main mk-wrap">
        <section id="categories" class="mk-section">
            <div class="mk-row-head">
                <h2>{{ __('bikube.classifieds.market.categories') }}</h2>
                <a href="{{ route('account.classifieds.create') }}">{{ __('bikube.classifieds.market.create_ad') }}</a>
            </div>
            <div class="mk-categories-grid">
                @forelse($categories as $category)
                    <a class="mk-category-card" href="{{ route('public.classifieds.index', ['category' => $category->slug]) }}">
                        <span class="mk-category-image" style="background-image:url('{{ $category->image_path ? asset($category->image_path) : asset('images/bikube/home/category-classifieds.png') }}')"></span>
                        <span class="mk-category-name">{{ trans()->has("bikube.classifieds.categories.{$category->slug}") ? __("bikube.classifieds.categories.{$category->slug}") : $category->name }}</span>
                        <span class="mk-category-count">{{ $category->listings_count }} {{ __('bikube.classifieds.market.ads') }}</span>
                    </a>
                @empty
                    @foreach(__('bikube.classifieds.market.default_categories') as $name)
                        <article class="mk-category-card">
                            <span class="mk-category-image" style="background-image:url('{{ asset('images/bikube/home/category-classifieds.png') }}')"></span>
                            <span class="mk-category-name">{{ $name }}</span>
                            <span class="mk-category-count">{{ __('bikube.classifieds.market.awaiting_setup') }}</span>
                        </article>
                    @endforeach
                @endforelse
            </div>
        </section>

        @if($featured->isNotEmpty())
            <section class="mk-section">
                <div class="mk-row-head"><h2>{{ __('bikube.classifieds.market.featured') }}</h2></div>
                <div class="mk-ads-grid mk-ads-grid-featured">
                    @foreach($featured as $listing)
                        @include('public.classifieds.partials.card', ['listing' => $listing])
                    @endforeach
                </div>
            </section>
        @endif

        <section class="mk-layout">
            <aside class="mk-filters">
                <h3>{{ __('bikube.classifieds.market.safe_market') }}</h3>
                <p>{{ __('bikube.classifieds.market.safe_market_text') }}</p>
                <ul>
                    <li>{{ __('bikube.classifieds.market.rule_moderation') }}</li>
                    <li>{{ __('bikube.classifieds.market.rule_no_fake_payment') }}</li>
                    <li>{{ __('bikube.classifieds.market.rule_local_first') }}</li>
                </ul>
            </aside>

            <div class="mk-results">
                <div class="mk-row-head">
                    <h2>{{ __('bikube.classifieds.market.latest') }}</h2>
                    <span>{{ $listings->total() }} {{ __('bikube.classifieds.market.results') }}</span>
                </div>
                <div class="mk-ads-grid">
                    @forelse($listings as $listing)
                        @include('public.classifieds.partials.card', ['listing' => $listing])
                    @empty
                        <div class="mk-empty">
                            <h3>{{ __('bikube.classifieds.market.empty_title') }}</h3>
                            <p>{{ __('bikube.classifieds.market.empty_text') }}</p>
                            <a class="mk-btn mk-btn-primary" href="{{ route('account.classifieds.create') }}">{{ __('bikube.classifieds.market.create_first') }}</a>
                        </div>
                    @endforelse
                </div>
                <div class="mk-pagination">{{ $listings->links() }}</div>
            </div>
        </section>
    </main>
</section>

@include('public.classifieds.partials.styles')
@endsection
