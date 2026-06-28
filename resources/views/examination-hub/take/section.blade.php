<x-layouts.exam>
<script>
// Define fullscreenGate early to ensure it's available when the button is clicked
window.fullscreenGate = {
    isActive: false,
    isSupported: !!(document.fullscreenEnabled || document.webkitFullscreenEnabled || document.mozFullScreenEnabled || document.msFullscreenEnabled),
    init() {
        // If fullscreen not supported, skip initialization
        if (!this.isSupported) {
            console.warn('Fullscreen not supported on this browser');
            this.showExamContentOnly();
            return;
        }

        // Listen for fullscreen change events to detect unexpected exits
        document.addEventListener('fullscreenchange', () => this.onFullscreenChange());
        document.addEventListener('webkitfullscreenchange', () => this.onFullscreenChange()); // Safari
        document.addEventListener('mozfullscreenchange', () => this.onFullscreenChange());   // Firefox
        document.addEventListener('MSFullscreenChange', () => this.onFullscreenChange());    // IE/Edge

        // Listen for section started event
        window.addEventListener('section-started', () => {
            if (this.isActive) {
                this.hideInstructionPanel();
            } else {
                this.showExamContentOnly();
            }
        });

        // Listen for show exam content event
        window.addEventListener('show-exam-content', () => {
            if (this.isActive) {
                this.hideInstructionPanel();
            } else {
                this.showExamContentOnly();
            }
        });

        // Initialize fullscreen gate state
        this.isActive = document.fullscreenElement != null;
    },

    async request() {
        if (this.isActive) return;
        if (!this.isSupported) {
            console.warn('Fullscreen not supported, proceeding without it');
            this.showExamContentOnly();
            return;
        }

        try {
            const elem = document.documentElement;
            const requestFullscreen = elem.requestFullscreen
                || elem.webkitRequestFullscreen
                || elem.mozRequestFullScreen
                || elem.msRequestFullscreen;

            if (!requestFullscreen) {
                console.warn('Fullscreen API not available');
                this.showExamContentOnly();
                return;
            }

            await requestFullscreen.call(elem);
            this.isActive = true;

            setTimeout(() => {
                if (this.isActive) {
                    this.hideInstructionPanel();
                }
            }, 100);

        } catch (err) {
            console.warn('Fullscreen request failed:', err.message);
            console.log('Proceeding without fullscreen mode');
            this.showExamContentOnly();
        }
    },

    async exit() {
        if (!this.isActive) return;

        try {
            await (document.exitFullscreen
                || document.webkitExitFullscreen
                || document.mozCancelFullScreen
                || document.msExitFullscreen).call(document);

            this.isActive = false;
        } catch (err) {
            console.error('Error exiting fullscreen:', err);
        }
    },

    onFullscreenChange() {
        const nowFullscreen = document.fullscreenElement != null;

        if (this.isActive && !nowFullscreen) {
            this.handleUnexpectedExit();
        }

        this.isActive = nowFullscreen;

        if (!this.isActive) {
            const examContent = document.getElementById('exam-content-area');
            const instructionPanel = document.getElementById('fullscreen-instruction-panel');

            if (examContent && examContent.style.display !== 'none') {
                this.showExamContentOnly();
            } else {
                this.showInstructionPanel();
            }
        }
    },

    hideInstructionPanel() {
        const instructionPanel = document.getElementById('fullscreen-instruction-panel');
        if (instructionPanel) {
            instructionPanel.style.display = 'none';
        }
        const examContent = document.getElementById('exam-content-area');
        if (examContent) {
            examContent.style.display = 'block';
        }
    },

    showInstructionPanel() {
        const instructionPanel = document.getElementById('fullscreen-instruction-panel');
        if (instructionPanel) {
            instructionPanel.style.display = 'block';
        }
        const examContent = document.getElementById('exam-content-area');
        if (examContent) {
            examContent.style.display = 'none';
        }
    },

    showExamContentOnly() {
        const instructionPanel = document.getElementById('fullscreen-instruction-panel');
        if (instructionPanel) {
            instructionPanel.style.display = 'none';
        }
        const examContent = document.getElementById('exam-content-area');
        if (examContent) {
            examContent.style.display = 'block';
        }
    },

    handleUnexpectedExit() {
        // Report violation to backend
        fetch(`{{ route('examination-hub.take.proctor.event', $exam) }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                type: 'fullscreen_exit',
                data: { reason: 'unexpected_exit' },
                timestamp: Date.now()
            })
        }).catch(console.error);

        // Show dialog to force re-entry into fullscreen
        this.showFullscreenExitDialog();
    },

    showFullscreenExitDialog() {
        const existing = document.getElementById('fullscreen-exit-dialog');
        if (existing) existing.remove();

        const dialog = document.createElement('div');
        dialog.id = 'fullscreen-exit-dialog';
        dialog.className = 'fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm';
        dialog.innerHTML = `
            <div class="bg-white dark:bg-slate-900 rounded-lg w-full max-w-md p-6 text-center shadow-2xl border border-slate-200 dark:border-slate-700">
                <div class="w-16 h-16 mx-auto mb-4 flex items-center justify-center rounded-full bg-red-100 dark:bg-red-900/30">
                    <svg class="w-8 h-8 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2">Fullscreen Required</h3>
                <p class="text-slate-600 dark:text-slate-400 mb-6">You have exited fullscreen mode. This exam must be taken in fullscreen. Please click the button below to return to fullscreen mode.</p>
                <button id="reenter-fullscreen-btn" class="w-full px-4 py-3 bg-amber-600 hover:bg-amber-700 text-white font-semibold rounded-lg transition-colors shadow-lg">
                    Re-enter Fullscreen
                </button>
            </div>
        `;
        document.body.appendChild(dialog);

        document.getElementById('reenter-fullscreen-btn').addEventListener('click', () => {
            dialog.remove();
            this.request();
        });
    }
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => window.fullscreenGate.init());
} else {
    window.fullscreenGate.init();
}
</script>

@php $isSingleSection = $exam->sections->count() === 1; @endphp

<div class="w-full h-full">
    {{-- ══════════════════════════════════════════════════════════
         FULLSCREEN / RULES PANEL
         Shown only for multi-section exams. For single-section
         exams this is skipped — the rules are integrated into
         the section info card inside the Livewire component.
    ══════════════════════════════════════════════════════════ --}}
    @if(!$isSingleSection)
    <div id="fullscreen-instruction-panel"
         class="fixed inset-0 z-[100] bg-slate-100 dark:bg-slate-950 flex items-center justify-center px-4 py-8 overflow-y-auto">

        <div class="w-full max-w-lg overflow-hidden"
             style="border-radius: 2px;
                    background: var(--tw-bg-opacity, #ffffff);
                    box-shadow: 0 0 0 1px rgba(0,0,0,0.06), 0 20px 60px -10px rgba(0,0,0,0.2), 0 4px 16px rgba(0,0,0,0.08);"
             class="bg-white dark:bg-slate-900">

            {{-- Accent bar --}}
            <div class="h-1 w-full" style="background: linear-gradient(90deg, #b45309, #d97706, #fbbf24);"></div>

            {{-- Dark header --}}
            <div class="px-8 py-6 bg-slate-800 dark:bg-slate-900 border-b border-slate-700 dark:border-slate-800">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-medium tracking-widest uppercase text-amber-400 mb-1"
                           style="font-family: 'system-ui', sans-serif; letter-spacing: 0.15em;">Online Examination</p>
                        <h1 class="text-xl font-bold text-white leading-snug"
                            style="letter-spacing: -0.02em; font-family: 'Georgia', serif;">
                            {{ $exam->title }}
                        </h1>
                        <p class="text-slate-400 text-sm mt-0.5"
                           style="font-family: 'system-ui', sans-serif;">
                            Section {{ $sectionIndex + 1 }} — {{ $sectionTitle }}
                        </p>
                    </div>
                    <button onclick="Livewire.dispatch('close-exam')"
                            class="mt-1 flex-shrink-0 w-8 h-8 flex items-center justify-center text-slate-400 hover:text-white border border-slate-600 hover:border-slate-400 transition-colors"
                            style="border-radius: 2px;"
                            title="Exit examination">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Body --}}
            <div class="px-8 py-7 space-y-5 bg-white dark:bg-slate-900"
                 style="font-family: 'system-ui', sans-serif;">

                {{-- Fullscreen notice --}}
                <div style="border-left: 3px solid #d97706; padding-left: 1rem; padding-top: 0.75rem; padding-bottom: 0.75rem;">
                    <h3 class="text-xs font-bold text-amber-700 dark:text-amber-500 uppercase tracking-wider mb-2"
                        style="font-size: 10px; letter-spacing: 0.12em;">Before You Begin</h3>
                    <ul class="space-y-1.5 text-sm text-slate-600 dark:text-slate-400">
                        <li class="flex items-start gap-2">
                            <svg class="w-4 h-4 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5v-4m0 0h-4m4 0l-5-5"/>
                            </svg>
                            <span>This examination must be taken in <strong class="text-slate-800 dark:text-slate-200">full screen</strong> mode.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="w-4 h-4 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Exiting full screen will be logged as a <strong class="text-slate-800 dark:text-slate-200">violation</strong>.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="w-4 h-4 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"/>
                            </svg>
                            <span>Ensure you have a <strong class="text-slate-800 dark:text-slate-200">stable internet connection</strong> before proceeding.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="w-4 h-4 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Complete all questions before the <strong class="text-slate-800 dark:text-slate-200">time expires</strong>.</span>
                        </li>
                    </ul>
                </div>

                {{-- Stats --}}
                <div class="grid grid-cols-3 gap-3">
                    <div class="px-4 py-3 bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700" style="border-radius: 2px;">
                        <p class="text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1" style="font-size: 10px; letter-spacing: 0.1em;">Questions</p>
                        <p class="text-xl font-bold text-slate-900 dark:text-white" style="letter-spacing: -0.03em;">{{ $questions->count() }}</p>
                    </div>
                    <div class="px-4 py-3 {{ $sectionTimeLimit ? 'bg-amber-50 dark:bg-amber-900/20' : 'bg-slate-50 dark:bg-slate-800/60' }} border border-slate-200 dark:border-slate-700" style="border-radius: 2px; {{ $sectionTimeLimit ? 'border-color: rgba(180,83,9,0.2);' : '' }}">
                        <p class="{{ $sectionTimeLimit ? 'text-amber-700 dark:text-amber-500' : 'text-slate-500 dark:text-slate-400' }} uppercase tracking-wider mb-1" style="font-size: 10px; letter-spacing: 0.1em;">Time Limit</p>
                        <p class="text-xl font-bold {{ $sectionTimeLimit ? 'text-amber-800 dark:text-amber-400' : 'text-slate-900 dark:text-white' }}" style="letter-spacing: -0.03em;">
                            @if($sectionTimeLimit)
                                {{ gmdate('H:i', $sectionTimeLimit) }}
                            @else
                                —
                            @endif
                        </p>
                    </div>
                    <div class="px-4 py-3 bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700" style="border-radius: 2px;">
                        <p class="text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1" style="font-size: 10px; letter-spacing: 0.1em;">Marks</p>
                        <p class="text-xl font-bold text-slate-900 dark:text-white" style="letter-spacing: -0.03em;">{{ $totalMarks }}</p>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="pt-1 flex items-center gap-3">
                    <button
                        id="enter-fullscreen-btn"
                        onclick="
                            fullscreenGate.request();
                            setTimeout(() => {
                                const wire = document.querySelector('[wire\\:id]')?.__livewire;
                                if (wire) wire.call('startSection');
                                setTimeout(() => {
                                    if (fullscreenGate.isActive) {
                                        fullscreenGate.hideInstructionPanel();
                                    } else {
                                        fullscreenGate.showExamContentOnly();
                                    }
                                }, 100);
                            }, 200);"
                        class="flex-1 flex items-center justify-center gap-2 px-6 py-3.5 text-sm font-semibold text-white transition-all duration-200"
                        style="border-radius: 2px; background: linear-gradient(135deg, #b45309, #d97706); box-shadow: 0 2px 12px rgba(180,83,9,0.35);">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5v-4m0 0h-4m4 0l-5-5"/>
                        </svg>
                        Enter Fullscreen &amp; Begin
                    </button>

                    <button
                        onclick="Livewire.dispatch('close-exam')"
                        class="px-5 py-3.5 text-sm font-medium border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors"
                        style="border-radius: 2px;">
                        Cancel
                    </button>
                </div>

            </div>{{-- /body --}}
        </div>{{-- /card --}}
    </div>{{-- /fullscreen-instruction-panel --}}
    @endif

    {{-- ══════════════════════════════════════════════════════════
         EXAM CONTENT AREA
         For multi-section exams: hidden until fullscreen is entered.
         For single-section exams: visible immediately (section info
         card inside the Livewire component shows combined rules).
    ══════════════════════════════════════════════════════════ --}}
    <div id="exam-content-area" class="h-full"
         style="display: {{ $isSingleSection ? 'block' : 'none' }};"
         role="main" aria-label="Exam content">
        <nav aria-label="Skip links" class="sr-only">
            <a href="#question-navigation">Skip to question navigation</a>
            <a href="#question-content">Skip to current question</a>
        </nav>

        <div class="h-full flex flex-col">
            @livewire('examination-hub.exam-section-taking', [
                'isSingleSection' => $isSingleSection,
                'exam' => $exam,
                'submission' => $submission,
                'section' => $section,
                'sectionIndex' => $sectionIndex,
                'questions' => $questions,
                'initialQuestionIndex' => session('restored_question', request()->query('q', 0)),
                 'timeRemaining' => $timeRemaining,
            ])
        </div>
    </div>{{-- /exam-content-area --}}
</div>{{-- /wrapper --}}

<script>
    const proctoringEnabled = @json($proctoringEnabled ?? false);
    const sessionId         = @json($proctoringSessionId ?? null);
    const endpoint          = @json(route('examination-hub.take.proctor.event', ['exam' => $exam]));

    if (proctoringEnabled && sessionId) {
        window.ExamProctorConfig = {
            eventUrl: endpoint,
            csrfToken: document.querySelector('meta[name="csrf-token"]')?.content,
            hardenedMode: @json($exam->hardened_mode ?? false),
            requireFullscreen: @json($exam->require_fullscreen ?? false),
            autoSubmitUrl: null
        };
    }
</script>

    @if($proctoringEnabled ?? false)
        @push('exam-scripts')
            @assets('js/exam-proctor.js')
        @endpush
    @endif

    @push('exam-scripts')
        @vite(['resources/js/exam-heartbeat.js', 'resources/js/exam-sync.js', 'resources/js/exam-timer.js'])
    @endpush

@push('exam-scripts')
    <script>
        // DISABLE RIGHT CLICK, COPY, PASTE, CUT, AND DEV TOOLS
        document.addEventListener('contextmenu', function(e) {
            e.preventDefault();
        });
        document.addEventListener('copy', function(e) {
            e.preventDefault();
        });
        document.addEventListener('paste', function(e) {
            e.preventDefault();
        });
        document.addEventListener('cut', function(e) {
            e.preventDefault();
        });
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && (e.key === 'c' || e.key === 'v' || e.key === 'x' || e.key === 'u')) {
                e.preventDefault();
            }
            if (e.key === 'F12' || (e.ctrlKey && e.shiftKey && (e.key === 'I' || e.key === 'J' || e.key === 'C')) || (e.ctrlKey && e.key === 'U')) {
                e.preventDefault();
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            if (typeof ExamSessionSync !== 'undefined') {
                const examId = @json($exam->id);
                const submissionId = @json($submission->id ?? null);
                const userId = @json(auth()->id());

                if (examId && submissionId) {
                    window.examSync = new ExamSessionSync(examId, submissionId, userId);
                    window.examSync.init();

                    Livewire.on('examDataSyncing', () => {
                        if (window.examSync) {
                            window.examSync.syncImmediate();
                        }
                    });
                }
            }

            if (typeof ExamHeartbeat !== 'undefined') {
                const examId = @json($exam->id);
                const submissionId = @json($submission->id ?? null);

                if (examId && submissionId) {
                    window.examHeartbeat = new ExamHeartbeat({
                        examId: examId,
                        heartbeatUrl: "{{ route('examination-hub.take.heartbeat', ['exam' => $exam]) }}",
                        initUrl: "{{ route('examination-hub.take.heartbeat.init', ['exam' => $exam]) }}",
                        acknowledgeUrl: "{{ route('examination-hub.take.heartbeat.acknowledge-warning', ['exam' => $exam]) }}",
                        interval: 15000,
                        onWarning: function(warning) {
                            window.examHeartbeat.defaultWarningHandler(warning);
                        },
                        onTerminated: function(data) {
                            window.hasUnsavedChanges = false;
                            if (document.getElementById('terminated-modal')) return;

                            const modal = document.createElement('div');
                            modal.id = 'terminated-modal';
                            modal.style.cssText = 'position:fixed;inset:0;z-index:9999;display:flex;align-items:center;justify-content:center;padding:1rem;background:rgba(0,0,0,.8);';
                            modal.innerHTML = `
        <div style="background:#fff;border-radius:2px;width:100%;max-width:28rem;padding:1.5rem;text-align:center;box-shadow:0 20px 60px rgba(0,0,0,.3);">
            <div style="width:4rem;height:4rem;margin:0 auto 1rem;display:flex;align-items:center;justify-content:center;border-radius:50%;background:#fee2e2;">
                <svg style="width:2rem;height:2rem;color:#dc2626;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                </svg>
            </div>
            <h3 style="font-size:1.125rem;font-weight:700;color:#0f172a;margin:0 0 .5rem;">Session Terminated</h3>
            <p style="color:#475569;margin:0 0 .5rem;">Your exam session has been terminated by the administrator.</p>
            <p style="font-size:.875rem;color:#64748b;margin:0 0 1rem;">${data.reason || data.message || ''}</p>
            <p style="font-size:.875rem;color:#94a3b8;">Redirecting in <span id="term-countdown">3</span>s&hellip;</p>
        </div>
    `;
                            document.body.appendChild(modal);

                            let s = 3;
                            const iv = setInterval(() => {
                                s--;
                                const el = document.getElementById('term-countdown');
                                if (el) el.textContent = s;
                                if (s <= 0) {
                                    clearInterval(iv);
                                    window.location.href = "{{ route('examination-hub.take.completed', $exam) }}";
                                }
                            }, 1000);
                        },
                        onMessage: function(messageData) {
                            // Extract the actual text — heartbeat sends an object, not a raw string
                            const text = (messageData && typeof messageData === 'object')
                                ? (messageData.message || '')
                                : String(messageData || '');
                            if (!text) return;

                            const existing = document.getElementById('admin-msg-toast');
                            if (existing) existing.remove();

                            const toast = document.createElement('div');
                            toast.id = 'admin-msg-toast';
                            toast.style.cssText = 'position:fixed;top:1rem;right:1rem;z-index:9999;max-width:22rem;border-radius:2px;box-shadow:0 8px 32px rgba(0,0,0,.2);';
                            toast.innerHTML = `
        <div style="background:#1d4ed8;color:#fff;padding:1rem;">
            <div style="display:flex;align-items:flex-start;gap:.75rem;">
                <svg style="width:1.25rem;height:1.25rem;flex-shrink:0;margin-top:.1rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-3 3v-3z"/>
                </svg>
                <div style="flex:1;min-width:0;">
                    <p style="font-weight:600;font-size:.875rem;margin:0 0 .25rem;">Message from Examiner</p>
                    <p style="font-size:.875rem;opacity:.9;margin:0;word-break:break-word;">${text}</p>
                </div>
                <button onclick="document.getElementById('admin-msg-toast')?.remove();"
                        style="flex-shrink:0;background:none;border:none;color:rgba(255,255,255,.7);cursor:pointer;padding:0;margin-left:.5rem;">
                    <svg style="width:1rem;height:1rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    `;
                            document.body.appendChild(toast);
                            setTimeout(() => toast?.remove(), 30000); // stays visible 30 s
                        },
                        onForceSubmit: function(data) {
                            const modal = document.createElement('div');
                            modal.className = 'fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70';
                            modal.innerHTML = `
                                <div class="bg-white rounded-lg w-full max-w-md p-6 text-center">
                                    <div class="w-16 h-16 mx-auto mb-4 flex items-center justify-center rounded-full bg-red-100">
                                        <svg class="w-8 h-8 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-bold text-slate-900 mb-2">Exam Submitted</h3>
                                    <p class="text-slate-600 mb-2">Your exam has been submitted by the administrator.</p>
                                    <p class="text-sm text-slate-500 mb-6">Reason: ${data.message || 'Not specified'}</p>
                                    <p class="text-sm text-slate-500">Redirecting to results...</p>
                                </div>
                            `;
                            document.body.appendChild(modal);
                            setTimeout(() => {
                                window.location.href = "{{ route('examination-hub.take.completed', $exam) }}";
                            }, 3000);
                        },
                        onTimeExtended: function(additionalMinutes) {
                            // ── 1. Update the running timer immediately ───────
                            // window.examTimerExtend is exposed by exam-timer.js
                            // init(). Calling it adds the minutes to the live
                            // countdown and re-arms the interval if it already
                            // expired. Guard in case the timer isn't mounted yet.
                            if (typeof window.examTimerExtend === 'function') {
                                window.examTimerExtend(additionalMinutes);
                            }

                            // ── 2. Show the candidate a notification ──────────
                            // exam-timer.js showTimeExtendedBanner() is already
                            // called by extendByMinutes(), so we only need a
                            // fallback toast here in case the timer isn't mounted.
                            if (typeof window.examTimerExtend !== 'function') {
                                const toast = document.createElement('div');
                                toast.style.cssText = 'position:fixed;bottom:1rem;right:1rem;z-index:9999;max-width:22rem;border-radius:2px;padding:1rem;background:#059669;color:#fff;font-size:.875rem;font-weight:600;box-shadow:0 4px 20px rgba(5,150,105,.4);display:flex;align-items:center;gap:.75rem;';
                                toast.innerHTML = `
                                    <svg style="width:1.25rem;height:1.25rem;flex-shrink:0;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span>+${additionalMinutes} minute${additionalMinutes !== 1 ? 's' : ''} added to your time</span>
                                `;
                                document.body.appendChild(toast);
                                setTimeout(() => toast.remove(), 8000);
                            }
                        }
                    });
                }
            }
        });

        window.hasUnsavedChanges = false;
        window.currentResponses = {};
        window.autoSaveInterval = null;

        document.addEventListener('livewire:initialized', () => {
            Livewire.on('responseUpdated', (data) => {
                window.hasUnsavedChanges = true;
                if (data && data.questionId && data.response) {
                    window.currentResponses[data.questionId] = data.response;
                }
            });

            Livewire.on('examAutoSubmitted', (payload) => {
                window.hasUnsavedChanges = false;

                const redirectUrl = (payload && payload.redirectUrl)
                    ? payload.redirectUrl
                    : @json(route('examination-hub.take.completed', $exam));

                const existingModal = document.getElementById('auto-submit-modal');
                if (existingModal) existingModal.remove();

                const modal = document.createElement('div');
                modal.id = 'auto-submit-modal';
                modal.className = 'fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-black/70';
                modal.innerHTML = `
                    <div class="bg-white dark:bg-slate-900 rounded-lg w-full max-w-md p-6 text-center shadow-xl">
                        <div class="w-16 h-16 mx-auto mb-4 flex items-center justify-center rounded-full bg-red-100 dark:bg-red-900/30">
                            <svg class="w-8 h-8 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2">Time's Up!</h3>
                        <p class="text-slate-600 dark:text-slate-400 mb-2">Your exam has been automatically submitted.</p>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">${(payload && payload.reason) ? payload.reason : ''}</p>
                        <p class="text-sm text-slate-500 dark:text-slate-400">
                            Redirecting to results in <span id="auto-submit-countdown">3</span>s &hellip;
                        </p>
                    </div>
                `;
                document.body.appendChild(modal);

                let secondsLeft = 3;
                const countdownEl = document.getElementById('auto-submit-countdown');
                const countdownInterval = setInterval(() => {
                    secondsLeft--;
                    if (countdownEl) countdownEl.textContent = secondsLeft;
                    if (secondsLeft <= 0) {
                        clearInterval(countdownInterval);
                        window.location.href = redirectUrl;
                    }
                }, 1000);
            });
        });

        function startAutoSave() {
            window.autoSaveInterval = setInterval(() => {
                if (window.hasUnsavedChanges) {
                    saveResponsesSilently();
                }
            }, 30000);
        }

        function saveResponsesSilently() {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
            if (!csrfToken) return;

            const livewireComponent = document.querySelector('[wire:id]');
            if (!livewireComponent) return;

            const data = new FormData();
            data.append('_token', csrfToken);
            data.append('_method', 'POST');

            navigator.sendBeacon(
                "{{ route('examination-hub.take.save-response', $exam) }}",
                data
            );

            window.hasUnsavedChanges = false;
        }

        window.addEventListener('beforeunload', (e) => {
            if (window.hasUnsavedChanges) {
                saveResponsesSilently();
                e.preventDefault();
                e.returnValue = '';
                return '';
            }
        });

        window.addEventListener('section-started', () => {
            startAutoSave();
        });

        document.addEventListener('DOMContentLoaded', () => {
           // startTimer();
            const examContent = document.getElementById('exam-content-area');
            if (examContent && examContent.style.display !== 'none') {
                startAutoSave();
            }
        });



        function confirmSubmission() {
            const totalQuestions = {{ $questions->count() }};
            const answeredQuestions = Object.keys(window.currentResponses || {}).length;

            if (answeredQuestions === 0) {
                return confirm('⚠️ WARNING: You haven\'t answered any questions!\n\nAre you sure you want to submit an empty exam? This cannot be undone.');
            }

            if (answeredQuestions < totalQuestions * 0.5) {
                return confirm(`⚠️ You've only answered ${answeredQuestions} of ${totalQuestions} questions.\n\nAre you sure you want to submit?`);
            }

            return true;
        }

        window.examBookmarks = {
            bookmarks: new Set(),
            init() {
                const saved = localStorage.getItem('exam_bookmarks_' + {{ $submission->id }});
                if (saved) {
                    try {
                        const parsed = JSON.parse(saved);
                        this.bookmarks = new Set(parsed);
                    } catch (e) {
                        console.error('Failed to load bookmarks:', e);
                    }
                }
            },
            toggle(questionId) {
                const questionIdStr = String(questionId);
                if (this.bookmarks.has(questionIdStr)) {
                    this.bookmarks.delete(questionIdStr);
                    this.showNotification('Bookmark removed', 'info');
                } else {
                    this.bookmarks.add(questionIdStr);
                    this.showNotification('Question bookmarked for review', 'success');
                }
                this.save();
                this.updateUI();
            },
            isBookmarked(questionId) {
                return this.bookmarks.has(String(questionId));
            },
            save() {
                localStorage.setItem(
                    'exam_bookmarks_' + {{ $submission->id }},
                    JSON.stringify(Array.from(this.bookmarks))
                );
            },
            showNotification(message, type = 'info') {
                const toast = document.createElement('div');
                const bgColor = type === 'success' ? 'bg-green-600' : 'bg-blue-600';
                toast.className = `fixed bottom-4 right-4 z-50 ${bgColor} text-white px-4 py-3 rounded-lg shadow-lg transition-all duration-300 transform translate-y-0`;
                toast.innerHTML = `
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span class="text-sm font-medium">${message}</span>
                    </div>
                `;
                document.body.appendChild(toast);
                setTimeout(() => {
                    toast.style.opacity = '0';
                    toast.style.transform = 'translateY(20px)';
                    setTimeout(() => toast.remove(), 300);
                }, 2000);
            },
            updateUI() {
                document.querySelectorAll('[data-question-id]').forEach(el => {
                    const questionId = el.getAttribute('data-question-id');
                    const bookmarkIcon = el.querySelector('.bookmark-icon');
                    if (bookmarkIcon) {
                        if (this.isBookmarked(questionId)) {
                            bookmarkIcon.classList.remove('hidden');
                            bookmarkIcon.setAttribute('aria-label', 'Remove bookmark');
                        } else {
                            bookmarkIcon.classList.add('hidden');
                            bookmarkIcon.setAttribute('aria-label', 'Add bookmark');
                        }
                    }
                });
            },
            showBookmarkedList() {
                if (this.bookmarks.size === 0) {
                    this.showNotification('No bookmarked questions', 'info');
                    return;
                }
                const modal = document.createElement('div');
                modal.className = 'fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70';
                modal.setAttribute('role', 'dialog');
                modal.setAttribute('aria-modal', 'true');
                modal.setAttribute('aria-labelledby', 'bookmarks-title');

                let bookmarkedList = '';
                this.bookmarks.forEach(id => {
                    bookmarkedList += `
                        <div class="flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-800 rounded-lg">
                            <span class="text-sm font-medium text-slate-900 dark:text-white">Question ${id}</span>
                            <button onclick="window.examBookmarks.goToQuestion(${id})"
                                    class="text-xs px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                                Go to Question
                            </button>
                        </div>
                    `;
                });

                modal.innerHTML = `
                    <div class="bg-white dark:bg-slate-900 rounded-lg max-w-md w-full max-h-[80vh] overflow-hidden shadow-xl">
                        <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
                            <h3 id="bookmarks-title" class="text-lg font-semibold text-slate-900 dark:text-white">
                                Bookmarked Questions (${this.bookmarks.size})
                            </h3>
                            <button onclick="this.closest('.fixed').remove()"
                                    class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300"
                                    aria-label="Close bookmarks dialog">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        <div class="p-6 overflow-y-auto max-h-[60vh] space-y-2">
                            ${bookmarkedList}
                        </div>
                        <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
                            <button onclick="this.closest('.fixed').remove()"
                                    class="w-full px-4 py-2 bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 rounded-lg transition-colors">
                                Close
                            </button>
                        </div>
                    </div>
                `;
                document.body.appendChild(modal);
                modal.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape') modal.remove();
                });
                modal.addEventListener('click', (e) => {
                    if (e.target === modal) modal.remove();
                });
            },
            goToQuestion(questionId) {
                document.querySelector('.fixed[role="dialog"]')?.remove();
                this.showNotification(`Navigate to question ${questionId} (integration needed)`, 'info');
            }
        };

        document.addEventListener('DOMContentLoaded', () => {
            window.examBookmarks.init();
        });

        document.addEventListener('keydown', (e) => {
            if ((e.ctrlKey || e.metaKey) && e.key === 'b') {
                e.preventDefault();
                const livewireComponent = document.querySelector('[wire:id]');
                if (livewireComponent) {
                    console.log('Bookmark shortcut pressed - integration needed');
                }
            }
        });
    </script>
@endpush
</x-layouts.exam>