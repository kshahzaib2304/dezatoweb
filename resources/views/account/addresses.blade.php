@extends('layouts.account')

@section('account')
    <header class="account-head">
        <h1>Saved addresses</h1>
        <p>Use these at checkout for faster delivery.</p>
    </header>

    @if ($errors->any())
        <div class="flash flash--error" role="alert">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    @if (count($addresses) === 0)
        <p class="admin-empty">No saved addresses yet. Add one below.</p>
    @else
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
                        <p>{{ collect([$address['area'], $address['city']])->filter()->implode(', ') }}</p>
                    </div>
                    <div class="address-card__actions">
                        <form method="post" action="{{ route('account.addresses.destroy', $address['id']) }}">
                            @csrf
                            @method('DELETE')
                            <button class="text-btn" type="submit">Remove</button>
                        </form>
                    </div>
                </li>
            @endforeach
        </ul>
    @endif

    <details class="address-add" @if($errors->any()) open @endif>
        <summary>Add address</summary>
        <form class="account-form" method="post" action="{{ route('account.addresses.store') }}">
            @csrf
            <div class="form-grid">
                <div class="form-row">
                    <label class="field-label" for="label">Label</label>
                    <input id="label" class="field-input" type="text" name="label" value="{{ old('label') }}" placeholder="Home, Office…">
                </div>
                <div class="form-row form-row--full">
                    <label class="field-label" for="line1">Street address</label>
                    <input id="line1" class="field-input" type="text" name="line1" value="{{ old('line1') }}" required>
                </div>
                <div class="form-row">
                    <label class="field-label" for="area">Area</label>
                    <input id="area" class="field-input" type="text" name="area" value="{{ old('area') }}" placeholder="DHA Phase 6">
                </div>
                <div class="form-row">
                    <label class="field-label" for="city">City</label>
                    <input id="city" class="field-input" type="text" name="city" value="{{ old('city', 'Karachi') }}">
                </div>
            </div>
            <button class="btn btn--primary" type="submit">Save address</button>
        </form>
    </details>
@endsection
