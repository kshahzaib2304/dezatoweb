@props([
    'eyebrow' => null,
    'title',
    'text' => null,
    'image' => null,
    'compact' => false,
])

<section class="page-hero {{ $compact ? 'page-hero--compact' : '' }}" @if($image) style="--page-hero-image: url('{{ asset($image) }}')" @endif>
    <div class="page-hero__veil" aria-hidden="true"></div>
    <div class="container page-hero__inner">
        @if ($eyebrow)
            <p class="page-hero__eyebrow">{{ $eyebrow }}</p>
        @endif
        <h1 class="page-hero__title">{{ $title }}</h1>
        @if ($text)
            <p class="page-hero__text">{{ $text }}</p>
        @endif
        {{ $slot }}
    </div>
</section>
