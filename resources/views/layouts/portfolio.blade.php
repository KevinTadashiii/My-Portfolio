<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Primary SEO --}}
    <title>{{ config('portfolio.seo.title') }}</title>
    <meta name="title" content="{{ config('portfolio.seo.title') }}">
    <meta name="description" content="{{ config('portfolio.seo.description') }}">
    <meta name="keywords" content="{{ implode(', ', config('portfolio.seo.keywords')) }}">
    <meta name="author" content="{{ config('portfolio.profile.name') }}">
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
    <meta name="googlebot" content="index, follow, max-image-preview:large, max-snippet:-1">
    <link rel="canonical" href="{{ url('/') }}">

    {{-- Theme / PWA --}}
    <meta name="theme-color" content="#F5F0E8">
    <meta name="color-scheme" content="light">

    {{-- Open Graph (Facebook, WhatsApp, LinkedIn, iMessage) --}}
    <meta property="og:type" content="{{ config('portfolio.seo.type') }}">
    <meta property="og:site_name" content="{{ config('portfolio.seo.site_name') }}">
    <meta property="og:title" content="{{ config('portfolio.seo.title') }}">
    <meta property="og:description" content="{{ config('portfolio.seo.description') }}">
    <meta property="og:url" content="{{ url('/') }}">
    <meta property="og:image" content="{{ asset('og-image.png') }}">
    <meta property="og:image:type" content="image/png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="{{ config('portfolio.seo.image_alt') }}">
    <meta property="og:locale" content="{{ config('portfolio.seo.locale') }}">

    {{-- Twitter --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ config('portfolio.seo.title') }}">
    <meta name="twitter:description" content="{{ config('portfolio.seo.description') }}">
    <meta name="twitter:image" content="{{ asset('og-image.png') }}">
    <meta name="twitter:image:alt" content="{{ config('portfolio.seo.image_alt') }}">
    <meta name="twitter:site" content="{{ config('portfolio.seo.twitter_site') }}">

    {{-- Favicons --}}
    <link rel="icon" href="/favicon.svg" type="image/svg+xml" sizes="16x16">
    <link rel="icon" href="/favicon-32.svg" type="image/svg+xml" sizes="32x32">
    <link rel="icon" href="/favicon.ico" sizes="16x16 32x32 48x48" type="image/x-icon">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png" sizes="180x180">
    <link rel="manifest" href="/site.webmanifest">

    {{-- Fonts: non-blocking load (SEO performance / LCP) --}}
    <link rel="preconnect" href="https://fonts.bunny.net" crossorigin>
    <link rel="dns-prefetch" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=lora:400,500,600,700|inter:400,500,600|jetbrains-mono:400,500,700|caveat:400,600&display=swap" rel="stylesheet" media="print" onload="this.media='all'">
    <noscript><link href="https://fonts.bunny.net/css?family=lora:400,500,600,700|inter:400,500,600|jetbrains-mono:400,500,700|caveat:400,600&display=swap" rel="stylesheet"></noscript>

    {{-- Structured data: Person + WebSite + WebPage (@graph) --}}
    @php
        $structuredData = [
            '@context' => 'https://schema.org',
            '@graph' => [
                [
                    '@type' => 'Person',
                    '@id' => url('/#person'),
                    'name' => config('portfolio.profile.name'),
                    'alternateName' => config('portfolio.profile.last_name'),
                    'jobTitle' => 'Software Engineering Student',
                    'description' => config('portfolio.seo.description'),
                    'email' => config('portfolio.profile.email'),
                    'url' => url('/'),
                    'image' => asset('og-image.png'),
                    'address' => [
                        '@type' => 'PostalAddress',
                        'addressLocality' => config('portfolio.profile.city'),
                        'addressCountry' => 'ID',
                    ],
                    'knowsAbout' => [
                        'C', 'C++', 'Python', 'JavaScript', 'TypeScript', 'Laravel',
                        'Linux', 'Docker', 'SQL', 'Git', 'AI/ML',
                    ],
                    'sameAs' => [
                        config('portfolio.profile.github'),
                        'https://www.instagram.com/bayabaytjn_',
                    ],
                ],
                [
                    '@type' => 'WebSite',
                    '@id' => url('/#website'),
                    'url' => url('/'),
                    'name' => config('portfolio.seo.site_name'),
                    'publisher' => ['@id' => url('/#person')],
                    'inLanguage' => 'en',
                ],
                [
                    '@type' => 'WebPage',
                    '@id' => url('/'),
                    'url' => url('/'),
                    'name' => config('portfolio.seo.title'),
                    'description' => config('portfolio.seo.description'),
                    'inLanguage' => 'en',
                    'isPartOf' => ['@id' => url('/#website')],
                    'about' => ['@id' => url('/#person')],
                ],
                [
                    '@type' => 'ProfilePage',
                    '@id' => url('/#profile'),
                    'url' => url('/'),
                    'mainEntity' => ['@id' => url('/#person')],
                ],
            ],
        ];
    @endphp
    <script type="application/ld+json">
    @json($structuredData, 320)
    </script>

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
        <span>&copy; {{ date('Y') }} {{ config('portfolio.profile.name') }} — {{ config('portfolio.profile.location') }}</span>
        <span>Built with Laravel And ❤️ from the terminal</span>
        <nav aria-label="Footer">
            <a href="#about">Bio</a> ·
            <a href="#projects">Projects</a> ·
            <a href="#skills">Skills</a> ·
            <a href="{{ config('portfolio.profile.github') }}" rel="me noopener noreferrer" target="_blank">GitHub</a> ·
            <a href="{{ route('sitemap') }}">Sitemap</a> ·
            <a href="#top">Back to the desk &uarr;</a>
        </nav>
    </footer>

    @stack('scripts')
</body>
</html>
