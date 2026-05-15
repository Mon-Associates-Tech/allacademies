<x-layouts.exam>
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 font-sans bg-gradient-to-br from-slate-900 to-slate-800">
        <div class="max-w-md w-full space-y-7">

            {{-- ── PAGE HEADER ── --}}
            <div class="overflow-hidden text-center rounded-[2px] bg-gradient-to-br from-slate-900 to-slate-800 shadow-xl">
                <div class="h-1 w-full bg-gradient-to-r from-violet-600 via-violet-400 to-indigo-300"></div>
                <div class="px-7 py-6">
                    <h1 class="text-2xl font-bold text-white leading-snug tracking-tight font-serif">
                        Join Examination
                    </h1>
                    <p class="text-slate-400 mt-2 text-sm">
                        Enter your access code to begin
                    </p>
                </div>
            </div>

            {{-- ── AUTH FORM CARD ── --}}
            <x-ui.card variant="default" shadow="true">
                <x-ui.card-header title="Participant Details" accent="info" />

                <form method="POST" action="{{ route('examination-hub.take.authenticate') }}" class="p-5 space-y-5">
                    @csrf

                    {{-- Errors --}}
                    @if($errors->any())
                        <x-ui.card variant="accent" accent="danger" shadow="true">
                            <x-ui.card-header title="Validation Error" accent="danger" />
                            <div class="p-5">
                                <p class="text-sm text-red-700 dark:text-red-400">{{ $errors->first() }}</p>
                            </div>
                        </x-ui.card>
                    @endif

                    {{-- Access Code --}}
                    <div>
                        <label for="access_code" class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2">
                            Access Code <span class="text-red-500">*</span>
                        </label>
                        <input
                            id="access_code"
                            name="access_code"
                            type="text"
                            required
                            value="{{ old('access_code') }}"
                            class="w-full px-4 py-2.5 text-sm border border-slate-300 dark:border-slate-700 rounded-[2px] focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 dark:bg-slate-800 dark:text-white transition-all text-center font-mono uppercase tracking-widest"
                            placeholder="XXXXXXXX"
                            maxlength="8"
                        >
                    </div>

                    {{-- Name --}}
                    <div>
                        <label for="name" class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2">
                            Your Name <span class="text-red-500">*</span>
                        </label>
                        <input
                            id="name"
                            name="name"
                            type="text"
                            value="{{ old('name') }}"
                            class="w-full px-4 py-2.5 text-sm border border-slate-300 dark:border-slate-700 rounded-[2px] focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 dark:bg-slate-800 dark:text-white transition-all"
                            placeholder="Enter your full name"
                        >
                    </div>

                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2">
                            Email Address <span class="text-red-500">*</span>
                        </label>
                        <input
                            id="email"
                            name="email"
                            type="email"
                            value="{{ old('email') }}"
                            class="w-full px-4 py-2.5 text-sm border border-slate-300 dark:border-slate-700 rounded-[2px] focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 dark:bg-slate-800 dark:text-white transition-all"
                            placeholder="your.email@example.com"
                        >
                    </div>

                    {{-- Unique Code --}}
                    <div>
                        <label for="unique_code" class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2">
                            Unique Code <span class="text-slate-400">(if provided)</span>
                        </label>
                        <input
                            id="unique_code"
                            name="unique_code"
                            type="text"
                            value="{{ old('unique_code') }}"
                            class="w-full px-4 py-2.5 text-sm border border-slate-300 dark:border-slate-700 rounded-[2px] focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 dark:bg-slate-800 dark:text-white transition-all"
                            placeholder="Your unique participant code"
                        >
                    </div>

                    {{-- Submit Button --}}
                    <div class="pt-2">
                        <x-ui.button
                            variant="primary"
                            size="md"
                            type="submit"
                            fullWidth="true"
                        >
                            <x-heroicon-o-arrow-left-on-rectangle class="w-4 h-4"/>
                            Join Examination
                        </x-ui.button>
                    </div>
                </form>
            </x-ui.card>

            {{-- ── FOOTER NOTE ── --}}
            <div class="text-center">
                <p class="text-xs text-slate-400">
                    Make sure you have a stable internet connection before starting
                </p>
            </div>
        </div>
    </div>
</x-layouts.exam>
