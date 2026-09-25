@props([
    'options' => null,
])

@php
    $rows = \App\Support\CartExtras::rows(is_array($options) ? $options : null);
@endphp

@if ($rows !== [])
    <ul {{ $attributes->class(['cart-extras']) }}>
        @foreach ($rows as $row)
            <li class="cart-extras__row">
                <span class="cart-extras__copy">
                    @if ($row['meta'])
                        <span class="cart-extras__meta">{{ $row['meta'] }}</span>
                    @endif
                    <span class="cart-extras__label">{{ $row['label'] }}</span>
                </span>
                @if ($row['amount'] !== null)
                    <span class="cart-extras__amount">+{{ pkr($row['amount']) }}</span>
                @endif
            </li>
        @endforeach
    </ul>
@endif
