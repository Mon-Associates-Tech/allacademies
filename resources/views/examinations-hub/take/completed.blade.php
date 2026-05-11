<x-layouts.exam>
    {{-- ═══════════════════════════════════════════════════════════
         PAGE SHELL
    ═══════════════════════════════════════════════════════════ --}}
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8"
         style="font-family: 'system-ui', -apple-system, sans-serif; background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);">

        <div class="max-w-md w-full space-y-7">

            {{-- ── SUCCESS CARD ── --}}
            <div class="bg-white dark:bg-slate-900 overflow-hidden"
                 style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                
                <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                    <div class="w-1 h-5" style="background: linear-gradient(180deg, #065f46, #059669); border-radius: 1px;"></div>
                    <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider" style="letter-spacing: 0.1em;">Submission Successful</h2>
                </div>

                <div class="p-5 text-center">
                    <div class="w-16 h-16 mx-auto mb-4 flex items-center justify-center"
                         style="border-radius: 2px; background: linear-gradient(135deg, #ecfdf5, #dcfce7);">
                        <svg class="w-10 h-10 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>

                    <h3 class="text-2xl font-bold text-slate-900 dark:text-white leading-snug" style="letter-spacing: -0.02em; font-family: 'Georgia', serif;">
                        Examination Submitted!
                    </h3>
                    
                    <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">
                        Your responses have been successfully submitted for <strong>{{ $exam->title }}</strong>
                    </p>

                    {{-- Results Info --}}
                    <div class="mt-5 p-4 border"
                         style="border-radius: 2px; color:#1d4ed8;background:#eff6ff;border-color:#bfdbfe;">
                        <p class="text-sm text-slate-700 dark:text-slate-300">
                            @if($exam->canShowResults())
                                Your results will be available shortly. Please check back later.
                            @else
                                Your results will be released by the examiner. You will be notified when they are available.
                            @endif
                        </p>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="mt-6 space-y-3">
                        @if($participantEmail)
                            <a href="{{ route('examinations-hub.results.index', ['email' => $participantEmail]) }}" 
                               class="inline-flex items-center justify-center w-full px-5 py-2.5 text-sm font-semibold text-white transition-all"
                               style="border-radius: 2px; background: linear-gradient(135deg, #7c3aed, #a78bfa); box-shadow: 0 2px 10px rgba(124,58,237,0.3);">
                                View My Results
                            </a>
                        @endif
                        
                        <a href="{{ route('examinations-hub.take.join') }}" 
                           class="inline-flex items-center justify-center w-full px-5 py-2.5 text-sm font-semibold {{ $participantEmail ? 'text-slate-700 dark:text-slate-200 border' : 'text-white' }} transition-all"
                           style="border-radius: 2px; {{ $participantEmail ? 'border-color: rgba(0,0,0,0.06); background: linear-gradient(135deg, #f8fafc, #f1f5f9);' : 'background: linear-gradient(135deg, #2563eb, #60a5fa); box-shadow: 0 2px 10px rgba(37,99,235,0.3);' }}">
                            Take Another Exam
                        </a>
                        
                        <a href="{{ url('/') }}" 
                           class="inline-flex items-center justify-center w-full px-5 py-2.5 text-sm font-semibold text-slate-700 dark:text-slate-200 transition-all border"
                           style="border-radius: 2px; border-color: rgba(0,0,0,0.06); background: linear-gradient(135deg, #f8fafc, #f1f5f9);">
                            Return to Home
                        </a>
                    </div>
                </div>
            </div>

            {{-- ── FOOTER NOTE ── --}}
            <div class="text-center">
                <p class="text-xs text-slate-400">
                    Thank you for participating!
                </p>
            </div>

        </div>
    </div>
</x-layouts.exam>