@extends('worker.layout')

@section('title', 'Cockpit')

@section('content')
@php
    $workerName = trim((string) (auth()->user()?->name ?? 'Worker'));
    $firstName = preg_split('/\s+/', $workerName)[0] ?: 'Worker';
@endphp

<section class="worker-lock-stage" aria-labelledby="worker-lock-title">
    <div class="worker-lock-map" aria-hidden="true">
        <span class="worker-lock-gps">GPS inactive</span>
        <span class="worker-lock-city">Narvik</span>
        <span class="worker-lock-node worker-lock-node-a"></span>
        <span class="worker-lock-node worker-lock-node-b"></span>
        <span class="worker-lock-route worker-lock-route-a"></span>
        <span class="worker-lock-route worker-lock-route-b"></span>
    </div>

    <aside class="worker-lock-panel">
        <div class="worker-lock-panel-head">
            <div>
                <p class="worker-lock-kicker">BiKuBe Worker Cockpit</p>
                <h1 id="worker-lock-title">Hei, {{ $firstName }}</h1>
            </div>
            <span class="worker-lock-offline"><span></span> Offline</span>
        </div>

        <div class="worker-lock-card worker-lock-card-main">
            <span class="worker-lock-status-dot" aria-hidden="true"></span>
            <h2>Worker profile is not approved yet.</h2>
            <p>
                Admin approval is required before online status, assignments, or location sharing can be enabled.
            </p>
        </div>

        <button type="button" class="worker-lock-slider" disabled>
            <span class="worker-lock-slider-knob">→</span>
            <span>Approval required before going online</span>
        </button>

        <p class="worker-lock-note">
            Online mode starts only after an approved worker profile and a real browser GPS permission signal.
            No fake presence, assignment, or map marker is created here.
        </p>

        <div class="worker-lock-readiness">
            <article>
                <span>Profile</span>
                <strong>Waiting for admin approval</strong>
            </article>
            <article>
                <span>GPS</span>
                <strong>Inactive until approved</strong>
            </article>
            <article>
                <span>Assignments</span>
                <strong>Blocked until profile approval</strong>
            </article>
        </div>

        <div class="worker-lock-actions">
            @if(\Illuminate\Support\Facades\Route::has('public.workers.apply'))
                <a href="{{ route('public.workers.apply') }}">Worker application</a>
            @endif
            @if(\Illuminate\Support\Facades\Route::has('account.dashboard'))
                <a href="{{ route('account.dashboard') }}">Account</a>
            @endif
        </div>
    </aside>
</section>

<style>
    .worker-lock-stage {
        position: relative;
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(360px, 500px);
        min-height: calc(100vh - 128px);
        overflow: hidden;
        border: 1px solid rgba(148, 163, 184, .16);
        border-radius: 28px;
        background:
            radial-gradient(circle at 72% 18%, rgba(37, 211, 102, .18), transparent 30%),
            radial-gradient(circle at 24% 78%, rgba(34, 211, 238, .14), transparent 34%),
            #07111f;
        box-shadow: 0 28px 90px rgba(0, 0, 0, .36);
    }

    .worker-lock-map {
        position: relative;
        min-height: 620px;
        background:
            linear-gradient(115deg, rgba(5, 12, 22, .22), rgba(5, 12, 22, .9)),
            repeating-linear-gradient(26deg, rgba(148, 163, 184, .12) 0 1px, transparent 1px 82px),
            repeating-linear-gradient(116deg, rgba(148, 163, 184, .10) 0 1px, transparent 1px 78px),
            radial-gradient(circle at 52% 48%, rgba(148, 163, 184, .12), transparent 12%),
            #050a12;
    }

    .worker-lock-map::before {
        content: "";
        position: absolute;
        inset: 0;
        background:
            linear-gradient(90deg, transparent 0 18%, rgba(255, 255, 255, .08) 18.2% 18.5%, transparent 18.8%),
            linear-gradient(22deg, transparent 0 35%, rgba(255, 255, 255, .06) 35.2% 35.6%, transparent 35.9%),
            linear-gradient(150deg, transparent 0 42%, rgba(255, 255, 255, .07) 42.2% 42.6%, transparent 43%),
            radial-gradient(ellipse at 46% 72%, rgba(255, 255, 255, .16), transparent 20%);
        opacity: .52;
        mix-blend-mode: screen;
    }

    .worker-lock-gps {
        position: absolute;
        top: 28px;
        left: 28px;
        z-index: 2;
        border-radius: 14px;
        background: rgba(15, 23, 42, .92);
        padding: 13px 18px;
        color: #f8fafc;
        font-size: 13px;
        font-weight: 900;
        letter-spacing: .03em;
        text-transform: uppercase;
        box-shadow: 0 18px 50px rgba(0, 0, 0, .34);
    }

    .worker-lock-city {
        position: absolute;
        left: 44%;
        top: 48%;
        color: rgba(226, 232, 240, .42);
        font-size: clamp(32px, 5vw, 72px);
        font-weight: 950;
        letter-spacing: .04em;
        text-transform: uppercase;
        text-shadow: 0 0 30px rgba(0, 0, 0, .7);
    }

    .worker-lock-node,
    .worker-lock-route {
        position: absolute;
        display: block;
        pointer-events: none;
    }

    .worker-lock-node {
        width: 14px;
        height: 14px;
        border-radius: 999px;
        background: #22c55e;
        box-shadow: 0 0 0 12px rgba(34, 197, 94, .12), 0 0 32px rgba(34, 197, 94, .42);
        opacity: .34;
    }

    .worker-lock-node-a { left: 31%; top: 36%; }
    .worker-lock-node-b { left: 62%; top: 62%; background: #22d3ee; box-shadow: 0 0 0 12px rgba(34, 211, 238, .10), 0 0 32px rgba(34, 211, 238, .36); }

    .worker-lock-route {
        height: 2px;
        border-radius: 999px;
        background: linear-gradient(90deg, transparent, rgba(34, 211, 238, .50), transparent);
        transform-origin: left center;
        opacity: .38;
    }

    .worker-lock-route-a { left: 24%; top: 42%; width: 330px; transform: rotate(18deg); }
    .worker-lock-route-b { left: 43%; top: 68%; width: 280px; transform: rotate(-28deg); }

    .worker-lock-panel {
        position: relative;
        z-index: 3;
        align-self: center;
        margin: 22px;
        border: 1px solid rgba(148, 163, 184, .18);
        border-radius: 30px;
        background: rgba(15, 23, 42, .91);
        padding: 28px;
        color: #f8fafc;
        box-shadow: 0 26px 70px rgba(0, 0, 0, .42);
        backdrop-filter: blur(22px);
    }

    .worker-lock-panel-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 18px;
        margin-bottom: 28px;
    }

    .worker-lock-kicker {
        margin: 0 0 8px;
        color: #94a3b8;
        font-size: 12px;
        font-weight: 950;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .worker-lock-panel h1 {
        margin: 0;
        color: #fff;
        font-size: clamp(32px, 4vw, 44px);
        line-height: 1.02;
        letter-spacing: 0;
    }

    .worker-lock-offline {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border: 1px solid rgba(244, 63, 94, .42);
        border-radius: 999px;
        background: rgba(127, 29, 29, .32);
        padding: 9px 14px;
        color: #fb7185;
        font-size: 12px;
        font-weight: 950;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .worker-lock-offline span {
        width: 8px;
        height: 8px;
        border-radius: 999px;
        background: #fb7185;
        box-shadow: 0 0 18px rgba(251, 113, 133, .75);
    }

    .worker-lock-card {
        border: 1px solid rgba(148, 163, 184, .18);
        border-radius: 24px;
        background: rgba(30, 41, 59, .72);
        padding: 24px;
    }

    .worker-lock-card-main {
        text-align: center;
    }

    .worker-lock-status-dot {
        display: inline-flex;
        width: 58px;
        height: 58px;
        margin-bottom: 18px;
        border-radius: 20px;
        background:
            radial-gradient(circle, rgba(251, 191, 36, .95) 0 5px, transparent 6px),
            rgba(15, 23, 42, .72);
        box-shadow: inset 0 0 0 1px rgba(251, 191, 36, .16);
    }

    .worker-lock-card h2 {
        margin: 0 0 10px;
        color: #fff;
        font-size: 25px;
        line-height: 1.12;
    }

    .worker-lock-card p,
    .worker-lock-note {
        margin: 0;
        color: #cbd5e1;
        font-size: 16px;
        line-height: 1.55;
    }

    .worker-lock-slider {
        display: flex;
        width: 100%;
        align-items: center;
        gap: 18px;
        margin: 20px 0;
        border: 1px solid rgba(148, 163, 184, .24);
        border-radius: 999px;
        background: rgba(2, 6, 23, .52);
        padding: 10px 18px 10px 10px;
        color: #e2e8f0;
        font-size: 15px;
        font-weight: 900;
        opacity: 1;
    }

    .worker-lock-slider-knob {
        display: grid;
        width: 58px;
        height: 58px;
        flex: 0 0 58px;
        place-items: center;
        border-radius: 999px;
        background: #f8fafc;
        color: #0f172a;
        font-size: 24px;
        box-shadow: 0 12px 30px rgba(0, 0, 0, .32);
    }

    .worker-lock-readiness {
        display: grid;
        gap: 12px;
        margin-top: 22px;
    }

    .worker-lock-readiness article {
        border: 1px solid rgba(148, 163, 184, .14);
        border-radius: 18px;
        background: rgba(15, 23, 42, .72);
        padding: 16px;
    }

    .worker-lock-readiness span {
        display: block;
        margin-bottom: 4px;
        color: #94a3b8;
        font-size: 12px;
        font-weight: 900;
        letter-spacing: .06em;
        text-transform: uppercase;
    }

    .worker-lock-readiness strong {
        color: #f8fafc;
        font-size: 15px;
    }

    .worker-lock-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin-top: 22px;
    }

    .worker-lock-actions a {
        border: 1px solid rgba(45, 212, 191, .24);
        border-radius: 14px;
        background: rgba(20, 184, 166, .12);
        padding: 12px 14px;
        color: #ccfbf1;
        font-weight: 900;
        text-decoration: none;
    }

    @media (max-width: 980px) {
        .worker-lock-stage {
            grid-template-columns: 1fr;
        }

        .worker-lock-map {
            min-height: 360px;
        }

        .worker-lock-panel {
            align-self: auto;
        }
    }

    @media (max-width: 640px) {
        .worker-lock-stage {
            border-radius: 20px;
        }

        .worker-lock-panel {
            margin: 12px;
            padding: 20px;
            border-radius: 24px;
        }

        .worker-lock-panel-head {
            flex-direction: column;
        }
    }
</style>
@endsection
