@extends('layouts.auth')

@section('content')
    <h1>Reset password</h1>
    <p class="auth-lead">Choose a new password for your account.</p>

    @if (session('status'))
        <p class="flash" role="status">{{ session('status') }}</p>
    @endif

    <form class="auth-form" method="post" action="{{ route('auth.stub') }}">
        @csrf
        <div class="form-row">
            <label class="field-label" for="email">Email</label>
            <input id="email" class="field-input" type="email" name="email" autocomplete="email" required>
        </div>
        <div class="form-row">
            <label class="field-label" for="password">New password</label>
            <input id="password" class="field-input" type="password" name="password" autocomplete="new-password" required>
        </div>
        <div class="form-row">
            <label class="field-label" for="password_confirmation">Confirm password</label>
            <input id="password_confirmation" class="field-input" type="password" name="password_confirmation" autocomplete="new-password" required>
        </div>
        <button class="btn btn--primary btn--block" type="submit">Update password</button>
    </form>
@endsection
