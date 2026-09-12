@extends('layouts.account')

@section('account')
    <header class="account-head">
        <h1>Saved addresses</h1>
        <p>Use these at checkout for faster delivery.</p>
    </header>

    <ul class="address-list">
        @foreach ($addresses as $address)
            <li class="address-card">
                <div>
                    <p class="address-card__label">
                        {{ $address['label'] }}
                        @if ($address['is_default'])
                            <span>Default</span>
                        @endif
                    </p>
                    <p>{{ $address['line1'] }}</p>
                    <p>{{ $address['area'] }}, {{ $address['city'] }}</p>
                </div>
                <div class="address-card__actions">
                    <form method="post" action="{{ route('account.stub') }}">@csrf<button class="text-btn" type="submit">Edit</button></form>
                    <form method="post" action="{{ route('account.stub') }}">@csrf<button class="text-btn" type="submit">Remove</button></form>
                </div>
            </li>
        @endforeach
    </ul>

    <details class="address-add">
        <summary>Add address</summary>
        <form class="account-form" method="post" action="{{ route('account.stub') }}">
            @csrf
            <div class="form-grid">
                <div class="form-row">
                    <label class="field-label" for="label">Label</label>
                    <input id="label" class="field-input" type="text" name="label" placeholder="Home, Office…">
                </div>
                <div class="form-row form-row--full">
                    <label class="field-label" for="line1">Street address</label>
                    <input id="line1" class="field-input" type="text" name="line1" required>
                </div>
                <div class="form-row">
                    <label class="field-label" for="area">Area</label>
                    <input id="area" class="field-input" type="text" name="area" placeholder="DHA Phase 6">
                </div>
                <div class="form-row">
                    <label class="field-label" for="city">City</label>
                    <input id="city" class="field-input" type="text" name="city" value="Karachi">
                </div>
            </div>
            <button class="btn btn--primary" type="submit">Save address</button>
        </form>
    </details>
@endsection
