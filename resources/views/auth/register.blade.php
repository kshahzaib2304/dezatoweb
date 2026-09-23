@extends('layouts.auth')

@section('content')
    <h1>Create account</h1>
    <p class="auth-lead">Save addresses, track orders, and checkout faster.</p>

    @if ($errors->any())
        <div class="flash flash--error" role="alert">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <x-social-login :social-providers="$socialProviders ?? []" />

    <form class="auth-form" method="post" action="{{ route('register') }}">
        @csrf
        <div class="form-row">
            <label class="field-label" for="name">Full name</label>
            <input id="name" class="field-input" type="text" name="name" value="{{ old('name') }}" autocomplete="name" required>
        </div>
        <div class="form-row">
            <label class="field-label" for="email">Email</label>
            <input id="email" class="field-input" type="email" name="email" value="{{ old('email') }}" autocomplete="email" required>
        </div>
        <div class="form-row">
            <label class="field-label" for="phone">Phone</label>
            <input id="phone" class="field-input" type="tel" name="phone" value="{{ old('phone') }}" autocomplete="tel">
        </div>
        <div class="form-row">
            <label class="field-label" for="password">Password</label>
            <input id="password" class="field-input" type="password" name="password" autocomplete="new-password" required>
        </div>
        <div class="form-row">
            <label class="field-label" for="password_confirmation">Confirm password</label>
            <input id="password_confirmation" class="field-input" type="password" name="password_confirmation" autocomplete="new-password" required>
        </div>
        <button class="btn btn--primary btn--block" type="submit">Create account</button>
    </form>

    <p class="auth-switch">Already have an account? <a href="{{ route('login') }}">Sign in</a></p>
@endsection
