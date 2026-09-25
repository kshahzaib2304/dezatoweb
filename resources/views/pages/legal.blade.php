@extends('layouts.app')

@section('content')
    <x-page-hero page="legal" :title="$pageTitle" />

    <section class="section-block">
        <div class="container legal-page" data-reveal>
            <article class="legal-prose">
                @foreach (preg_split("/\n\s*\n/", trim($body)) as $paragraph)
                    @php $lines = preg_split("/\r\n|\r|\n/", trim($paragraph)); @endphp
                    @if (count($lines) === 1)
                        <p>{{ $lines[0] }}</p>
                    @else
                        <p><strong>{{ $lines[0] }}</strong></p>
                        @foreach (array_slice($lines, 1) as $line)
                            @if (trim($line) !== '')
                                <p>{{ $line }}</p>
                            @endif
                        @endforeach
                    @endif
                @endforeach
            </article>
        </div>
    </section>
@endsection
