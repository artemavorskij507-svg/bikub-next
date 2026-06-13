@extends('layouts.account-shell')
@section('title','Create support ticket')
@section('content')
<section class="shell-auth"><span class="shell-eyebrow">Account support</span><h1>Create support ticket</h1><form class="shell-form" method="post" action="{{ route('account.support.store') }}">@csrf<label>Subject<input name="subject" required maxlength="255"></label><label>Message<textarea name="summary" required rows="8"></textarea></label><button type="submit">Create ticket</button></form></section>
@endsection
