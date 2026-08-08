<x-layouts.app>
<div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- ── PAGE HEADER ── --}}
    <div class="rounded-sm mb-6 overflow-hidden"
         style="background: #0d1520; border: 1px solid rgba(255,255,255,0.07);">
        <div style="height: 1px; background: rgba(255,255,255,0.1);"></div>
        <div class="px-6 py-4 flex items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-sm flex items-center justify-center flex-shrink-0"
                     style="background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.09);">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-sm font-semibold text-white tracking-tight">{{ $mockExam->title }}</h1>
                    <p class="text-xs text-slate-600 mt-0.5 font-mono">PDF Preview &amp; Export</p>
                </div>
            </div>
            <x-ui.button href="{{ route('mock-exams.show', $mockExam) }}" variant="ghost" size="sm" icon="arrow-left">
                Back to Exam
            </x-ui.button>
        </div>
    </div>

    {{-- ── TWO-COLUMN LAYOUT ── --}}
    <div class="flex gap-5 items-start">

        {{-- ── LEFT: CONTROLS ── --}}
        <div style="width: 264px; min-width: 264px;" class="sticky top-4 space-y-3">

            {{-- Font Size --}}
            <div class="rounded-sm overflow-hidden"
                 style="background: #0d1520; border: 1px solid rgba(255,255,255,0.07);">
                <div class="px-4 py-2.5"
                     style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-widest">Font Size</span>
                </div>
                <div class="p-4 space-y-4">
                    <div class="flex items-baseline justify-between">
                        <span class="text-xs text-slate-600">Current</span>
                        <span id="fontSizeDisplay" class="text-xl font-bold text-white tabular-nums">{{ $fontSize }}pt</span>
                    </div>
                    <div>
                        <input type="range" id="fontSizeSlider"
                               min="8" max="14" step="0.5" value="{{ $fontSize }}"
                               class="w-full cursor-pointer"
                               style="accent-color: #888; height: 2px;">
                        <div class="flex justify-between mt-1.5">
                            <span class="text-xs text-slate-700">8pt</span>
                            <span class="text-xs text-slate-700">14pt</span>
                        </div>
                    </div>
                    <div class="grid grid-cols-3 gap-1.5">
                        @foreach([['8','XS'],['9','SM'],['10','MD'],['11','LG'],['12','XL'],['14','2X']] as [$sz,$label])
                            <button onclick="setFontSize({{ $sz }})"
                                    data-size="{{ $sz }}"
                                    class="preset-btn py-1.5 rounded-sm text-xs font-semibold transition-all duration-100"
                                    style="background: rgba(255,255,255,0.03); color: #64748b; border: 1px solid rgba(255,255,255,0.06);">
                                {{ $label }}
                                <span class="block text-[10px] font-normal opacity-50">{{ $sz }}pt</span>
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Export --}}
            <div class="rounded-sm overflow-hidden"
                 style="background: #0d1520; border: 1px solid rgba(255,255,255,0.07);">
                <div class="px-4 py-2.5"
                     style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-widest">Export</span>
                </div>
                <div class="p-4 space-y-2">
                    <button onclick="updatePreview()"
                            class="w-full flex items-center justify-center gap-2 py-2 rounded-sm text-xs font-medium transition-all duration-100 hover:brightness-125"
                            style="background: rgba(255,255,255,0.04); color: #94a3b8; border: 1px solid rgba(255,255,255,0.08);">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Refresh Preview
                    </button>

                    <a id="downloadExamBtn"
                       href="{{ route('mock-exams.pdf.exam', $mockExam) }}?font_size={{ $fontSize }}"
                       class="w-full flex items-center justify-center gap-2 py-2 rounded-sm text-xs font-medium transition-all duration-100 hover:brightness-125"
                       style="background: rgba(255,255,255,0.06); color: #e2e8f0; border: 1px solid rgba(255,255,255,0.12); text-decoration: none;">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Download Exam PDF
                    </a>

                    @if(Route::has('mock-exams.pdf.answer-key'))
                        <a id="downloadKeyBtn"
                           href="{{ route('mock-exams.pdf.answer-key', $mockExam) }}"
                           class="w-full flex items-center justify-center gap-2 py-2 rounded-sm text-xs font-medium transition-all duration-100 hover:brightness-125"
                           style="background: rgba(255,255,255,0.03); color: #64748b; border: 1px solid rgba(255,255,255,0.07); text-decoration: none;">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                            </svg>
                            Download Answer Key
                        </a>
                    @endif
                </div>
            </div>

            {{-- Details --}}
            <div class="rounded-sm overflow-hidden"
                 style="background: #0d1520; border: 1px solid rgba(255,255,255,0.07);">
                <div class="px-4 py-2.5"
                     style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-widest">Details</span>
                </div>
                <div class="p-4 space-y-2.5">
                    <div class="flex justify-between">
                        <span class="text-xs text-slate-600">Subjects</span>
                        <span class="text-xs text-slate-400">{{ $mockExam->subjectExams->count() }}</span>
                    </div>
                    @if($mockExam->starts_at)
                        <div class="flex justify-between">
                            <span class="text-xs text-slate-600">Date</span>
                            <span class="text-xs text-slate-400">{{ $mockExam->starts_at->format('d M Y') }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between">
                        <span class="text-xs text-slate-600">Format</span>
                        <span class="text-xs text-slate-400">A4 Portrait</span>
                    </div>
                </div>
            </div>

        </div>{{-- end left --}}

        {{-- ── RIGHT: PREVIEW ── --}}
        <div class="flex-1 min-w-0 overflow-hidden rounded-sm"
             style="border: 1px solid rgba(255,255,255,0.07);">

            <div class="flex items-center justify-between px-4 py-2"
                 style="background: #0d1520; border-bottom: 1px solid rgba(255,255,255,0.05);">
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full" style="background: #2a3040;"></div>
                    <div class="w-2 h-2 rounded-full" style="background: #2a3040;"></div>
                    <div class="w-2 h-2 rounded-full" style="background: #2a3040;"></div>
                    <span class="ml-1.5 text-xs text-slate-700 font-mono">preview</span>
                </div>
                <div id="previewStatus" class="flex items-center gap-1.5">
                    <div class="w-1.5 h-1.5 rounded-full bg-emerald-600"></div>
                    <span class="text-xs text-slate-600">Live</span>
                </div>
            </div>

            <iframe id="pdfPreview"
                    src="{{ route('mock-exams.pdf.preview', $mockExam) }}?font_size={{ $fontSize }}"
                    class="w-full border-0 block"
                    style="height: calc(100vh - 136px); min-height: 700px; background: #fff;"
                    title="PDF Preview">
            </iframe>
        </div>{{-- end right --}}

    </div>{{-- end two-column --}}

</div>

<script>
    const slider    = document.getElementById('fontSizeSlider');
    const sizeDisp  = document.getElementById('fontSizeDisplay');
    const dlExamBtn = document.getElementById('downloadExamBtn');
    const iframe    = document.getElementById('pdfPreview');
    const examId    = {{ $mockExam->id }};

    function setFontSize(size) {
        slider.value = size;
        syncUI();
    }

    function syncUI() {
        const sz = parseFloat(slider.value);
        sizeDisp.textContent = sz + 'pt';
        if (dlExamBtn) dlExamBtn.href = `/mock-exams/${examId}/pdf?font_size=${sz}`;
        document.querySelectorAll('.preset-btn').forEach(btn => {
            const active = parseFloat(btn.dataset.size) === sz;
            btn.style.background = active ? 'rgba(255,255,255,0.08)' : 'rgba(255,255,255,0.03)';
            btn.style.color      = active ? '#e2e8f0' : '#64748b';
            btn.style.border     = active ? '1px solid rgba(255,255,255,0.16)' : '1px solid rgba(255,255,255,0.06)';
        });
    }

    function updatePreview() {
        const sz = parseFloat(slider.value);
        syncUI();
        const status = document.getElementById('previewStatus');
        status.innerHTML = '<div class="w-1.5 h-1.5 rounded-full bg-yellow-600 animate-pulse"></div><span class="text-xs text-slate-600">Loading…</span>';
        iframe.src = `/mock-exams/${examId}/pdf/preview?font_size=${sz}`;
        iframe.onload = () => {
            status.innerHTML = '<div class="w-1.5 h-1.5 rounded-full bg-emerald-600"></div><span class="text-xs text-slate-600">Live</span>';
        };
    }

    document.addEventListener('keydown', e => {
        if ((e.metaKey || e.ctrlKey) && e.key === 'Enter') { e.preventDefault(); updatePreview(); }
    });

    slider.addEventListener('input', syncUI);
    syncUI();
</script>
</x-layouts.app>