<x-layouts.app>
<div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- ── PAGE HEADER ── --}}
    <div class="overflow-hidden rounded-sm bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 shadow-2xl mb-6">
        <div class="h-[3px] w-full" style="background: linear-gradient(to right, #c9a227, #e8c85a, #c9a227);"></div>
        <div class="px-8 py-5 flex items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                {{-- Icon badge --}}
                <div class="flex-shrink-0 w-10 h-10 rounded-sm flex items-center justify-center"
                     style="background: rgba(201,162,39,0.15); border: 1px solid rgba(201,162,39,0.3);">
                    <svg class="w-5 h-5" style="color: #c9a227;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-white leading-snug tracking-tight font-serif">
                        {{ $mockExam->title }}
                    </h1>
                    <p class="text-slate-400 text-xs mt-0.5">PDF Preview &amp; Export Studio</p>
                </div>
            </div>
            <x-ui.button href="{{ route('mock-exams.show', $mockExam) }}" variant="ghost" size="sm" icon="arrow-left">
                Back to Exam
            </x-ui.button>
        </div>
    </div>

    {{-- ── TWO-COLUMN LAYOUT ── --}}
    <div class="flex gap-5 items-start">

        {{-- ── LEFT: SETTINGS PANEL ── --}}
        <div style="width: 288px; min-width: 288px;" class="sticky top-4 space-y-4">

            {{-- Font Size Card --}}
            <div class="overflow-hidden rounded-sm shadow-lg"
                 style="background: #0f172a; border: 1px solid rgba(255,255,255,0.07);">
                <div class="px-4 py-3 flex items-center gap-2"
                     style="border-bottom: 1px solid rgba(255,255,255,0.07); background: rgba(255,255,255,0.03);">
                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6h16M4 12h10M4 18h7"/>
                    </svg>
                    <span class="text-xs font-semibold text-slate-300 uppercase tracking-widest">Font Size</span>
                </div>
                <div class="p-4 space-y-4">
                    {{-- Current size display --}}
                    <div class="flex items-end justify-between">
                        <span class="text-slate-400 text-xs">Current size</span>
                        <span id="fontSizeDisplay"
                              class="font-bold text-2xl tabular-nums"
                              style="color: #c9a227; font-family: 'DejaVu Sans', monospace;">
                            {{ $fontSize }}pt
                        </span>
                    </div>

                    {{-- Slider --}}
                    <div class="space-y-1">
                        <input type="range" id="fontSizeSlider"
                               min="8" max="14" step="0.5" value="{{ $fontSize }}"
                               class="w-full h-1.5 rounded-full appearance-none cursor-pointer"
                               style="background: #1e293b; accent-color: #c9a227;">
                        <div class="flex justify-between">
                            <span class="text-xs text-slate-600">8pt</span>
                            <span class="text-xs text-slate-600">14pt</span>
                        </div>
                    </div>

                    {{-- Presets --}}
                    <div>
                        <span class="text-xs text-slate-500 uppercase tracking-widest block mb-2">Presets</span>
                        <div class="grid grid-cols-3 gap-1.5">
                            @foreach([['8', 'XS'], ['9', 'SM'], ['10', 'MD'], ['11', 'LG'], ['12', 'XL'], ['14', '2X']] as [$sz, $label])
                                <button onclick="setFontSize({{ $sz }})"
                                        data-size="{{ $sz }}"
                                        class="preset-btn py-1.5 rounded-sm text-xs font-semibold transition-all duration-150"
                                        style="background: #1e293b; color: #94a3b8; border: 1px solid transparent;">
                                    {{ $label }}
                                    <span class="block text-[10px] font-normal opacity-70">{{ $sz }}pt</span>
                                </button>
                            @endforeach
                        </div>
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

                    <a id="downloadExamBtn"
                       href="{{ route('mock-exams.pdf.exam', $mockExam) }}?font_size={{ $fontSize }}"
                       class="w-full flex items-center justify-center gap-2 py-2.5 rounded-sm text-sm font-semibold transition-all duration-150 hover:brightness-110 active:scale-[0.98]"
                       style="background: rgba(201,162,39,0.15); color: #c9a227; border: 1px solid rgba(201,162,39,0.35); text-decoration: none;">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Download Exam PDF
                    </a>

                    @if(Route::has('mock-exams.pdf.answer-key'))
                        <a id="downloadKeyBtn"
                           href="{{ route('mock-exams.pdf.answer-key', $mockExam) }}"
                           class="w-full flex items-center justify-center gap-2 py-2.5 rounded-sm text-sm font-semibold transition-all duration-150 hover:brightness-110 active:scale-[0.98]"
                           style="background: rgba(185,28,28,0.12); color: #fca5a5; border: 1px solid rgba(185,28,28,0.3); text-decoration: none;">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                            </svg>
                            Download Answer Key
                        </a>
                    @endif
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
                    <div class="flex justify-between items-start gap-2">
                        <span class="text-xs text-slate-500">Subjects</span>
                        <span class="text-xs text-slate-300 font-medium text-right">
                            {{ $mockExam->subjectExams->count() }} subject{{ $mockExam->subjectExams->count() !== 1 ? 's' : '' }}
                        </span>
                    </div>
                    @if($mockExam->starts_at)
                        <div class="flex justify-between items-start gap-2">
                            <span class="text-xs text-slate-500">Exam Date</span>
                            <span class="text-xs text-slate-300 font-medium">{{ $mockExam->starts_at->format('d M Y') }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between items-start gap-2">
                        <span class="text-xs text-slate-500">Format</span>
                        <span class="text-xs font-medium" style="color: #c9a227;">A4 Portrait</span>
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
                    src="{{ route('mock-exams.pdf.preview', $mockExam) }}?font_size={{ $fontSize }}"
                    class="w-full border-0 block"
                    style="height: calc(100vh - 140px); min-height: 700px; background: #fff;"
                    title="PDF Preview">
            </iframe>
        </div>
        {{-- end right panel --}}

    </div>{{-- end two-column flex --}}

</div>

<script>
    const slider     = document.getElementById('fontSizeSlider');
    const sizeDisp   = document.getElementById('fontSizeDisplay');
    const dlExamBtn  = document.getElementById('downloadExamBtn');
    const iframe     = document.getElementById('pdfPreview');
    const examId     = {{ $mockExam->id }};

    function setFontSize(size) {
        slider.value = size;
        syncUI();
    }

    function syncUI() {
        const sz = parseFloat(slider.value);
        sizeDisp.textContent = sz + 'pt';

        // update download URL
        if (dlExamBtn) dlExamBtn.href = `/mock-exams/${examId}/pdf?font_size=${sz}`;

        // highlight active preset button
        document.querySelectorAll('.preset-btn').forEach(btn => {
            const bsz = parseFloat(btn.dataset.size);
            if (bsz === sz) {
                btn.style.background = 'rgba(201,162,39,0.18)';
                btn.style.color      = '#c9a227';
                btn.style.border     = '1px solid rgba(201,162,39,0.4)';
            } else {
                btn.style.background = '#1e293b';
                btn.style.color      = '#94a3b8';
                btn.style.border     = '1px solid transparent';
            }
        });
    }

    function updatePreview() {
        const sz = parseFloat(slider.value);
        syncUI();
        const status = document.getElementById('previewStatus');
        status.innerHTML = '<div class="w-1.5 h-1.5 rounded-full bg-yellow-400 animate-pulse"></div><span class="text-xs text-slate-500">Loading…</span>';
        iframe.src = `/mock-exams/${examId}/pdf/preview?font_size=${sz}`;
        iframe.onload = () => {
            status.innerHTML = '<div class="w-1.5 h-1.5 rounded-full bg-emerald-400"></div><span class="text-xs text-slate-500">Live</span>';
        };
    }

    // Keyboard shortcut: Cmd/Ctrl+Enter to refresh
    document.addEventListener('keydown', e => {
        if ((e.metaKey || e.ctrlKey) && e.key === 'Enter') {
            e.preventDefault();
            updatePreview();
        }
    });

    // Boot
    slider.addEventListener('input', syncUI);
    syncUI();
</script>
</x-layouts.app>