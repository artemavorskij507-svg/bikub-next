@extends('layouts.account-shell')

@section('title', __('bikube.classifieds.account.title'))

@section('content')
<section class="mk-page" style="margin:0">
    <main class="mk-main mk-wrap">
        <div class="mk-row-head">
            <div>
                <h1 style="margin:0;font-size:42px">{{ __('bikube.classifieds.account.title') }}</h1>
                <p style="color:#b9cbe3">{{ __('bikube.classifieds.account.subtitle') }}</p>
            </div>
            <a class="mk-btn mk-btn-primary" href="{{ route('account.classifieds.create') }}">{{ __('bikube.classifieds.market.create_ad') }}</a>
        </div>

        <div class="mk-ads-grid">
            @forelse($listings as $listing)
                <article class="mk-ad-card">
                    <div class="mk-ad-body">
                        <div class="mk-ad-meta"><span>{{ $listing->listing_number }}</span><span>{{ $listing->status }}</span></div>
                        <h2 class="mk-ad-title">{{ $listing->title }}</h2>
                        <strong class="mk-ad-price">{{ $listing->formattedPrice() }}</strong>
                        <p style="color:#b9cbe3">{{ $listing->location }} · {{ $listing->category ? (trans()->has("bikube.classifieds.categories.{$listing->category->slug}") ? __("bikube.classifieds.categories.{$listing->category->slug}") : $listing->category->name) : __('bikube.classifieds.market.no_category') }}</p>
                        @if($listing->moderation_note)
                            <p style="color:#fcd34d">{{ $listing->moderation_note }}</p>
                        @endif
                    </div>
                </article>
            @empty
                <div class="mk-empty">
                    <h3>{{ __('bikube.classifieds.account.empty_title') }}</h3>
                    <p>{{ __('bikube.classifieds.account.empty_text') }}</p>
                    <a class="mk-btn mk-btn-primary" href="{{ route('account.classifieds.create') }}">{{ __('bikube.classifieds.account.create_first') }}</a>
                </div>
            @endforelse
        </div>

        <div class="mk-pagination">{{ $listings->links() }}</div>
    </main>
</section>

@include('public.classifieds.partials.styles')
@endsection
