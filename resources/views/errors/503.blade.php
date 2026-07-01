@php
    // Dynamically determine the layout to avoid duplicating the entire HTML structure
    $layout = Auth::check() ? 'layouts.app' : 'layouts.bare';
@endphp

<x-dynamic-component :component="$layout" background="bg-white dark:bg-gray-900">
    <div class="relative min-h-[calc(100vh-4rem)] flex items-center justify-center px-4 sm:px-6 lg:px-8 py-12 overflow-hidden">

        <!-- Subtle Background Elements (Mesh Gradient Effect) -->
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute inset-0 bg-gradient-to-br from-slate-50 via-white to-slate-50 dark:from-slate-900 dark:via-slate-950 dark:to-slate-900"></div>
        </div>
        <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-amber-200/30 dark:bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-indigo-200/30 dark:bg-indigo-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative w-full max-w-2xl mx-auto text-center">

            <!-- Animated Icon -->
            <div class="relative inline-flex mb-8">
                <div class="absolute inset-0 bg-amber-400/20 dark:bg-amber-500/20 rounded-full blur-xl animate-pulse"></div>
                <div class="relative w-24 h-24 flex items-center justify-center bg-white dark:bg-slate-800 rounded-2xl shadow-xl shadow-amber-500/10 dark:shadow-amber-500/5 border border-slate-200 dark:border-slate-700 rotate-3 hover:rotate-0 transition-transform duration-500">
                    <svg class="w-12 h-12 text-amber-500 dark:text-amber-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z" />
                    </svg>
                </div>
            </div>

            <!-- Live Status Badge -->
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-amber-50 dark:bg-amber-900/30 border border-amber-200 dark:border-amber-800/50 text-amber-700 dark:text-amber-300 text-xs font-semibold uppercase tracking-wider mb-6 shadow-sm">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span>
                </span>
                Scheduled Maintenance
            </div>

            <!-- Headings -->
            <h1 class="text-6xl sm:text-7xl font-extrabold tracking-tight mb-4">
                <span class="bg-gradient-to-r from-slate-900 via-slate-700 to-slate-900 dark:from-white dark:via-slate-200 dark:to-white bg-clip-text text-transparent">
                    503
                </span>
            </h1>
            <h2 class="text-2xl sm:text-3xl font-bold text-slate-800 dark:text-slate-100 mb-3">
                We’ll be right back
            </h2>
            <p class="text-lg text-slate-500 dark:text-slate-400 max-w-lg mx-auto mb-8 leading-relaxed">
                Our site is currently undergoing scheduled maintenance. We're making improvements to keep the platform running smoothly for you.
            </p>

            <!-- Glassmorphic Info Card -->
            <div class="relative max-w-md mx-auto mb-10 p-px bg-gradient-to-r from-amber-200 via-amber-400 to-amber-200 dark:from-amber-800 dark:via-amber-600 dark:to-amber-800 rounded-xl shadow-lg">
                <div class="bg-white dark:bg-slate-800 rounded-[11px] p-5 text-left">
                    <div class="flex gap-3">
                        <div class="flex-shrink-0 mt-0.5">
                            <svg class="w-5 h-5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-slate-900 dark:text-white">What's happening?</h3>
                            <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">
                                We are deploying updates and performing routine checks. This usually takes less than 30 minutes. Thank you for your patience!
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                @if(Auth::check())
                    <a href="{{ route('dashboard') }}" class="group inline-flex items-center justify-center px-6 py-3 bg-slate-900 hover:bg-slate-800 dark:bg-white dark:hover:bg-slate-100 text-white dark:text-slate-900 font-semibold rounded-xl transition-all duration-200 shadow-lg shadow-slate-900/10 dark:shadow-white/10 hover:shadow-xl hover:-translate-y-0.5">
                        <svg class="w-5 h-5 mr-2 transition-transform group-hover:-translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
                        Back to Dashboard
                    </a>
                    <a href="{{ route('home') }}" class="inline-flex items-center justify-center px-6 py-3 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-semibold rounded-xl border border-slate-200 dark:border-slate-700 transition-all duration-200 hover:-translate-y-0.5">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        Go Home
                    </a>
                @else
                    <a href="{{ route('home') }}" class="group inline-flex items-center justify-center px-6 py-3 bg-slate-900 hover:bg-slate-800 dark:bg-white dark:hover:bg-slate-100 text-white dark:text-slate-900 font-semibold rounded-xl transition-all duration-200 shadow-lg shadow-slate-900/10 dark:shadow-white/10 hover:shadow-xl hover:-translate-y-0.5">
                        <svg class="w-5 h-5 mr-2 transition-transform group-hover:-translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        Go Home
                    </a>
                    <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-6 py-3 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-semibold rounded-xl border border-slate-200 dark:border-slate-700 transition-all duration-200 hover:-translate-y-0.5">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                        </svg>
                        Sign In
                    </a>
                @endif
            </div>
        </div>
    </div>
</x-dynamic-component>
