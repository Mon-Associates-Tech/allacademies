<x-layouts.exam>
    <style>
        :root {
            --card-bg: #ffffff;
            --card-border: #e2e8f0;
            --card-text: #1e293b;
            --section-bg: #f8fafc;
            --section-border: #cbd5e1;
            --input-bg: #eff6ff;
            --input-border: #bfdbfe;
            --input-text: #1e293b;
        }

        .dark {
            --card-bg: #1e293b;
            --card-border: #334155;
            --card-text: #e2e8f0;
            --section-bg: #0f172a;
            --section-border: #475569;
            --input-bg: #0f172a;
            --input-border: #334155;
            --input-text: #e2e8f0;
        }

        body { background-color: var(--section-bg); }

        .preview-card {
            background-color: var(--card-bg);
            border-color: var(--card-border);
            color: var(--card-text);
        }

        .preview-section {
            background-color: var(--section-bg);
            border-color: var(--section-border);
        }

        .preview-input-group {
            background-color: var(--input-bg);
            border-color: var(--input-border);
            color: var(--input-text);
        }

        .preview-text-blue { color: #2563eb; }
        .dark .preview-text-blue { color: #60a5fa; }

        .preview-text-indigo { color: #4f46e5; }
        .dark .preview-text-indigo { color: #818cf8; }

        .preview-text-amber { color: #b45309; }
        .dark .preview-text-amber { color: #fbbf24; }

        .preview-text-muted { opacity: 0.6; }

        .preview-divider {
            border-color: var(--section-border);
        }

        .info-card-blue {
            border-left: 3px solid #2563eb !important;
            border-radius: 0 2px 2px 0 !important;
        }
        .dark .info-card-blue { border-left-color: #60a5fa !important; }

        .info-card-indigo {
            border-left: 3px solid #4f46e5 !important;
            border-radius: 0 2px 2px 0 !important;
        }
        .dark .info-card-indigo { border-left-color: #818cf8 !important; }

        .info-card-amber {
            border-left: 3px solid #b45309 !important;
            border-radius: 0 2px 2px 0 !important;
        }
        .dark .info-card-amber { border-left-color: #fbbf24 !important; }
    </style>

    <div class="min-h-screen flex flex-col items-center justify-center px-4 sm:px-6 py-8">
        <div class="w-full max-w-2xl">

            <div class="flex items-center justify-between mb-6">
                <div></div>
                <x-snippets.theme-toggle />
            </div>

            {{-- Main Card --}}
            <div class="preview-card border overflow-hidden shadow-xl" style="border-radius: 2px;">

                {{-- Accent bar --}}
                <div class="h-1.5 w-full bg-gradient-to-r from-blue-600 via-indigo-600 to-violet-600"></div>

                {{-- Header --}}
                <div class="px-8 pt-8 pb-7 border-b preview-section">
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0 w-11 h-11 bg-gradient-to-br from-blue-600 to-indigo-600 flex items-center justify-center shadow-md"
                             style="border-radius: 2px;">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-semibold uppercase tracking-widest preview-text-blue mb-1"
                               style="letter-spacing: 0.15em;">Examination Preview</p>
                            <h1 class="text-2xl font-bold leading-snug"
                                style="letter-spacing: -0.02em; font-family: 'Georgia', serif;">
                                {{ $exam->title }}
                            </h1>
                            @if($exam->instructions)
                                <p class="text-sm mt-2 leading-relaxed preview-text-muted">
                                    {{ $exam->instructions }}
                                </p>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Content --}}
                <div class="px-8 py-7 space-y-6">

                    {{-- Candidate --}}
                    <div>
                        <div class="flex items-center gap-2 mb-3">
                            <svg class="w-3.5 h-3.5 preview-text-blue" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                            </svg>
                            <h2 class="text-xs font-semibold uppercase preview-text-muted"
                                style="letter-spacing: 0.08em;">Candidate</h2>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div class="preview-input-group info-card-blue p-4 border">
                                <p class="text-xs font-medium preview-text-blue uppercase mb-1"
                                   style="letter-spacing: 0.05em;">Full Name</p>
                                <p class="text-base font-semibold">{{ $candidateName }}</p>
                            </div>
                            <div class="preview-input-group info-card-blue p-4 border">
                                <p class="text-xs font-medium preview-text-blue uppercase mb-1"
                                   style="letter-spacing: 0.05em;">Email Address</p>
                                <p class="text-base font-semibold break-all">{{ $candidateEmail }}</p>
                            </div>
                        </div>
                    </div>

                    <hr class="border-t preview-divider">

                    {{-- Programme/Course --}}
                    <div>
                        <div class="flex items-center gap-2 mb-3">
                            <svg class="w-3.5 h-3.5 preview-text-indigo" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"/>
                            </svg>
                            <h2 class="text-xs font-semibold uppercase preview-text-muted"
                                style="letter-spacing: 0.08em;">Programme/Course</h2>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div class="preview-input-group info-card-indigo p-4 border">
                                <p class="text-xs font-medium preview-text-indigo uppercase mb-1"
                                   style="letter-spacing: 0.05em;">Programme/Course</p>
                                <p class="text-base font-semibold">
                                    {{ $exam->academicSubject?->name ?? 'Not specified' }}
                                </p>
                            </div>
                            <div class="preview-input-group info-card-indigo p-4 border">
                                <p class="text-xs font-medium preview-text-indigo uppercase mb-1"
                                   style="letter-spacing: 0.05em;">Profession</p>
                                <p class="text-base font-semibold">
                                    {{ $exam->academicSubject?->academicLevel?->name ?? 'Not specified' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <hr class="border-t preview-divider">

                    {{-- Stats --}}
                    <div>
                        <div class="flex items-center gap-2 mb-3">
                            <svg class="w-3.5 h-3.5 preview-text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                            <h2 class="text-xs font-semibold uppercase preview-text-muted"
                                style="letter-spacing: 0.08em;">At a Glance</h2>
                        </div>
                        <div class="grid grid-cols-3 gap-3">
                            <div class="preview-section border text-center py-5 px-3" style="border-radius: 2px;">
                                <svg class="w-5 h-5 mx-auto mb-2 preview-text-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M4 6h16M4 10h16M4 14h8"/>
                                </svg>
                                <p class="text-2xl font-bold">{{ $exam->sections->count() }}</p>
                                <p class="text-xs uppercase tracking-wider preview-text-muted mt-1"
                                   style="font-size: 10px; letter-spacing: 0.06em;">Sections</p>
                            </div>
                            <div class="preview-section border text-center py-5 px-3" style="border-radius: 2px;">
                                <svg class="w-5 h-5 mx-auto mb-2 preview-text-indigo" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <p class="text-2xl font-bold">{{ $exam->sections->sum('questions_count') }}</p>
                                <p class="text-xs uppercase tracking-wider preview-text-muted mt-1"
                                   style="font-size: 10px; letter-spacing: 0.06em;">Questions</p>
                            </div>
                            <div class="preview-section border text-center py-5 px-3" style="border-radius: 2px;">
                                <svg class="w-5 h-5 mx-auto mb-2 text-violet-500 dark:text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <p class="text-2xl font-bold">
                                    {{ $exam->duration_in_minutes ? $exam->duration_in_minutes . ' min' : '—' }}
                                </p>
                                <p class="text-xs uppercase tracking-wider preview-text-muted mt-1"
                                   style="font-size: 10px; letter-spacing: 0.06em;">Duration</p>
                            </div>
                        </div>
                    </div>

                    {{-- Total Marks --}}
                    @if($exam->total_marks)
                        <div class="preview-input-group info-card-amber border flex items-center justify-between px-5 py-4">
                            <div>
                                <p class="text-xs font-semibold uppercase preview-text-amber mb-0.5"
                                   style="letter-spacing: 0.06em;">Total Marks</p>
                                <p class="text-xs preview-text-muted">Maximum achievable score</p>
                            </div>
                            <p class="text-4xl font-bold preview-text-amber">{{ $exam->total_marks }}</p>
                        </div>
                    @endif
                </div>

                {{-- Actions --}}
                <div class="px-8 py-6 preview-section border-t space-y-3">
                    <a href="{{ route('examination-hub.take.start', $exam) }}"
                       class="flex items-center justify-center gap-2.5 w-full px-6 py-3.5 font-semibold transition-all duration-200 shadow-md hover:shadow-lg hover:-translate-y-px"
                       style="border-radius: 2px; background: linear-gradient(to right, #2563eb, #4f46e5); color: #ffffff !important;"
                       onmouseover="this.style.background='linear-gradient(to right, #1d4ed8, #4338ca)'"
                       onmouseout="this.style.background='linear-gradient(to right, #2563eb, #4f46e5)'"
                       aria-label="Proceed to start the examination">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Start Examination
                    </a>

                    <a href="{{ route('examination-hub.take.join') }}"
                       class="flex items-center justify-center gap-2 w-full px-6 py-3 border font-medium transition-all duration-200 text-center preview-text-muted"
                       style="border-radius: 2px; border-color: var(--section-border);"
                       onmouseover="this.style.backgroundColor='var(--section-bg)'"
                       onmouseout="this.style.backgroundColor='transparent'"
                       aria-label="Return to join page">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Cancel
                    </a>
                </div>

                {{-- Footer --}}
                <div class="px-8 py-4 preview-section border-t">
                    <p class="text-xs text-center leading-relaxed preview-text-muted">
                        Verify all details above before proceeding. The timer starts immediately once you begin.
                    </p>
                </div>

            </div>
        </div>
    </div>
</x-layouts.exam>
