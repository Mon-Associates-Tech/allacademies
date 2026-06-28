<x-layouts.exam>
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 font-sans bg-slate-100 dark:bg-gradient-to-br dark:from-slate-900 dark:to-slate-800">
        <div class="max-w-md w-full space-y-7">
            <div class="flex justify-end">
                <x-snippets.theme-toggle />
            </div>

            {{-- ── SUCCESS CARD ── --}}
            <x-ui.card variant="default" shadow="true">
                <x-ui.card-header title="Submission Successful" accent="success" />

                <div class="p-5 text-center">
                    <div class="w-16 h-16 mx-auto mb-4 flex items-center justify-center rounded-[2px] bg-gradient-to-br from-emerald-50 to-emerald-100 dark:from-emerald-900/20 dark:to-emerald-900/10">
                        <svg class="w-10 h-10 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>

                    <h3 class="text-2xl font-bold text-slate-900 dark:text-white leading-snug tracking-tight font-serif">
                        Examination Completed
                    </h3>

                    <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">
                        @if($submission?->auto_submitted)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400">
                                <svg class="w-3 h-3 mr-1.5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z"/>
                                    <path d="M12 8v5l3 3"/>
                                </svg>
                                Automatically Submitted
                            </span>
                        @endif
                    </p>

                    <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">
                        Your responses have been successfully submitted for <strong>{{ $exam->title }}</strong>
                    </p>

                    {{-- Results Info --}}
                    <div class="mt-5 hidden p-4 border border-blue-200/50 rounded-[2px] bg-blue-50 text-blue-800 dark:bg-blue-900/20 dark:border-blue-800/50 dark:text-blue-200">
                        <p class="text-sm font-medium mb-2">
                            @if($exam->canShowResults())
                                📧 An email with your results link has been sent to <strong>{{ $participantEmail }}</strong>
                            @else
                                Your results will be released by the examiner. You will be notified when they are available.
                            @endif
                        </p>
                        <p class="text-xs opacity-80">
                            The secure access link will expire in 7 days for security purposes.
                        </p>
                    </div>

                    @if($submission?->auto_submitted)
                    <div class="mt-5 p-4 border border-amber-200/50 rounded-[2px] bg-amber-50 text-amber-800 dark:bg-amber-900/20 dark:border-amber-800/50 dark:text-amber-300">
                        <p class="text-sm inline-flex font-medium mb-2">
                            <svg class="w-3 h-3 mr-1.5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z"/>
                                <path d="M12 8v5l3 3"/>
                            </svg>
                            <span>This exam was automatically submitted because the time duration for the exam has expired.</span>

                        </p>
                        <p class="text-xs opacity-80">
                            @if($submission?->auto_submit_reason)
                                <span class="italic">Reason: {{ $submission->auto_submit_reason }}</span>
                            @endif
                        </p>
                    </div>
                    @endif

                    {{-- Action Buttons --}}
                    <div class="mt-6 space-y-3 hidden">
                        @if($participantEmail)
                            <x-ui.button
                                variant="primary"
                                size="md"
                                href="{{ route('examination-hub.results.index', ['email' => $participantEmail]) }}"
                                fullWidth="true"
                            >
                                View My Results
                            </x-ui.button>
                        @endif

                        <x-ui.button
                            variant="{{ $participantEmail ? 'ghost' : 'secondary' }}"
                            size="md"
                            href="{{ route('examination-hub.take.join') }}"
                            fullWidth="true"
                        >
                            Take Another Exam
                        </x-ui.button>

                        <x-ui.button
                            variant="ghost"
                            size="md"
                            href="{{ url('/') }}"
                            fullWidth="true"
                        >
                            Return to Home
                        </x-ui.button>
                    </div>
                </div>
            </x-ui.card>

            {{-- ── FOOTER NOTE ── --}}
            <div class="text-center">
                <p class="text-xs text-slate-400">Thank you for participating!</p>
            </div>
        </div>
    </div>
</x-layouts.exam>
