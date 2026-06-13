@extends('layouts.public-shell')
@section('title', 'Sign in')
@section('content')
<section class="shell-auth">
    <span class="shell-eyebrow">Secure account access</span>
    <h1>Sign in to BiKuBe</h1>
    <p>Continue to your account or worker cockpit. Admin users can use the Admin OS login.</p>
    <form method="POST" action="{{ route('login.store') }}" class="shell-form">
        @csrf
        <label>Email<input name="email" type="email" value="{{ old('email') }}" required autocomplete="email" autofocus></label>
        <label>Password<input name="password" type="password" required autocomplete="current-password"></label>
        <label class="shell-check"><input name="remember" type="checkbox"> Keep me signed in</label>
        @if($errors->any())<p class="shell-error" role="alert">{{ $errors->first() }}</p>@endif
        <button type="submit">Sign in</button>
    </form>
    <a class="shell-secondary" href="{{ route('filament.admin.auth.login') }}">Admin OS sign in</a>
</section>
@endsection
