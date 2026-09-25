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
    <link rel="stylesheet" href="{{ asset('css/dezato.css') }}?v={{ filemtime(public_path('css/dezato.css')) }}">
    <link rel="stylesheet" href="{{ asset('css/features.css') }}?v={{ filemtime(public_path('css/features.css')) }}">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}?v={{ filemtime(public_path('css/admin.css')) }}">
    <noscript>
        <style>
            .admin-menu-btn { display: none !important; }
            .admin-shell { grid-template-columns: minmax(0, 1fr) !important; }
            .admin-sidebar {
                position: static !important;
                transform: none !important;
                visibility: visible !important;
                width: auto !important;
                height: auto !important;
                grid-column: 1 !important;
            }
            .admin-main { grid-column: 1 !important; margin-left: 0 !important; width: 100% !important; }
            .admin-backdrop { display: none !important; }
        </style>
    </noscript>
</head>
<body class="admin-body">
    <div class="admin-shell" data-admin-shell>
        <button type="button" class="admin-backdrop" data-admin-backdrop data-admin-close tabindex="-1" aria-hidden="true" aria-label="Close menu"></button>

        <aside class="admin-sidebar" id="admin-sidebar" aria-label="Admin menu">
            <div class="admin-sidebar__head">
                <a class="admin-brand" href="{{ route('admin.dashboard') }}">
                    <img src="{{ asset('images/brand/logo-mark.svg') }}" width="32" height="32" alt="">
                    <span>Dezato Admin</span>
                </a>
                <button type="button" class="admin-menu-btn admin-menu-btn--close" data-admin-close aria-label="Close menu">
                    <svg viewBox="0 0 24 24" width="22" height="22" aria-hidden="true">
                        <path d="M6 6l12 12M18 6 6 18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </button>
            </div>

            <nav>
                @foreach ($adminNav as $group)
                    <div class="admin-nav-group">
                        @if (! empty($group['label']))
                            <p class="admin-nav-label">{{ $group['label'] }}</p>
                        @endif
                        @foreach ($group['items'] as $item)
                            <a
                                href="{{ route($item['route']) }}"
                                @class(['is-active' => ($active ?? '') === $item['id']])
                                @if (($active ?? '') === $item['id']) aria-current="page" @endif
                            >
                                {{ $item['label'] }}
                            </a>
                        @endforeach
                    </div>
                @endforeach
            </nav>

            <div class="admin-sidebar__tools">
                <a class="admin-sidebar__store" href="{{ route('home') }}" target="_blank" rel="noopener">View website</a>
                <form class="admin-sidebar__logout" method="post" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit">Sign out</button>
                </form>
            </div>
        </aside>

        <div class="admin-main">
            <header class="admin-top">
                <div class="admin-top__bar">
                    <button
                        type="button"
                        class="admin-menu-btn"
                        data-admin-open
                        aria-controls="admin-sidebar"
                        aria-expanded="false"
                        aria-label="Open menu"
                    >
                        <svg viewBox="0 0 24 24" width="22" height="22" aria-hidden="true">
                            <path d="M4 7h16M4 12h16M4 17h16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                        <span>Menu</span>
                    </button>
                    <h1>{{ $heading }}</h1>
                </div>
            </header>

            @if (session('status'))
                <p class="flash flash--success" role="status">{{ session('status') }}</p>
            @endif

            @if (isset($errors) && $errors->any())
                <div class="flash flash--error" role="alert">
                    <p><strong>Please fix the following:</strong></p>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </div>
    </div>
    <script src="{{ asset('js/admin.js') }}" defer></script>
</body>
</html>
