@extends('public.layouts.app')
@section('content')
<article class="bkb-confirm-page">

    {{-- Status header --}}
    <header class="bkb-confirm-hero">
        <div class="bkb-confirm-badge">
            <span class="bkb-confirm-badge__dot"></span>
            {{ __('bikube.public.confirmation.badge') }}
        </div>
        <p class="eyebrow">{{ __('bikube.public.confirmation.eyebrow') }}</p>
        <h1>{{ $order->order_number }}</h1>
        <p class="subtitle">{{ __('bikube.public.confirmation.status_label') }}</p>
    </header>

    {{-- Payment honest blocker --}}
    <div class="bkb-confirm-blocker">
        <span class="bkb-confirm-blocker__icon">⚠️</span>
        <div>
            <strong>{{ __('bikube.public.confirmation.payment_not_connected_title') }}</strong>
            <p>{{ __('bikube.public.confirmation.payment_not_connected_body') }}</p>
        </div>
    </div>

    {{-- What happens next --}}
    <section class="bkb-confirm-next">
        <h2>{{ __('bikube.public.confirmation.next_title') }}</h2>
        <ol class="bkb-confirm-steps">
            <li>
                <span class="bkb-confirm-step__num">1</span>
                <div>
                    <strong>{{ __('bikube.public.confirmation.next_step1_title') }}</strong>
                    <p>{{ __('bikube.public.confirmation.next_step1_body') }}</p>
                </div>
            </li>
            <li>
                <span class="bkb-confirm-step__num">2</span>
                <div>
                    <strong>{{ __('bikube.public.confirmation.next_step2_title') }}</strong>
                    <p>{{ __('bikube.public.confirmation.next_step2_body') }}</p>
                </div>
            </li>
            <li>
                <span class="bkb-confirm-step__num">3</span>
                <div>
                    <strong>{{ __('bikube.public.confirmation.next_step3_title') }}</strong>
                    <p>{{ __('bikube.public.confirmation.next_step3_body') }}</p>
                </div>
            </li>
            <li>
                <span class="bkb-confirm-step__num">4</span>
                <div>
                    <strong>{{ __('bikube.public.confirmation.next_step4_title') }}</strong>
                    <p>{{ __('bikube.public.confirmation.next_step4_body') }}</p>
                </div>
            </li>
        </ol>
    </section>

    {{-- Order summary --}}
    <section class="bkb-confirm-summary">
        <h2>{{ __('bikube.public.confirmation.summary_title') }}</h2>
        <dl class="bkb-dl">
            <div class="bkb-dl__row">
                <dt>{{ __('bikube.public.confirmation.field_service') }}</dt>
                <dd>{{ $order->scenario?->title ?? $order->service_scenario_key }}</dd>
            </div>
            <div class="bkb-dl__row">
                <dt>{{ __('bikube.public.confirmation.field_status') }}</dt>
                <dd>{{ ucfirst($order->status->value) }}</dd>
            </div>
            <div class="bkb-dl__row">
                <dt>{{ __('bikube.public.confirmation.field_payment') }}</dt>
                <dd>{{ __('bikube.public.confirmation.payment_pending_label') }}</dd>
            </div>
        </dl>

        @if (!empty($order->metadata['intake']))
            <h3 style="margin-top:1.5rem; margin-bottom:.75rem; font-size:1rem; font-weight:800; color:#c8dde8;">
                {{ __('bikube.public.confirmation.submitted_details') }}
            </h3>
            <dl class="bkb-dl">
                @foreach ($order->metadata['intake'] as $key => $value)
                    <div class="bkb-dl__row">
                        <dt>{{ $order->scenario?->fields->firstWhere('field_key', $key)?->label ?? str($key)->replace('_', ' ')->title() }}</dt>
                        <dd>{{ is_bool($value) ? ($value ? __('bikube.public.confirmation.yes') : __('bikube.public.confirmation.no')) : ($value === '1' ? __('bikube.public.confirmation.yes') : ($value === '0' ? __('bikube.public.confirmation.no') : $value)) }}</dd>
                    </div>
                @endforeach
            </dl>
        @endif

        @php($quote = $order->latestPriceQuote())
        @if ($quote)
            <h3 style="margin-top:1.5rem; margin-bottom:.75rem; font-size:1rem; font-weight:800; color:#c8dde8;">
                {{ __('bikube.public.confirmation.estimate_title') }}
            </h3>
            <dl class="bkb-dl">
                @foreach ($quote->breakdown ?? [] as $line)
                    <div class="bkb-dl__row">
                        <dt>{{ $line['label'] }}</dt>
                        <dd>{{ number_format((float) $line['amount'], 2) }} {{ $quote->currency }}</dd>
                    </div>
                @endforeach
                <div class="bkb-dl__row">
                    <dt>{{ __('bikube.public.confirmation.subtotal') }}</dt>
                    <dd>{{ number_format((float) $quote->subtotal, 2) }} {{ $quote->currency }}</dd>
                </div>
                <div class="bkb-dl__row bkb-dl__row--total">
                    <dt>{{ __('bikube.public.confirmation.estimated_total') }}</dt>
                    <dd>{{ number_format((float) $quote->total, 2) }} {{ $quote->currency }}</dd>
                </div>
            </dl>
            <p class="bkb-confirm-estimate-note">{{ __('bikube.public.confirmation.estimate_note') }}</p>
        @endif
    </section>

    {{-- Actions --}}
    <div class="bkb-confirm-actions">
        @auth
            <a class="bkb-confirm-btn bkb-confirm-btn--primary" href="{{ route('account.orders.index') }}">
                {{ __('bikube.public.confirmation.cta_my_orders') }}
            </a>
        @endauth
        <a class="bkb-confirm-btn bkb-confirm-btn--secondary" href="{{ route('account.support.create') }}">
            {{ __('bikube.public.confirmation.cta_support') }}
        </a>
        <a class="bkb-confirm-btn bkb-confirm-btn--ghost" href="/">
            {{ __('bikube.public.confirmation.cta_home') }}
        </a>
    </div>

</article>

<style>
.bkb-confirm-page { max-width: 46rem; margin: 0 auto; padding: 0 0 4rem; display: flex; flex-direction: column; gap: 2rem; }

.bkb-confirm-hero { }
.bkb-confirm-badge {
    display: inline-flex;
    align-items: center;
    gap: .6rem;
    padding: .5rem .9rem;
    border-radius: 999px;
    background: rgba(51, 228, 157, .1);
    border: 1px solid rgba(51, 228, 157, .3);
    color: #33e49d;
    font-size: .78rem;
    font-weight: 850;
    text-transform: uppercase;
    letter-spacing: .08em;
    margin-bottom: 1rem;
}
.bkb-confirm-badge__dot {
    width: 8px; height: 8px;
    border-radius: 999px;
    background: #33e49d;
    box-shadow: 0 0 10px rgba(51, 228, 157, .8);
    flex-shrink: 0;
}
.bkb-confirm-hero h1 {
    font-size: clamp(2rem, 5vw, 3.4rem);
    font-weight: 950;
    line-height: 1.05;
    margin: .4rem 0 .8rem;
    letter-spacing: -.02em;
    color: #fff;
}

.bkb-confirm-blocker {
    display: flex;
    align-items: flex-start;
    gap: .75rem;
    padding: 1rem 1.2rem;
    background: rgba(255, 200, 60, .07);
    border: 1px solid rgba(255, 200, 60, .25);
    border-radius: 10px;
    color: #d4ae5c;
    font-size: .88rem;
    line-height: 1.5;
}
.bkb-confirm-blocker__icon { font-size: 1.1rem; flex-shrink: 0; margin-top: .1rem; }
.bkb-confirm-blocker strong { display: block; margin-bottom: .2rem; color: #e8c46a; }
.bkb-confirm-blocker p { margin: 0; color: #b89d50; font-size: .82rem; }

.bkb-confirm-next { }
.bkb-confirm-next h2 { font-size: 1.3rem; font-weight: 850; color: #fff; margin: 0 0 1.2rem; letter-spacing: -.01em; }
.bkb-confirm-steps { list-style: none; margin: 0; padding: 0; display: flex; flex-direction: column; gap: .8rem; }
.bkb-confirm-steps li {
    display: flex;
    gap: 1rem;
    align-items: flex-start;
    background: rgba(8, 24, 40, .6);
    border: 1px solid rgba(137, 165, 194, .15);
    border-radius: 10px;
    padding: 1rem 1.2rem;
}
.bkb-confirm-step__num {
    width: 32px; height: 32px; flex-shrink: 0;
    border-radius: 999px;
    background: rgba(51, 228, 157, .1);
    border: 1px solid rgba(51, 228, 157, .3);
    display: flex; align-items: center; justify-content: center;
    font-size: .9rem; font-weight: 900; color: #33e49d;
}
.bkb-confirm-steps li div { flex: 1; }
.bkb-confirm-steps li strong { display: block; font-size: .92rem; color: #e0eef8; margin-bottom: .2rem; }
.bkb-confirm-steps li p { margin: 0; font-size: .83rem; color: #7f96b1; line-height: 1.5; }

.bkb-confirm-summary { background: rgba(8, 24, 40, .6); border: 1px solid rgba(137, 165, 194, .15); border-radius: 14px; padding: 1.6rem; }
.bkb-confirm-summary h2 { font-size: 1.1rem; font-weight: 850; color: #33e49d; margin: 0 0 1.2rem; font-size: .78rem; text-transform: uppercase; letter-spacing: .1em; }
.bkb-dl { display: flex; flex-direction: column; gap: .5rem; }
.bkb-dl__row { display: grid; grid-template-columns: 1fr 1.2fr; gap: .5rem; padding: .6rem .8rem; border-radius: 7px; background: rgba(4, 14, 28, .5); }
.bkb-dl__row--total { background: rgba(51, 228, 157, .06); border: 1px solid rgba(51, 228, 157, .2); }
.bkb-dl__row dt { color: #7f96b1; font-size: .82rem; font-weight: 700; }
.bkb-dl__row dd { color: #f0f7ff; font-size: .88rem; font-weight: 600; margin: 0; }
.bkb-confirm-estimate-note { margin-top: .8rem; font-size: .78rem; color: #7f96b1; line-height: 1.5; }

.bkb-confirm-actions { display: flex; flex-wrap: wrap; gap: .75rem; }
.bkb-confirm-btn {
    display: inline-flex; align-items: center; justify-content: center;
    min-height: 2.8rem; border-radius: 8px; padding: .65rem 1.2rem;
    font-weight: 800; font-size: .9rem; text-decoration: none;
    border: 1px solid transparent; cursor: pointer; transition: all .2s;
}
.bkb-confirm-btn--primary  { background: linear-gradient(135deg, #1ab07e, #0b7657); color: #fff; border-color: rgba(47, 229, 157, .5); box-shadow: 0 10px 30px rgba(23, 207, 142, .18); }
.bkb-confirm-btn--primary:hover { filter: brightness(1.1); transform: translateY(-2px); }
.bkb-confirm-btn--secondary { background: rgba(10, 29, 48, .8); color: #dcefff; border-color: rgba(137, 165, 194, .3); }
.bkb-confirm-btn--secondary:hover { border-color: rgba(51, 228, 157, .4); color: #33e49d; }
.bkb-confirm-btn--ghost { color: #7f96b1; border-color: rgba(100, 130, 160, .2); background: transparent; }
.bkb-confirm-btn--ghost:hover { color: #b0c8d8; border-color: rgba(100, 130, 160, .4); }
</style>
@endsection
