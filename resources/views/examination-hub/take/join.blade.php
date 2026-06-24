@php
    $branding = config('exam-branding');
@endphp

<x-layouts.exam>
    {{-- Skip to main content link for accessibility --}}
    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 focus:z-50 focus:px-4 focus:py-2 focus:text-white focus:rounded-lg" style="background-color: {{ $branding['primary_color'] }};">
        Skip to main content
    </a>

    <div class="h-screen flex items-center justify-center px-4 sm:px-6 lg:px-8 overflow-hidden" style="font-family: {{ $branding['font_family'] }}; background-color: {{ $branding['background_color'] }};">
        <div class="w-full max-w-5xl h-[90vh] max-h-[700px] flex shadow-2xl rounded-2xl overflow-hidden border border-slate-200/50 dark:border-slate-800/50">

            {{-- Left Column - Branding & Info --}}
            <div class="hidden lg:flex lg:w-2/5 p-8 flex-col justify-between relative overflow-hidden" style="background-color: {{ $branding['primary_color'] }};">
                {{-- Decorative circles --}}
                <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/10 rounded-full translate-y-1/2 -translate-x-1/2"></div>

                <div class="relative z-10">
                    <div class="flex items-center gap-3 mb-8">
                        @if($branding['logo'])
                            <img src="{{ $branding['logo'] }}" alt="{{ $branding['brand_name'] }}" class="w-12 h-12">
                        @else
                            <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center">
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                        @endif
                        <h1 class="text-sm font-bold text-white">{{ $branding['brand_name'] }}</h1>
                    </div>

                    <div class="space-y-6">
                        <div>
                            <h2 class="text-xl font-bold text-white mb-3">Ready to Begin?</h2>
                            <p class="text-white/90 text-sm leading-relaxed">
                                Enter your access code and participant details to start your examination.
                            </p>
                        </div>

                        <div class="space-y-3">
                            @foreach($branding['exam_rules'] as $rule)
                                <div class="flex items-start gap-3">
                                    <div class="w-6 h-6 bg-white/20 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-white text-sm">{{ $rule }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="relative z-10">
                    <p class="text-white/60 text-xs">{{ $branding['footer_text'] }}</p>
                </div>
            </div>

            {{-- Right Column - Form --}}
            <div id="main-content" class="w-full lg:w-3/5 bg-white dark:bg-slate-900 flex flex-col">
                {{-- Compact Header with Theme Toggle --}}
                <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 dark:border-slate-800">
                    <div class="lg:hidden flex items-center gap-2">
                        @if($branding['logo'])
                            <img src="{{ $branding['logo'] }}" alt="{{ $branding['brand_name'] }}" class="w-8 h-8">
                        @else
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background-color: {{ $branding['primary_color'] }};">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                        @endif
                        <h1 class="text-lg font-bold text-slate-900 dark:text-white">Join Exam</h1>
                    </div>
                    <div class="ml-auto">
                        <x-snippets.theme-toggle />
                    </div>
                </div>

                {{-- Form Content --}}
                <div class="flex-1 flex items-center justify-center p-6 lg:p-8 overflow-y-auto">
                    <form method="POST" action="{{ route('examination-hub.take.authenticate') }}" class="w-full max-w-md space-y-4" novalidate>
                        @csrf

                        {{-- Errors --}}
                        @if($errors->any())
                            <div role="alert" aria-live="assertive" class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-3">
                                <p class="text-sm text-red-700 dark:text-red-400">{{ $errors->first() }}</p>
                            </div>
                        @endif

                        {{-- Access Code --}}
                        <div>
                            <label for="access_code" class="block text-xs uppercase font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                                Access Code <span class="text-red-500" aria-label="required">*</span>
                            </label>
                            <input
                                id="access_code"
                                name="access_code"
                                type="text"
                                required
                                aria-required="true"
                                value="{{ old('access_code') }}"
                                class="w-full px-3 py-2.5 text-sm border border-slate-300 dark:border-slate-700 rounded-none focus:outline-none focus:ring-2 dark:bg-slate-800 dark:text-white transition-all text-center font-mono uppercase tracking-widest"
                                placeholder="XXXXXXXX"
                                maxlength="8"
                                autocomplete="off"
                                style="focus-ring-color: {{ $branding['primary_color'] }}; border-color: {{ $branding['primary_color'] }};"
                            >
                            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">{{$branding['access_code_length']}}-character code for the course  you are taking.</p>
                        </div>

                        {{-- Name --}}
                        <div>
                            <label for="name" class="block text-xs font-semibold uppercase text-slate-700 dark:text-slate-300 mb-1.5">
                                Full Name <span class="text-red-500" aria-label="required">*</span>
                            </label>
                            <input
                                id="name"
                                name="name"
                                type="text"
                                required
                                aria-required="true"
                                value="{{ old('name') }}"
                                class="w-full px-3 py-2.5 text-sm border border-slate-300 dark:border-slate-700 rounded-none focus:outline-none focus:ring-2 dark:bg-slate-800 dark:text-white transition-all"
                                placeholder="Enter your full name"
                                autocomplete="name"
                                style="border-color: {{ $branding['primary_color'] }};"
                            >
                        </div>

                        {{-- Email --}}
                        <div>
                            <label for="email" class="block text-xs uppercase font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                                Email Address <span class="text-red-500" aria-label="required">*</span>
                            </label>
                            <input
                                id="email"
                                name="email"
                                type="email"
                                required
                                aria-required="true"
                                value="{{ old('email') }}"
                                class="w-full px-3 py-2.5 text-sm border border-slate-300 dark:border-slate-700 rounded-none focus:outline-none focus:ring-2 dark:bg-slate-800 dark:text-white transition-all"
                                placeholder="your.email@example.com"
                                autocomplete="email"
                                style="border-color: {{ $branding['primary_color'] }};"
                            >
                        </div>

                        {{-- Unique Code --}}
                        <div>
                            <label for="unique_code" class="block text-xs uppercase font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                                Unique Code <span class="text-slate-400 font-normal"></span>
                            </label>
                            <input
                                id="unique_code"
                                name="unique_code"
                                type="text"
                                value="{{ old('unique_code') }}"
                                class="w-full px-3 py-2.5 text-sm border border-slate-300 dark:border-slate-700 rounded-none focus:outline-none focus:ring-2 dark:bg-slate-800 dark:text-white transition-all"
                                placeholder="If provided"
                                autocomplete="off"
                                style="border-color: {{ $branding['primary_color'] }};"
                            >
                        </div>

                        {{-- Submit Button --}}
                        <div class="pt-2">
                            <x-ui.button
                                variant="primary"
                                size="md"
                                type="submit"
                                fullWidth="true"
                                style="background-color: {{ $branding['button_color'] }};"
                                aria-label="Join examination and start taking the test"
                            >
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                                </svg>
                                Join Examination
                            </x-ui.button>
                        </div>

                        <p class="text-center text-xs text-slate-500 dark:text-slate-400 pt-2">
                            Ensure stable internet connection before starting
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layouts.exam>
