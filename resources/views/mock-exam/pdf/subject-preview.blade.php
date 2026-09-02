<x-layouts.app>
    <div class="min-h-screen" style="background: #0f172a;">
        {{-- Header --}}
        <div class="px-6 py-4 border-b border-slate-800" style="background: rgba(15,23,42,0.95);">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-xl font-bold text-white">Subject Exam Preview</h1>
                    <p class="text-sm text-slate-400 mt-1">{{ $subjectExam->getDisplayTitle() }}</p>
                </div>
                <a href="{{ route('mock-exams.show', $mockExam) }}"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-sm text-sm font-medium transition-all"
                   style="background: rgba(255,255,255,0.05); color: #94a3b8; border: 1px solid rgba(255,255,255,0.1);">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Mock Exam
                </a>
            </div>
        </div>

        {{-- Main Content --}}
        <div class="flex gap-6 p-6" style="height: calc(100vh - 80px);">

            {{-- ── LEFT: CONTROLS PANEL ── --}}
            <div class="w-80 flex-shrink-0 space-y-4 overflow-y-auto">

                {{-- Font Size Card --}}
                <div class="overflow-hidden rounded-sm shadow-lg"
                     style="background: #0f172a; border: 1px solid rgba(255,255,255,0.07);">
                    <div class="px-4 py-3 flex items-center gap-2"
                         style="border-bottom: 1px solid rgba(255,255,255,0.07); background: rgba(255,255,255,0.03);">
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4 6h16M4 12h16m-7 6h7"/>
                        </svg>
                        <span class="text-xs font-semibold text-slate-300 uppercase tracking-widest">Font Size</span>
                    </div>
                    <div class="p-4">
                        <div class="grid grid-cols-4 gap-2">
                            @foreach([8, 9, 10, 10.5, 11, 12, 13, 14, 16, 18, 20, 22] as $sz)
                                <button onclick="setFontSize({{ $sz }})"
                                        class="font-size-btn flex flex-col items-center justify-center py-2 rounded-sm transition-all duration-150 {{ $fontSize == $sz ? 'ring-2 ring-blue-500' : '' }}"
                                        data-size="{{ $sz }}"
                                        style="background: {{ $fontSize == $sz ? 'rgba(59,130,246,0.15)' : 'rgba(255,255,255,0.03)' }};
                                               color: {{ $fontSize == $sz ? '#60a5fa' : '#94a3b8' }};
                                               border: 1px solid {{ $fontSize == $sz ? 'rgba(59,130,246,0.4)' : 'rgba(255,255,255,0.08)' }};">
                                    <span class="text-sm font-semibold">{{ $sz }}</span>
                                    <span class="block text-[10px] font-normal opacity-70">{{ $sz }}pt</span>
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>
            

                {{-- Actions Card --}}
                <div class="overflow-hidden rounded-sm shadow-lg"
                     style="background: #0f172a; border: 1px solid rgba(255,255,255,0.07);">
                    <div class="px-4 py-3 flex items-center gap-2"
                         style="border-bottom: 1px solid rgba(255,255,255,0.07); background: rgba(255,255,255,0.03);">
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                        </svg>
                        <span class="text-xs font-semibold text-slate-300 uppercase tracking-widest">Export</span>
                    </div>
                    <div class="p-4 space-y-2">
                        <button onclick="updatePreview()"
                                class="w-full flex items-center justify-center gap-2 py-2.5 rounded-sm text-sm font-semibold transition-all duration-150 hover:brightness-110 active:scale-[0.98]"
                                style="background: #1e3a5f; color: #93c5fd; border: 1px solid rgba(147,197,253,0.2);">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            Refresh Preview
                        </button>

                        <a id="downloadBtn"
                           href="{{ route('mock-exams.subject-exams.pdf.download', [$mockExam, $subjectExam]) }}?font_size={{ $fontSize }}"
                           class="w-full flex items-center justify-center gap-2 py-2.5 rounded-sm text-sm font-semibold transition-all duration-150 hover:brightness-110 active:scale-[0.98]"
                           style="background: rgba(201,162,39,0.15); color: #c9a227; border: 1px solid rgba(201,162,39,0.35); text-decoration: none;">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Download PDF
                        </a>
                    </div>
                </div>

                {{-- Exam Info Card --}}
                <div class="overflow-hidden rounded-sm shadow"
                     style="background: #0f172a; border: 1px solid rgba(255,255,255,0.07);">
                    <div class="px-4 py-3 flex items-center gap-2"
                         style="border-bottom: 1px solid rgba(255,255,255,0.07); background: rgba(255,255,255,0.03);">
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="text-xs font-semibold text-slate-300 uppercase tracking-widest">Exam Details</span>
                    </div>
                    <div class="p-4 space-y-2">
                        @if($subjectExam->academicGroup)
                            <div class="flex justify-between items-start gap-2">
                                <span class="text-xs text-slate-500">Group</span>
                                <span class="text-xs text-slate-300 font-medium text-right">{{ $subjectExam->academicGroup->name }}</span>
                            </div>
                        @endif
                        @if($subjectExam->academicLevel)
                            <div class="flex justify-between items-start gap-2">
                                <span class="text-xs text-slate-500">Level</span>
                                <span class="text-xs text-slate-300 font-medium text-right">{{ $subjectExam->academicLevel->name }}</span>
                            </div>
                        @endif
                        @if($subjectExam->academicSubject)
                            <div class="flex justify-between items-start gap-2">
                                <span class="text-xs text-slate-500">Subject</span>
                                <span class="text-xs text-slate-300 font-medium text-right">{{ $subjectExam->academicSubject->name }}</span>
                            </div>
                        @endif
                        @if($subjectExam->duration_in_minutes)
                            <div class="flex justify-between items-start gap-2">
                                <span class="text-xs text-slate-500">Duration</span>
                                <span class="text-xs text-slate-300 font-medium">{{ $subjectExam->duration_in_minutes }} min</span>
                            </div>
                        @endif
                        <div class="flex justify-between items-start gap-2">
                            <span class="text-xs text-slate-500">Sections</span>
                            <span class="text-xs text-slate-300 font-medium">{{ $subjectExam->sections->count() }}</span>
                        </div>
                        <div class="flex justify-between items-start gap-2">
                            <span class="text-xs text-slate-500">Questions</span>
                            <span class="text-xs text-slate-300 font-medium">{{ $subjectExam->sections->sum(fn($s) => $s->questions->count()) }}</span>
                        </div>
                        <div class="flex justify-between items-start gap-2">
                            <span class="text-xs text-slate-500">Total Marks</span>
                            <span class="text-xs font-medium" style="color: #c9a227;">{{ number_format($subjectExam->sections->sum(fn($s) => $s->getTotalMarks()), 1) }}</span>
                        </div>
                    </div>
                </div>

            </div>
            {{-- end left panel --}}

            {{-- ── RIGHT: PREVIEW IFRAME ── --}}
            <div class="flex-1 min-w-0 overflow-hidden rounded-sm shadow-2xl"
                 style="border: 1px solid rgba(255,255,255,0.08);">

                {{-- iframe toolbar --}}
                <div class="flex items-center justify-between px-4 py-2.5"
                     style="background: #0f172a; border-bottom: 1px solid rgba(255,255,255,0.07);">
                    <div class="flex items-center gap-2">
                        <div class="w-2.5 h-2.5 rounded-full bg-red-500 opacity-70"></div>
                        <div class="w-2.5 h-2.5 rounded-full bg-yellow-400 opacity-70"></div>
                        <div class="w-2.5 h-2.5 rounded-full bg-green-500 opacity-70"></div>
                        <span class="ml-2 text-xs text-slate-500 font-mono">PDF Preview</span>
                    </div>
                    <div id="previewStatus" class="flex items-center gap-1.5">
                        <div class="w-1.5 h-1.5 rounded-full bg-emerald-400"></div>
                        <span class="text-xs text-slate-500">Live</span>
                    </div>
                </div>

                {{-- The iframe --}}
                <iframe id="pdfPreview"
                        src="{{ route('mock-exams.subject-exams.pdf.preview', [$mockExam, $subjectExam]) }}?font_size={{ $fontSize }}"
                        class="w-full border-0 block"
                        style="height: calc(100vh - 140px); min-height: 700px; background: #fff;"
                        title="PDF Preview">
                </iframe>
            </div>
            {{-- end right panel --}}

        </div>{{-- end two-column flex --}}

    </div>

    <script>
        let currentFontSize = {{ $fontSize }};

        function setFontSize(size) {
            currentFontSize = size;

            // Update button styles
            document.querySelectorAll('.font-size-btn').forEach(btn => {
                const btnSize = parseFloat(btn.dataset.size);
                if (btnSize === size) {
                    btn.style.background = 'rgba(59,130,246,0.15)';
                    btn.style.color = '#60a5fa';
                    btn.style.border = '1px solid rgba(59,130,246,0.4)';
                    btn.classList.add('ring-2', 'ring-blue-500');
                } else {
                    btn.style.background = 'rgba(255,255,255,0.03)';
                    btn.style.color = '#94a3b8';
                    btn.style.border = '1px solid rgba(255,255,255,0.08)';
                    btn.classList.remove('ring-2', 'ring-blue-500');
                }
            });

            updatePreview();
        }

        function updatePreview() {
            const iframe = document.getElementById('pdfPreview');
            const downloadBtn = document.getElementById('downloadBtn');
            const status = document.getElementById('previewStatus');

            // Show loading status
            status.innerHTML = `
                <svg class="animate-spin w-3 h-3 text-blue-400" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="text-xs text-slate-500">Loading...</span>
            `;

            // Update iframe and download URL
            const newUrl = '{{ route('mock-exams.subject-exams.pdf.preview', [$mockExam, $subjectExam]) }}?font_size=' + currentFontSize;
            iframe.src = newUrl;

            downloadBtn.href = '{{ route('mock-exams.subject-exams.pdf.download', [$mockExam, $subjectExam]) }}?font_size=' + currentFontSize;

            // Reset status after load
            iframe.onload = function() {
                status.innerHTML = `
                    <div class="w-1.5 h-1.5 rounded-full bg-emerald-400"></div>
                    <span class="text-xs text-slate-500">Live</span>
                `;
            };
        }
    </script>
</x-layouts.app>
