@extends('public.layouts.app')
@section('content')
<article>
    <header class="content-hero">
        <p class="eyebrow">Service request</p>
        <h1>{{ $scenario->title }}</h1>
        <p class="subtitle">Provide the operational details needed to review this request. Payment and dispatch are not connected yet.</p>
    </header>

    @if($errors->any())<div class="form-errors" role="alert"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif

    @if($scenario->fields->isEmpty())
        <div class="form-errors">Scenario intake fields are not configured yet.</div>
    @else
        <form class="request-form" method="POST" action="{{ route('public.orders.store', $scenario->slug) }}">
            @csrf
            <fieldset>
                <legend>Contact details</legend>
                <label>Name<input name="customer_name" required maxlength="255" value="{{ old('customer_name') }}"></label>
                <label>Email<input type="email" name="customer_email" maxlength="255" value="{{ old('customer_email') }}"></label>
                <label>Phone<input type="tel" name="customer_phone" maxlength="50" value="{{ old('customer_phone') }}"></label>
            </fieldset>

            <fieldset>
                <legend>Request details</legend>
                @foreach($scenario->fields as $field)
                    @php($name = "intake[{$field->field_key}]")
                    @php($value = old("intake.{$field->field_key}"))
                    @if($field->type === 'textarea')
                        <label>{{ $field->label }} @if($field->required)<span aria-hidden="true">*</span>@endif
                            <textarea name="{{ $name }}" maxlength="5000" @required($field->required)>{{ $value }}</textarea>
                        </label>
                    @elseif($field->type === 'boolean')
                        <label class="request-checkbox">
                            <input type="hidden" name="{{ $name }}" value="0">
                            <input type="checkbox" name="{{ $name }}" value="1" @checked((string) $value === '1')>
                            <span>{{ $field->label }}</span>
                        </label>
                    @elseif($field->type === 'select')
                        <label>{{ $field->label }} @if($field->required)<span aria-hidden="true">*</span>@endif
                            <select name="{{ $name }}" @required($field->required)>
                                <option value="">Select an option</option>
                                @foreach(($field->options ?? []) as $optionValue => $optionLabel)
                                    <option value="{{ $optionValue }}" @selected((string) $value === (string) $optionValue)>{{ $optionLabel }}</option>
                                @endforeach
                            </select>
                        </label>
                    @else
                        <label>{{ $field->label }} @if($field->required)<span aria-hidden="true">*</span>@endif
                            <input
                                type="{{ match($field->type) { 'email' => 'email', 'phone' => 'tel', 'number' => 'number', 'date' => 'date', 'datetime' => 'datetime-local', default => 'text' } }}"
                                name="{{ $name }}"
                                value="{{ $value }}"
                                @required($field->required)
                                @if($field->type === 'number') step="any" @else maxlength="500" @endif
                            >
                        </label>
                    @endif
                @endforeach
                <label>Additional notes<textarea name="customer_notes" maxlength="5000">{{ old('customer_notes') }}</textarea></label>
            </fieldset>
            <button type="submit">Submit request</button>
        </form>
    @endif
</article>
@endsection
