@extends('layouts.account-shell')

@section('title', __('bikube.classifieds.account.create_title'))

@section('content')
<section class="mk-page" style="margin:0">
    <main class="mk-main mk-wrap">
        <div class="mk-row-head">
            <div>
                <h1 style="margin:0;font-size:42px">{{ __('bikube.classifieds.account.create_title') }}</h1>
                <p style="color:#b9cbe3">{{ __('bikube.classifieds.account.create_subtitle') }}</p>
            </div>
            <a class="mk-btn mk-btn-ghost" href="{{ route('account.classifieds.index') }}">{{ __('bikube.classifieds.account.back_to_my_ads') }}</a>
        </div>

        <form class="mk-form mk-detail-card" method="POST" action="{{ route('account.classifieds.store') }}">
            @csrf
            <label>
                <span>{{ __('bikube.classifieds.fields.category') }}</span>
                <select name="classified_category_id">
                    <option value="">{{ __('bikube.classifieds.market.any_category') }}</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" @selected(old('classified_category_id') == $category->id)>{{ trans()->has("bikube.classifieds.categories.{$category->slug}") ? __("bikube.classifieds.categories.{$category->slug}") : $category->name }}</option>
                    @endforeach
                </select>
            </label>
            <label>
                <span>{{ __('bikube.classifieds.fields.title') }}</span>
                <input name="title" value="{{ old('title') }}" required maxlength="120">
            </label>
            <label>
                <span>{{ __('bikube.classifieds.fields.description') }}</span>
                <textarea name="description" required maxlength="4000">{{ old('description') }}</textarea>
            </label>
            <div style="display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px">
                <label><span>{{ __('bikube.classifieds.fields.price') }}</span><input name="price_amount" type="number" min="0" step="1" value="{{ old('price_amount') }}"></label>
                <label><span>{{ __('bikube.classifieds.fields.condition') }}</span><input name="condition" value="{{ old('condition') }}" maxlength="80"></label>
                <label><span>{{ __('bikube.classifieds.fields.location') }}</span><input name="location" value="{{ old('location', 'Narvik') }}" required maxlength="120"></label>
            </div>
            <div style="display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px">
                <label><span>{{ __('bikube.classifieds.fields.contact_name') }}</span><input name="contact_name" value="{{ old('contact_name', auth()->user()->name) }}" maxlength="120"></label>
                <label><span>{{ __('bikube.classifieds.fields.contact_email') }}</span><input name="contact_email" type="email" value="{{ old('contact_email', auth()->user()->email) }}" maxlength="190"></label>
                <label><span>{{ __('bikube.classifieds.fields.contact_phone') }}</span><input name="contact_phone" value="{{ old('contact_phone') }}" maxlength="40"></label>
            </div>
            <p style="color:#fcd34d">{{ __('bikube.classifieds.account.moderation_notice') }}</p>
            <button class="mk-btn mk-btn-primary" type="submit">{{ __('bikube.classifieds.account.submit_for_review') }}</button>
        </form>
    </main>
</section>

@include('public.classifieds.partials.styles')
@endsection
