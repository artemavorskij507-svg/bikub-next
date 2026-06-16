@extends('worker.layout')

@section('title', $order->order_number)

@section('content')
@php
    $completionProof = $order->completionProofs->first();
    $intake = $order->metadata['intake'] ?? [];
    $pickup  = $intake['pickup_address']      ?? $intake['vehicle_location']  ?? $intake['task_location']   ?? null;
    $dropoff = $intake['dropoff_address']     ?? $intake['destination_address'] ?? null;
    $statusValue = str($order->status->value)->replace('_', ' ')->title();
@endphp

{{-- Page header --}}
<div class="worker-page-head">
    <div>
        <p class="worker-hero-eyebrow">Assignment detail</p>
        <h1>{{ $order->order_number }}</h1>
        <p class="muted">{{ $order->scenario?->title ?? $order->service_scenario_key }}</p>
    </div>
    <div class="actions">
        <span class="worker-status-pill">{{ $statusValue }}</span>
        <a class="worker-btn" href="{{ route('worker.orders.index') }}">← Assignments</a>
    </div>
</div>

{{-- Next action — most prominent --}}
<section class="worker-card" style="border-color:rgba(52,230,154,.32);background:linear-gradient(145deg,rgba(12,40,30,.9),rgba(5,20,14,.9));margin-bottom:1rem">
    <p class="worker-hero-eyebrow" style="margin-bottom:.5rem">Next action</p>
    @if($nextAction)
        <form method="post" action="{{ route('worker.orders.'.$nextAction['key'], $order) }}">
            @csrf
            <button class="btn primary" style="width:100%;font-size:1rem;min-height:3rem">
                {{ $nextAction['label'] }}
            </button>
        </form>
        <p class="muted" style="margin-top:.55rem;font-size:.78rem">
            Only the next valid delivery step is shown. Every action is recorded in the order and dispatch audit trail.
        </p>
    @else
        <p class="muted">No further worker action is available for this order.</p>
    @endif
</section>

<div class="grid cards">

    {{-- Order info --}}
    <section class="worker-card">
        <h2 style="margin:0 0 .85rem;font-size:1rem">Order info</h2>
        <div class="kv"><span>Customer</span><strong>{{ $order->customer_name }}</strong></div>
        <div class="kv"><span>Contact</span><strong>{{ $order->customer_phone ?: $order->customer_email }}</strong></div>
        <div class="kv"><span>Quote</span><strong>{{ $order->estimated_total ? 'NOK '.$order->estimated_total : 'Manual review' }}</strong></div>
        <div class="kv"><span>Payment</span><strong>{{ str($order->payment_status->value)->replace('_', ' ')->title() }}</strong></div>
        <div class="kv" style="border:none"><span>Status</span><strong>{{ $statusValue }}</strong></div>
    </section>

    {{-- Intake fields --}}
    <section class="worker-card">
        <h2 style="margin:0 0 .85rem;font-size:1rem">Intake details</h2>
        @forelse($intake as $key => $value)
            <div class="kv" style="{{ $loop->last ? 'border:none' : '' }}">
                <span>{{ str($key)->replace('_', ' ')->title() }}</span>
                <strong>
                    @if(is_bool($value))
                        {{ $value ? 'Yes' : 'No' }}
                    @elseif(is_array($value))
                        {{ json_encode($value) }}
                    @else
                        {{ $value }}
                    @endif
                </strong>
            </div>
        @empty
            <p class="muted">No intake data captured.</p>
        @endforelse
    </section>

</div>

{{-- Worker timeline --}}
<section class="worker-card" style="margin-top:1rem">
    <h2 style="margin:0 0 .85rem;font-size:1rem">Worker timeline</h2>
    @php $workerEvents = $order->events->filter(fn($e) => str_starts_with($e->event_type, 'worker.')); @endphp
    @forelse($workerEvents as $event)
        <div class="kv" style="{{ $loop->last ? 'border:none' : '' }}">
            <span>{{ str($event->event_type)->replace(['worker.', '_'], ['', ' '])->title() }}</span>
            <strong>{{ $event->created_at?->format('Y-m-d H:i') }}</strong>
        </div>
    @empty
        <p class="muted">No worker progress recorded yet.</p>
    @endforelse
</section>

{{-- Completion proof --}}
<section class="worker-card" style="margin-top:1rem">
    <h2 style="margin:0 0 .85rem;font-size:1rem">Completion proof</h2>

    @if($completionProof)
        <div class="kv"><span>Status</span><strong>{{ str($completionProof->status)->replace('_', ' ')->title() }}</strong></div>
        <div class="kv" style="border:none"><span>Submitted</span><strong>{{ $completionProof->submitted_at?->format('Y-m-d H:i') }}</strong></div>
        @if($completionProof->worker_note)
            <p style="margin-top:.75rem;padding:.75rem;border-radius:9px;background:rgba(148,163,184,.08)">{{ $completionProof->worker_note }}</p>
        @endif
        <p class="muted" style="margin-top:.6rem;font-size:.8rem">
            @if($completionProof->status === 'submitted')
                Waiting for customer confirmation. A duplicate proof cannot be submitted.
            @elseif($completionProof->status === 'accepted')
                Customer accepted the completion proof. Payment and payout remain separate processes.
            @else
                Customer disputed the completion proof. Support review is required.
            @endif
        </p>

    @elseif($order->status->value === 'in_progress')
        <form method="post" action="{{ route('worker.orders.completion-proof.submit', $order) }}">
            @csrf
            <label for="worker-note">
                Completion note
                <textarea
                    id="worker-note"
                    name="worker_note"
                    required
                    maxlength="5000"
                    rows="4"
                    placeholder="Describe the real completed work and handover."
                >{{ old('worker_note') }}</textarea>
            </label>
            <button
                class="btn primary"
                style="width:100%;margin-top:.5rem"
                onclick="return confirm('Submit this completion proof for customer review?')"
            >
                Submit completion proof
            </button>
        </form>
        <p class="muted" style="margin-top:.6rem;font-size:.78rem">
            Text proof is persisted and audited. Photo proof is unavailable until a customer-safe media policy is implemented.
        </p>

    @else
        <p class="muted">Completion proof is available only while the assigned order is in progress.</p>
    @endif
</section>

{{-- External navigation --}}
<section class="worker-card" style="margin-top:1rem">
    <h2 style="margin:0 0 .5rem;font-size:1rem">External navigation</h2>
    <p class="muted" style="font-size:.8rem;margin-bottom:.85rem">
        Opens the selected navigation app using captured addresses. BiKuBe does not claim route optimisation.
    </p>

    <label for="navigation-app" style="margin-bottom:.75rem">
        Navigation app
        <select id="navigation-app" class="btn" style="width:100%;margin-top:.35rem">
            <option value="https://www.google.com/maps/search/?api=1&query=">Google Maps</option>
            <option value="https://www.waze.com/ul?q=">Waze</option>
            <option value="https://wego.here.com/directions/mix/">HERE WeGo</option>
            <option value="https://maps.apple.com/?q=">Apple Maps</option>
            <option value="https://yandex.com/maps/?text=">Yandex Maps</option>
            <option value="https://2gis.com/search/">2GIS</option>
        </select>
    </label>

    <div class="actions">
        @if($pickup)
            <a id="navigate-pickup" class="worker-btn" target="_blank" rel="noopener"
               data-address="{{ $pickup }}" href="#">
                Navigate to pickup
            </a>
        @else
            <span class="worker-btn" aria-disabled="true" style="opacity:.5;cursor:not-allowed">
                Pickup: no address captured
            </span>
        @endif

        @if($dropoff)
            <a id="navigate-dropoff" class="worker-btn" target="_blank" rel="noopener"
               data-address="{{ $dropoff }}" href="#">
                Navigate to dropoff
            </a>
        @else
            <span class="worker-btn" aria-disabled="true" style="opacity:.5;cursor:not-allowed">
                Dropoff: no address captured
            </span>
        @endif
    </div>
</section>

{{-- Real GPS --}}
<section class="worker-card" style="margin-top:1rem;margin-bottom:2rem">
    <h2 style="margin:0 0 .85rem;font-size:1rem">Real GPS ping</h2>

    <div class="kv"><span>Presence</span><strong>{{ str(auth()->user()->workerAvailability?->status ?? 'offline')->title() }}</strong></div>
    <div class="kv"><span>Secure context</span><strong id="secure-context-state">Checking…</strong></div>
    <div class="kv" style="border:none"><span>Permission state</span><strong id="permission-state">Not requested</strong></div>

    <p class="muted" style="margin:.75rem 0;font-size:.8rem">
        Requires phone/browser location permission. HTTPS may be required.
        No fake or fallback coordinates are used. Accuracy above 5 000 m is rejected.
    </p>

    <button id="send-location" class="btn primary" style="width:100%">Send real GPS ping now</button>

    <div id="location-result" class="muted" style="margin-top:.75rem;font-size:.82rem;min-height:1.4rem">
        {{ $lastPing
            ? 'Last real ping: '.$lastPing->captured_at?->diffForHumans().' · accuracy '.$lastPing->accuracy_meters.' m'
            : 'No location ping recorded for this order.' }}
    </div>
</section>
@endsection

@push('scripts')
<script>
(function () {
    // Navigation app selector
    const nav = document.getElementById('navigation-app');
    function updateNavLinks() {
        ['pickup', 'dropoff'].forEach(function (type) {
            const link = document.getElementById('navigate-' + type);
            if (link) link.href = nav.value + encodeURIComponent(link.dataset.address);
        });
    }
    if (nav) { nav.addEventListener('change', updateNavLinks); updateNavLinks(); }

    // Secure context & permission state display
    const secureEl     = document.getElementById('secure-context-state');
    const permissionEl = document.getElementById('permission-state');
    if (secureEl) {
        secureEl.textContent = window.isSecureContext
            ? 'Yes'
            : 'No — GPS may be blocked. Use HTTPS / ngrok tunnel / staging.';
    }
    if (permissionEl && navigator.permissions) {
        navigator.permissions.query({ name: 'geolocation' }).then(function (s) {
            permissionEl.textContent = s.state;
            s.onchange = function () { permissionEl.textContent = s.state; };
        }).catch(function () {});
    }

    // GPS ping
    document.getElementById('send-location')?.addEventListener('click', function () {
        const out = document.getElementById('location-result');

        if (!window.isSecureContext) {
            out.textContent = 'HTTPS / secure context is required for mobile browser geolocation.';
            return;
        }
        if (!navigator.geolocation) {
            out.textContent = 'Browser geolocation is unavailable. Use a supported phone browser over HTTPS.';
            return;
        }

        out.textContent = 'Requesting precise location permission…';

        navigator.geolocation.getCurrentPosition(
            async function (position) {
                try {
                    const response = await fetch(@json(route('worker.location-pings.store')), {
                        method: 'POST',
                        headers: {
                            'Content-Type':  'application/json',
                            'X-CSRF-TOKEN':  document.querySelector('meta[name=csrf-token]').content,
                            'Accept':        'application/json',
                        },
                        body: JSON.stringify({
                            order_id:        {{ $order->id }},
                            latitude:        position.coords.latitude,
                            longitude:       position.coords.longitude,
                            accuracy_meters: position.coords.accuracy,
                            heading:         position.coords.heading,
                            speed_mps:       position.coords.speed,
                            captured_at:     new Date(position.timestamp).toISOString(),
                            consent:         true,
                        }),
                    });
                    const data = await response.json();
                    if (response.ok) {
                        out.textContent = 'Real GPS ping recorded. Lat ' + position.coords.latitude.toFixed(5)
                            + ', lng ' + position.coords.longitude.toFixed(5)
                            + ', accuracy ' + data.accuracy_meters + ' m'
                            + ', captured ' + data.captured_at + '.';
                    } else {
                        out.textContent = Object.values(data.errors || { error: data.message || 'Server rejected the location ping.' }).flat().join(' ');
                    }
                } catch (err) {
                    out.textContent = 'Location was not sent because the server request failed.';
                }
            },
            function (err) {
                if (err.code === 1) {
                    out.textContent = 'Location permission denied. Allow precise location and try again.';
                } else if (err.code === 3) {
                    out.textContent = 'Location request timed out. Move outdoors and try again.';
                } else {
                    out.textContent = 'Location was not sent: ' + err.message;
                }
            },
            { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 }
        );
    });
})();
</script>
@endpush
