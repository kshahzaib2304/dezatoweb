@extends('layouts.app')

@section('content')
<div class="account-shell">
    <div class="container account-layout">
        <aside class="account-nav" aria-label="Account">
            <p class="account-nav__eyebrow">My account</p>
            <nav>
                @foreach ($nav as $item)
                    <a
                        href="{{ route($item['route']) }}"
                        class="{{ $active === $item['id'] ? 'is-active' : '' }}"
                        @if ($active === $item['id']) aria-current="page" @endif
                    >
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </nav>
            <a class="account-nav__out" href="{{ route('login') }}">Sign out (UI)</a>
        </aside>
        <div class="account-main">
            @if (session('status'))
                <p class="flash" role="status">{{ session('status') }}</p>
            @endif
            @yield('account')
        </div>
    </div>
</div>
@endsection
