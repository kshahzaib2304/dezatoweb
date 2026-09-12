@php
    use App\Support\Fulfillment;

    $method = old('method', ($currentFulfillment['method'] ?? null) ?: Fulfillment::METHOD_PICKUP);
    $locations = $fulfillmentLocations ?? [];
    $shippingFee = $shippingFee ?? (float) config('dezato.shipping.fee', 0);
    $shippingEta = $shippingEta ?? (string) config('dezato.shipping.eta', '');
    $forceOpen = request()->boolean('fulfillment') || session('open_fulfillment');
    $autoOpen = $forceOpen || ((! ($hasFulfillment ?? false)) && (! ($welcomeSeen ?? false)));
@endphp

<div
    class="fulfillment-modal"
    id="fulfillment-modal"
    data-fulfillment-modal
    data-auto-open="{{ $autoOpen ? '1' : '0' }}"
    hidden
>
    <div class="fulfillment-modal__backdrop" data-fulfillment-dismiss></div>

    <div
        class="fulfillment-modal__dialog"
        role="dialog"
        aria-modal="true"
        aria-labelledby="fulfillment-modal-title"
        tabindex="-1"
    >
        <button class="fulfillment-modal__close" type="button" data-fulfillment-dismiss aria-label="Close">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path d="M6 6l12 12M18 6 6 18"/>
            </svg>
        </button>

        <div class="fulfillment-modal__scroll" data-fulfillment>
            <div class="fulfillment-card__intro">
                <p class="fulfillment-card__eyebrow">Welcome to Dezato</p>
                <h2 id="fulfillment-modal-title">How would you like to get your order?</h2>
                <p>Choose pickup, Karachi delivery, or Pakistan courier to personalize your experience.</p>
            </div>

            @if (session('status') && $forceOpen)
                <p class="flash" role="status">{{ session('status') }}</p>
            @endif

            @if ($errors->any())
                <div class="form-errors" role="alert">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="post" action="{{ route('order.fulfillment.store') }}" class="fulfillment-form">
                @csrf

                <div class="fulfillment-tabs fulfillment-tabs--three" role="tablist" aria-label="Order method">
                    <button class="fulfillment-tab {{ $method === 'pickup' ? 'is-active' : '' }}" type="button" role="tab" data-fulfillment-tab="pickup" aria-selected="{{ $method === 'pickup' ? 'true' : 'false' }}">Pickup</button>
                    <button class="fulfillment-tab {{ $method === 'delivery' ? 'is-active' : '' }}" type="button" role="tab" data-fulfillment-tab="delivery" aria-selected="{{ $method === 'delivery' ? 'true' : 'false' }}">Delivery</button>
                    <button class="fulfillment-tab {{ $method === 'shipping' ? 'is-active' : '' }}" type="button" role="tab" data-fulfillment-tab="shipping" aria-selected="{{ $method === 'shipping' ? 'true' : 'false' }}">Courier</button>
                </div>

                <input type="hidden" name="method" id="fulfillment-method" value="{{ $method }}">

                <div class="fulfillment-panel" data-fulfillment-panel="delivery" @if ($method !== 'delivery') hidden @endif>
                    <label class="field-label" for="delivery-address">Delivery address or area</label>
                    <input
                        id="delivery-address"
                        class="field-input"
                        type="text"
                        name="address"
                        value="{{ old('address', ($currentFulfillment['method'] ?? null) === 'delivery' ? ($currentFulfillment['address'] ?? '') : '') }}"
                        placeholder="Street, neighborhood, or landmark"
                        autocomplete="street-address"
                        data-delivery-address
                        @if ($method !== 'delivery') disabled @endif
                    >
                </div>

                <div class="fulfillment-panel" data-fulfillment-panel="shipping" @if ($method !== 'shipping') hidden @endif>
                    <p class="field-hint shipping-banner">
                        Pakistan courier · {{ pkr($shippingFee) }} · {{ $shippingEta }}
                    </p>
                    <div class="form-grid">
                        <div class="form-row form-row--full">
                            <label class="field-label" for="ship-address">Shipping address</label>
                            <input id="ship-address" class="field-input" type="text" name="address" value="{{ old('address', ($currentFulfillment['method'] ?? null) === 'shipping' ? ($currentFulfillment['address'] ?? '') : '') }}" placeholder="Street address" autocomplete="shipping street-address" data-shipping-address @if ($method !== 'shipping') disabled @endif>
                        </div>
                        <div class="form-row">
                            <label class="field-label" for="ship-city">City</label>
                            <input id="ship-city" class="field-input" type="text" name="city" value="{{ old('city', $currentFulfillment['city'] ?? '') }}" autocomplete="shipping address-level2" data-shipping-field @if ($method !== 'shipping') disabled @endif>
                        </div>
                        <div class="form-row">
                            <label class="field-label" for="ship-region">Province</label>
                            <input id="ship-region" class="field-input" type="text" name="region" value="{{ old('region', $currentFulfillment['region'] ?? 'Sindh') }}" autocomplete="shipping address-level1" data-shipping-field @if ($method !== 'shipping') disabled @endif>
                        </div>
                        <div class="form-row">
                            <label class="field-label" for="ship-postal">Postal code</label>
                            <input id="ship-postal" class="field-input" type="text" name="postal_code" value="{{ old('postal_code', $currentFulfillment['postal_code'] ?? '') }}" autocomplete="shipping postal-code" data-shipping-field @if ($method !== 'shipping') disabled @endif>
                        </div>
                    </div>
                </div>

                <div data-store-section @if ($method === 'shipping') hidden @endif>
                    <div class="fulfillment-search">
                        <label class="field-label" for="store-search">Find a bakery</label>
                        <input id="store-search" class="field-input" type="search" placeholder="Search by city or store name" data-store-search autocomplete="off">
                    </div>

                    <fieldset class="store-list">
                        <legend class="sr-only">Select bakery location</legend>
                        @foreach ($locations as $location)
                            @php
                                $selected = old('location_id', $currentFulfillment['location_id'] ?? null) === $location['id'];
                                $delivers = ! empty($location['delivers']);
                            @endphp
                            <label
                                class="store-option"
                                data-store-option
                                data-store-name="{{ strtolower($location['name'].' '.$location['city'].' '.$location['region']) }}"
                                data-delivers="{{ $delivers ? '1' : '0' }}"
                            >
                                <input type="radio" name="location_id" value="{{ $location['id'] }}" @checked($selected) data-store-radio>
                                <span class="store-option__body">
                                    <span class="store-option__title">{{ $location['name'] }}</span>
                                    <span class="store-option__meta">{{ $location['address'] }} · {{ $location['city'] }}, {{ $location['region'] }}</span>
                                    <span class="store-option__tags">
                                        <span>Pickup</span>
                                        @if ($delivers)
                                            <span>Delivery {{ pkr($location['delivery_fee']) }}</span>
                                        @else
                                            <span class="is-muted">Pickup only</span>
                                        @endif
                                    </span>
                                </span>
                            </label>
                        @endforeach
                    </fieldset>
                    <p class="store-empty" data-store-empty hidden>No bakeries match your search.</p>
                </div>

                <button class="btn btn--primary btn--block fulfillment-submit" type="submit">Start ordering</button>
            </form>

            <form method="post" action="{{ route('order.fulfillment.dismiss') }}" class="fulfillment-dismiss" id="fulfillment-dismiss-form">
                @csrf
                <button class="text-btn" type="submit">Browse the site first</button>
            </form>
        </div>
    </div>
</div>
