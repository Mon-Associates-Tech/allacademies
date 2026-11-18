@php
    $branding = config('exam-branding');
@endphp

<x-layouts.exam>
    {{-- Skip to main content link for accessibility --}}
    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 focus:z-50 focus:px-4 focus:py-2 focus:text-white focus:rounded-lg" style="background-color: {{ $branding['primary_color'] }};">
        Skip to main content
    </a>

    {{-- Animated gradient background --}}
    <div class="min-h-screen flex items-center justify-center px-4 sm:px-6 lg:px-8 overflow-hidden relative" style="font-family: {{ $branding['font_family'] }};">
        {{-- Background layers --}}
        <div class="absolute inset-0 bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 dark:from-slate-950 dark:via-slate-900 dark:to-indigo-950"></div>
        <div class="absolute inset-0 opacity-40 dark:opacity-20" style="background-image: radial-gradient(circle at 20% 50%, {{ $branding['primary_color'] }}22 0%, transparent 50%), radial-gradient(circle at 80% 20%, {{ $branding['primary_color'] }}15 0%, transparent 50%), radial-gradient(circle at 60% 80%, {{ $branding['primary_color'] }}18 0%, transparent 50%);"></div>

        {{-- Grid pattern overlay --}}
        <div class="absolute inset-0 opacity-[0.03] dark:opacity-[0.05]" style="background-image: linear-gradient({{ $branding['primary_color'] }} 1px, transparent 1px), linear-gradient(90deg, {{ $branding['primary_color'] }} 1px, transparent 1px); background-size: 60px 60px;"></div>

        <div class="w-full max-w-6xl relative z-10">
            {{-- Main card --}}
            <div class="flex flex-col lg:flex-row rounded-3xl overflow-hidden shadow-[0_35px_60px_-15px_rgba(0,0,0,0.15)] dark:shadow-[0_35px_60px_-15px_rgba(0,0,0,0.4)] border border-white/20 dark:border-slate-700/30 backdrop-blur-sm">

                {{-- Left Panel - Branding & Info --}}
                <div class="hidden lg:flex lg:w-5/12 relative flex-col justify-between p-10 overflow-hidden" style="background: linear-gradient(135deg, {{ $branding['primary_color'] }} 0%, {{ $branding['primary_color'] }}dd 100%);">
                    {{-- Decorative elements --}}
                    <div class="absolute -top-16 -right-16 w-72 h-72 bg-white/[0.08] rounded-full blur-2xl"></div>
                    <div class="absolute -bottom-20 -left-20 w-80 h-80 bg-white/[0.06] rounded-full blur-2xl"></div>
                    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-white/[0.04] rounded-full blur-3xl"></div>

                    {{-- Geometric accents --}}
                    <div class="absolute top-8 right-8 w-20 h-20 border border-white/10 rounded-2xl rotate-12"></div>
                    <div class="absolute bottom-24 right-16 w-12 h-12 border border-white/10 rounded-lg -rotate-12"></div>

                    <div class="relative z-10">
                        {{-- Brand header --}}
                        <div class="flex items-center gap-3 mb-10">
                            <div class="w-11 h-11 bg-white/15 backdrop-blur-md rounded-2xl flex items-center justify-center border border-white/20 shadow-lg">
                                @if($branding['logo'])
                                    <img src="{{ $branding['logo'] }}" alt="{{ $branding['brand_name'] }}" class="w-8 h-8 object-contain">
                                @else
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>
                                    </svg>
                                @endif
                            </div>
                            <span class="text-white/90 text-sm font-semibold tracking-wide">{{ $branding['brand_name'] }}</span>
                        </div>

                        {{-- Hero text --}}
                        <div class="mb-10">
                            <h2 class="text-3xl font-extrabold text-white mb-4 leading-tight">
                                Ready to<br>Begin?
                            </h2>
                            <p class="text-white/75 text-base leading-relaxed max-w-xs">
                                Enter your access code and participant details to start your examination securely.
                            </p>
                        </div>

                        {{-- Exam rules --}}
                        <div class="space-y-4">
                            <p class="text-white/50 text-xs font-semibold uppercase tracking-widest">Examination Rules</p>
                            @foreach($branding['exam_rules'] as $index => $rule)
                                <div class="flex items-start gap-3 group">
                                    <div class="flex-shrink-0 w-6 h-6 bg-white/15 backdrop-blur-sm rounded-lg flex items-center justify-center mt-0.5 border border-white/10 group-hover:bg-white/25 transition-colors">
                                        <svg class="w-3.5 h-3.5 text-white/90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </div>
                                    <p class="text-white/85 text-sm leading-relaxed">{{ $rule }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="relative z-10 mt-12">
                        <div class="flex items-center gap-2 mb-3">
                            <div class="h-px flex-1 bg-white/15"></div>
                            <div class="w-2 h-2 bg-white/25 rounded-full"></div>
                            <div class="h-px flex-1 bg-white/15"></div>
                        </div>
                        <p class="text-white/40 text-xs">{{ $branding['footer_text'] }}</p>
                    </div>
                </div>

                {{-- Right Panel - Form --}}
                <div id="main-content" class="w-full lg:w-7/12 bg-white/80 dark:bg-slate-900/90 backdrop-blur-xl flex flex-col">
                    {{-- Top bar --}}
                    <div class="flex items-center justify-between px-8 py-5 border-b border-slate-100/80 dark:border-slate-800/50">
                        <div class="lg:hidden flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl flex items-center justify-center shadow-md" style="background: linear-gradient(135deg, {{ $branding['primary_color'] }}, {{ $branding['primary_color'] }}cc);">
                                @if($branding['logo'])
                                    <img src="{{ $branding['logo'] }}" alt="{{ $branding['brand_name'] }}" class="w-6 h-6 object-contain">
                                @else
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>
                                    </svg>
                                @endif
                            </div>
                            <h1 class="text-base font-bold text-slate-800 dark:text-white">Join Exam</h1>
                        </div>
                        <div class="lg:ml-auto">
                            <x-snippets.theme-toggle />
                        </div>
                    </div>

                    {{-- Form content --}}
                    <div class="flex-1 flex items-center justify-center p-8 lg:p-10 overflow-y-auto">
                        <div class="w-full max-w-lg">
                            {{-- Mobile hero --}}
                            <div class="lg:hidden mb-8 text-center">
                                <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl mb-4 shadow-lg" style="background: linear-gradient(135deg, {{ $branding['primary_color'] }}, {{ $branding['primary_color'] }}cc);">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
                                    </svg>
                                </div>
                                <h2 class="text-2xl font-extrabold text-slate-900 dark:text-white mb-2">Join Examination</h2>
                                <p class="text-slate-500 dark:text-slate-400 text-sm">Enter your details to get started</p>
                            </div>

                            <form method="POST" action="{{ route('examination-hub.take.authenticate') }}" class="space-y-5" novalidate>
                                @csrf

                                {{-- Errors --}}
                                @if($errors->any())
                                    <div role="alert" aria-live="assertive" class="bg-red-50 dark:bg-red-900/20 border border-red-200/60 dark:border-red-800/40 rounded-2xl p-4 flex items-start gap-3">
                                        <div class="flex-shrink-0 w-5 h-5 bg-red-100 dark:bg-red-900/40 rounded-full flex items-center justify-center mt-0.5">
                                            <svg class="w-3 h-3 text-red-600 dark:text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                        <p class="text-sm text-red-700 dark:text-red-300 font-medium">{{ $errors->first() }}</p>
                                    </div>
                                @endif

                                {{-- Exam/Access Code --}}
                                <div class="group">
                                    <label for="access_code" class="flex items-center gap-2 text-xs font-semibold text-slate-600 dark:text-slate-300 mb-2 uppercase tracking-wider">
                                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z"/>
                                        </svg>
                                        Exam Code <span class="text-red-500" aria-label="required">*</span>
                                    </label>
                                    <input
                                        id="access_code"
                                        name="access_code"
                                        type="text"
                                        required
                                        aria-required="true"
                                        value="{{ old('access_code') }}"
                                        class="w-full px-4 py-3 text-base bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-offset-0 dark:focus:ring-offset-0 dark:bg-slate-800 dark:text-white transition-all duration-200 text-center font-mono uppercase tracking-[0.3em] placeholder:tracking-normal placeholder:font-sans placeholder:text-slate-400 hover:border-slate-300 dark:hover:border-slate-600"
                                        placeholder="XXXXXXXX"
                                        maxlength="8"
                                        autocomplete="off"
                                        style="--ring-color: {{ $branding['primary_color'] }}; focus-ring-color: {{ $branding['primary_color'] }};"
                                    >
                                    <p class="mt-2 text-xs text-slate-400 dark:text-slate-500 flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        {{ $branding['access_code_length'] }}-character code for the course you are taking.
                                    </p>
                                </div>

                                {{-- Name --}}
                                <div class="group">
                                    <label for="name" class="flex items-center gap-2 text-xs font-semibold text-slate-600 dark:text-slate-300 mb-2 uppercase tracking-wider">
                                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
                                        </svg>
                                        Full Name <span class="text-red-500" aria-label="required">*</span>
                                    </label>
                                    <input
                                        id="name"
                                        name="name"
                                        type="text"
                                        required
                                        aria-required="true"
                                        value="{{ old('name') }}"
                                        class="w-full px-4 py-3 text-sm bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-offset-0 dark:focus:ring-offset-0 dark:bg-slate-800 dark:text-white transition-all duration-200 placeholder:text-slate-400 hover:border-slate-300 dark:hover:border-slate-600"
                                        placeholder="Enter your full name"
                                        autocomplete="name"
                                        style="--ring-color: {{ $branding['primary_color'] }};"
                                    >
                                </div>

                                {{-- Email Address / Username --}}
                                <div class="group">
                                    <label for="email" class="flex items-center gap-2 text-xs font-semibold text-slate-600 dark:text-slate-300 mb-2 uppercase tracking-wider">
                                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/>
                                        </svg>
                                        Email Address / Username <span class="text-red-500" aria-label="required">*</span>
                                    </label>
                                    <input
                                        id="email"
                                        name="email"
                                        type="text"
                                        required
                                        aria-required="true"
                                        value="{{ old('email') }}"
                                        class="w-full px-4 py-3 text-sm bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-offset-0 dark:focus:ring-offset-0 dark:bg-slate-800 dark:text-white transition-all duration-200 placeholder:text-slate-400 hover:border-slate-300 dark:hover:border-slate-600"
                                        placeholder="your.email@example.com or JOHSMI"
                                        autocomplete="email"
                                        style="--ring-color: {{ $branding['primary_color'] }};"
                                    >
                                </div>

                                {{-- Index Number --}}
                                <div class="group">
                                    <label for="unique_code" class="flex items-center gap-2 text-xs font-semibold text-slate-600 dark:text-slate-300 mb-2 uppercase tracking-wider">
                                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z"/>
                                        </svg>
                                        Index Number
                                        <span class="text-slate-400 font-normal text-[10px] normal-case tracking-normal uppercase">optional</span>
                                    </label>
                                    <input
                                        id="unique_code"
                                        name="unique_code"
                                        type="text"
                                        value="{{ old('unique_code') }}"
                                        class="w-full px-4 py-3 text-sm bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-offset-0 dark:focus:ring-offset-0 dark:bg-slate-800 dark:text-white transition-all duration-200 placeholder:text-slate-400 hover:border-slate-300 dark:hover:border-slate-600"
                                        placeholder="e.g. 1234567"
                                        autocomplete="off"
                                        style="--ring-color: {{ $branding['primary_color'] }};"
                                    >
                                </div>

                                {{-- Submit Button --}}
                                <div class="pt-3">
                                    <x-ui.button
                                        variant="primary"
                                        size="lg"
                                        type="submit"
                                        fullWidth="true"
                                        style="background: linear-gradient(135deg, {{ $branding['button_color'] }}, {{ $branding['button_color'] }}dd);"
                                        aria-label="Join examination and start taking the test"
                                    >
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/>
                                        </svg>
                                        Join Examination
                                    </x-ui.button>
                                </div>

                                {{-- Footer note --}}
                                <div class="flex items-center justify-center gap-2 pt-2">
                                    <div class="h-px w-12 bg-slate-200 dark:bg-slate-700"></div>
                                    <svg class="w-3.5 h-3.5 text-slate-300 dark:text-slate-600" fill="currentColor" viewBox="0 0 24 24">
                                        <path fill-rule="evenodd" d="M12 1.5a5.25 5.25 0 00-5.25 5.25v3a3 3 0 00-3 3v6.75a3 3 0 003 3h10.5a3 3 0 003-3v-6.75a3 3 0 00-3-3v-3A5.25 5.25 0 0012 1.5zm0 12a.75.75 0 100-1.5.75.75 0 000 1.5zm0-2.25a.75.75 0 110-1.5.75.75 0 010 1.5z" clip-rule="evenodd"/>
                                    </svg>
                                    <p class="text-xs text-slate-400 dark:text-slate-500">Comply with all rules and regulations.</p>
                                    <div class="h-px w-12 bg-slate-200 dark:bg-slate-700"></div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.exam>
