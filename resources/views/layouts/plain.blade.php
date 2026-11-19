<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.head.seo')
    @include('partials.head.brand')
    @yield('head')
    @stack('head')
    @vite(['resources/css/frontend/frontend.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="@yield('body_class', '')">
    @yield('before_content')
    <main id="@yield('main_id', 'plan-main')" class="@yield('main_class', 'main')">
        @if (session('success'))
            <div class="container my-3"><div class="alert alert-success" role="status">{{ session('success') }}</div></div>
        @endif
        @yield('content')
    </main>
    @yield('after_content')
    @stack('scripts')
    @if (app()->getLocale() !== config('languages.default'))
        @include('partials.google-translate')
    @endif
</body>
</html>
