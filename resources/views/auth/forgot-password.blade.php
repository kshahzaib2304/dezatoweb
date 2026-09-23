@extends('layouts.auth')

@section('content')
    <h1>Forgot password</h1>
    <p class="auth-lead">Enter your email and we’ll send a reset link.</p>

    @if (session('status'))
        <p class="flash" role="status">{{ session('status') }}</p>
    @endif

    @if ($errors->any())
        <div class="flash flash--error" role="alert">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form class="auth-form" method="post" action="{{ route('password.email') }}">
        @csrf
        <div class="form-row">
            <label class="field-label" for="email">Email</label>
            <input id="email" class="field-input" type="email" name="email" value="{{ old('email') }}" autocomplete="email" required>
        </div>
        <button class="btn btn--primary btn--block" type="submit">Send reset link</button>
    </form>

    <p class="auth-switch"><a href="{{ route('login') }}">Back to sign in</a></p>
@endsection
