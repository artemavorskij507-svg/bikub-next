@extends('public.layouts.app')

@section('content')
    <article>
        <header class="content-hero">
            <p class="eyebrow">{{ ucfirst($page->type) }} page</p>
            <h1>{{ $page->title }}</h1>
            @if ($page->subtitle)<p class="subtitle">{{ $page->subtitle }}</p>@endif
            @if ($page->published_at)<p class="published">Published {{ $page->published_at->toFormattedDateString() }}</p>@endif
        </header>
        @if ($page->body)<div class="content-body">{{ $page->body }}</div>@endif
    </article>
@endsection
