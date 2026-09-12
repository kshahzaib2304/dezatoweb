<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#3a2438">
    <title>{{ $title ?? 'Dezato Admin' }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/brand/logo-mark.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,600;9..40,700&family=Fraunces:opsz,wght@9..144,500;9..144,600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/dezato.css') }}">
    <link rel="stylesheet" href="{{ asset('css/features.css') }}">
</head>
<body class="admin-body">
    <div class="admin-shell">
        <aside class="admin-sidebar">
            <a class="admin-brand" href="{{ route('admin.dashboard') }}">
                <img src="{{ asset('images/brand/logo-mark.svg') }}" width="32" height="32" alt="">
                <span>Dezato Admin</span>
            </a>
            <nav aria-label="Admin">
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
            <a class="admin-sidebar__store" href="{{ route('home') }}">View storefront</a>
        </aside>
        <div class="admin-content">
            <header class="admin-top">
                <h1>{{ $heading }}</h1>
                <p class="admin-top__note">UI preview · data is mock until backend is wired</p>
            </header>
            @if (session('status'))
                <p class="flash" role="status">{{ session('status') }}</p>
            @endif
            @yield('content')
        </div>
    </div>
</body>
</html>
