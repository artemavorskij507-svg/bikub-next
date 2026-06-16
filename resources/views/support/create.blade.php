@extends('layouts.account-shell')

@section('title', 'Create support ticket')

@section('content')

<div style="max-width:42rem;margin:0 auto">

    <header style="margin-bottom:1.5rem">
        <span class="shell-eyebrow">Account support</span>
        <h1 style="margin:.35rem 0 .4rem;font-size:clamp(1.5rem,3vw,2rem);font-weight:950">Create support ticket</h1>
        <p style="margin:0;color:var(--shell-muted)">Describe your issue clearly. Operations will respond as soon as possible.</p>
    </header>

    <section class="shell-card">
        <form class="shell-form" method="post" action="{{ route('account.support.store') }}">
            @csrf

            @if(request('order_id'))
                <input type="hidden" name="order_id" value="{{ request('order_id') }}">
                <div style="padding:.75rem;border-radius:6px;border:1px solid rgba(37,220,145,.24);background:rgba(10,50,35,.5);margin-bottom:.5rem">
                    <p style="margin:0;font-size:.82rem;color:#d0ffe8">
                        This ticket will be linked to your order.
                    </p>
                </div>
            @endif

            <label>
                Subject <span style="color:#fca5a5">*</span>
                <input
                    name="subject"
                    required
                    maxlength="255"
                    placeholder="Briefly describe the issue"
                    value="{{ old('subject') }}"
                >
            </label>

            <label>
                Message <span style="color:#fca5a5">*</span>
                <textarea
                    name="summary"
                    required
                    rows="8"
                    placeholder="Describe in detail what happened, what you expected, and any relevant order numbers or dates."
                >{{ old('summary') }}</textarea>
            </label>

            <button type="submit" class="shell-primary">Create ticket</button>
        </form>
    </section>

    <p style="margin:1rem 0 0;text-align:center;font-size:.82rem;color:var(--shell-muted)">
        <a href="{{ route('account.support.index') }}" style="color:var(--bkb-accent,#25dc91);text-decoration:none">
            ← Back to support tickets
        </a>
    </p>

</div>
@endsection
