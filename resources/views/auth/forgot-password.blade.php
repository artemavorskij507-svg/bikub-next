@extends('layouts.public-shell')
@section('title', 'Reset password')
@section('content')
<section class="shell-auth">
    <span class="shell-eyebrow">Account recovery</span>
    <h1>Reset your password</h1>
    <p>Enter your email address and BiKuBe will send a password reset link if the account exists.</p>

    @if(session('status'))
        <p style="padding:.75rem;border-radius:8px;border:1px solid rgba(37,220,145,.28);background:rgba(10,50,35,.7);color:#d0ffe8" role="status">{{ session('status') }}</p>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="shell-form">
        @csrf
        <label>Email<input name="email" type="email" value="{{ old('email') }}" required autocomplete="email" autofocus></label>
        @if($errors->any())<p class="shell-error" role="alert">{{ $errors->first() }}</p>@endif
        <button type="submit">Send reset link</button>
    </form>

    <a class="shell-secondary" href="{{ route('login') }}">Back to sign in</a>
</section>
@endsection
