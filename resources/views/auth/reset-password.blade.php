@extends('layouts.auth')

@section('content')
    <h1>Reset password</h1>
    <p class="auth-lead">Choose a new password for your account.</p>

    @if ($errors->any())
        <div class="flash flash--error" role="alert">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form class="auth-form" method="post" action="{{ route('password.update') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <div class="form-row">
            <label class="field-label" for="email">Email</label>
            <input id="email" class="field-input" type="email" name="email" value="{{ old('email', $email) }}" autocomplete="email" required>
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
