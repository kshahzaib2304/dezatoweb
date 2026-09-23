{{-- Branded first-visit loader only. Return navigations skip via sessionStorage + html.skip-bake-loader. --}}
<div
    id="bake-loader"
    class="bake-loader"
    role="status"
    aria-live="polite"
    aria-busy="true"
    aria-label="Loading Dezato Cake House"
>
    <div class="bake-loader__stage" aria-hidden="true">
        <img
            class="bake-loader__media"
            src="{{ asset('images/brand/loader-cake.png') }}"
            alt=""
            width="180"
            height="180"
            decoding="async"
        >
    </div>
    <p class="bake-loader__label">Baking something sweet…</p>
</div>
