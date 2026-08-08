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

<div x-data="{ showSectionInfo: true }" class="w-full h-full">
    <!-- SECTION INFO OVERLAY -->
    <template x-if="showSectionInfo">
        <div id="fullscreen-instruction-panel" class="fixed inset-0 z-[100] bg-white dark:bg-slate-900 overflow-y-auto">
            <div class="max-w-4xl mx-auto px-6 py-8">
                <!-- Header with logo -->
                <div class="flex items-start justify-between mb-8">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-xl font-bold text-slate-900 dark:text-white">{{ $exam->title }}</h1>
                            <p class="text-slate-600 dark:text-slate-400 text-sm">{{ $sectionTitle }}</p>
                        </div>
                    </div>
                    <button @click="Livewire.dispatch('close-exam')"
                            class="p-2 text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Section Info Content -->
                <div class="space-y-6">
                    <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg p-5">
                        <div class="flex items-start gap-3">
                            <div class="flex-shrink-0">
                                <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-amber-800 dark:text-amber-200">Important Instructions</h3>
                                <ul class="mt-2 space-y-2 text-amber-700 dark:text-amber-300 text-sm list-disc pl-5">
                                    <li>This exam requires full screen mode for integrity</li>
                                    <li>You must remain in full screen throughout the exam</li>
                                    <li>Exiting full screen will be recorded as a violation</li>
                                    <li>Ensure you have a stable internet connection</li>
                                    <li>Complete all questions before the time expires</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg p-4">
                            <div class="text-slate-500 dark:text-slate-400 text-sm">Questions</div>
                            <div class="text-2xl font-bold text-slate-900 dark:text-white mt-1">{{ $questions->count() }}</div>
                        </div>
                        <div class="bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg p-4">
                            <div class="text-slate-500 dark:text-slate-400 text-sm">Time Limit</div>
                            <div class="text-2xl font-bold text-slate-900 dark:text-white mt-1">
                                @if($sectionTimeLimit)
                                    {{ gmdate('H:i:s', $sectionTimeLimit) }}
                                @else
                                    No Limit
                                @endif
                            </div>
                        </div>
                        <div class="bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg p-4">
                            <div class="text-slate-500 dark:text-slate-400 text-sm">Total Marks</div>
                            <div class="text-2xl font-bold text-slate-900 dark:text-white mt-1">{{ $totalMarks }}</div>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-4 pt-6">
                        <button
                            @click="fullscreenGate.request(); setTimeout(() => {
                                $wire.call('startSection');
                                setTimeout(() => {
                                    if (fullscreenGate.isActive) {
                                        fullscreenGate.hideInstructionPanel();
                                    } else {
                                        fullscreenGate.showExamContentOnly();
                                    }
                                }, 100);
                            }, 200);"
                            class="flex-1 bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white font-semibold py-4 px-6 rounded-lg transition-all duration-200 transform hover:scale-[1.02] shadow-lg">
                            <div class="flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5v-4m0 0h-4m4 0l-5-5" />
                                </svg>
                                Enter Fullscreen & Start Exam
                            </div>
                        </button>

                        <button
                            @click="Livewire.dispatch('close-exam')"
                            class="px-6 py-4 border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 font-semibold rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </template>

    <!-- EXAM CONTENT AREA - Initially hidden until section starts -->
    <div id="exam-content-area" class="h-full" style="display: none;" role="main" aria-label="Exam content">
        <nav aria-label="Skip links" class="sr-only">
            <a href="#question-navigation">Skip to question navigation</a>
            <a href="#question-content">Skip to current question</a>
        </nav>

        <div class="h-full flex flex-col">
            @livewire('examination-hub.exam-section-taking', [
                'exam' => $exam,
                'submission' => $submission,
                'section' => $section,
                'sectionIndex' => $sectionIndex,
                'questions' => $questions,
                'initialQuestionIndex' => session('restored_question', request()->query('q', 0)),
                 'timeRemaining' => $timeRemaining,
            ])
        </div>
    </div>
</div>

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
        @vite(['resources/js/exam-sync.js', 'resources/js/exam-timer.js'])
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
                            const toast = document.createElement('div');
                            toast.className = 'fixed bottom-4 right-4 z-50 max-w-sm p-4 bg-green-600 text-white rounded-lg shadow-lg';
                            toast.innerHTML = `
                                <div class="flex items-start gap-3">
                                    <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <div>
                                        <p class="font-semibold">Time Extended</p>
                                        <p class="text-sm opacity-90">Your exam time has been extended by ${additionalMinutes} minutes.</p>
                                    </div>
                                    <button onclick="this.closest('.fixed').remove();" class="ml-auto text-white/70 hover:text-white">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            `;
                            document.body.appendChild(toast);
                            setTimeout(() => toast.remove(), 8000);
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
