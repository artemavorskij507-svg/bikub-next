@extends('public.layouts.app')

@section('content')
<article class="bkb-delivery-page">

    {{-- HERO --}}
    <section class="bkb-delivery-hero">
        <div class="bkb-delivery-hero__copy">
            <p class="eyebrow">{{ __('bikube.public.delivery.eyebrow') }}</p>
            <h1>{{ __('bikube.public.delivery.hero_title') }}</h1>
            <p class="subtitle">{{ __('bikube.public.delivery.hero_subtitle') }}</p>
            <div class="bkb-delivery-actions">
                @if ($scenario)
                    <a class="bkb-btn bkb-btn--primary" href="{{ route('public.orders.request', $scenario->slug) }}">
                        {{ __('bikube.public.delivery.cta_start') }}
                    </a>
                @else
                    <button type="button" class="bkb-btn bkb-btn--primary" disabled>
                        {{ __('bikube.public.delivery.cta_unavailable') }}
                    </button>
                @endif
                <a class="bkb-btn bkb-btn--secondary" href="{{ route('account.orders.index') }}">
                    {{ __('bikube.public.delivery.cta_track') }}
                </a>
            </div>
            <p class="bkb-delivery-honesty">
                {{ __('bikube.public.delivery.payment_honest') }}
            </p>
        </div>
        <div class="bkb-delivery-hero__visual" aria-hidden="true">
            <span class="bkb-pin bkb-pin--green" style="left:22%;top:28%"></span>
            <span class="bkb-pin bkb-pin--blue"  style="left:68%;top:38%"></span>
            <span class="bkb-pin bkb-pin--green" style="left:42%;top:70%"></span>
            <span class="bkb-pin bkb-pin--amber" style="left:78%;top:72%"></span>
            <svg class="bkb-route" viewBox="0 0 300 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M66 56 Q180 80 204 76 Q240 70 126 140 Q100 155 234 144" stroke="#33e49d" stroke-width="1.5" stroke-dasharray="6 4" fill="none" opacity="0.5"/>
            </svg>
        </div>
    </section>

    {{-- HOW IT WORKS --}}
    <section class="bkb-section" id="how-it-works">
        <div class="bkb-section__head">
            <p class="eyebrow">{{ __('bikube.public.delivery.how_eyebrow') }}</p>
            <h2>{{ __('bikube.public.delivery.how_title') }}</h2>
        </div>
        <div class="bkb-steps">
            <div class="bkb-step">
                <div class="bkb-step__num">1</div>
                <h3>{{ __('bikube.public.delivery.step1_title') }}</h3>
                <p>{{ __('bikube.public.delivery.step1_body') }}</p>
            </div>
            <div class="bkb-step">
                <div class="bkb-step__num">2</div>
                <h3>{{ __('bikube.public.delivery.step2_title') }}</h3>
                <p>{{ __('bikube.public.delivery.step2_body') }}</p>
            </div>
            <div class="bkb-step">
                <div class="bkb-step__num">3</div>
                <h3>{{ __('bikube.public.delivery.step3_title') }}</h3>
                <p>{{ __('bikube.public.delivery.step3_body') }}</p>
            </div>
            <div class="bkb-step">
                <div class="bkb-step__num">4</div>
                <h3>{{ __('bikube.public.delivery.step4_title') }}</h3>
                <p>{{ __('bikube.public.delivery.step4_body') }}</p>
            </div>
        </div>
    </section>

    {{-- SERVICE CARDS --}}
    <section class="bkb-section">
        <div class="bkb-section__head">
            <p class="eyebrow">{{ __('bikube.public.delivery.services_eyebrow') }}</p>
            <h2>{{ __('bikube.public.delivery.services_title') }}</h2>
        </div>
        <div class="bkb-service-cards">
            <div class="bkb-service-card">
                <div class="bkb-service-card__icon">🛒</div>
                <h3>{{ __('bikube.public.delivery.svc_grocery_title') }}</h3>
                <p>{{ __('bikube.public.delivery.svc_grocery_body') }}</p>
                @if ($scenario)
                    <a class="bkb-service-card__link" href="{{ route('public.orders.request', $scenario->slug) }}">
                        {{ __('bikube.public.delivery.svc_request') }} →
                    </a>
                @endif
            </div>
            <div class="bkb-service-card">
                <div class="bkb-service-card__icon">🍱</div>
                <h3>{{ __('bikube.public.delivery.svc_meal_title') }}</h3>
                <p>{{ __('bikube.public.delivery.svc_meal_body') }}</p>
                @if ($scenario)
                    <a class="bkb-service-card__link" href="{{ route('public.orders.request', $scenario->slug) }}">
                        {{ __('bikube.public.delivery.svc_request') }} →
                    </a>
                @endif
            </div>
            <div class="bkb-service-card">
                <div class="bkb-service-card__icon">📦</div>
                <h3>{{ __('bikube.public.delivery.svc_bulky_title') }}</h3>
                <p>{{ __('bikube.public.delivery.svc_bulky_body') }}</p>
                @if ($scenario)
                    <a class="bkb-service-card__link" href="{{ route('public.orders.request', $scenario->slug) }}">
                        {{ __('bikube.public.delivery.svc_request') }} →
                    </a>
                @endif
            </div>
        </div>
    </section>

    {{-- COVERAGE --}}
    <section class="bkb-section bkb-coverage">
        <div class="bkb-section__head">
            <p class="eyebrow">{{ __('bikube.public.delivery.coverage_eyebrow') }}</p>
            <h2>{{ __('bikube.public.delivery.coverage_title') }}</h2>
            <p class="bkb-coverage__text">{{ __('bikube.public.delivery.coverage_body') }}</p>
        </div>
        <div class="bkb-zone-badges">
            <span class="bkb-zone-badge bkb-zone-badge--active">📍 Narvik</span>
            <span class="bkb-zone-badge bkb-zone-badge--active">📍 Ballangen</span>
        </div>
    </section>

    {{-- READINESS BLOCK --}}
    <section class="bkb-section bkb-readiness" id="readiness">
        <div class="bkb-section__head">
            <p class="eyebrow">{{ __('bikube.public.delivery.readiness_eyebrow') }}</p>
            <h2>{{ __('bikube.public.delivery.readiness_title') }}</h2>
        </div>
        <div class="bkb-readiness-grid">
            <div class="bkb-readiness-item bkb-readiness-item--ready">
                <span class="bkb-readiness-item__label">{{ __('bikube.public.delivery.readiness_request_label') }}</span>
                <strong class="bkb-readiness-item__status">{{ __('bikube.status.ready') }}</strong>
                <p>{{ $scenario ? __('bikube.public.delivery.readiness_request_ready') : __('bikube.public.delivery.readiness_request_missing') }}</p>
            </div>
            <div class="bkb-readiness-item bkb-readiness-item--blocked">
                <span class="bkb-readiness-item__label">{{ __('bikube.public.delivery.readiness_payment_label') }}</span>
                <strong class="bkb-readiness-item__status">{{ __('bikube.status.blocked') }}</strong>
                <p>{{ __('bikube.public.delivery.readiness_payment_body') }}</p>
            </div>
            <div class="bkb-readiness-item bkb-readiness-item--review">
                <span class="bkb-readiness-item__label">{{ __('bikube.public.delivery.readiness_dispatch_label') }}</span>
                <strong class="bkb-readiness-item__status">{{ __('bikube.status.review') }}</strong>
                <p>{{ __('bikube.public.delivery.readiness_dispatch_body') }}</p>
            </div>
            <div class="bkb-readiness-item bkb-readiness-item--blocked">
                <span class="bkb-readiness-item__label">{{ __('bikube.public.delivery.readiness_gps_label') }}</span>
                <strong class="bkb-readiness-item__status">{{ __('bikube.status.blocked') }}</strong>
                <p>{{ __('bikube.public.delivery.readiness_gps_body') }}</p>
            </div>
        </div>
    </section>

    {{-- BOTTOM CTA --}}
    <section class="bkb-section bkb-cta-section">
        <div class="bkb-cta-block">
            <h2>{{ __('bikube.public.delivery.bottom_cta_title') }}</h2>
            <p>{{ __('bikube.public.delivery.bottom_cta_body') }}</p>
            <div class="bkb-delivery-actions">
                @if ($scenario)
                    <a class="bkb-btn bkb-btn--primary" href="{{ route('public.orders.request', $scenario->slug) }}">
                        {{ __('bikube.public.delivery.cta_start') }}
                    </a>
                @else
                    <button type="button" class="bkb-btn bkb-btn--primary" disabled>
                        {{ __('bikube.public.delivery.cta_unavailable') }}
                    </button>
                @endif
                <a class="bkb-btn bkb-btn--secondary" href="{{ route('account.orders.index') }}">
                    {{ __('bikube.public.delivery.cta_track') }}
                </a>
            </div>
        </div>
    </section>

    {{-- FOOTER NOTE --}}
    <div class="bkb-delivery-footer-note">
        BiKuBe · Narvik operations · {{ __('bikube.public.delivery.footer_note') }}
    </div>

</article>

<style>
/* === BiKuBe Delivery Landing — premium dark OS === */
.bkb-delivery-page {
    background: #040e1c;
    color: #eef7ff;
    margin: -2rem -1rem 0;
    padding: 0 0 4rem;
    min-height: calc(100vh - 6rem);
    font-family: Inter, ui-sans-serif, system-ui, sans-serif;
}

/* Hero */
.bkb-delivery-hero {
    position: relative;
    overflow: hidden;
    display: grid;
    grid-template-columns: minmax(0, 1.1fr) minmax(0, 0.85fr);
    gap: 2rem;
    align-items: center;
    max-width: 1180px;
    margin: 0 auto;
    padding: 4rem 1.5rem 3rem;
}
.bkb-delivery-hero::before {
    content: "";
    position: absolute;
    inset: 0;
    background:
        radial-gradient(circle at 75% 25%, rgba(35, 225, 151, .18), transparent 30%),
        radial-gradient(circle at 25% 10%, rgba(56, 217, 255, .12), transparent 35%);
    pointer-events: none;
}
.bkb-delivery-hero__copy { position: relative; z-index: 2; }
.bkb-delivery-hero__copy h1 {
    font-size: clamp(2.4rem, 7vw, 5rem);
    line-height: 1;
    font-weight: 950;
    margin: .6rem 0 0;
    letter-spacing: -.02em;
    color: #fff;
}
.bkb-delivery-hero__copy h1 em {
    font-style: normal;
    background: linear-gradient(to right, #33e49d, #3dd9ff);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}
.subtitle { max-width: 640px; color: #b0c6db; font-size: clamp(1rem, 2vw, 1.3rem); line-height: 1.55; margin: 1.2rem 0 0; }
.bkb-delivery-actions { display: flex; flex-wrap: wrap; gap: .75rem; margin-top: 1.6rem; }
.bkb-delivery-honesty {
    margin-top: 1.2rem;
    font-size: .83rem;
    color: #7f96b1;
    background: rgba(255, 200, 60, .06);
    border: 1px solid rgba(255, 200, 60, .2);
    border-radius: 8px;
    padding: .65rem .9rem;
    max-width: 540px;
    line-height: 1.5;
}

/* Visual panel */
.bkb-delivery-hero__visual {
    position: relative;
    aspect-ratio: 1.1;
    border: 1px solid rgba(137, 165, 194, .18);
    border-radius: 16px;
    background: linear-gradient(145deg, rgba(9, 26, 43, .92), rgba(3, 13, 24, .95));
    box-shadow: 0 30px 80px rgba(0, 0, 0, .35);
    overflow: hidden;
    z-index: 1;
}
.bkb-delivery-hero__visual::before {
    content: "";
    position: absolute;
    inset: 10%;
    background-image:
        linear-gradient(rgba(122, 160, 194, .1) 1px, transparent 1px),
        linear-gradient(90deg, rgba(122, 160, 194, .1) 1px, transparent 1px);
    background-size: 40px 40px;
    transform: perspective(480px) rotateX(55deg);
    transform-origin: center bottom;
}
.bkb-route { position: absolute; inset: 0; width: 100%; height: 100%; }
.bkb-pin {
    position: absolute;
    width: 12px; height: 12px;
    border-radius: 999px;
    transform: translate(-50%, -50%);
}
.bkb-pin--green { background: #33e49d; box-shadow: 0 0 0 7px rgba(51, 228, 157, .14), 0 0 20px rgba(51, 228, 157, .7); }
.bkb-pin--blue  { background: #55d9ff; box-shadow: 0 0 0 7px rgba(85, 217, 255, .14), 0 0 20px rgba(85, 217, 255, .7); }
.bkb-pin--amber { background: #f5bd54; box-shadow: 0 0 0 7px rgba(245, 189, 84, .14),  0 0 20px rgba(245, 189, 84, .7); }

/* Buttons */
.bkb-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 3rem;
    border-radius: 8px;
    padding: .7rem 1.2rem;
    font-weight: 800;
    font-size: .9rem;
    text-decoration: none;
    border: 1px solid transparent;
    cursor: pointer;
    transition: all .2s ease;
}
.bkb-btn--primary {
    background: linear-gradient(135deg, #1ab07e, #0b7657);
    color: #fff;
    border-color: rgba(47, 229, 157, .5);
    box-shadow: 0 14px 40px rgba(23, 207, 142, .2);
}
.bkb-btn--primary:hover { filter: brightness(1.1); transform: translateY(-2px); }
.bkb-btn--primary:disabled { opacity: .5; cursor: not-allowed; transform: none; filter: none; }
.bkb-btn--secondary {
    background: rgba(10, 29, 48, .8);
    color: #dcefff;
    border-color: rgba(137, 165, 194, .28);
}
.bkb-btn--secondary:hover { border-color: rgba(51, 228, 157, .4); color: #33e49d; }

/* Sections */
.bkb-section {
    max-width: 1180px;
    margin: 0 auto;
    padding: 4rem 1.5rem;
}
.bkb-section__head { margin-bottom: 2.5rem; max-width: 740px; }
.bkb-section__head h2 {
    font-size: clamp(1.8rem, 4vw, 3rem);
    font-weight: 850;
    color: #fff;
    letter-spacing: -.02em;
    line-height: 1.1;
    margin: .4rem 0 0;
}
.eyebrow {
    color: #33e49d;
    font-size: .78rem;
    font-weight: 950;
    letter-spacing: .1em;
    text-transform: uppercase;
}

/* Steps */
.bkb-steps {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1.2rem;
}
.bkb-step {
    background: rgba(8, 24, 40, .7);
    border: 1px solid rgba(137, 165, 194, .15);
    border-radius: 14px;
    padding: 1.8rem 1.4rem;
    display: flex;
    flex-direction: column;
    gap: .6rem;
    transition: border-color .2s;
}
.bkb-step:hover { border-color: rgba(51, 228, 157, .3); }
.bkb-step__num {
    width: 40px; height: 40px;
    border-radius: 999px;
    background: rgba(51, 228, 157, .12);
    border: 1px solid rgba(51, 228, 157, .35);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    font-weight: 900;
    color: #33e49d;
    margin-bottom: .3rem;
}
.bkb-step h3 { font-size: 1rem; font-weight: 800; color: #fff; margin: 0; }
.bkb-step p  { font-size: .88rem; color: #9ab3c8; line-height: 1.5; margin: 0; }

/* Service cards */
.bkb-service-cards {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1.2rem;
}
.bkb-service-card {
    background: rgba(8, 24, 40, .7);
    border: 1px solid rgba(137, 165, 194, .15);
    border-radius: 16px;
    padding: 2rem 1.6rem;
    display: flex;
    flex-direction: column;
    gap: .6rem;
    transition: border-color .2s, transform .2s;
}
.bkb-service-card:hover { border-color: rgba(51, 228, 157, .35); transform: translateY(-4px); }
.bkb-service-card__icon { font-size: 2.2rem; line-height: 1; }
.bkb-service-card h3 { font-size: 1.15rem; font-weight: 800; color: #fff; margin: 0; }
.bkb-service-card p  { font-size: .9rem; color: #9ab3c8; line-height: 1.5; margin: 0; flex: 1; }
.bkb-service-card__link {
    margin-top: .6rem;
    color: #33e49d;
    font-size: .85rem;
    font-weight: 700;
    text-decoration: none;
    display: inline-block;
    transition: letter-spacing .15s;
}
.bkb-service-card__link:hover { letter-spacing: .02em; }

/* Coverage */
.bkb-coverage { border-top: 1px solid rgba(137, 165, 194, .1); }
.bkb-coverage__text { color: #9ab3c8; margin-top: .5rem; font-size: 1rem; line-height: 1.6; }
.bkb-zone-badges { display: flex; flex-wrap: wrap; gap: .75rem; margin-top: 1.4rem; }
.bkb-zone-badge {
    display: inline-flex;
    align-items: center;
    gap: .5rem;
    padding: .55rem 1rem;
    border-radius: 999px;
    font-weight: 700;
    font-size: .85rem;
}
.bkb-zone-badge--active {
    background: rgba(51, 228, 157, .1);
    border: 1px solid rgba(51, 228, 157, .35);
    color: #33e49d;
}

/* Readiness */
.bkb-readiness { background: rgba(5, 14, 26, .6); border-top: 1px solid rgba(137, 165, 194, .1); border-bottom: 1px solid rgba(137, 165, 194, .1); }
.bkb-readiness-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1rem;
}
.bkb-readiness-item {
    border: 1px solid rgba(137, 165, 194, .18);
    border-radius: 10px;
    background: rgba(8, 24, 40, .8);
    padding: 1.2rem;
    min-height: 9rem;
}
.bkb-readiness-item__label { color: #7f96b1; font-size: .7rem; font-weight: 950; text-transform: uppercase; display: block; }
.bkb-readiness-item__status { display: block; margin-top: .4rem; font-size: 1.1rem; color: #f2fbff; }
.bkb-readiness-item p { margin-top: .4rem; color: #8da4b5; font-size: .82rem; line-height: 1.5; }
.bkb-readiness-item--ready  .bkb-readiness-item__status { color: #33e49d; }
.bkb-readiness-item--blocked .bkb-readiness-item__status { color: #ff7b7b; }
.bkb-readiness-item--review  .bkb-readiness-item__status { color: #f5bd54; }

/* CTA section */
.bkb-cta-section { border-top: 1px solid rgba(137, 165, 194, .1); }
.bkb-cta-block { max-width: 620px; }
.bkb-cta-block h2 { font-size: clamp(1.6rem, 3.5vw, 2.4rem); font-weight: 850; color: #fff; margin: 0 0 .8rem; letter-spacing: -.02em; }
.bkb-cta-block p { color: #9ab3c8; margin: 0 0 1.6rem; font-size: 1rem; line-height: 1.6; }

/* Footer note */
.bkb-delivery-footer-note {
    max-width: 1180px;
    margin: 0 auto;
    padding: 0 1.5rem 1.5rem;
    font-size: .78rem;
    color: #4a5e6e;
}

/* Responsive */
@media (max-width: 900px) {
    .bkb-delivery-hero { grid-template-columns: 1fr; }
    .bkb-delivery-hero__visual { min-height: 16rem; }
    .bkb-steps { grid-template-columns: repeat(2, 1fr); }
    .bkb-service-cards { grid-template-columns: 1fr; }
    .bkb-readiness-grid { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 540px) {
    .bkb-steps { grid-template-columns: 1fr; }
    .bkb-readiness-grid { grid-template-columns: 1fr; }
}
</style>
@endsection
