@extends('public.layouts.app')
@section('content')
<article><header class="content-hero"><p class="eyebrow">Service request</p><h1>{{ $scenario->title }}</h1><p class="subtitle">Send a request for review. Payment and dispatch are not connected yet.</p></header>
@if($errors->any())<div class="form-errors"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<form class="request-form" method="POST" action="{{ route('public.orders.store', $scenario->slug) }}">@csrf
<label>Name<input name="customer_name" required maxlength="255" value="{{ old('customer_name') }}"></label>
<label>Email<input type="email" name="customer_email" maxlength="255" value="{{ old('customer_email') }}"></label>
<label>Phone<input name="customer_phone" maxlength="50" value="{{ old('customer_phone') }}"></label>
@if($scenario->supports_scheduling)<label>Preferred time<input type="datetime-local" name="scheduled_at" value="{{ old('scheduled_at') }}"></label>@endif
<label>What do you need?<textarea name="customer_notes" maxlength="5000">{{ old('customer_notes') }}</textarea></label>
<button type="submit">Submit request</button></form></article>
@endsection
