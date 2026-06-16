@extends('public.layouts.app')

@section('content')
<article class="bkb-delivery-page">
    <section class="bkb-delivery-hero">
        <div>
            <p class="eyebrow">{{ __('bikube.delivery.category_eyebrow') }}</p>
            <h1>{{ __('bikube.delivery.category_title') }}</h1>
            <p class="subtitle">{{ __('bikube.delivery.category_subtitle') }}</p>
            <div class="bkb-delivery-actions">
                @if ($scenario)
                    <a class="bkb-delivery-primary" href="{{ route('public.orders.request', $scenario->slug) }}">
                        {{ __('bikube.delivery.open_request') }}
                    </a>
                @else
                    <button type="button" class="bkb-delivery-primary" disabled title="{{ __('bikube.delivery.scenario_missing') }}">
                        {{ __('bikube.delivery.request_disabled') }}
                    </button>
                @endif
                <a class="bkb-delivery-secondary" href="{{ route('public.workers.apply') }}">
                    {{ __('bikube.delivery.become_worker') }}
                </a>
            </div>
        </div>
        <div class="bkb-delivery-visual" aria-hidden="true">
            <span></span><span></span><span></span><span></span>
        </div>
    </section>

    <section class="bkb-delivery-grid" aria-label="{{ __('bikube.delivery.readiness_label') }}">
        <article>
            <span>{{ __('bikube.delivery.step_request') }}</span>
            <strong>{{ $scenario ? __('bikube.status.ready') : __('bikube.status.blocked') }}</strong>
            <p>{{ $scenario ? __('bikube.delivery.request_ready') : __('bikube.delivery.scenario_missing') }}</p>
        </article>
        <article>
            <span>{{ __('bikube.delivery.step_payment') }}</span>
            <strong>{{ __('bikube.status.blocked') }}</strong>
            <p>{{ __('bikube.delivery.payment_blocker') }}</p>
        </article>
        <article>
            <span>{{ __('bikube.delivery.step_dispatch') }}</span>
            <strong>{{ __('bikube.status.review') }}</strong>
            <p>{{ __('bikube.delivery.dispatch_note') }}</p>
        </article>
        <article>
            <span>{{ __('bikube.delivery.step_tracking') }}</span>
            <strong>{{ __('bikube.status.blocked') }}</strong>
            <p>{{ __('bikube.delivery.tracking_blocker') }}</p>
        </article>
    </section>
</article>

<style>
.bkb-delivery-page{background:#06101e;color:#eef7ff;margin:-2rem -1rem 0;padding:2rem 1rem 4rem;min-height:calc(100vh - 6rem)}
.bkb-delivery-hero{position:relative;overflow:hidden;display:grid;grid-template-columns:minmax(0,1.05fr) minmax(20rem,.8fr);gap:2rem;align-items:center;max-width:1180px;margin:0 auto;padding:3.5rem 0}
.bkb-delivery-hero:before{position:absolute;inset:0;background:radial-gradient(circle at 78% 26%,rgba(35,225,151,.2),transparent 28%),radial-gradient(circle at 30% 8%,rgba(56,217,255,.16),transparent 32%);content:"";pointer-events:none}
.bkb-delivery-hero>*{position:relative}
.bkb-delivery-hero .eyebrow{color:#49e6a3;font-size:.78rem;font-weight:950;letter-spacing:.08em;text-transform:uppercase}
.bkb-delivery-hero h1{max-width:760px;margin:.55rem 0 0;font-size:clamp(2.4rem,7vw,5.6rem);line-height:.95;font-weight:950;letter-spacing:0}
.bkb-delivery-hero .subtitle{max-width:700px;margin:1.15rem 0 0;color:#b8c8db;font-size:clamp(1rem,2.1vw,1.45rem);line-height:1.55}
.bkb-delivery-actions{display:flex;flex-wrap:wrap;gap:.75rem;margin-top:1.6rem}
.bkb-delivery-primary,.bkb-delivery-secondary{display:inline-flex;align-items:center;justify-content:center;min-height:3.1rem;border-radius:7px;padding:.75rem 1rem;font-weight:900;text-decoration:none}
.bkb-delivery-primary{border:1px solid rgba(47,229,157,.6);background:linear-gradient(135deg,#19b67e,#0b7657);color:#fff;box-shadow:0 18px 48px rgba(23,207,142,.2)}
.bkb-delivery-primary:disabled{cursor:not-allowed;opacity:.55}
.bkb-delivery-secondary{border:1px solid rgba(137,165,194,.28);background:rgba(10,29,48,.82);color:#dcefff}
.bkb-delivery-visual{aspect-ratio:1.1;border:1px solid rgba(137,165,194,.18);border-radius:12px;background:linear-gradient(145deg,rgba(9,26,43,.92),rgba(3,13,24,.95));box-shadow:0 30px 90px rgba(0,0,0,.35);position:relative;overflow:hidden}
.bkb-delivery-visual:before{position:absolute;inset:12%;background-image:linear-gradient(rgba(122,160,194,.12) 1px,transparent 1px),linear-gradient(90deg,rgba(122,160,194,.12) 1px,transparent 1px);background-size:38px 38px;content:"";transform:perspective(500px) rotateX(58deg);transform-origin:center bottom}
.bkb-delivery-visual span{position:absolute;width:13px;height:13px;border-radius:999px;background:#33e49d;box-shadow:0 0 0 8px rgba(51,228,157,.12),0 0 24px rgba(51,228,157,.7)}
.bkb-delivery-visual span:nth-child(1){left:22%;top:28%}.bkb-delivery-visual span:nth-child(2){left:68%;top:38%;background:#55d9ff}.bkb-delivery-visual span:nth-child(3){left:42%;top:70%}.bkb-delivery-visual span:nth-child(4){left:78%;top:72%;background:#f5bd54}
.bkb-delivery-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:1rem;max-width:1180px;margin:0 auto}
.bkb-delivery-grid article{border:1px solid rgba(137,165,194,.18);border-radius:8px;background:rgba(8,24,40,.86);padding:1rem;min-height:10rem}
.bkb-delivery-grid span{color:#7f96b1;font-size:.72rem;font-weight:950;text-transform:uppercase}
.bkb-delivery-grid strong{display:block;margin-top:.5rem;color:#f2fbff;font-size:1.2rem}
.bkb-delivery-grid p{margin-top:.5rem;color:#a9bad0;line-height:1.5}
@media(max-width:850px){.bkb-delivery-hero,.bkb-delivery-grid{grid-template-columns:1fr}.bkb-delivery-visual{min-height:18rem}}
</style>
@endsection
