<x-layouts.exam>
    {{-- ═══════════════════════════════════════════════════════════
         PAGE SHELL
    ═══════════════════════════════════════════════════════════ --}}
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8"
         style="font-family: 'system-ui', -apple-system, sans-serif; background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);">

        <div class="max-w-md w-full space-y-7">

            {{-- ── PAGE HEADER ── --}}
            <div class="overflow-hidden text-center"
                 style="border-radius: 2px; background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); box-shadow: 0 4px 24px rgba(0,0,0,0.15);">
                <div class="h-1 w-full" style="background: linear-gradient(90deg, #7c3aed, #a78bfa, #c4b5fd);"></div>
                <div class="px-7 py-6">
                    <h1 class="text-2xl font-bold text-white leading-snug" style="letter-spacing: -0.02em; font-family: 'Georgia', serif;">
                        Join Examination
                    </h1>
                    <p class="text-slate-400 mt-2 text-sm">
                        Enter your access code to begin
                    </p>
                </div>
            </div>

            {{-- ── AUTH FORM CARD ── --}}
            <div class="bg-white dark:bg-slate-900 overflow-hidden"
                 style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                
                <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                    <div class="w-1 h-5" style="background: linear-gradient(180deg, #2563eb, #60a5fa); border-radius: 1px;"></div>
                    <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider" style="letter-spacing: 0.1em;">Participant Details</h2>
                </div>

                <form method="POST" action="{{ route('examination-hub.take.authenticate') }}" class="p-5 space-y-5">
                    @csrf

                    {{-- Errors --}}
                    @if($errors->any())
                        <div class="bg-white dark:bg-slate-900 overflow-hidden"
                             style="border-radius: 2px; border: 1px solid rgba(220,38,38,0.2); box-shadow: 0 1px 6px rgba(220,38,38,0.08);">
                            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2"
                                 style="background: linear-gradient(135deg, #fef2f2, #fee2e2);">
                                <div class="w-1 h-5" style="background: linear-gradient(180deg, #dc2626, #ef4444); border-radius: 1px;"></div>
                                <h2 class="font-bold text-red-800 dark:text-red-300 text-xs uppercase tracking-wider" style="letter-spacing: 0.1em;">Validation Error</h2>
                            </div>
                            <div class="p-5">
                                <p class="text-sm text-red-700 dark:text-red-400">{{ $errors->first() }}</p>
                            </div>
                        </div>
                    @endif

                    {{-- Access Code --}}
                    <div>
                        <label for="access_code" class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">
                            Access Code <span class="text-red-500">*</span>
                        </label>
                        <input 
                            id="access_code" 
                            name="access_code" 
                            type="text" 
                            required 
                            value="{{ old('access_code') }}"
                            class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-none focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 dark:bg-slate-800 dark:text-white transition-all text-center font-mono uppercase tracking-widest"
                            style="border-radius: 2px;"
                            placeholder="XXXXXXXX"
                            maxlength="8"
                        >
                    </div>

                    {{-- Name --}}
                    <div>
                        <label for="name" class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">
                            Your Name <span class="text-red-500">*</span>
                        </label>
                        <input 
                            id="name" 
                            name="name" 
                            type="text" 
                            value="{{ old('name') }}"
                            class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-none focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 dark:bg-slate-800 dark:text-white transition-all"
                            style="border-radius: 2px;"
                            placeholder="Enter your full name"
                        >
                    </div>

                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">
                            Email Address <span class="text-red-500">*</span>
                        </label>
                        <input 
                            id="email" 
                            name="email" 
                            type="email" 
                            value="{{ old('email') }}"
                            class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-none focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 dark:bg-slate-800 dark:text-white transition-all"
                            style="border-radius: 2px;"
                            placeholder="your.email@example.com"
                        >
                    </div>

                    {{-- Unique Code --}}
                    <div>
                        <label for="unique_code" class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">
                            Unique Code <span class="text-slate-400">(if provided)</span>
                        </label>
                        <input 
                            id="unique_code" 
                            name="unique_code" 
                            type="text" 
                            value="{{ old('unique_code') }}"
                            class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-none focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 dark:bg-slate-800 dark:text-white transition-all"
                            style="border-radius: 2px;"
                            placeholder="Your unique participant code"
                        >
                    </div>

                    {{-- Submit Button --}}
                    <div class="pt-2">
                        <button 
                            type="submit" 
                            class="w-full inline-flex items-center justify-center gap-2 px-6 py-2.5 text-sm font-semibold text-white transition-all"
                            style="border-radius: 2px; background: linear-gradient(135deg, #7c3aed, #a78bfa); box-shadow: 0 2px 10px rgba(124,58,237,0.3);">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                            </svg>
                            Join Examination
                        </button>
                    </div>
                </form>
            </div>

            {{-- ── FOOTER NOTE ── --}}
            <div class="text-center">
                <p class="text-xs text-slate-400">
                    Make sure you have a stable internet connection before starting
                </p>
            </div>

        </div>
    </div>
</x-layouts.exam>