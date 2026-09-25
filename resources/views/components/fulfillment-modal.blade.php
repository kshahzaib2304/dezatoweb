@php
    use App\Support\Fulfillment;
    use App\Support\KarachiAreas;

    $method = old('method', ($currentFulfillment['method'] ?? null));
    if (! in_array($method, [Fulfillment::METHOD_DELIVERY, Fulfillment::METHOD_PICKUP], true)) {
        $method = Fulfillment::METHOD_DELIVERY;
    }

    $areas = KarachiAreas::all();
    $selectedArea = old('area_id', $currentFulfillment['area_id'] ?? '');
    $forceOpen = request()->boolean('fulfillment') || session('open_fulfillment');
    $autoOpen = $forceOpen || ((! ($hasFulfillment ?? false)) && (! ($welcomeSeen ?? false)));
@endphp

<div
    class="fulfillment-modal"
    id="fulfillment-modal"
    data-fulfillment-modal
    data-auto-open="{{ $autoOpen ? '1' : '0' }}"
    data-force-open="{{ $forceOpen ? '1' : '0' }}"
    hidden
>
    <div class="fulfillment-modal__backdrop" data-fulfillment-dismiss></div>

    <div
        class="fulfillment-modal__dialog fulfillment-modal__dialog--compact"
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

        <div class="fulfillment-sheet" data-fulfillment>
            <div class="fulfillment-sheet__brand">
                <img src="{{ asset('images/brand/logo-mark.svg') }}" width="72" height="72" alt="{{ $brandShortName ?? 'Dezato' }}">
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

            <form method="post" action="{{ route('order.fulfillment.store') }}" class="fulfillment-sheet__form" data-fulfillment-form>
                @csrf

                <h2 id="fulfillment-modal-title" class="fulfillment-sheet__title">Select your order type</h2>

                <div class="fulfillment-switch" role="tablist" aria-label="Order type">
                    <button
                        class="fulfillment-switch__btn {{ $method === 'delivery' ? 'is-active' : '' }}"
                        type="button"
                        role="tab"
                        data-fulfillment-tab="delivery"
                        aria-selected="{{ $method === 'delivery' ? 'true' : 'false' }}"
                    >Delivery</button>
                    <button
                        class="fulfillment-switch__btn {{ $method === 'pickup' ? 'is-active' : '' }}"
                        type="button"
                        role="tab"
                        data-fulfillment-tab="pickup"
                        aria-selected="{{ $method === 'pickup' ? 'true' : 'false' }}"
                    >Pick-Up</button>
                </div>

                <input type="hidden" name="method" id="fulfillment-method" value="{{ $method }}">
                <input type="hidden" name="location_id" id="fulfillment-location" value="{{ old('location_id', $currentFulfillment['location_id'] ?? '') }}" data-fulfillment-location>
                <input type="hidden" name="address" id="fulfillment-address" value="{{ old('address', $currentFulfillment['address'] ?? '') }}" data-fulfillment-address>

                <p class="fulfillment-sheet__subtitle">Please select your location</p>

                <button class="fulfillment-locate" type="button" data-fulfillment-locate>
                    <svg viewBox="0 0 24 24" width="16" height="16" aria-hidden="true" fill="currentColor">
                        <path d="M12 8a4 4 0 1 0 0 8 4 4 0 0 0 0-8zm0-6a1 1 0 0 1 1 1v1.06A8.004 8.004 0 0 1 20.94 11H22a1 1 0 1 1 0 2h-1.06A8.004 8.004 0 0 1 13 20.94V22a1 1 0 1 1-2 0v-1.06A8.004 8.004 0 0 1 3.06 13H2a1 1 0 1 1 0-2h1.06A8.004 8.004 0 0 1 11 3.06V2a1 1 0 0 1 1-1zm0 4a6 6 0 1 0 0 12 6 6 0 0 0 0-12z"/>
                    </svg>
                    <span>Use Current Location</span>
                </button>
                <p class="fulfillment-locate__hint" data-fulfillment-locate-hint hidden></p>

                <div class="fulfillment-city" role="radiogroup" aria-label="City">
                    <label class="fulfillment-city__card is-active">
                        <input type="radio" name="city" value="karachi" checked>
                        <span class="fulfillment-city__icon" aria-hidden="true">
                            <svg viewBox="0 0 48 48" width="40" height="40" fill="none" stroke="currentColor" stroke-width="1.6">
                                <path d="M24 42c8-8 12-14 12-20a12 12 0 1 0-24 0c0 6 4 12 12 20z"/>
                                <circle cx="24" cy="22" r="4"/>
                            </svg>
                        </span>
                        <span>Karachi</span>
                    </label>
                </div>

                <div class="fulfillment-area">
                    <label class="sr-only" for="fulfillment-area">Area</label>
                    <select
                        id="fulfillment-area"
                        class="field-input fulfillment-area__select"
                        name="area_id"
                        required
                        data-fulfillment-area
                    >
                        <option value="">Please select your location</option>
                        @foreach ($areas as $area)
                            <option
                                value="{{ $area['id'] }}"
                                data-location-id="{{ $area['location_id'] }}"
                                data-label="{{ $area['label'] }}"
                                @selected($selectedArea === $area['id'])
                            >{{ $area['label'] }}</option>
                        @endforeach
                    </select>
                </div>

                <button class="btn btn--primary btn--block fulfillment-submit" type="submit" data-fulfillment-submit disabled>Select</button>
            </form>

            <form method="post" action="{{ route('order.fulfillment.dismiss') }}" id="fulfillment-dismiss-form" hidden>
                @csrf
            </form>
        </div>
    </div>
</div>
