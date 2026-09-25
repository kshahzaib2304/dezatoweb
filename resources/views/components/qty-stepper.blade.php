@props([
    'name' => 'quantity',
    'id' => null,
    'value' => 1,
    'min' => 1,
    'max' => 99,
    'disabled' => false,
    'form' => null,
])

@php
    $inputId = $id ?: 'qty-'.uniqid();
    $min = (int) $min;
    $max = max($min, (int) $max);
    $value = max($min, min($max, (int) $value));
@endphp

<div
    {{ $attributes->class(['qty-stepper'])->merge(['data-qty-stepper' => true, 'data-min' => $min, 'data-max' => $max]) }}
    @if ($disabled) data-disabled="1" @endif
>
    <button
        class="qty-stepper__btn"
        type="button"
        data-qty-dec
        aria-label="Decrease quantity"
        @disabled($disabled || $value <= $min)
    >−</button>
    <input
        id="{{ $inputId }}"
        class="qty-stepper__input"
        type="number"
        name="{{ $name }}"
        value="{{ $value }}"
        min="{{ $min }}"
        max="{{ $max }}"
        inputmode="numeric"
        data-qty-input
        @if ($form) form="{{ $form }}" @endif
        @disabled($disabled)
    >
    <button
        class="qty-stepper__btn"
        type="button"
        data-qty-inc
        aria-label="Increase quantity"
        @disabled($disabled || $value >= $max)
    >+</button>
</div>
