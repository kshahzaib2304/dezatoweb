@props([
    'paymentHint' => null,
    'scheduleNote' => null,
    'compact' => false,
    'context' => 'order',
])

@php
    $payments = \App\Support\PaymentMethods::forCheckout();
    $paymentLabels = collect($payments)->pluck('label')->filter()->values()->all();
    $whatsappPrefill = match ($context) {
        'product' => 'Hi Dezato, I have a question about a cake on your menu.',
        'cart' => 'Hi Dezato, I have a question about my cart.',
        'confirmation' => 'Hi Dezato, I have a question about my order.',
        default => 'Hi Dezato, I need help with my order.',
    };
    $whatsappUrl = \App\Support\BakeryProfile::whatsappUrl($whatsappPrefill);
    $phone = \App\Support\BakeryProfile::phone();
@endphp

<div {{ $attributes->class(['order-assurance', 'order-assurance--compact' => $compact]) }}>
    <ul class="order-assurance__list">
        @if (filled($paymentHint))
            <li>{{ $paymentHint }}</li>
        @endif
        @if ($paymentLabels !== [])
            <li>Pay with {{ implode(' · ', $paymentLabels) }}</li>
        @endif
        @if (filled($scheduleNote))
            <li>{{ $scheduleNote }}</li>
        @endif
        <li>Baked fresh in Karachi · Prices in PKR</li>
        @if ($whatsappUrl)
            <li>
                <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener noreferrer">Chat on WhatsApp</a>
                @if ($phone !== '')
                    <span class="order-assurance__sep" aria-hidden="true">·</span>
                    <a href="tel:{{ preg_replace('/\s+/', '', $phone) }}">{{ $phone }}</a>
                @endif
            </li>
        @elseif ($phone !== '')
            <li><a href="tel:{{ preg_replace('/\s+/', '', $phone) }}">Call {{ $phone }}</a></li>
        @endif
    </ul>
</div>
