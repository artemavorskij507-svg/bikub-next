@extends('public.layouts.app')
@section('content')
<article class="bkb-request-page">

    <header class="bkb-request-hero">
        <p class="eyebrow">{{ __('bikube.public.request.eyebrow') }}</p>
        <h1>{{ $scenario->title }}</h1>
        <p class="subtitle">{{ __('bikube.public.request.subtitle') }}</p>

        {{-- Honest payment blocker --}}
        <div class="bkb-request-blocker">
            <span class="bkb-request-blocker__icon">⚠️</span>
            <span>{{ __('bikube.public.request.payment_not_connected') }}</span>
        </div>
    </header>

    @if ($errors->any())
        <div class="bkb-form-errors" role="alert">
            <p class="bkb-form-errors__title">{{ __('bikube.public.request.errors_title') }}</p>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if ($scenario->fields->isEmpty())
        <div class="bkb-form-errors">
            {{ __('bikube.public.request.no_fields') }}
        </div>
    @else
        <form class="bkb-request-form" method="POST" action="{{ route('public.orders.store', $scenario->slug) }}">
            @csrf

            {{-- Contact section --}}
            <fieldset class="bkb-fieldset">
                <legend class="bkb-fieldset__legend">{{ __('bikube.public.request.section_contact') }}</legend>
                <div class="bkb-field-grid">
                    <label class="bkb-label">
                        {{ __('bikube.public.request.field_name') }} <span class="bkb-required" aria-hidden="true">*</span>
                        <input
                            class="bkb-input"
                            name="customer_name"
                            required
                            maxlength="255"
                            value="{{ old('customer_name') }}"
                            placeholder="{{ __('bikube.public.request.field_name_placeholder') }}"
                        >
                    </label>
                    <label class="bkb-label">
                        {{ __('bikube.public.request.field_email') }}
                        <input
                            class="bkb-input"
                            type="email"
                            name="customer_email"
                            maxlength="255"
                            value="{{ old('customer_email') }}"
                            placeholder="{{ __('bikube.public.request.field_email_placeholder') }}"
                        >
                    </label>
                    <label class="bkb-label">
                        {{ __('bikube.public.request.field_phone') }}
                        <input
                            class="bkb-input"
                            type="tel"
                            name="customer_phone"
                            maxlength="50"
                            value="{{ old('customer_phone') }}"
                            placeholder="{{ __('bikube.public.request.field_phone_placeholder') }}"
                        >
                    </label>
                </div>
                <p class="bkb-field-hint">{{ __('bikube.public.request.contact_hint') }}</p>
            </fieldset>

            {{-- Dynamic scenario fields --}}
            <fieldset class="bkb-fieldset">
                <legend class="bkb-fieldset__legend">{{ __('bikube.public.request.section_details') }}</legend>
                <div class="bkb-field-grid">
                    @foreach ($scenario->fields as $field)
                        @php($name  = "intake[{$field->field_key}]")
                        @php($value = old("intake.{$field->field_key}"))

                        @if ($field->type === 'textarea')
                            <label class="bkb-label bkb-label--full">
                                {{ $field->label }}
                                @if ($field->required)<span class="bkb-required" aria-hidden="true">*</span>@endif
                                <textarea
                                    class="bkb-input bkb-input--textarea"
                                    name="{{ $name }}"
                                    maxlength="5000"
                                    @required($field->required)
                                >{{ $value }}</textarea>
                            </label>

                        @elseif ($field->type === 'boolean')
                            <label class="bkb-label bkb-label--checkbox">
                                <input type="hidden"   name="{{ $name }}" value="0">
                                <input type="checkbox" name="{{ $name }}" value="1" @checked((string) $value === '1')>
                                <span>{{ $field->label }}</span>
                            </label>

                        @elseif ($field->type === 'select')
                            <label class="bkb-label">
                                {{ $field->label }}
                                @if ($field->required)<span class="bkb-required" aria-hidden="true">*</span>@endif
                                <select class="bkb-input bkb-input--select" name="{{ $name }}" @required($field->required)>
                                    <option value="">{{ __('bikube.public.request.select_placeholder') }}</option>
                                    @foreach (($field->options ?? []) as $optionValue => $optionLabel)
                                        <option value="{{ $optionValue }}" @selected((string) $value === (string) $optionValue)>
                                            {{ $optionLabel }}
                                        </option>
                                    @endforeach
                                </select>
                            </label>

                        @else
                            <label class="bkb-label">
                                {{ $field->label }}
                                @if ($field->required)<span class="bkb-required" aria-hidden="true">*</span>@endif
                                <input
                                    class="bkb-input"
                                    type="{{ match($field->type) { 'email' => 'email', 'phone' => 'tel', 'number' => 'number', 'date' => 'date', 'datetime' => 'datetime-local', default => 'text' } }}"
                                    name="{{ $name }}"
                                    value="{{ $value }}"
                                    @required($field->required)
                                    @if ($field->type === 'number') step="any" @else maxlength="500" @endif
                                >
                            </label>
                        @endif
                    @endforeach
                </div>
            </fieldset>

            {{-- Notes --}}
            <fieldset class="bkb-fieldset">
                <legend class="bkb-fieldset__legend">{{ __('bikube.public.request.section_notes') }}</legend>
                <label class="bkb-label">
                    {{ __('bikube.public.request.field_notes') }}
                    <textarea
                        class="bkb-input bkb-input--textarea"
                        name="customer_notes"
                        maxlength="5000"
                        placeholder="{{ __('bikube.public.request.field_notes_placeholder') }}"
                    >{{ old('customer_notes') }}</textarea>
                </label>
            </fieldset>

            {{-- Submit --}}
            <div class="bkb-request-submit">
                <button type="submit" class="bkb-submit-btn">
                    {{ __('bikube.public.request.submit') }}
                </button>
                <p class="bkb-submit-hint">{{ __('bikube.public.request.submit_hint') }}</p>
            </div>
        </form>
    @endif

</article>

<style>
.bkb-request-page {
    max-width: 46rem;
    margin: 0 auto;
    padding: 0 0 4rem;
}
.bkb-request-hero { margin-bottom: 2rem; }
.bkb-request-hero h1 { font-size: clamp(2rem, 5vw, 3.4rem); font-weight: 950; line-height: 1.05; margin: .5rem 0 1rem; letter-spacing: -.02em; color: #fff; }
.bkb-request-blocker {
    display: flex;
    align-items: flex-start;
    gap: .6rem;
    margin-top: 1rem;
    padding: .75rem 1rem;
    background: rgba(255, 200, 60, .07);
    border: 1px solid rgba(255, 200, 60, .25);
    border-radius: 8px;
    font-size: .85rem;
    color: #d4ae5c;
    line-height: 1.5;
}
.bkb-request-blocker__icon { flex-shrink: 0; font-size: 1rem; }

.bkb-form-errors {
    margin-bottom: 1.5rem;
    padding: 1rem 1.2rem;
    background: rgba(255, 100, 100, .08);
    border: 1px solid rgba(255, 100, 100, .3);
    border-radius: 8px;
    color: #ff9c9c;
    font-size: .9rem;
}
.bkb-form-errors__title { font-weight: 800; margin: 0 0 .5rem; }
.bkb-form-errors ul { margin: 0; padding-left: 1.2rem; }
.bkb-form-errors li { margin-bottom: .3rem; }

.bkb-request-form { display: flex; flex-direction: column; gap: 1.5rem; }

.bkb-fieldset {
    border: 1px solid rgba(137, 165, 194, .18);
    border-radius: 14px;
    background: rgba(8, 24, 40, .7);
    padding: 1.6rem;
    margin: 0;
}
.bkb-fieldset__legend {
    color: #33e49d;
    font-size: .75rem;
    font-weight: 950;
    text-transform: uppercase;
    letter-spacing: .1em;
    padding: 0 .5rem;
}
.bkb-field-grid {
    display: grid;
    gap: 1rem;
    grid-template-columns: repeat(auto-fill, minmax(min(100%, 18rem), 1fr));
    margin-top: 1rem;
}
.bkb-label {
    display: flex;
    flex-direction: column;
    gap: .4rem;
    font-size: .88rem;
    font-weight: 700;
    color: #c8dde8;
}
.bkb-label--full { grid-column: 1 / -1; }
.bkb-label--checkbox { flex-direction: row; align-items: center; gap: .6rem; cursor: pointer; }
.bkb-required { color: #ff9c9c; margin-left: .15rem; }
.bkb-input {
    width: 100%;
    border: 1px solid rgba(100, 140, 175, .25);
    border-radius: 8px;
    background: rgba(4, 14, 28, .9);
    color: #f0f7ff;
    padding: .7rem .9rem;
    font-size: .9rem;
    font-family: inherit;
    transition: border-color .2s;
    outline: none;
}
.bkb-input:focus { border-color: rgba(51, 228, 157, .5); box-shadow: 0 0 0 3px rgba(51, 228, 157, .08); }
.bkb-input--textarea { min-height: 8rem; resize: vertical; }
.bkb-input--select { cursor: pointer; }
.bkb-field-hint { margin-top: .8rem; font-size: .8rem; color: #6b8299; line-height: 1.5; }

.bkb-request-submit { display: flex; flex-direction: column; gap: .75rem; }
.bkb-submit-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    height: 3.2rem;
    padding: 0 2rem;
    border-radius: 10px;
    background: linear-gradient(135deg, #1ab07e, #0b7657);
    color: #fff;
    font-weight: 850;
    font-size: 1rem;
    border: 1px solid rgba(47, 229, 157, .5);
    cursor: pointer;
    transition: filter .2s, transform .2s;
    box-shadow: 0 12px 36px rgba(23, 207, 142, .2);
    align-self: flex-start;
}
.bkb-submit-btn:hover { filter: brightness(1.1); transform: translateY(-2px); }
.bkb-submit-hint { font-size: .8rem; color: #6b8299; line-height: 1.5; }
</style>
@endsection
