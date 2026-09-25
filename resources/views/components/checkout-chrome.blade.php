@php
    $brandShortName = $brandShortName ?? 'Dezato';
@endphp

<header class="checkout-chrome" role="banner">
    <div class="container checkout-chrome__inner">
        <a class="checkout-chrome__brand" href="{{ route('home') }}" aria-label="{{ $brandName ?? 'Dezato Cake House' }} home">
            <img src="{{ asset('images/brand/logo-mark.svg') }}" width="32" height="32" alt="">
            <span>{{ $brandShortName }}</span>
        </a>
        <a class="checkout-chrome__back" href="{{ route('cart.show') }}">Back to cart</a>
    </div>
</header>
