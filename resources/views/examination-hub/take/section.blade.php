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

                try {
                    await document.documentElement.requestFullscreen().catch(e => {
                        // Safari/iOS
                        if (document.documentElement.webkitRequestFullscreen) {
                            return document.documentElement.webkitRequestFullscreen();
                        }
                        throw e;
                    });
                    
                    this.isActive = true;
                    
                    // IMPORTANT: Only hide the instruction panel after successfully entering fullscreen
                    // This ensures the exam content is displayed when in fullscreen
                    setTimeout(() => {
                        if (this.isActive) {
                            this.hideInstructionPanel();
                        }
                    }, 100); // Small delay to ensure fullscreen is established
                    
                } catch (err) {
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
                const nowFullscreen = document.fullscreenElement != null;
                
                if (this.isActive && !nowFullscreen) {
                    // Fullscreen was unexpectedly exited - record violation
                    this.handleUnexpectedExit();
                }
                
                this.isActive = nowFullscreen;
                
                // If we're no longer in fullscreen and were showing exam content, 
                // decide whether to show instructions or keep showing exam content
                if (!this.isActive) {
                    // When leaving fullscreen, keep showing exam content if section has started
                    // We can determine this by checking if the exam content area is supposed to be visible
                    const examContent = document.getElementById('exam-content-area');
                    const instructionPanel = document.getElementById('fullscreen-instruction-panel');
                    
                    if (examContent && examContent.style.display !== 'none') {
                        // Section has started, keep showing exam content
                        this.showExamContentOnly();
                    } else {
                        // Section hasn't started, show instructions
                        this.showInstructionPanel();
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
                }).catch(console.error); // Don't let network errors break the flow
            }
        };

        // Initialize fullscreen gate when DOM is loaded
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
                                    // Make sure to show exam content after section starts
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
        <div id="exam-content-area" class="h-full" style="display: none;">
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
    
    @if($proctoringEnabled ?? false)
        @push('exam-scripts')
            <script src="{{ asset('js/exam-proctor.js') }}"></script>
        @endpush
    @endif
    
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
                                // Show warning modal or notification
                                alert('Warning: ' + warning.message);
                            },
                            
                            onTerminated: function(data) {
                                console.log('Session terminated:', data);
                                // Redirect to completion page
                                window.location.href = data.redirect || "{{ route('examination-hub.take.completed', $exam) }}";
                            },
                            
                            onMessage: function(message) {
                                console.log('Admin message:', message);
                                // Show admin message
                                alert('Message from admin: ' + message);
                            }
                        });
                    }
                }
            });
        </script>
    @endpush
</x-layouts.exam>