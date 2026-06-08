@extends('public.layouts.app')

@section('content')
    <article>
        <header class="content-hero">
            <p class="eyebrow">BiKuBe service</p>
            <h1>{{ $page->title }}</h1>
            @if ($page->subtitle)<p class="subtitle">{{ $page->subtitle }}</p>@endif
            @if ($page->short_description)<p class="subtitle">{{ $page->short_description }}</p>@endif
            @if ($page->published_at)<p class="published">Published {{ $page->published_at->toFormattedDateString() }}</p>@endif
        </header>
        @if ($page->body)<div class="content-body">{{ $page->body }}</div>@endif
        @if ($page->scenario_key)
            @php($scenario = \App\Models\ServiceScenario::active()->where('scenario_key', $page->scenario_key)->first())
            @if ($scenario)<p><a class="public-action" href="{{ route('public.orders.request', $scenario->slug) }}">Request service</a></p>@endif
        @endif
    </article>
@endsection
