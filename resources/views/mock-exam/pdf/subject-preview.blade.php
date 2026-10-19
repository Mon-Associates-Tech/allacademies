<x-layouts.app>
    <div class="min-h-screen" style="background: #0f172a;">
        {{-- Header --}}
        <div class="px-6 py-4 border-b border-slate-800" style="background: rgba(15,23,42,0.95);">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-xl font-bold text-white">Subject Exam Preview</h1>
                    <p class="text-sm text-slate-400 mt-1">{{ $subjectExam->getDisplayTitle() }}</p>
                </div>
                @if(isset($fromSubscription) && $fromSubscription)
                    <a href="{{ route('mock-exams.subscription.dashboard') }}"
                       class="inline-flex items-center gap-2 px-4 py-2 rounded-sm text-sm font-medium transition-all"
                       style="background: rgba(255,255,255,0.05); color: #94a3b8; border: 1px solid rgba(255,255,255,0.1);">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Back to My Subscriptions
                    </a>
                @else
                    <a href="{{ route('mock-exams.show', $mockExam) }}"
                       class="inline-flex items-center gap-2 px-4 py-2 rounded-sm text-sm font-medium transition-all"
                       style="background: rgba(255,255,255,0.05); color: #94a3b8; border: 1px solid rgba(255,255,255,0.1);">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Back to Mock Exam
                    </a>
                @endif
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
            

                {{-- Choice Layout Card --}}
                <div class="overflow-hidden rounded-sm shadow-lg"
                     style="background: #0f172a; border: 1px solid rgba(255,255,255,0.07);">
                    <div class="px-4 py-3 flex items-center gap-2"
                         style="border-bottom: 1px solid rgba(255,255,255,0.07); background: rgba(255,255,255,0.03);">
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4 6h16M4 12h16M4 18h7"/>
                        </svg>
                        <span class="text-xs font-semibold text-slate-300 uppercase tracking-widest">Choice Layout</span>
                    </div>
                    <div class="p-4 space-y-2">
                        @php
                            $choiceLayout = $subjectExam->layout_config['choiceLayout'] ?? 'vertical';
                        @endphp
                        <button onclick="setChoiceLayout('vertical')"
                                id="choiceLayoutVertical"
                                class="choice-layout-btn w-full flex items-center gap-3 py-2.5 px-3 rounded-sm text-sm font-semibold transition-all duration-150 {{ $choiceLayout === 'vertical' ? 'ring-2 ring-blue-500' : '' }}"
                                style="background: {{ $choiceLayout === 'vertical' ? 'rgba(59,130,246,0.15)' : 'rgba(255,255,255,0.03)' }};
                                       color: {{ $choiceLayout === 'vertical' ? '#60a5fa' : '#94a3b8' }};
                                       border: 1px solid {{ $choiceLayout === 'vertical' ? 'rgba(59,130,246,0.4)' : 'rgba(255,255,255,0.08)' }};">
                            <span class="flex flex-col gap-0.5 text-xs" style="min-width: 60px;">
                                <span>a.</span>
                                <span>b.</span>
                                <span>c.</span>
                                <span>d.</span>
                            </span>
                            <span class="text-xs opacity-70">Vertical</span>
                        </button>

                        <button onclick="setChoiceLayout('horizontal')"
                                id="choiceLayoutHorizontal"
                                class="choice-layout-btn w-full flex items-center gap-3 py-2.5 px-3 rounded-sm text-sm font-semibold transition-all duration-150 {{ $choiceLayout === 'horizontal' ? 'ring-2 ring-blue-500' : '' }}"
                                style="background: {{ $choiceLayout === 'horizontal' ? 'rgba(59,130,246,0.15)' : 'rgba(255,255,255,0.03)' }};
                                       color: {{ $choiceLayout === 'horizontal' ? '#60a5fa' : '#94a3b8' }};
                                       border: 1px solid {{ $choiceLayout === 'horizontal' ? 'rgba(59,130,246,0.4)' : 'rgba(255,255,255,0.08)' }};">
                            <span class="flex flex-row gap-2 text-xs" style="min-width: 60px;">
                                <span>a.</span>
                                <span>b.</span>
                                <span>c.</span>
                                <span>d.</span>
                            </span>
                            <span class="text-xs opacity-70">Horizontal</span>
                        </button>

                        <button onclick="setChoiceLayout('grid')"
                                id="choiceLayoutGrid"
                                class="choice-layout-btn w-full flex items-center gap-3 py-2.5 px-3 rounded-sm text-sm font-semibold transition-all duration-150 {{ $choiceLayout === 'grid' ? 'ring-2 ring-blue-500' : '' }}"
                                style="background: {{ $choiceLayout === 'grid' ? 'rgba(59,130,246,0.15)' : 'rgba(255,255,255,0.03)' }};
                                       color: {{ $choiceLayout === 'grid' ? '#60a5fa' : '#94a3b8' }};
                                       border: 1px solid {{ $choiceLayout === 'grid' ? 'rgba(59,130,246,0.4)' : 'rgba(255,255,255,0.08)' }};">
                            <span class="grid grid-cols-2 gap-0.5 gap-x-3 text-xs" style="min-width: 60px;">
                                <span>a.</span>
                                <span>b.</span>
                                <span>c.</span>
                                <span>d.</span>
                            </span>
                            <span class="text-xs opacity-70">Grid (2×2)</span>
                        </button>
                    </div>
                </div>

                {{-- Edit Layout Card --}}
                <div class="overflow-hidden rounded-sm shadow-lg"
                     style="background: #0f172a; border: 1px solid rgba(255,255,255,0.07);">
                    <div class="px-4 py-3 flex items-center gap-2"
                         style="border-bottom: 1px solid rgba(255,255,255,0.07); background: rgba(255,255,255,0.03);">
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        <span class="text-xs font-semibold text-slate-300 uppercase tracking-widest">Layout Editor</span>
                    </div>
                    <div class="p-4 space-y-2">
                        <button id="editLayoutBtn" onclick="toggleEditMode()"
                                class="w-full flex items-center justify-center gap-2 py-2.5 rounded-sm text-sm font-semibold transition-all duration-150 hover:brightness-110 active:scale-[0.98]"
                                style="background: rgba(59,130,246,0.15); color: #60a5fa; border: 1px solid rgba(59,130,246,0.3);">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Edit Layout
                        </button>

                        <button id="saveLayoutBtn" onclick="saveLayoutFromPreview()"
                                class="w-full flex items-center justify-center gap-2 py-2.5 rounded-sm text-sm font-semibold transition-all duration-150 hover:brightness-110 active:scale-[0.98]"
                                style="background: rgba(34,197,94,0.15); color: #4ade80; border: 1px solid rgba(34,197,94,0.3);"
                                style="display: none;">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M5 13l4 4L19 7"/>
                            </svg>
                            Save Layout
                        </button>

                        <div id="layoutSaveStatus" class="text-xs text-center" style="color: #94a3b8; display: none;"></div>
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

                        <x-exam.button
                            type="button"
                            variant="primary"
                            clientLoading
                            data-download-url="{{ isset($fromSubscription) && $fromSubscription
                                ? route('mock-exams.subject-exams.subscription-preview.content', [$mockExam, $subjectExam]) . '?font_size=' . $fontSize
                                : route('mock-exams.subject-exams.pdf.download', [$mockExam, $subjectExam]) . '?font_size=' . $fontSize }}"
                            loading="Generating PDF..."
                            id="downloadBtn"
                            class="w-full">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Download PDF
                        </x-exam.button>

                        @if(isset($fromSubscription) && $fromSubscription && Route::has('mock-exams.subscription-preview.answer-key'))
                            <a id="answerKeyBtn"
                               href="{{ route('mock-exams.subscription-preview.answer-key', $mockExam) }}"
                               target="_blank"
                               class="w-full flex items-center justify-center gap-2 py-2.5 rounded-sm text-sm font-semibold transition-all duration-150 hover:brightness-110 active:scale-[0.98]"
                               style="background: rgba(124,58,237,0.12); color: #a78bfa; border: 1px solid rgba(124,58,237,0.3); text-decoration: none;">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                                </svg>
                                Marking Scheme
                            </a>
                        @elseif(Route::has('mock-exams.pdf.answer-key'))
                            <a id="answerKeyBtn"
                               href="{{ route('mock-exams.pdf.answer-key', $mockExam) }}"
                               target="_blank"
                               class="w-full flex items-center justify-center gap-2 py-2.5 rounded-sm text-sm font-semibold transition-all duration-150 hover:brightness-110 active:scale-[0.98]"
                               style="background: rgba(124,58,237,0.12); color: #a78bfa; border: 1px solid rgba(124,58,237,0.3); text-decoration: none;">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                                </svg>
                                Marking Scheme
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
                @php
                    $previewRoute = isset($fromSubscription) && $fromSubscription
                        ? route('mock-exams.subject-exams.subscription-preview.content', [$mockExam, $subjectExam])
                        : route('mock-exams.subject-exams.pdf.preview', [$mockExam, $subjectExam]);
                @endphp
                <iframe id="pdfPreview"
                        src="{{ $previewRoute }}?font_size={{ $fontSize }}"
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
        let editMode = false;
        const isSubscription = {{ isset($fromSubscription) && $fromSubscription ? 'true' : 'false' }};
        const previewRoute = isSubscription
            ? '{{ route('mock-exams.subject-exams.subscription-preview.content', [$mockExam, $subjectExam]) }}'
            : '{{ route('mock-exams.subject-exams.pdf.preview', [$mockExam, $subjectExam]) }}';
        const editRoute = isSubscription
            ? null
            : '{{ route('mock-exams.subject-exams.pdf.edit', [$mockExam, $subjectExam]) }}';
        const saveLayoutRoute = isSubscription
            ? null
            : '{{ route('mock-exams.subject-exams.layout.save', [$mockExam, $subjectExam]) }}';
        const downloadRoute = isSubscription
            ? '{{ route('mock-exams.subject-exams.subscription-preview.content', [$mockExam, $subjectExam]) }}'
            : '{{ route('mock-exams.subject-exams.pdf.download', [$mockExam, $subjectExam]) }}';

        function toggleEditMode() {
            const iframe = document.getElementById('pdfPreview');
            const editBtn = document.getElementById('editLayoutBtn');
            const saveBtn = document.getElementById('saveLayoutBtn');
            const status = document.getElementById('previewStatus');

            if (!editRoute) return;

            editMode = !editMode;

            if (editMode) {
                // Switch to editable preview
                iframe.src = editRoute + '?font_size=' + currentFontSize;
                editBtn.innerHTML = `
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Viewing
                `;
                editBtn.style.background = 'rgba(239,68,68,0.15)';
                editBtn.style.color = '#f87171';
                editBtn.style.border = '1px solid rgba(239,68,68,0.3)';
                saveBtn.style.display = 'flex';
                status.innerHTML = `
                    <div class="w-1.5 h-1.5 rounded-full bg-blue-400"></div>
                    <span class="text-xs text-slate-500">Edit Mode</span>
                `;
            } else {
                // Switch back to normal preview
                iframe.src = previewRoute + '?font_size=' + currentFontSize;
                editBtn.innerHTML = `
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Edit Layout
                `;
                editBtn.style.background = 'rgba(59,130,246,0.15)';
                editBtn.style.color = '#60a5fa';
                editBtn.style.border = '1px solid rgba(59,130,246,0.3)';
                saveBtn.style.display = 'none';
                status.innerHTML = `
                    <div class="w-1.5 h-1.5 rounded-full bg-emerald-400"></div>
                    <span class="text-xs text-slate-500">Live</span>
                `;
            }
        }

        function saveLayoutFromPreview() {
            const iframe = document.getElementById('pdfPreview');
            const statusEl = document.getElementById('layoutSaveStatus');
            const saveBtn = document.getElementById('saveLayoutBtn');

            if (!iframe.contentWindow || !saveLayoutRoute) return;

            saveBtn.disabled = true;
            statusEl.style.display = 'block';
            statusEl.textContent = 'Saving...';
            statusEl.style.color = '#fbbf24';

            // Call save in the iframe
            iframe.contentWindow.postMessage({ action: 'saveLayout' }, '*');

            // Fallback: directly call the save endpoint via the iframe's LayoutEditor
            setTimeout(() => {
                try {
                    const container = iframe.contentWindow.document.getElementById('examContainer');
                    if (container && iframe.contentWindow.LayoutEditor) {
                        const config = iframe.contentWindow.LayoutEditor.collectLayoutConfig(container);
                        fetch(saveLayoutRoute, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                            },
                            body: JSON.stringify({ layout_config: config }),
                        })
                        .then(r => r.json())
                        .then(data => {
                            statusEl.textContent = 'Saved!';
                            statusEl.style.color = '#34d399';
                            saveBtn.disabled = false;
                            setTimeout(() => { statusEl.style.display = 'none'; }, 2000);
                        })
                        .catch(err => {
                            statusEl.textContent = 'Save failed!';
                            statusEl.style.color = '#f87171';
                            saveBtn.disabled = false;
                        });
                    } else {
                        statusEl.textContent = 'Editor not ready';
                        statusEl.style.color = '#f87171';
                        saveBtn.disabled = false;
                    }
                } catch(e) {
                    statusEl.textContent = 'Cross-origin error';
                    statusEl.style.color = '#f87171';
                    saveBtn.disabled = false;
                }
            }, 500);
        }

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

        function setChoiceLayout(layout) {
            // Update button styles
            document.querySelectorAll('.choice-layout-btn').forEach(btn => {
                btn.style.background = 'rgba(255,255,255,0.03)';
                btn.style.color = '#94a3b8';
                btn.style.border = '1px solid rgba(255,255,255,0.08)';
                btn.classList.remove('ring-2', 'ring-blue-500');
            });

            const activeBtn = document.getElementById('choiceLayout' + layout.charAt(0).toUpperCase() + layout.slice(1));
            if (activeBtn) {
                activeBtn.style.background = 'rgba(59,130,246,0.15)';
                activeBtn.style.color = '#60a5fa';
                activeBtn.style.border = '1px solid rgba(59,130,246,0.4)';
                activeBtn.classList.add('ring-2', 'ring-blue-500');
            }

            // Save to backend
            fetch(saveLayoutRoute, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                },
                body: JSON.stringify({
                    layout_config: {
                        choiceLayout: layout,
                    },
                }),
            })
            .then(r => r.json())
            .then(() => {
                updatePreview();
            })
            .catch(err => {
                console.error('Failed to save choice layout:', err);
            });
        }

        function updatePreview() {
            const iframe = document.getElementById('pdfPreview');
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
            iframe.src = previewRoute + '?font_size=' + currentFontSize;
            const downloadBtn = document.getElementById('downloadBtn');
            if (downloadBtn) {
                downloadBtn.dataset.downloadUrl = downloadRoute + '?font_size=' + currentFontSize;
            }

            // Reset status after load
            iframe.onload = function() {
                status.innerHTML = `
                    <div class="w-1.5 h-1.5 rounded-full bg-emerald-400"></div>
                    <span class="text-xs text-slate-500">Live</span>
                `;
            };
        }

        // Client loading buttons — show loading spinner before triggering download
        (function() {
            document.querySelectorAll('[data-client-loading="true"]').forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const url = btn.dataset.downloadUrl;
                    if (!url) return;

                    // Show loading state
                    const defaultEl = btn.querySelector('.client-btn-default');
                    const loadingEl = btn.querySelector('.client-btn-loading');
                    if (defaultEl) defaultEl.classList.add('hidden');
                    if (loadingEl) loadingEl.classList.remove('hidden');
                    btn.disabled = true;

                    // Trigger download via hidden anchor so the page doesn't navigate away
                    setTimeout(function() {
                        const a = document.createElement('a');
                        a.href = url;
                        a.style.display = 'none';
                        document.body.appendChild(a);
                        a.click();
                        document.body.removeChild(a);

                        // Reset button state after a short delay
                        setTimeout(function() {
                            if (defaultEl) defaultEl.classList.remove('hidden');
                            if (loadingEl) loadingEl.classList.add('hidden');
                            btn.disabled = false;
                        }, 1000);
                    }, 100);
                });
            });
        })();
    </script>
</x-layouts.app>