@extends('public.layouts.app')

@section('content')
    <article>
        <header class="content-hero">
            <p class="eyebrow">{{ $scenario->category?->title ?? $scenario->service_type }}</p>
            <h1>{{ $scenario->title }}</h1>
            @if ($scenario->subtitle)<p class="subtitle">{{ $scenario->subtitle }}</p>@endif
            @if ($scenario->description)<p class="subtitle">{{ $scenario->description }}</p>@endif
        </header>

        <div class="content-body">
            @if ($scenario->base_price !== null)<p><strong>Base price:</strong> {{ $scenario->base_price }} {{ $scenario->currency }}</p>@endif
            <p><strong>Service status:</strong> Active</p>
            <p>
                <strong>Capabilities:</strong>
                {{ collect([
                    $scenario->supports_scheduling ? 'Scheduling' : null,
                    $scenario->supports_live_tracking ? 'Live tracking' : null,
                    $scenario->requires_worker ? 'Worker execution' : null,
                    $scenario->requires_partner ? 'Partner fulfilment' : null,
                ])->filter()->join(', ') ?: 'Service definition only' }}
            </p>
            <a class="public-action" href="{{ route('public.orders.request', $scenario->slug) }}">Request service</a>
        </div>
    </article>
@endsection
