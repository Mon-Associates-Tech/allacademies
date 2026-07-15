<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- PWA Meta Tags --}}
    <meta name="theme-color" content="#3b82f6">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Examination Hub">
    <link rel="manifest" href="/manifest.json">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">

    <title>{{ config('app.name') }} - Examination</title>

    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">

    <style>
        [x-cloak] { display: none !important; }
    </style>

    <script>
        if (localStorage.getItem('dark-mode') === 'true' ||
            (localStorage.getItem('dark-mode') === null &&
                window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])


    @livewireStyles

</head>
<body class="font-sans antialiased bg-gray-50 dark:bg-gray-900" x-data>
    {{ $slot }}

    <script>
        document.addEventListener('alpine:initializing', () => {
            Alpine.store('darkMode', {
                on: false,
                init() {
                    this.on = localStorage.getItem('dark-mode') === 'true' ||
                        (localStorage.getItem('dark-mode') === null &&
                            window.matchMedia('(prefers-color-scheme: dark)').matches);
                },
                toggle(value = null) {
                    this.on = value !== null ? value : !this.on;
                    localStorage.setItem('dark-mode', this.on);
                    document.documentElement.classList.toggle('dark', this.on);
                }
            });
        });
    </script>
    @stack('scripts')
    @livewireScriptConfig
</body>
</html>
