@extends('layouts.account')

@section('account')
    <header class="account-head">
        <h1>Profile</h1>
        <p>Update how we reach you for orders and delivery.</p>
    </header>

    @if ($errors->any())
        <div class="flash flash--error" role="alert">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form class="account-form" method="post" action="{{ route('account.profile.update') }}">
        @csrf
        <div class="form-grid">
            <div class="form-row form-row--full">
                <label class="field-label" for="name">Full name</label>
                <input id="name" class="field-input" type="text" name="name" value="{{ old('name', $profile['name']) }}" required>
            </div>
            <div class="form-row">
                <label class="field-label" for="email">Email</label>
                <input id="email" class="field-input" type="email" value="{{ $profile['email'] }}" disabled>
                <p class="field-hint">Email cannot be changed here. Contact us if you need an update.</p>
            </div>
            <div class="form-row">
                <label class="field-label" for="phone">Phone</label>
                <input id="phone" class="field-input" type="tel" name="phone" value="{{ old('phone', $profile['phone']) }}">
            </div>
        </div>
        <button class="btn btn--primary" type="submit">Save profile</button>
    </form>
@endsection
