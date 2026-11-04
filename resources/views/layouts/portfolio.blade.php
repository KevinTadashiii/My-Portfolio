<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#F5F0E8">
    <meta name="description" content="{{ config('portfolio.profile.name') }} — {{ config('portfolio.profile.role') }} in {{ config('portfolio.profile.location') }}. A student desk wired to a terminal.">

    <title>{{ config('portfolio.profile.last_name') }} — {{ config('portfolio.brand.name') }}</title>

    <link rel="icon" href="/favicon.svg" type="image/svg+xml" sizes="16x16">
    <link rel="icon" href="/favicon-32.svg" type="image/svg+xml" sizes="32x32">
    <link rel="icon" href="/favicon.ico" sizes="16x16 32x32 48x48" type="image/x-icon">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=lora:400,500,600,700|inter:400,500,600|jetbrains-mono:400,500,700|caveat:400,600&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-w-[320px] overflow-x-hidden antialiased">
    <a class="skip-link" href="#main">Skip to content</a>

    <header class="site-header">
        <div class="site-header__rail" aria-hidden="true"></div>
        <div class="site-header__bar">
            <a class="brand" href="#top" aria-label="{{ config('portfolio.profile.name') }} — back to the top of the desk">
                <span class="brand__mark" aria-hidden="true"></span>
                <span class="brand__text">
                    <span class="brand__name">{{ config('portfolio.profile.name') }}</span>
                    <span class="brand__sub">{{ config('portfolio.profile.handle') }}</span>
                </span>
            </a>

            <nav class="site-nav" aria-label="Sections">
                <a href="#about"><span class="site-nav__index">01</span><span class="site-nav__label">Bio</span></a>
                <a href="#projects"><span class="site-nav__index">02</span><span class="site-nav__label">Projects</span></a>
                <a href="#skills"><span class="site-nav__index">03</span><span class="site-nav__label">Skills</span></a>
                <a href="#journey"><span class="site-nav__index">04</span><span class="site-nav__label">Journey</span></a>
                <a href="#contact"><span class="site-nav__index">05</span><span class="site-nav__label">Contact</span></a>
            </nav>
            <span class="site-nav__marker" aria-hidden="true"></span>
        </div>

        <nav class="site-nav-mobile" aria-label="Quick links">
            <a href="#about"><span class="site-nav-mobile__index">01</span> Bio</a>
            <a href="#projects"><span class="site-nav-mobile__index">02</span> Projects</a>
            <a href="#skills"><span class="site-nav-mobile__index">03</span> Skills</a>
            <a href="#journey"><span class="site-nav-mobile__index">04</span> Journey</a>
            <a href="#contact"><span class="site-nav-mobile__index">05</span> Contact</a>
        </nav>
    </header>

    <main id="main" tabindex="-1">
        @yield('content')
    </main>

    <footer class="site-footer" aria-label="Site footer">
        <span>&copy; {{ date('Y') }} {{ config('portfolio.profile.name') }}</span>
        <span>Built with Laravel And ❤️ from the terminal</span>
        <a href="#top">Back to the desk &uarr;</a>
    </footer>

    @stack('scripts')
</body>
</html>
