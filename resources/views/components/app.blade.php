@props([
    {{-- ── Page identity ───────────────────────────────────────────────────────── --}}
    'pageName'      => '',
    'title'         => null,
    'description'   => null,
    'keywords'      => null,
    'canonical'     => null,

    {{-- ── Indexing ─────────────────────────────────────────────────────────────── --}}
    'robots'        => 'index, follow',
    'noindex'       => false,

    {{-- ── Open Graph ─────────────────────────────────────────────────────────── --}}
    'ogType'        => 'website',
    'ogTitle'       => null,
    'ogDescription' => null,
    'ogImage'       => null,

    {{-- ── Twitter Card ───────────────────────────────────────────────────────── --}}
    'twitterCard'        => 'summary_large_image',
    'twitterTitle'       => null,
    'twitterDescription' => null,
])

@php
    $siteName            = config('app.name');
    $resolvedTitle       = $title
                            ?? ($siteName . ($pageName ? ' — ' . $pageName : ''));
    $resolvedDescription = $description
                            ?? config('seo.description', $siteName . ' — ' . config('seo.tagline', ''));
    $resolvedCanonical   = $canonical ?? url()->current();
    $effectiveRobots     = $noindex ? 'noindex, nofollow' : $robots;

    $ogImageRaw          = $ogImage ?? config('seo.og_image');
    $resolvedOgImage     = $ogImageRaw
                            ? (Str::startsWith($ogImageRaw, ['http://', 'https://']) ? $ogImageRaw : asset($ogImageRaw))
                            : null;

    $resolvedOgTitle          = $ogTitle          ?? $resolvedTitle;
    $resolvedOgDescription    = $ogDescription    ?? $resolvedDescription;
    $resolvedTwitterTitle     = $twitterTitle     ?? $resolvedTitle;
    $resolvedTwitterDescription = $twitterDescription ?? $resolvedDescription;

    $twitterHandle = config('seo.twitter.handle', '');
    $locale        = config('seo.locale', 'en_US');
    $siteUrl       = config('app.url');
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- ── Title ──────────────────────────────────────────────────────────────── --}}
    <title>{{ $resolvedTitle }}</title>

    {{-- ── Core SEO ─────────────────────────────────────────────────────────── --}}
    @if($resolvedDescription)
    <meta name="description" content="{{ Str::limit($resolvedDescription, 160) }}">
    @endif
    @if($keywords ?? config('seo.keywords'))
    <meta name="keywords" content="{{ $keywords ?? config('seo.keywords') }}">
    @endif
    <meta name="robots" content="{{ $effectiveRobots }}">
    <link rel="canonical" href="{{ $resolvedCanonical }}">

    {{-- ── Open Graph ─────────────────────────────────────────────────────────── --}}
    <meta property="og:type"        content="{{ $ogType }}">
    <meta property="og:site_name"   content="{{ $siteName }}">
    <meta property="og:locale"      content="{{ $locale }}">
    <meta property="og:url"         content="{{ $resolvedCanonical }}">
    <meta property="og:title"       content="{{ Str::limit($resolvedOgTitle, 70) }}">
    @if($resolvedOgDescription)
    <meta property="og:description" content="{{ Str::limit($resolvedOgDescription, 200) }}">
    @endif
    @if($resolvedOgImage)
    <meta property="og:image"        content="{{ $resolvedOgImage }}">
    <meta property="og:image:width"  content="{{ config('seo.og_image_width',  1200) }}">
    <meta property="og:image:height" content="{{ config('seo.og_image_height',  630) }}">
    <meta property="og:image:alt"    content="{{ $resolvedOgTitle }}">
    @endif

    {{-- ── Twitter Card ───────────────────────────────────────────────────────── --}}
    <meta name="twitter:card"        content="{{ $twitterCard }}">
    @if($twitterHandle)
    <meta name="twitter:site"        content="{{ $twitterHandle }}">
    @endif
    <meta name="twitter:title"       content="{{ Str::limit($resolvedTwitterTitle, 70) }}">
    @if($resolvedTwitterDescription)
    <meta name="twitter:description" content="{{ Str::limit($resolvedTwitterDescription, 200) }}">
    @endif
    @if($resolvedOgImage)
    <meta name="twitter:image"       content="{{ $resolvedOgImage }}">
    <meta name="twitter:image:alt"   content="{{ $resolvedTwitterTitle }}">
    @endif

    {{-- ── Theme colour ────────────────────────────────────────────────────────── --}}
    <meta name="theme-color" content="{{ config('seo.theme_color',      '#3B82F6') }}"
          media="(prefers-color-scheme: light)">
    <meta name="theme-color" content="{{ config('seo.theme_color_dark', '#1e293b') }}"
          media="(prefers-color-scheme: dark)">

    {{-- ── Favicons & Web App Manifest ───────────────────────────────────────── --}}
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">

    {{-- ── JSON-LD ─────────────────────────────────────────────────────────────
         Baseline WebSite schema emitted on every page.  Exam-specific pages
         (results, certificates) can push richer schemas via @stack('schema').
    ─────────────────────────────────────────────────────────────────────────── --}}
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "WebSite",
        "name":        "{{ $siteName }}",
        "url":         "{{ $siteUrl }}",
        "description": "{{ addslashes($resolvedDescription) }}"
    }
    </script>
    @stack('schema')

    {{-- ── Fonts ──────────────────────────────────────────────────────────────── --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="dns-prefetch" href="https://rsms.me">
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
    <link href="https://fonts.googleapis.com/css2?family=Exo:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

    {{-- ── App assets ─────────────────────────────────────────────────────────── --}}
    <style>[x-cloak] { display: none !important; }</style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
    @livewireStyles
</head>
<body {{ $attributes->merge(['class' => 'antialiased bg-gray-100']) }}>
    {{ $slot }}
    @livewireScriptConfig
    @stack('scripts')
</body>
</html>