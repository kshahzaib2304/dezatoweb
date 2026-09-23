@extends('layouts.auth')

@section('content')
    <h1>Welcome back</h1>
    <p class="auth-lead">Sign in to track orders, save addresses, and manage your bakery (admins).</p>

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

    <form class="auth-form" method="post" action="{{ route('login') }}">
        @csrf
        <div class="form-row">
            <label class="field-label" for="email">Email</label>
            <input id="email" class="field-input" type="email" name="email" value="{{ old('email') }}" autocomplete="email" required>
        </div>
        <div class="form-row">
            <label class="field-label" for="password">Password</label>
            <input id="password" class="field-input" type="password" name="password" autocomplete="current-password" required>
        </div>
        <div class="auth-row">
            <label class="check-inline">
                <input type="checkbox" name="remember" value="1">
                <span>Remember me</span>
            </label>
            <a href="{{ route('password.request') }}">Forgot password?</a>
        </div>
        <button class="btn btn--primary btn--block" type="submit">Sign in</button>
    </form>

    <p class="auth-switch">New here? <a href="{{ route('register') }}">Create an account</a></p>
@endsection
