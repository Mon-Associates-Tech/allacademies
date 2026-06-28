/**
 * Exam Heartbeat System
 * Sends periodic heartbeats to the server to track participant activity.
 * Dispatches CustomEvents so all timer instances (desktop + mobile) are updated.
 */
class ExamHeartbeat {
    constructor(options = {}) {
        this.examId       = options.examId;
        this.heartbeatUrl = options.heartbeatUrl;
        this.initUrl      = options.initUrl;
        this.acknowledgeUrl = options.acknowledgeUrl;
        this.interval     = options.interval || 15000; // 15 seconds default
        this.sessionToken = null;
        this.intervalId   = null;
        this.isFocused    = true;
        this.currentQuestionIndex = 0;
        this.currentSectionIndex  = 0;
        this.questionsAnswered    = 0;

        // Callbacks
        this.onWarning     = options.onWarning     || this.defaultWarningHandler;
        this.onTerminated  = options.onTerminated  || this.defaultTerminatedHandler;
        this.onMessage     = options.onMessage     || this.defaultMessageHandler;
        this.onForceSubmit = options.onForceSubmit || null;
        this.onTimeExtended = options.onTimeExtended || null;
        // onTimeSync uses CustomEvent dispatch — no window global needed
        this.onTimeSync    = options.onTimeSync    || this.defaultTimeSyncHandler.bind(this);

        this.init();
    }

    async init() {
        const browserInfo = this.detectBrowser();

        try {
            const response = await fetch(this.initUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                },
                body: JSON.stringify(browserInfo),
            });

            const data = await response.json();

            if (data.session_token) {
                this.sessionToken = data.session_token;
                this.startHeartbeat();
                this.setupEventListeners();
                this.setupEchoListener();
            }
        } catch (error) {
            console.error('Failed to initialize heartbeat session:', error);
        }
    }

    detectBrowser() {
        const ua = navigator.userAgent;
        let browser = 'Unknown';
        let os      = 'Unknown';

        if      (ua.includes('Firefox')) browser = 'Firefox';
        else if (ua.includes('Chrome'))  browser = 'Chrome';
        else if (ua.includes('Safari'))  browser = 'Safari';
        else if (ua.includes('Edge'))    browser = 'Edge';
        else if (ua.includes('Opera'))   browser = 'Opera';

        if      (ua.includes('Windows')) os = 'Windows';
        else if (ua.includes('Mac'))     os = 'macOS';
        else if (ua.includes('Linux'))   os = 'Linux';
        else if (ua.includes('Android')) os = 'Android';
        else if (ua.includes('iOS'))     os = 'iOS';

        return { browser, os, screen_width: window.screen.width, screen_height: window.screen.height };
    }

    startHeartbeat() {
        this.sendHeartbeat();
        this.intervalId = setInterval(() => this.sendHeartbeat(), this.interval);
    }

    async sendHeartbeat() {
        try {
            const response = await fetch(this.heartbeatUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                },
                body: JSON.stringify({
                    is_focused:             this.isFocused,
                    current_question_index: this.currentQuestionIndex,
                    current_section_index:  this.currentSectionIndex,
                    questions_answered:     this.questionsAnswered,
                }),
            });

            if (!response.ok) {
                console.error('Heartbeat HTTP error:', response.status, response.statusText);
                return;
            }

            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                console.error('Heartbeat: Invalid response type:', contentType);
                return;
            }

            const data = await response.json();

            // ── Handle admin actions ──────────────────────────────────────────
            if (data.status === 'terminated') {
                this.stop();
                this.onTerminated(data);
                return;
            }

            if (data.status === 'force_submitted') {
                this.stop();
                if (this.onForceSubmit) {
                    this.onForceSubmit(data);
                } else if (data.redirect) {
                    window.location.href = data.redirect;
                }
                return;
            }

            if (data.warning)       this.onWarning(data.warning);
            if (data.admin_message) this.onMessage(data.admin_message);

            // ── Timer re-sync (polling fallback for when Echo is unavailable) ──
            // The server returns remaining_seconds on every beat, incorporating
            // extra_time_minutes so extensions are caught within one poll cycle.
            if (data.remaining_seconds !== undefined && data.remaining_seconds !== null) {
                this.onTimeSync(data.remaining_seconds, data.extra_time_minutes ?? 0);
            }
        } catch (error) {
            console.error('Heartbeat failed:', error.message);
        }
    }

    setupEventListeners() {
        window.addEventListener('focus', () => { this.isFocused = true; });
        window.addEventListener('blur',  () => { this.isFocused = false; });
        document.addEventListener('visibilitychange', () => {
            this.isFocused = document.visibilityState === 'visible';
        });
    }

    setupEchoListener() {
        if (typeof Echo === 'undefined' || !this.sessionToken) return;

        Echo.channel('exam-participant.' + this.sessionToken)
            .listen('.admin.action', (data) => {
                switch (data.action) {
                    case 'warning':
                        this.onWarning({ message: data.message, warned_at: data.timestamp });
                        break;

                    case 'terminate':
                        this.stop();
                        this.onTerminated({ reason: data.message, message: 'Your exam session has been terminated by the administrator.' });
                        break;

                    case 'force_submit':
                        if (this.onForceSubmit) this.onForceSubmit(data);
                        break;

                    case 'message':
                        this.onMessage(data.message);
                        break;

                    case 'extend_time':
                        // Real-time path: Echo delivers the extension instantly.
                        //
                        // Prefer remaining_seconds (authoritative) if the broadcast
                        // includes it. Fall back to additional_minutes if not.
                        //
                        // We dispatch exam:sync-time or exam:extend-time so that
                        // ALL timer instances (desktop + mobile) are updated via
                        // their document event listeners — no window global needed.
                        const addedMinutes = data.data?.additional_minutes ?? 0;
                        const newRemaining = data.data?.remaining_seconds  ?? null;

                        if (newRemaining !== null) {
                            document.dispatchEvent(new CustomEvent('exam:sync-time', {
                                detail: {
                                    remaining:   newRemaining,
                                    extraMinutes: data.data?.extra_time_minutes ?? 0,
                                },
                            }));
                        } else if (addedMinutes > 0) {
                            document.dispatchEvent(new CustomEvent('exam:extend-time', {
                                detail: { minutes: addedMinutes },
                            }));
                        }

                        // Still call the custom callback (e.g. for logging / UI outside the timer)
                        if (this.onTimeExtended) this.onTimeExtended(addedMinutes);
                        break;
                }
            });
    }

    updateProgress(questionIndex, sectionIndex, answeredCount) {
        this.currentQuestionIndex = questionIndex;
        this.currentSectionIndex  = sectionIndex;
        this.questionsAnswered    = answeredCount;
    }

    async acknowledgeWarning() {
        try {
            await fetch(this.acknowledgeUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                },
            });
        } catch (error) {
            console.error('Failed to acknowledge warning:', error);
        }
    }

    stop() {
        if (this.intervalId) {
            clearInterval(this.intervalId);
            this.intervalId = null;
        }
    }

    // ── Default handlers ──────────────────────────────────────────────────────

    // Dispatches CustomEvent so every timer instance on the page receives the
    // sync simultaneously. No window global — no "last writer wins" race.
    defaultTimeSyncHandler(remainingSeconds, extraTimeMinutes) {
        document.dispatchEvent(new CustomEvent('exam:sync-time', {
            detail: {
                remaining:    remainingSeconds,
                extraMinutes: extraTimeMinutes ?? 0,
            },
        }));
    }

    defaultWarningHandler(warning) {
        const modal = document.createElement('div');
        modal.className = 'fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70';
        modal.innerHTML = `
            <div class="bg-white rounded-lg w-full max-w-md p-6 text-center">
                <div class="w-16 h-16 mx-auto mb-4 flex items-center justify-center rounded-full bg-yellow-100">
                    <svg class="w-8 h-8 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-slate-900 mb-2">Warning from Proctor</h3>
                <p class="text-slate-600 mb-6">${warning.message}</p>
                <button onclick="this.closest('.fixed').remove(); window.examHeartbeat?.acknowledgeWarning();"
                        class="px-6 py-2 bg-yellow-500 text-white font-semibold rounded hover:bg-yellow-600">
                    I Understand
                </button>
            </div>
        `;
        document.body.appendChild(modal);
    }

    defaultTerminatedHandler(data) {
        const modal = document.createElement('div');
        modal.className = 'fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70';
        modal.innerHTML = `
            <div class="bg-white rounded-lg w-full max-w-md p-6 text-center">
                <div class="w-16 h-16 mx-auto mb-4 flex items-center justify-center rounded-full bg-red-100">
                    <svg class="w-8 h-8 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-slate-900 mb-2">Session Terminated</h3>
                <p class="text-slate-600 mb-2">${data.message}</p>
                <p class="text-sm text-slate-500 mb-6">Reason: ${data.reason || 'Not specified'}</p>
                <p class="text-sm text-slate-500">Redirecting...</p>
            </div>
        `;
        document.body.appendChild(modal);
        setTimeout(() => { if (data.redirect) window.location.href = data.redirect; }, 3000);
    }

    defaultMessageHandler(message) {
        const text = typeof message === 'object' ? (message.message ?? JSON.stringify(message)) : message;
        const toast = document.createElement('div');
        toast.className = 'fixed bottom-4 right-4 z-50 max-w-sm p-4 bg-blue-600 text-white rounded-lg shadow-lg';
        toast.innerHTML = `
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                </svg>
                <div>
                    <p class="font-semibold">Message from Proctor</p>
                    <p class="text-sm opacity-90">${text}</p>
                </div>
                <button onclick="this.closest('.fixed').remove();" class="ml-auto text-white/70 hover:text-white">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        `;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 10000);
    }
}

window.ExamHeartbeat = ExamHeartbeat;
