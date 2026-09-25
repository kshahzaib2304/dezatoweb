<footer class="site-footer site-footer--brand" id="contact">
    <div class="container footer-brand">
        <div class="footer-brand__intro">
            <img src="{{ asset($brandLogoMark ?? 'images/brand/logo-mark.svg') }}" width="48" height="48" alt="{{ $brandName ?? 'Dezato Cake House' }}">
            <div>
                <strong>{{ $brandName ?? 'Dezato Cake House' }}</strong>
                <p>{{ $brandTagline ?? 'Cakes, cupcakes, eclairs & more - baked fresh in Karachi.' }}</p>
            </div>
        </div>

        <div class="footer-brand__cols">
            <div>
                <h3>Shop</h3>
                <a href="{{ route('menu') }}">Menu</a>
                @foreach (($footerCategories ?? []) as $category)
                    <a href="{{ route('menu', ['category' => $category['id']]) }}">{{ $category['label'] }}</a>
                @endforeach
            </div>
            <div>
                <h3>Company</h3>
                <a href="{{ route('about') }}">About Us</a>
                <a href="{{ route('services') }}">Our Services</a>
                <a href="{{ route('customization') }}">Cake Customization</a>
                <a href="{{ route('locations') }}">Locations</a>
            </div>
            <div>
                <h3>Contact</h3>
                @if (! empty($bakeryPhone))
                    <a href="tel:{{ preg_replace('/\s+/', '', $bakeryPhone) }}">{{ $bakeryPhone }}</a>
                @endif
                @if (! empty($bakeryWhatsAppUrl))
                    <a href="{{ $bakeryWhatsAppUrl }}" target="_blank" rel="noopener noreferrer">WhatsApp</a>
                @endif
                @if (! empty($bakeryEmail))
                    <a href="mailto:{{ $bakeryEmail }}">{{ $bakeryEmail }}</a>
                @endif
                <button type="button" data-fulfillment-open data-fulfillment-method="pickup">Pickup</button>
                <button type="button" data-fulfillment-open data-fulfillment-method="delivery">Delivery</button>
            </div>
        </div>

        @if (! empty($socialLinks))
            <nav class="footer-brand__social" aria-label="Social media">
                @foreach ($socialLinks as $link)
                    <a href="{{ $link['url'] }}" target="_blank" rel="noopener noreferrer">{{ $link['label'] }}</a>
                @endforeach
            </nav>
        @endif
    </div>

    <div class="footer-brand__legal">
        <div class="container footer-brand__legal-inner">
            <p>&copy; {{ date('Y') }} {{ $brandName ?? 'Dezato Cake House' }}, Karachi</p>
            <nav class="footer-brand__legal-links" aria-label="Legal">
                <a href="{{ route('pages.privacy') }}">Privacy</a>
                <a href="{{ route('pages.terms') }}">Terms</a>
                <a href="{{ route('pages.faq') }}">FAQ</a>
                <a href="{{ route('home') }}#newsletter">Newsletter</a>
            </nav>
        </div>
    </div>
</footer>
