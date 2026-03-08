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
    <style>
        [x-cloak] {
            display: none !important;
        }

        /* Header animation styles */
        .header-blur {
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
        }

        .nav-item {
            position: relative;
            transition: all 0.3s ease;
        }

        .nav-item::after {
            content: '';
            position: absolute;
            bottom: -4px;
            left: 50%;
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, #3B82F6, #8B5CF6);
            transform: translateX(-50%);
            transition: width 0.3s ease;
            border-radius: 1px;
        }

        .nav-item:hover::after,
        .nav-item.active::after {
            width: 100%;
        }

        .mobile-menu-enter {
            animation: slideDown 0.3s ease-out forwards;
        }

        .mobile-menu-exit {
            animation: slideUp 0.3s ease-in forwards;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
                max-height: 0;
            }
            to {
                opacity: 1;
                transform: translateY(0);
                max-height: 500px;
            }
        }

        @keyframes slideUp {
            from {
                opacity: 1;
                transform: translateY(0);
                max-height: 500px;
            }
            to {
                opacity: 0;
                transform: translateY(-10px);
                max-height: 0;
            }
        }

        /* Header scroll effect */
        .header-scrolled {
            transform: translateY(0);
            box-shadow: 0 4px 20px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .header-hidden {
            transform: translateY(-100%);
        }

        /* Logo animation */
        .logo-container {
            transition: transform 0.3s ease;
        }

        .logo-container:hover {
            transform: scale(1.05);
        }

        /* CTA button enhancement */
        .cta-button {
            background: linear-gradient(135deg, #3B82F6 0%, #8B5CF6 100%);
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .cta-button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.2) 0%, rgba(255, 255, 255, 0.1) 100%);
            transition: left 0.5s ease;
        }

        .cta-button:hover::before {
            left: 100%;
        }

        .cta-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -3px rgba(59, 130, 246, 0.4);
        }

        /* Theme toggle enhancement */
        .theme-toggle {
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .theme-toggle:hover {
            transform: rotate(15deg) scale(1.1);
        }

        /* Mobile menu improvements */
        .mobile-nav-item {
            position: relative;
            padding: 12px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }

        .mobile-nav-item:hover {
            padding-left: 8px;
            color: #3B82F6;
        }

        .mobile-nav-item::before {
            content: '';
            position: absolute;
            left: -4px;
            top: 50%;
            width: 3px;
            height: 0;
            background: linear-gradient(135deg, #3B82F6, #8B5CF6);
            transform: translateY(-50%);
            transition: height 0.3s ease;
            border-radius: 2px;
        }

        .mobile-nav-item:hover::before {
            height: 100%;
        }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
    @livewireStyles
</head>
<body
    class="font-sans antialiased text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-900 transition-colors duration-200"
    x-data="{
        mobileMenuOpen: false,
        headerScrolled: false,
        lastScrollY: 0,
        headerHidden: false
    }"
    x-init="
        // Header scroll effect
        const updateHeader = () => {
            const currentScrollY = window.scrollY;
            headerScrolled = currentScrollY > 10;

            if (currentScrollY > 100) {
                if (currentScrollY > lastScrollY && currentScrollY > 300) {
                    headerHidden = true;
                } else {
                    headerHidden = false;
                }
            } else {
                headerHidden = false;
            }

            lastScrollY = currentScrollY;
        };

        window.addEventListener('scroll', updateHeader);
        updateHeader();
    "
    x-cloak
>
<!-- Page wrapper -->
<div class="min-h-screen flex flex-col">
    <!-- Header -->
    <header
        class="fixed top-0 left-0 right-0 z-50 transition-all duration-300 ease-in-out header-blur"
        :class="{
            'bg-white/95 dark:bg-gray-900/95 border-b border-gray-200/50 dark:border-gray-700/50 header-scrolled': headerScrolled,
            'bg-white/80 dark:bg-gray-900/80 border-b border-gray-200/30 dark:border-gray-700/30': !headerScrolled,
            'header-hidden': headerHidden
        }"
    >
        <!-- Top notification bar (optional) -->
        <div
            class="bg-gradient-to-r from-blue-600 to-purple-600 text-white text-center py-2 text-sm font-medium hidden lg:block">
            🎉 New: Advanced AI Assessment Tools Now Available!
            <a href="{{ route('branding.features') }}" class="underline hover:no-underline ml-1">Learn More →</a>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16 lg:h-20">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="{{ route('home') }}" class="flex items-center space-x-3 logo-container group">
                        <div class="relative">
                            <div
                                class="w-10 h-10 lg:w-12 lg:h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg group-hover:shadow-xl transition-shadow duration-300">
                                <img src="{{ asset('img/logo.png') }}" alt="{{ config('app.name') }} Logo"
                                     class="w-8 h-8">
                            </div>
                            <div
                                class="absolute -top-1 -right-1 w-4 h-4 bg-yellow-400 rounded-full flex items-center justify-center">
                                <div class="w-2 h-2 bg-white rounded-full animate-pulse"></div>
                            </div>
                        </div>
                        <div class="hidden sm:block">
                            <span
                                class="text-xl lg:text-2xl font-bold bg-gradient-to-r from-gray-900 to-gray-700 dark:from-white dark:to-gray-300 bg-clip-text text-transparent">
                                {{ config('app.name') }}
                            </span>
                            <div class="text-xs text-gray-500 dark:text-gray-400 font-medium tracking-wider">
                                Educational Excellence
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Desktop Navigation -->
                <nav class="hidden md:flex items-center space-x-8 lg:space-x-12">
                    <a href="{{ route('home') }}"
                       class="nav-item text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 font-medium transition-colors duration-200 {{ request()->routeIs('home') ? 'active text-blue-600 dark:text-blue-400' : '' }}">
                        Home
                    </a>
                    <div class="relative group">
                        <button
                            class="nav-item text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 font-medium transition-colors duration-200 flex items-center">
                            Solutions
                            <svg class="w-4 h-4 ml-1 transition-transform duration-200 group-hover:rotate-180"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <!-- Dropdown -->
                        <div
                            class="absolute top-full left-0 mt-2 w-72 bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-100 dark:border-gray-700 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 transform translate-y-2 group-hover:translate-y-0">
                            <div class="p-6">
                                <a href="{{ route('branding.features') }}"
                                   class="block p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                                    <div class="font-semibold text-gray-900 dark:text-white">Modules & Features</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">Comprehensive platform overview</div>
                                </a>
                                <a href="{{ route('solutions.schools') }}"
                                   class="block p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                                    <div class="font-semibold text-gray-900 dark:text-white">For Schools</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">Complete school management</div>
                                </a>
                                <a href="{{ route('solutions.teachers') }}"
                                   class="block p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                                    <div class="font-semibold text-gray-900 dark:text-white">For Teachers</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">Teaching and assessment tools</div>
                                </a>
                                <a href="{{ route('solutions.students') }}"
                                   class="block p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                                    <div class="font-semibold text-gray-900 dark:text-white">For Students</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">Interactive learning platform</div>
                                </a>
                            </div>
                        </div>
                    </div>
                    <a href="#"
                       class="nav-item text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 font-medium transition-colors duration-200">
                        Pricing
                    </a>
                    <a href="#"
                       class="nav-item text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 font-medium transition-colors duration-200">
                        Resources
                    </a>
                    <a href="{{ route('branding.contact') }}"
                       class="nav-item text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 font-medium transition-colors duration-200 {{ request()->routeIs('branding.contact') ? 'active text-blue-600 dark:text-blue-400' : '' }}">
                        Contact
                    </a>
                </nav>

                <!-- Right side actions -->
                <div class="flex items-center space-x-4">
                    <!-- Theme Toggle -->
                    <button
                        type="button"
                        class="theme-toggle flex items-center justify-center w-10 h-10 rounded-xl bg-gray-100/80 dark:bg-gray-800/80 backdrop-blur-sm transition-all duration-200 hover:bg-gray-200 dark:hover:bg-gray-700 border border-gray-200/50 dark:border-gray-700/50"
                        @click="$store.darkMode.toggle()"
                        x-data
                        :title="$store.darkMode.on ? 'Switch to light mode' : 'Switch to dark mode'"
                    >
                        <!-- Sun icon for light mode -->
                        <svg
                            x-show="!$store.darkMode.on"
                            class="w-5 h-5 text-yellow-500"
                            fill="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                d="M12 2.25a.75.75 0 01.75.75v2.25a.75.75 0 01-1.5 0V3a.75.75 0 01.75-.75zM7.5 12a4.5 4.5 0 119 0 4.5 4.5 0 01-9 0zM18.894 6.166a.75.75 0 00-1.06-1.06l-1.591 1.59a.75.75 0 101.06 1.061l1.591-1.59zM21.75 12a.75.75 0 01-.75.75h-2.25a.75.75 0 010-1.5H21a.75.75 0 01.75.75zM17.834 18.894a.75.75 0 001.06-1.06l-1.59-1.591a.75.75 0 10-1.061 1.06l1.59 1.591zM12 18a.75.75 0 01.75.75V21a.75.75 0 01-1.5 0v-2.25A.75.75 0 0112 18zM7.758 17.303a.75.75 0 00-1.061-1.06l-1.591 1.59a.75.75 0 001.06 1.061l1.591-1.59zM6 12a.75.75 0 01-.75.75H3a.75.75 0 010-1.5h2.25A.75.75 0 016 12zM6.697 7.757a.75.75 0 001.06-1.06l-1.59-1.591a.75.75 0 00-1.061 1.06l1.59 1.591z"/>
                        </svg>

                        <!-- Moon icon for dark mode -->
                        <svg
                            x-show="$store.darkMode.on"
                            class="w-5 h-5 text-blue-400"
                            fill="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path fill-rule="evenodd"
                                  d="M9.528 1.718a.75.75 0 01.162.819A8.97 8.97 0 009 6a9 9 0 009 9 8.97 8.97 0 003.463-.69.75.75 0 01.981.98 10.503 10.503 0 01-9.694 6.46c-5.799 0-10.5-4.701-10.5-10.5 0-4.368 2.667-8.112 6.46-9.694a.75.75 0 01.818.162z"
                                  clip-rule="evenodd"/>
                        </svg>
                    </button>

                    <!-- Auth buttons -->
                    <div class="hidden md:flex items-center space-x-3">
                        <a href="{{ route('login') }}"
                           class="px-4 py-2 text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 font-medium transition-colors duration-200 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800">
                            Sign In
                        </a>
                        <a href="{{ route('register') }}"
                           class="cta-button relative px-6 py-3 text-white font-semibold rounded-xl shadow-lg">
                            <span class="relative z-10">Get Started Free</span>
                        </a>
                    </div>

                    <!-- Mobile menu button -->
                    <button
                        type="button"
                        class="md:hidden p-2 rounded-xl text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors duration-200"
                        @click="mobileMenuOpen = !mobileMenuOpen"
                    >
                        <svg x-show="!mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                        <svg x-show="mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Mobile menu -->
            <div
                x-show="mobileMenuOpen"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform -translate-y-4"
                x-transition:enter-end="opacity-100 transform translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 transform translate-y-0"
                x-transition:leave-end="opacity-0 transform -translate-y-4"
                class="md:hidden border-t border-gray-200/50 dark:border-gray-700/50 bg-white/95 dark:bg-gray-900/95 backdrop-blur-sm"
                @click.outside="mobileMenuOpen = false"
            >
                <div class="px-4 py-6 space-y-2">
                    <a href="{{ route('home') }}"
                       class="mobile-nav-item block text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 font-medium transition-all duration-200">
                        🏠 Home
                    </a>

                    <!-- Mobile Solutions Menu -->
                    <div x-data="{ solutionsOpen: false }">
                        <button @click="solutionsOpen = !solutionsOpen"
                                class="mobile-nav-item w-full text-left text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 font-medium transition-all duration-200 flex items-center justify-between">
                            <span>🚀 Solutions</span>
                            <svg class="w-4 h-4 transition-transform duration-200"
                                 :class="{ 'rotate-180': solutionsOpen }" fill="none" stroke="currentColor"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="solutionsOpen"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 max-h-0"
                             x-transition:enter-end="opacity-100 max-h-96"
                             x-transition:leave="transition ease-in duration-200"
                             x-transition:leave-start="opacity-100 max-h-96"
                             x-transition:leave-end="opacity-0 max-h-0"
                             class="pl-6 mt-2 space-y-2 overflow-hidden">
                            <a href="{{ route('branding.features') }}"
                               class="block py-2 text-sm text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400">📋
                                Modules & Features</a>
                            <a href="#"
                               class="block py-2 text-sm text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400">🏫
                                For Schools</a>
                            <a href="#"
                               class="block py-2 text-sm text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400">👨‍🏫
                                For Teachers</a>
                            <a href="#"
                               class="block py-2 text-sm text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400">👨‍🎓
                                For Students</a>
                        </div>
                    </div>

                    <a href="#"
                       class="mobile-nav-item block text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 font-medium transition-all duration-200">
                        💰 Pricing
                    </a>
                    <a href="#"
                       class="mobile-nav-item block text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 font-medium transition-all duration-200">
                        📚 Resources
                    </a>
                    <a href="{{ route('branding.contact') }}"
                       class="mobile-nav-item block text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 font-medium transition-all duration-200">
                        📞 Contact
                    </a>

                    <div class="pt-6 border-t border-gray-200/50 dark:border-gray-700/50 space-y-3">
                        <a href="{{ route('login') }}"
                           class="block w-full px-4 py-3 text-center text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 font-medium border border-gray-300 dark:border-gray-600 rounded-xl hover:border-blue-300 dark:hover:border-blue-400 transition-all duration-200">
                            Sign In
                        </a>
                        <a href="{{ route('register') }}"
                           class="block w-full px-4 py-3 text-center text-white font-semibold bg-gradient-to-r from-blue-600 to-purple-600 rounded-xl hover:from-blue-700 hover:to-purple-700 transform hover:scale-105 transition-all duration-200 shadow-lg">
                            Get Started Free 🚀
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Add padding to account for fixed header -->
    <div class="pt-16 lg:pt-24">
        <!-- Main content -->
        <main class="flex-grow">
            {{ $slot }}
        </main>

        <!-- Footer -->
        <x-layouts.footer/>
    </div>
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
        // Dark mode store
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
