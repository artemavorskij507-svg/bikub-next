@extends('layouts.public-shell')
@section('title', 'Create account')
@section('content')
<section class="shell-auth">
    <span class="shell-eyebrow">Customer access</span>
    <h1>Create your BiKuBe account</h1>
    <p>Create an account to access your orders, support conversations and documents. Existing guest orders must be linked by operations before they appear.</p>

    <form method="POST" action="{{ route('register.store') }}" class="shell-form">
        @csrf
        <label>Name<input name="name" type="text" value="{{ old('name') }}" required autocomplete="name" autofocus></label>
        <label>Email<input name="email" type="email" value="{{ old('email') }}" required autocomplete="email"></label>
        <label>Password<input name="password" type="password" required autocomplete="new-password"></label>
        <label>Confirm password<input name="password_confirmation" type="password" required autocomplete="new-password"></label>

        <p style="margin:.15rem 0 .35rem;color:var(--shell-muted);font-size:.82rem;line-height:1.5">
            By creating an account, you agree to use BiKuBe only for real service requests. Payment, BankID and automated order linking are not enabled on this page.
        </p>

        @if($errors->any())<p class="shell-error" role="alert">{{ $errors->first() }}</p>@endif
        <button type="submit">Create account</button>
    </form>

    <a class="shell-secondary" href="{{ route('login') }}">Already have an account? Sign in</a>
</section>
@endsection
