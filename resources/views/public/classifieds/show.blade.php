@extends('layouts.public-shell')

@section('title', $listing->title)

@section('content')
<section class="mk-page">
    <main class="mk-main mk-wrap">
        <a class="mk-btn mk-btn-ghost" href="{{ route('public.classifieds.index') }}">{{ __('bikube.classifieds.market.back_to_market') }}</a>

        <div class="mk-detail" style="margin-top:18px">
            <article class="mk-detail-card">
                <div class="mk-ad-image" style="border-radius:18px;margin-bottom:18px;background-image:url('{{ $listing->image_path ? asset($listing->image_path) : asset('images/bikube/home/scenario-classifieds.png') }}')"></div>
                <h1>{{ $listing->title }}</h1>
                <div class="mk-detail-price">{{ $listing->formattedPrice() }}</div>
                <div class="mk-ad-meta" style="margin:14px 0 20px">
                    <span>{{ $listing->location }}</span>
                    <span>{{ $listing->category ? (trans()->has("bikube.classifieds.categories.{$listing->category->slug}") ? __("bikube.classifieds.categories.{$listing->category->slug}") : $listing->category->name) : __('bikube.classifieds.market.no_category') }}</span>
                    @if($listing->condition)<span>{{ $listing->condition }}</span>@endif
                </div>
                <p style="white-space:pre-line;color:#dbeafe;line-height:1.7">{{ $listing->description }}</p>
            </article>

            <aside class="mk-detail-card">
                <h2>{{ __('bikube.classifieds.market.seller_contact') }}</h2>
                <p style="color:#b9cbe3;line-height:1.6">{{ __('bikube.classifieds.market.contact_guardrail') }}</p>
                @auth
                    <a class="mk-btn mk-btn-primary" href="{{ route('account.support.create') }}">{{ __('bikube.classifieds.market.contact_bikube') }}</a>
                @else
                    <a class="mk-btn mk-btn-primary" href="{{ route('login') }}">{{ __('bikube.classifieds.market.sign_in_to_contact') }}</a>
                @endauth
                <hr style="border:0;border-top:1px solid #23344f;margin:22px 0">
                <p style="color:#9fb2cc;font-size:13px">{{ __('bikube.classifieds.market.no_external_payment') }}</p>
            </aside>
        </div>
    </main>
</section>

@include('public.classifieds.partials.styles')
@endsection
