<footer class="site-footer site-footer--brand" id="contact">
    <div class="container footer-brand">
        <div class="footer-brand__intro">
            <img src="{{ asset('images/brand/logo-mark.svg') }}" width="48" height="48" alt="">
            <div>
                <strong>Dezato Cake House</strong>
                <p>Cakes, cupcakes, eclairs &amp; more - baked fresh in Karachi.</p>
            </div>
        </div>

        <div class="footer-brand__cols">
            <div>
                <h3>Shop</h3>
                <a href="{{ route('menu') }}">Menu</a>
                <a href="{{ route('menu', ['category' => 'cakes']) }}">Cakes 2.5 lbs</a>
                <a href="{{ route('menu', ['category' => 'cupcakes']) }}">Cupcakes</a>
                <a href="{{ route('menu', ['category' => 'cheesecakes']) }}">Cheesecakes</a>
            </div>
            <div>
                <h3>Company</h3>
                <a href="{{ route('about') }}">About Us</a>
                <a href="{{ route('services') }}">Our Services</a>
                <a href="{{ route('customization') }}">Cake Customization</a>
                <a href="{{ route('locations') }}">Locations</a>
            </div>
            <div>
                <h3>Order</h3>
                <button type="button" data-fulfillment-open data-fulfillment-method="pickup">Pickup</button>
                <button type="button" data-fulfillment-open data-fulfillment-method="delivery">Delivery</button>
                <button type="button" data-fulfillment-open data-fulfillment-method="shipping">Courier</button>
            </div>
        </div>
    </div>

    <div class="footer-brand__legal">
        <div class="container footer-brand__legal-inner">
            <p>&copy; {{ date('Y') }} Dezato Cake House, Karachi</p>
            <a href="{{ route('home') }}#newsletter">Newsletter</a>
        </div>
    </div>
</footer>
