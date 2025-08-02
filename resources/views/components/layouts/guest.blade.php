@props(['pageName' => ''])

    <!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" :class="{ 'dark': $store.darkMode.on }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name') }}{{ $pageName ? ' - ' . $pageName : '' }}</title>

    <!-- Favicons -->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Scripts & Styles -->
    <style>[x-cloak] {
            display: none !important;
        }</style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
    @livewireStyles
</head>
<body
    class="font-sans antialiased text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-900 transition-colors duration-200"
    x-data="{ mobileMenuOpen: false }"
    x-cloak
>
<!-- Page wrapper -->
<div class="min-h-screen flex flex-col">
    <!-- Header -->
    <header
        class="bg-white/80 dark:bg-gray-900/80 backdrop-blur-md border-b border-gray-200 dark:border-gray-700 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center space-x-2">
                        <x-logo class="h-8 w-auto"/>
                        <span class="text-xl font-bold text-gray-900 dark:text-white">
                        {{ config('app.name') }}
                    </span>
                    </a>
                </div>

                <!-- Navigation -->
                <nav class="hidden md:flex items-center space-x-8">
                    <a href="{{route('home')}}"
                       class="text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition-colors duration-200">
                        Home
                    </a>
                    <a href="{{route('home')}}"
                       class="text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition-colors duration-200">
                        About
                    </a>
                    <a href="{{route('home')}}"
                       class="text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition-colors duration-200">
                        Features
                    </a>
                    <a href="{{route('branding.contact')}}"
                       class="text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition-colors duration-200">
                        Contact
                    </a>
                </nav>

                <!-- Right side actions -->
                <div class="flex items-center space-x-4">
                    <!-- Dark mode toggle -->
                    <button
                        type="button"
                        class="flex items-center justify-center w-8 h-8 rounded-lg bg-white/20 dark:bg-gray-800/50 backdrop-blur-sm transition-all duration-200 hover:bg-white/30 dark:hover:bg-gray-700/50"
                        @click="$store.darkMode.toggle()"
                        x-data
                        :title="$store.darkMode.on ? 'Switch to light mode' : 'Switch to dark mode'"
                    >
                        <!-- Sun icon for light mode -->
                        <svg
                            x-show="!$store.darkMode.on"
                            class="w-4 h-4 text-gray-700 dark:text-gray-300"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>

                        <!-- Moon icon for dark mode -->
                        <svg
                            x-show="$store.darkMode.on"
                            class="w-4 h-4 text-gray-700 dark:text-gray-300"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                        </svg>
                    </button>

                    <!-- Auth buttons -->
                    <div class="hidden md:flex items-center space-x-3">
                        <a href="{{ route('sign-in') }}"
                           class="text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition-colors duration-200">
                            Sign In
                        </a>
                        <a href="{{ route('sign-up') }}"
                           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200">
                            Sign Up
                        </a>
                    </div>

                    <!-- Mobile menu button -->
                    <button
                        type="button"
                        class="md:hidden p-2 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800"
                        @click="mobileMenuOpen = !mobileMenuOpen"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Mobile menu -->
            <div
                x-show="mobileMenuOpen"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 transform scale-95"
                x-transition:enter-end="opacity-100 transform scale-100"
                x-transition:leave="transition ease-in duration-75"
                x-transition:leave-start="opacity-100 transform scale-100"
                x-transition:leave-end="opacity-0 transform scale-95"
                class="md:hidden py-4 border-t border-gray-200 dark:border-gray-700"
            >
                <div class="flex flex-col space-y-3">
                    <a href="{{route('home')}}"
                       class="text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition-colors duration-200">
                        Home
                    </a>
                    <a href="{{route('home')}}"
                       class="text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition-colors duration-200">
                        About
                    </a>
                    <a href="{{route('home')}}"
                       class="text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition-colors duration-200">
                        Features
                    </a>
                    <a href="{{route('branding.contact')}}"
                       class="text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition-colors duration-200">
                        Contact
                    </a>
                    <div class="flex flex-col space-y-2 pt-3 border-t border-gray-200 dark:border-gray-700">
                        <a href="{{ route('sign-in') }}"
                           class="text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition-colors duration-200">
                            Sign In
                        </a>
                        <a href="{{ route('sign-up') }}"
                           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 text-center">
                            Sign Up
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main content -->
    <main class="flex-grow">
        {{ $slot }}
    </main>

    <!-- Footer -->
<x-layouts.footer />
</div>

<!-- Dark mode initialization script -->
<script>
    // Check for dark mode preference and apply immediately to prevent flash
    if (localStorage.getItem('dark-mode') === 'true' ||
        (localStorage.getItem('dark-mode') === null &&
            window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        document.documentElement.classList.add('dark');
        document.documentElement.style.colorScheme = 'dark';
    } else {
        document.documentElement.classList.remove('dark');
        document.documentElement.style.colorScheme = 'light';
    }
</script>

<!-- Alpine.js stores -->
<script>
    document.addEventListener('alpine:initializing', () => {
        // Dark mode store (same as your authenticated app)
        Alpine.store('darkMode', {
            on: false,

            init() {
                this.on = localStorage.getItem('dark-mode') === 'true' ||
                    (localStorage.getItem('dark-mode') === null &&
                        window.matchMedia('(prefers-color-scheme: dark)').matches);

                // Watch for system theme changes
                window.matchMedia('(prefers-color-scheme: dark)')
                    .addEventListener('change', e => {
                        if (localStorage.getItem('dark-mode') === null) {
                            this.toggle(e.matches);
                        }
                    });
            },

            toggle(value = null) {
                this.on = value !== null ? value : !this.on;
                localStorage.setItem('dark-mode', this.on);
                this.updateDOM();
            },

            updateDOM() {
                document.documentElement.classList.toggle('dark', this.on);
                document.documentElement.style.colorScheme = this.on ? 'dark' : 'light';
            }
        });
    });
</script>

@livewireScriptConfig
</body>
</html>
