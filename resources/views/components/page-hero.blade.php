@props([
    'page' => null,
    'eyebrow' => null,
    'title' => null,
    'text' => null,
    'image' => null,
    'compact' => null,
])

@php
    if (is_string($page) && $page !== '') {
        $hero = \App\Support\PageHeroes::get($page);
        $eyebrow = $eyebrow ?? $hero['eyebrow'];
        $title = $title ?? $hero['title'];
        $text = $text ?? $hero['text'];
        $image = $image ?? $hero['image'];
        $compact = $compact ?? $hero['compact'];
    }

    $compact = (bool) $compact;
@endphp

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
