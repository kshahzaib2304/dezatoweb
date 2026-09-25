@extends('layouts.app')

@section('content')
@php $b = $builder; @endphp

<section class="section-block builder-page">
    <div class="container">
        <header class="builder-head" data-reveal>
            <p class="home-kicker">Custom cake</p>
            <h1>Design your cake</h1>
            <p>Upload a reference, choose size and flavours - price updates live in PKR.</p>
        </header>

        @if (session('status'))
            <p class="flash" role="status">{{ session('status') }}</p>
        @endif

        @if (! empty($guidelines))
            <aside class="builder-guidelines" data-reveal>
                <h2>Before you design</h2>
                <ul>
                    @foreach ($guidelines as $line)
                        <li>{{ $line }}</li>
                    @endforeach
                </ul>
            </aside>
        @endif

        <form
            class="builder-layout"
            method="post"
            action="{{ route('builder.store') }}"
            enctype="multipart/form-data"
            data-cake-builder
            data-cart-add
            data-reveal
        >
            @csrf

            @if ($errors->any())
                <div class="form-errors" role="alert" style="grid-column:1/-1">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="builder-main">
                <section class="builder-block">
                    <h2>1. Reference image</h2>
                    <label class="upload-zone" for="ref-image">
                        <input id="ref-image" type="file" name="reference_image" accept="image/jpeg,image/png,image/webp" data-builder-upload>
                        <span data-upload-label>Upload a cake photo or screenshot</span>
                    </label>
                    <p class="field-hint">
                        Optional · {{ $referenceGuide['formats'] ?? 'JPG/PNG/WebP' }}, max {{ $referenceGuide['max'] ?? '3 MB' }}.
                        Clear photos work best{{ isset($referenceGuide['size']) ? ' (about '.$referenceGuide['size'].')' : '' }}.
                    </p>
                    <div class="upload-preview" data-upload-preview hidden>
                        <img src="" alt="Reference preview">
                    </div>
                </section>

                <section class="builder-block">
                    <h2>2. Size</h2>
                    <div class="option-grid" role="radiogroup" aria-label="Cake size">
                        @foreach ($b['sizes'] as $size)
                            <label class="option-tile">
                                <input type="radio" name="size" value="{{ $size['id'] }}" data-price="{{ $size['price'] }}" @checked($loop->index === 1) data-builder-price>
                                <span>
                                    <strong>{{ $size['label'] }}</strong>
                                    <small>Serves {{ $size['serves'] }}</small>
                                    <em>{{ pkr($size['price']) }}</em>
                                </span>
                            </label>
                        @endforeach
                    </div>
                </section>

                <section class="builder-block">
                    <h2>3. Shape</h2>
                    <div class="option-grid option-grid--compact" role="radiogroup" aria-label="Cake shape">
                        @foreach ($b['shapes'] as $shape)
                            <label class="option-tile">
                                <input type="radio" name="shape" value="{{ $shape['id'] }}" @checked($loop->first)>
                                <span><strong>{{ $shape['label'] }}</strong></span>
                            </label>
                        @endforeach
                    </div>
                </section>

                <section class="builder-block">
                    <h2>4. Flavour</h2>
                    <div class="form-grid">
                        <div class="form-row">
                            <label class="field-label" for="base">Base</label>
                            <select id="base" class="field-input" name="base" data-builder-price>
                                @foreach ($b['bases'] as $item)
                                    <option value="{{ $item['id'] }}" data-price="{{ $item['price'] }}">{{ $item['label'] }}@if($item['price'] > 0) (+{{ pkr($item['price']) }})@endif</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-row">
                            <label class="field-label" for="filling">Filling</label>
                            <select id="filling" class="field-input" name="filling" data-builder-price>
                                @foreach ($b['fillings'] as $item)
                                    <option value="{{ $item['id'] }}" data-price="{{ $item['price'] }}">{{ $item['label'] }}@if($item['price'] > 0) (+{{ pkr($item['price']) }})@endif</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-row form-row--full">
                            <label class="field-label" for="frosting">Frosting</label>
                            <select id="frosting" class="field-input" name="frosting" data-builder-price>
                                @foreach ($b['frostings'] as $item)
                                    <option value="{{ $item['id'] }}" data-price="{{ $item['price'] }}">{{ $item['label'] }}@if($item['price'] > 0) (+{{ pkr($item['price']) }})@endif</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </section>

                @if (! empty($b['diets']))
                    <section class="builder-block">
                        <h2>5. Dietary</h2>
                        <div class="check-grid">
                            @foreach ($b['diets'] as $diet)
                                <label class="check-tile">
                                    <input type="checkbox" name="diets[]" value="{{ $diet['id'] }}" data-price="{{ $diet['price'] }}" data-builder-price>
                                    <span>{{ $diet['label'] }} <em>+{{ pkr($diet['price']) }}</em></span>
                                </label>
                            @endforeach
                        </div>
                    </section>
                @endif

                <section class="builder-block">
                    <h2>6. Message &amp; colour</h2>
                    <div class="form-grid">
                        <div class="form-row form-row--full">
                            <label class="field-label" for="message">Text on cake</label>
                            <input id="message" class="field-input" type="text" name="message" maxlength="60" placeholder="Happy Birthday Ayaan">
                        </div>
                        <div class="form-row form-row--full">
                            <p class="field-label" id="icing-colour-label">Icing colour</p>
                            <div class="color-row" role="radiogroup" aria-labelledby="icing-colour-label" data-icing-colors>
                                @foreach ($b['colors'] as $color)
                                    <label class="color-swatch" title="{{ $color['label'] }}">
                                        <input
                                            type="radio"
                                            name="color"
                                            value="{{ $color['id'] }}"
                                            data-color-hex="{{ $color['hex'] }}"
                                            @checked($loop->first)
                                        >
                                        <span style="--swatch: {{ $color['hex'] }}"></span>
                                    </label>
                                @endforeach
                                <label class="color-swatch color-swatch--custom" title="Custom colour">
                                    <input type="radio" name="color" value="custom" data-color-custom-radio>
                                    <span class="color-swatch__custom" data-color-custom-preview style="--swatch: #c45c6a">
                                        <input
                                            type="color"
                                            name="color_custom"
                                            value="#c45c6a"
                                            data-color-picker
                                            aria-label="Pick a custom icing colour"
                                        >
                                    </span>
                                </label>
                            </div>
                            <input type="hidden" name="color_hex" value="{{ $b['colors'][0]['hex'] ?? '#f7f1e8' }}" data-color-hex-field>
                            <p class="color-hint">Choose a preset or tap the + swatch to pick any colour.</p>
                        </div>
                    </div>
                </section>

                <section class="builder-block">
                    <h2>7. Add-ons &amp; décor</h2>
                    <div class="addon-list">
                        @foreach ($b['addons'] as $addon)
                            @php
                                $isPerUnit = ($addon['billing'] ?? 'flat') === 'per_unit';
                                $unit = $addon['unit_label'] ?? 'item';
                            @endphp
                            @if ($isPerUnit)
                                <div class="addon-qty" data-addon-qty>
                                    <div class="addon-qty__copy">
                                        <strong>{{ $addon['label'] }}</strong>
                                        <span>{{ pkr($addon['price']) }} / {{ $unit ?: 'item' }}</span>
                                        @if (! empty($addon['hint']))
                                            <small>{{ $addon['hint'] }}</small>
                                        @endif
                                    </div>
                                    <div class="addon-qty__field">
                                        <label class="sr-only" for="addon-qty-{{ $addon['id'] }}">Quantity for {{ $addon['label'] }}</label>
                                        <x-qty-stepper
                                            :id="'addon-qty-'.$addon['id']"
                                            :name="'addon_qty['.$addon['id'].']'"
                                            :value="0"
                                            :min="0"
                                            :max="(int) ($addon['max_qty'] ?? 12)"
                                            data-builder-qty-wrap
                                            data-price="{{ $addon['price'] }}"
                                        />
                                    </div>
                                </div>
                            @else
                                <label class="check-tile check-tile--block">
                                    <input type="checkbox" name="addons[]" value="{{ $addon['id'] }}" data-price="{{ $addon['price'] }}" data-builder-price>
                                    <span>
                                        <strong>{{ $addon['label'] }}</strong>
                                        <em>+{{ pkr($addon['price']) }}</em>
                                        @if (! empty($addon['hint']))
                                            <small>{{ $addon['hint'] }}</small>
                                        @endif
                                    </span>
                                </label>
                            @endif
                        @endforeach
                    </div>
                </section>

                <section class="builder-block">
                    <h2>8. Notes for baker</h2>
                    <textarea class="field-input field-textarea" name="notes" rows="4" placeholder="Allergies, theme details, delivery notes…"></textarea>
                </section>
            </div>

            <aside class="builder-summary">
                <h2>Estimate</h2>
                <dl class="builder-totals">
                    <div><dt>Base</dt><dd data-sum-base>₨ 0</dd></div>
                    <div><dt>Extras</dt><dd data-sum-extras>₨ 0</dd></div>
                    <div class="builder-totals__total"><dt>Total</dt><dd data-sum-total>₨ 0</dd></div>
                </dl>
                <p class="builder-note">Final quote confirmed by bakery for complex designs.</p>
                <button class="btn btn--primary btn--block" type="submit" name="action" value="cart">Add to cart</button>
                <button class="btn btn--outline btn--block" type="submit" name="action" value="save">Save design</button>
            </aside>
        </form>
    </div>
</section>
@endsection

@push('scripts')
<script src="{{ asset('js/features.js') }}" defer></script>
@endpush
