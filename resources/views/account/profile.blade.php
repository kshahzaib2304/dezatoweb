@extends('layouts.account')

@section('account')
    <header class="account-head">
        <h1>Profile</h1>
        <p>Update how we reach you for orders and delivery.</p>
    </header>

    <form class="account-form" method="post" action="{{ route('account.stub') }}">
        @csrf
        <div class="form-grid">
            <div class="form-row form-row--full">
                <label class="field-label" for="name">Full name</label>
                <input id="name" class="field-input" type="text" name="name" value="{{ $profile['name'] }}" required>
            </div>
            <div class="form-row">
                <label class="field-label" for="email">Email</label>
                <input id="email" class="field-input" type="email" name="email" value="{{ $profile['email'] }}" required>
            </div>
            <div class="form-row">
                <label class="field-label" for="phone">Phone</label>
                <input id="phone" class="field-input" type="tel" name="phone" value="{{ $profile['phone'] }}" required>
            </div>
        </div>
        <button class="btn btn--primary" type="submit">Save profile</button>
    </form>
@endsection
