@props(['socialProviders' => []])

@if (! empty($socialProviders))
    <div class="auth-social" aria-label="Social sign-in">
        @foreach ($socialProviders as $provider)
            <a
                class="btn btn--outline btn--block auth-social__btn auth-social__btn--{{ $provider['id'] }}"
                href="{{ route('auth.social.redirect', $provider['id']) }}"
            >
                Continue with {{ $provider['label'] }}
            </a>
        @endforeach
    </div>
    <p class="auth-divider"><span>or</span></p>
@endif
