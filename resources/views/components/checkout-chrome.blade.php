@php
    $brandShortName = $brandShortName ?? 'Dezato';
    $brandLogoMark = $brandLogoMark ?? \App\Support\SiteBrand::logoMark();
@endphp

<header class="checkout-chrome" role="banner">
    <div class="container checkout-chrome__inner">
        <a class="checkout-chrome__brand" href="{{ route('home') }}" aria-label="{{ $brandName ?? 'Dezato Cake House' }} home">
            <img src="{{ asset($brandLogoMark) }}" width="32" height="32" alt="">
            <span>{{ $brandShortName }}</span>
        </a>
        <a class="checkout-chrome__back" href="{{ route('cart.show') }}">Back to cart</a>
    </div>
</header>
