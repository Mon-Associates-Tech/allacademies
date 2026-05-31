<x-layouts.exam>
    <script>
        // Define fullscreenGate early to ensure it's available when the button is clicked
        window.fullscreenGate = {
            isActive: false,

            init() {
                // Listen for fullscreen change events to detect unexpected exits
                document.addEventListener('fullscreenchange', () => this.onFullscreenChange());
                document.addEventListener('webkitfullscreenchange', () => this.onFullscreenChange()); // Safari
                document.addEventListener('mozfullscreenchange', () => this.onFullscreenChange());   // Firefox
                document.addEventListener('MSFullscreenChange', () => this.onFullscreenChange());    // IE/Edge

                // Listen for section started event
                window.addEventListener('section-started', () => {
                    // If we're in fullscreen, keep showing exam content
                    // If we're not in fullscreen, we should still show exam content after section starts
                    if (this.isActive) {
                        this.hideInstructionPanel();
                    } else {
                        // Even when not in fullscreen, show exam content after section starts
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

                // Grace period: suppress the blocking overlay for 1.5 s after a
                // programmatic fullscreen request.  Browsers sometimes fire a
                // spurious exit event while the fullscreen transition is settling.
                window.__fullscreenGracePeriod = true;
                clearTimeout(window.__fullscreenGraceTimer);
                window.__fullscreenGraceTimer = setTimeout(() => {
                    window.__fullscreenGracePeriod = false;
                }, 1500);

                try {
                    await document.documentElement.requestFullscreen().catch(e => {
                        // Safari/iOS
                        if (document.documentElement.webkitRequestFullscreen) {
                            return document.documentElement.webkitRequestFullscreen();
                        }
                        throw e;
                    });

                    this.isActive = true;
                    this.hideFullscreenAlert();

                    setTimeout(() => {
                        if (this.isActive) {
                            this.hideInstructionPanel();
                        }
                    }, 100);

                } catch (err) {
                    window.__fullscreenGracePeriod = false;
                    console.error('Fullscreen request denied:', err);
                    alert('Fullscreen mode is required for this exam. Please allow fullscreen permissions.');
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
                const nowFullscreen = document.fullscreenElement != null || document.webkitFullscreenElement != null;

                if (this.isActive && !nowFullscreen) {
                    // Unexpected exit — record violation and show blocking overlay
                    this.handleUnexpectedExit();
                } else if (!this.isActive && nowFullscreen) {
                    // Returned to fullscreen — clear blocking overlay
                    this.hideFullscreenAlert();
                }

                this.isActive = nowFullscreen;

                if (this.isActive) {
                    const examContent = document.getElementById('exam-content-area');
                    if (examContent && examContent.style.display !== 'none') {
                        this.hideInstructionPanel();
                    }
                }
            },

            hideInstructionPanel() {
                const instructionPanel = document.getElementById('fullscreen-instruction-panel');
                if (instructionPanel) {
                    instructionPanel.style.display = 'none';
                }
                // Show the actual exam content
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
                // Hide the actual exam content when not in fullscreen and section not started
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
                // Show the actual exam content
                const examContent = document.getElementById('exam-content-area');
                if (examContent) {
                    examContent.style.display = 'block';
                }
            },

            showFullscreenAlert() {
                if (document.getElementById('fullscreen-alert-overlay')) return;
                const overlay = document.createElement('div');
                overlay.id = 'fullscreen-alert-overlay';
                overlay.style.cssText = 'position:fixed;inset:0;z-index:99990;background:rgba(0,0,0,0.92);display:flex;align-items:center;justify-content:center;padding:16px;';
                overlay.innerHTML = `
                    <div style="background:#fff;border-radius:12px;padding:36px 32px;max-width:420px;width:100%;text-align:center;box-shadow:0 25px 50px rgba(0,0,0,0.5);">
                        <div style="width:64px;height:64px;background:#fef3c7;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                            <svg style="width:32px;height:32px;color:#d97706;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5v-4m0 0h-4m4 0l-5-5"/>
                            </svg>
                        </div>
                        <h3 style="font-size:1.2rem;font-weight:700;color:#111;margin:0 0 10px;">Fullscreen Required</h3>
                        <p style="color:#555;font-size:.9rem;margin:0 0 8px;">You exited fullscreen mode. This has been recorded as a <strong>violation</strong>.</p>
                        <p style="color:#555;font-size:.9rem;margin:0 0 24px;">The timer is still running. Return to fullscreen to continue your exam.</p>
                        <button onclick="window.fullscreenGate.request()"
                                style="background:linear-gradient(135deg,#b45309,#d97706);color:#fff;border:none;padding:13px 28px;border-radius:8px;font-weight:600;font-size:1rem;cursor:pointer;width:100%;">
                            Return to Fullscreen
                        </button>
                    </div>
                `;
                document.body.appendChild(overlay);
            },

            hideFullscreenAlert() {
                const overlay = document.getElementById('fullscreen-alert-overlay');
                if (overlay) overlay.remove();
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

                // During the grace period right after a programmatic fullscreen
                // request, skip the blocking overlay — the browser sometimes fires
                // a spurious exit event while the transition is settling.
                if (window.__fullscreenGracePeriod) return;

                this.showFullscreenAlert();
            }
        };

        // Initialize fullscreen gate when DOM is loaded
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => window.fullscreenGate.init());
        } else {
            window.fullscreenGate.init();
        }
    </script>

    <div x-data="{ showSectionInfo: true, timerExpired: false }" x-init="window.sectionInfoAlpine = $data" class="w-full h-full">
        <!-- FULLSCREEN GATE -->
        <template x-if="showSectionInfo && !timerExpired">
            <div id="fullscreen-instruction-panel"
                 class="fixed inset-0 z-[100] bg-slate-100 dark:bg-slate-950 flex flex-col overflow-y-auto"
                 style="font-family: 'system-ui', -apple-system, sans-serif;">

                {{-- Centred card --}}
                <div class="flex-1 flex items-center justify-center px-4 py-10">
                    <div class="w-full max-w-md">

                        {{-- Card --}}
                        <div class="bg-white dark:bg-slate-900 overflow-hidden"
                             style="border-radius: 2px; box-shadow: 0 0 0 1px rgba(0,0,0,0.07), 0 20px 60px -10px rgba(0,0,0,0.14), 0 4px 16px rgba(0,0,0,0.06);">

                            <div class="h-1 w-full" style="background: linear-gradient(90deg,#b45309,#d97706,#fbbf24);"></div>

                            {{-- Card header --}}
                            <div class="px-8 pt-7 pb-6 border-b border-slate-100 dark:border-slate-800">
                                <div class="flex items-center gap-3 mb-4">
                                    <div class="w-10 h-10 flex items-center justify-center flex-shrink-0"
                                         style="background: linear-gradient(135deg,#fef3c7,#fde68a); border-radius: 50%;">
                                        <svg class="w-5 h-5" style="color:#b45309;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5v-4m0 0h-4m4 0l-5-5"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h2 class="text-lg font-bold text-slate-900 dark:text-white" style="letter-spacing:-0.02em;">
                                            Fullscreen Required
                                        </h2>
                                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                            This exam must be taken in fullscreen mode
                                        </p>
                                    </div>
                                </div>

                                <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                                    Before you can begin, your browser will switch to fullscreen mode to maintain exam integrity.
                                    You will be able to review the section details and exam rules on the next screen.
                                </p>
                            </div>

                            {{-- Steps --}}
                            <div class="px-8 py-5 space-y-3 border-b border-slate-100 dark:border-slate-800">
                                <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider"
                                   style="font-size:10px; letter-spacing:0.12em;">What happens next</p>

                                @foreach([
                                    ['icon' => 'M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5v-4m0 0h-4m4 0l-5-5', 'label' => 'Your browser enters fullscreen mode'],
                                    ['icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'label' => 'Section details and exam rules are displayed'],
                                    ['icon' => 'M13 7l5 5m0 0l-5 5m5-5H6', 'label' => 'You click Begin Section to start the timer'],
                                ] as $i => $step)
                                    <div class="flex items-center gap-3">
                                        <div class="flex-shrink-0 w-6 h-6 flex items-center justify-center text-xs font-bold text-amber-700 dark:text-amber-400"
                                             style="background:rgba(217,119,6,0.1); border-radius:50%;">
                                            {{ $i + 1 }}
                                        </div>
                                        <div class="flex items-center gap-2.5 flex-1">
                                            <svg class="w-4 h-4 text-slate-400 dark:text-slate-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $step['icon'] }}"/>
                                            </svg>
                                            <span class="text-sm text-slate-700 dark:text-slate-300">{{ $step['label'] }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            {{-- Notice --}}
                            <div class="px-8 py-4 border-b border-slate-100 dark:border-slate-800"
                                 style="background: rgba(251,191,36,0.04);">
                                <div class="flex items-start gap-2.5">
                                    <svg class="w-4 h-4 text-amber-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                    </svg>
                                    <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed">
                                        Exiting fullscreen at any point during the exam will be recorded as a violation and will block your screen until you return. The timer continues running while you are outside fullscreen.
                                    </p>
                                </div>
                            </div>

                            {{-- Actions --}}
                            <div class="px-8 py-6 flex flex-col sm:flex-row gap-3">
                                <button @click="fullscreenGate.request();"
                                        class="flex-1 inline-flex items-center justify-center gap-2 py-3 px-6 text-sm font-semibold text-white transition-all"
                                        style="background: linear-gradient(135deg,#b45309,#d97706); border-radius: 2px; box-shadow: 0 2px 12px rgba(180,83,9,0.35);">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5v-4m0 0h-4m4 0l-5-5"/>
                                    </svg>
                                    Enter Fullscreen to Begin
                                </button>
                                <a href="{{ route('examination-hub.take.start', $exam) }}"
                                   class="inline-flex items-center justify-center gap-2 py-3 px-5 text-sm font-medium text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors"
                                   style="border-radius: 2px;">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                    </svg>
                                    Exit
                                </a>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </template>

        <!-- EXAM CONTENT AREA - Initially hidden until section starts -->
        <div id="exam-content-area" class="h-full" x-show="!timerExpired" style="display: none;" role="main" aria-label="Exam content">
        <style>
            /* Defaults live on :root so Livewire morphdom never clobbers them. */
            :root {
                --exam-qfont: 1rem;
                --exam-afont: 0.875rem;
                --exam-tfont: 1rem;
            }
            #exam-reading-area .exam-q-text             { font-size: var(--exam-qfont) !important; line-height: 1.75; }
            #exam-reading-area .exam-q-text .prose      { font-size: inherit !important; }
            #exam-reading-area .exam-q-text .prose *    { font-size: inherit !important; }
            #exam-reading-area .exam-opt-text           { font-size: var(--exam-afont) !important; }
            #exam-reading-area .exam-opt-text .prose *  { font-size: inherit !important; }
            #exam-reading-area .exam-tf-text            { font-size: var(--exam-tfont) !important; }
            #exam-reading-area .exam-essay              { font-size: var(--exam-afont) !important; }
        </style>
            {{-- Skip links for keyboard navigation --}}
            <nav aria-label="Skip links" class="sr-only">
                <a href="#question-navigation">Skip to question navigation</a>
                <a href="#question-content">Skip to current question</a>
            </nav>

            <!-- Main exam interface -->
            <div class="h-full flex flex-col">
                @livewire('examination-hub.exam-section-taking', [
                    'exam' => $exam,
                    'submission' => $submission,
                    'section' => $section,
                    'sectionIndex' => $sectionIndex,
                    'questions' => $questions,
                    'initialQuestionIndex' => session('restored_question', request()->query('q', 0)),
                ])
            </div>
        </div>
    </div>

    <script>
        // Proctoring initialization (only when needed)
        const proctoringEnabled = @json($proctoringEnabled ?? false);
        const sessionId         = @json($proctoringSessionId ?? null);
        const endpoint          = @json(route('examination-hub.take.proctor.event', ['exam' => $exam]));

        if (proctoringEnabled && sessionId) {
            // Configure the proctoring system BEFORE loading the script
            window.ExamProctorConfig = {
                eventUrl: endpoint,
                csrfToken: document.querySelector('meta[name="csrf-token"]')?.content,
                hardenedMode: @json($exam->hardened_mode ?? false),
                requireFullscreen: @json($exam->require_fullscreen ?? false),
                autoSubmitUrl: null // We'll handle submission via Livewire
            };
        }
    </script>


    @push('exam-scripts')
        <script src="{{ asset('js/exam-sync.js') }}"></script>
    @endpush

    @push('exam-scripts')
        <script>
            // Initialize exam sync after DOM is ready
            document.addEventListener('DOMContentLoaded', function() {
                // Only initialize if the exam sync class exists
                if (typeof ExamSessionSync !== 'undefined') {
                    const examId = @json($exam->id);
                    const submissionId = @json($submission->id ?? null);
                    const userId = @json(auth()->id());

                    if (examId && submissionId) {
                        window.examSync = new ExamSessionSync(examId, submissionId, userId);
                        window.examSync.init();

                        // Set up event listeners to update sync when responses change
                        Livewire.on('examDataSyncing', () => {
                            if (window.examSync) {
                                window.examSync.syncImmediate();
                            }
                        });
                    }
                }

                // Initialize exam heartbeat after exam sync
                if (typeof ExamHeartbeat !== 'undefined') {
                    const examId = @json($exam->id);
                    const submissionId = @json($submission->id ?? null);

                    if (examId && submissionId) {
                        window.examHeartbeat = new ExamHeartbeat({
                            examId: examId,
                            heartbeatUrl: "{{ route('examination-hub.take.heartbeat', ['exam' => $exam]) }}",
                            initUrl: "{{ route('examination-hub.take.heartbeat.init', ['exam' => $exam]) }}",
                            acknowledgeUrl: "{{ route('examination-hub.take.heartbeat.acknowledge-warning', ['exam' => $exam]) }}",
                            interval: 15000, // 15 seconds

                            onWarning: function(warning) {
                                console.log('Warning received:', warning);
                                // Use default warning handler (modal popup)
                                window.examHeartbeat.defaultWarningHandler(warning);
                            },

                            onTerminated: function(data) {
                                data.redirect = "{{ route('examination-hub.take.completed', $exam) }}";
                                window.examHeartbeat.defaultTerminatedHandler(data);
                            },

                            onMessage: function(message) {
                                console.log('Admin message:', message);
                                // Use default message handler (toast notification)
                                window.examHeartbeat.defaultMessageHandler(message);
                            },

                            onForceSubmit: function(data) {
                                console.log('Force submit received:', data);
                                // Show force submit notification and redirect
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

                                // Redirect after 3 seconds
                                setTimeout(() => {
                                    window.location.href = "{{ route('examination-hub.take.completed', $exam) }}";
                                }, 3000);
                            },

                            onTimeExtended: function(additionalMinutes) {
                                console.log('Time extended by:', additionalMinutes, 'minutes');
                                // Show time extension toast
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

                                // Auto remove after 8 seconds
                                setTimeout(() => toast.remove(), 8000);
                            }
                        });
                    }
                }
            });
        </script>

        {{-- Auto-save and submission validation --}}
        <script>
            // Track unsaved changes
            window.hasUnsavedChanges = false;
            window.currentResponses = {};
            window.autoSaveInterval = null;

            // Listen for Livewire response updates
            document.addEventListener('livewire:initialized', () => {
                Livewire.on('responseUpdated', (data) => {
                    window.hasUnsavedChanges = true;
                    if (data && data.questionId && data.response) {
                        window.currentResponses[data.questionId] = data.response;
                    }
                });
            });

            // Auto-save every 30 seconds
            function startAutoSave() {
                window.autoSaveInterval = setInterval(() => {
                    if (window.hasUnsavedChanges) {
                        saveResponsesSilently();
                    }
                }, 30000); // 30 seconds
            }

            // Silent auto-save using beacon API
            function saveResponsesSilently() {
                const csrfToken = document.queryDEBUGSelector('meta[name="csrf-token"]')?.content;
                if (!csrfToken) return;

                // Get current responses from Livewire component
                const livewireComponent = document.querySelector('[wire\:id]');
                if (!livewireComponent) return;

                // Use navigator.sendBeacon for reliable delivery even on page unload
                const data = new FormData();
                data.append('_token', csrfToken);
                data.append('_method', 'POST');

                // Send beacon to save endpoint
                navigator.sendBeacon(
                    "{{ route('examination-hub.take.save-response', $exam) }}",
                    data
                );

                window.hasUnsavedChanges = false;
                console.log('Auto-saved responses at', new Date().toISOString());
            }

            // Save before page unload
            window.addEventListener('beforeunload', (e) => {
                if (window.hasUnsavedChanges) {
                    saveResponsesSilently();

                    // Show warning if user tries to leave with unsaved changes
                    e.preventDefault();
                    e.returnValue = '';
                    return '';
                }
            });

            // Activate violation monitoring only once the section has actually started.
            // Loading the proctoring script and enabling right-click prevention before
            // this point would record false violations while the candidate is still on
            // the pre-fullscreen gate page.
            window.addEventListener('section-started', () => {
                startTimer();
                startAutoSave();

                // Disable right-click now that the exam is live
                document.addEventListener('contextmenu', function(e) {
                    e.preventDefault();
                }, true);

                // Load the proctoring script now that the section is active
                @if($proctoringEnabled ?? false)
                if (window.ExamProctorConfig) {
                    const s = document.createElement('script');
                    s.src = @json(asset('js/exam-proctor.js'));
                    document.head.appendChild(s);
                }
                @endif
            }, { once: true });

            // Time remaining countdown
            @php
                $jsTimeRemaining = $timeRemaining ?? ($section->time_limit_minutes ? $section->time_limit_minutes * 60 : 1800);
            @endphp
            let timeRemainingSeconds = {{ $jsTimeRemaining }};

            function startTimer(attemptsLeft) {
                attemptsLeft = (attemptsLeft === undefined) ? 20 : attemptsLeft;
                const timeElement = document.getElementById('time-remaining');
                if (!timeElement) {
                    // Livewire morph may not have completed yet — retry up to 2 s
                    if (attemptsLeft > 0) setTimeout(() => startTimer(attemptsLeft - 1), 100);
                    return;
                }
                updateTimerDisplay();
                setInterval(updateTimeRemaining, 1000);
            }

            function updateTimeRemaining() {
                const timeElement = document.getElementById('time-remaining');
                if (!timeElement) return;

                if (timeRemainingSeconds <= 0) {
                    timeElement.textContent = '00:00:00';
                    if (!window.__timerExpired) {
                        window.__timerExpired = true;
                        showTimeWarning('expired');
                        autoSubmitExpired();
                        if (window.Alpine) {
                            window.Alpine.store('examState').timerExpired = true;
                        }
                        if (window.sectionInfoAlpine) {
                            window.sectionInfoAlpine.timerExpired = true;
                        }
                    }
                    return;
                }

                timeRemainingSeconds--;
                updateTimerDisplay();

                if (timeRemainingSeconds <= 300) {
                    showTimeWarning('critical');
                } else if (timeRemainingSeconds <= 600) {
                    showTimeWarning('warning');
                }
            }

            function autoSubmitExpired() {
                const overlay = document.createElement('div');
                overlay.id = 'time-expired-overlay';
                overlay.style.cssText = 'position:fixed;inset:0;z-index:99999;background:rgba(0,0,0,0.88);display:flex;align-items:center;justify-content:center;padding:16px;pointer-events:all;';
                overlay.innerHTML = `
                    <div style="background:#fff;border-radius:12px;padding:36px 32px;max-width:400px;width:100%;text-align:center;box-shadow:0 25px 50px rgba(0,0,0,0.5);">
                        <div style="width:64px;height:64px;background:#fee2e2;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                            <svg style="width:32px;height:32px;color:#dc2626;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h3 style="font-size:1.25rem;font-weight:700;color:#111;margin:0 0 8px;">Time Expired</h3>
                        <p style="color:#555;font-size:.95rem;margin:0 0 20px;">Your exam time has ended. Submitting your answers automatically&hellip;</p>
                        <div style="width:100%;height:5px;background:#f3f4f6;border-radius:3px;overflow:hidden;">
                            <div id="expire-progress-bar" style="height:100%;background:linear-gradient(90deg,#dc2626,#ef4444);width:0%;transition:width 2s linear;border-radius:3px;"></div>
                        </div>
                    </div>
                `;
                document.body.appendChild(overlay);
                requestAnimationFrame(() => {
                    const bar = document.getElementById('expire-progress-bar');
                    if (bar) bar.style.width = '100%';
                });
                setTimeout(() => {
                    const form = document.getElementById('exam-submit-form');
                    if (form) {
                        form.submit();
                    } else {
                        const f = document.createElement('form');
                        f.method = 'POST';
                        f.action = '{{ route('examination-hub.take.submit', $exam) }}';
                        const t = document.createElement('input');
                        t.type = 'hidden'; t.name = '_token';
                        t.value = document.querySelector('meta[name="csrf-token"]')?.content || '';
                        f.appendChild(t);
                        document.body.appendChild(f);
                        f.submit();
                    }
                }, 2000);
            }

            function updateTimerDisplay() {
                const hours   = Math.floor(timeRemainingSeconds / 3600);
                const minutes = Math.floor((timeRemainingSeconds % 3600) / 60);
                const seconds = timeRemainingSeconds % 60;
                const str =
                    String(hours).padStart(2, '0') + ':' +
                    String(minutes).padStart(2, '0') + ':' +
                    String(seconds).padStart(2, '0');

                const el = document.getElementById('time-remaining');
                if (el) el.textContent = str;
                const elM = document.getElementById('time-remaining-mobile');
                if (elM) elM.textContent = str;
            }

            function showTimeWarning(level) {
                const base = 'inline-flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-semibold transition-all duration-300';
                const baseM = 'inline-flex items-center gap-1 px-2 py-1.5 text-xs font-semibold transition-all duration-300';
                let cls, clsM;

                if (level === 'expired') {
                    cls  = base  + ' bg-red-600 text-white animate-pulse';
                    clsM = baseM + ' bg-red-600 text-white animate-pulse';
                } else if (level === 'critical') {
                    cls  = base  + ' bg-red-100 dark:bg-red-900/30 border border-red-300 dark:border-red-700 text-red-700 dark:text-red-300';
                    clsM = baseM + ' bg-red-100 dark:bg-red-900/30 border border-red-300 dark:border-red-700 text-red-700 dark:text-red-300';
                } else if (level === 'warning') {
                    cls  = base  + ' bg-amber-100 dark:bg-amber-900/30 border border-amber-300 dark:border-amber-700 text-amber-700 dark:text-amber-300';
                    clsM = baseM + ' bg-amber-100 dark:bg-amber-900/30 border border-amber-300 dark:border-amber-700 text-amber-700 dark:text-amber-300';
                }

                const alertBox = document.getElementById('time-alert');
                if (alertBox && cls) { alertBox.className = cls; alertBox.style.borderRadius = '2px'; }
                const alertBoxM = document.getElementById('time-alert-mobile');
                if (alertBoxM && clsM) { alertBoxM.className = clsM; alertBoxM.style.borderRadius = '2px'; }
            }

            // Validation before final submission
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

            // Initialize when DOM is ready
            document.addEventListener('DOMContentLoaded', () => {
                // If section already started, begin auto-save
                const examContent = document.getElementById('exam-content-area');
                if (examContent && examContent.style.display !== 'none') {
                    startAutoSave();
                }
            });

            // ── BOOKMARKING FEATURE ──
            window.examBookmarks = {
                bookmarks: new Set(),

                init() {
                    // Load bookmarks from localStorage
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
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
                    // Update bookmark icons in the UI
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

                    // Create modal to show bookmarked questions
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
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
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

                    // Close on ESC key
                    modal.addEventListener('keydown', (e) => {
                        if (e.key === 'Escape') {
                            modal.remove();
                        }
                    });

                    // Close on backdrop click
                    modal.addEventListener('click', (e) => {
                        if (e.target === modal) {
                            modal.remove();
                        }
                    });
                },

                goToQuestion(questionId) {
                    // This would integrate with Livewire to navigate to the question
                    // For now, just close the modal and show a notification
                    document.querySelector('.fixed[role="dialog"]')?.remove();
                    this.showNotification(`Navigate to question ${questionId} (integration needed)`, 'info');
                }
            };

            // Initialize bookmarks when DOM is ready
            document.addEventListener('DOMContentLoaded', () => {
                window.examBookmarks.init();
            });


            // Keyboard shortcut for bookmarks (Ctrl+B or Cmd+B)
            document.addEventListener('keydown', (e) => {
                if ((e.ctrlKey || e.metaKey) && e.key === 'b') {
                    e.preventDefault();
                    // Get current question ID from Livewire component
                    const livewireComponent = document.querySelector('[wire\:id]');
                    if (livewireComponent) {
                        // This would need to get the current question ID from Livewire
                        console.log('Bookmark shortcut pressed - integration needed');
                    }
                }
            });
        </script>
    @endpush
</x-layouts.exam>
