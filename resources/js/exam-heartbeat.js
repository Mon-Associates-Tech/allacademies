/**
 * Exam Heartbeat System
 * Sends periodic heartbeats to the server to track participant activity.
 */
class ExamHeartbeat {
    constructor(options = {}) {
        this.examId = options.examId;
        this.heartbeatUrl = options.heartbeatUrl;
        this.initUrl = options.initUrl;
        this.acknowledgeUrl = options.acknowledgeUrl;
        this.interval = options.interval || 15000; // 15 seconds default
        this.sessionToken = null;
        this.intervalId = null;
        this.isFocused = true;
        this.currentQuestionIndex = 0;
        this.currentSectionIndex = 0;
        this.questionsAnswered = 0;

        // Callbacks
        this.onWarning      = options.onWarning      || this.defaultWarningHandler;
        this.onTerminated   = options.onTerminated   || this.defaultTerminatedHandler;
        this.onMessage      = options.onMessage      || this.defaultMessageHandler;
        this.onForceSubmit  = options.onForceSubmit  || null;
        this.onTimeExtended = options.onTimeExtended || null;
        this.onTimeSync     = options.onTimeSync     || this.defaultTimeSyncHandler.bind(this);
        // Fired when a second device takes over the session.
        this.onSessionSuperseded = options.onSessionSuperseded || this.defaultSessionSupersededHandler.bind(this);

        this.init();
    }

    async init() {
        // Detect browser and OS
        const browserInfo = this.detectBrowser();

        // Initialize session
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
        let os = 'Unknown';

        // Detect browser
        if (ua.includes('Firefox')) browser = 'Firefox';
        else if (ua.includes('Chrome')) browser = 'Chrome';
        else if (ua.includes('Safari')) browser = 'Safari';
        else if (ua.includes('Edge')) browser = 'Edge';
        else if (ua.includes('Opera')) browser = 'Opera';

        // Detect OS
        if (ua.includes('Windows')) os = 'Windows';
        else if (ua.includes('Mac')) os = 'macOS';
        else if (ua.includes('Linux')) os = 'Linux';
        else if (ua.includes('Android')) os = 'Android';
        else if (ua.includes('iOS')) os = 'iOS';

        return {
            browser,
            os,
            screen_width: window.screen.width,
            screen_height: window.screen.height,
        };
    }

    startHeartbeat() {
        // Send initial heartbeat
        this.sendHeartbeat();

        // Set up interval
        this.intervalId = setInterval(() => {
            this.sendHeartbeat();
        }, this.interval);
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
                    is_focused: this.isFocused,
                    current_question_index: this.currentQuestionIndex,
                    current_section_index: this.currentSectionIndex,
                    questions_answered: this.questionsAnswered,
                }),
            });

            // Validate HTTP response
            if (!response.ok) {
                console.error('Heartbeat HTTP error:', response.status, response.statusText);
                return;
            }

            // Validate content type before parsing JSON
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                console.error('Heartbeat: Invalid response type:', contentType);
                return;
            }

            const data = await response.json();

            // Handle responses
            if (data.status === 'terminated') {
                this.stop();
                this.onTerminated(data);
            } else if (data.status === 'session_superseded') {
                // A second device authenticated for the same exam.
                // Stop sending heartbeats and hand off to the superseded handler.
                this.stop();
                this.onSessionSuperseded(data);
            } else if (data.warning) {
                this.onWarning(data.warning);
            } else if (data.admin_message) {
                this.onMessage(data.admin_message);
            }

            // Re-sync timer from server's authoritative remaining time.
            // Called on every heartbeat so extensions are picked up within
            // one polling cycle even when Echo is unavailable.
            if (data.remaining_seconds !== undefined && data.remaining_seconds !== null) {
                this.onTimeSync(data.remaining_seconds, data.extra_time_minutes ?? 0);
            }
        } catch (error) {
            console.error('Heartbeat failed:', error.message);
        }
    }

    setupEventListeners() {
        // Track focus/blur
        window.addEventListener('focus', () => {
            this.isFocused = true;
        });

        window.addEventListener('blur', () => {
            this.isFocused = false;
        });

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
                        if (this.onForceSubmit) {
                            this.onForceSubmit(data);
                        }
                        break;
                    case 'message':
                        this.onMessage(data.message);
                        break;
                    case 'extend_time':
                        // Dispatch CustomEvent so ALL timer instances (desktop +
                        // mobile) receive the update via their own event listeners.
                        // No window global — avoids "last writer wins" race.
                        const addedMinutes = data.data?.additional_minutes ?? 0;
                        const newRemaining = data.data?.remaining_seconds   ?? null;

                        if (newRemaining !== null) {
                            document.dispatchEvent(new CustomEvent('exam:sync-time', {
                                detail: {
                                    remaining:    newRemaining,
                                    extraMinutes: data.data?.extra_time_minutes ?? 0,
                                },
                            }));
                        } else if (addedMinutes > 0) {
                            document.dispatchEvent(new CustomEvent('exam:extend-time', {
                                detail: { minutes: addedMinutes },
                            }));
                        }

                        if (this.onTimeExtended) {
                            this.onTimeExtended(addedMinutes);
                        }
                        break;
                }
            });
    }

    // Update tracking data
    updateProgress(questionIndex, sectionIndex, answeredCount) {
        this.currentQuestionIndex = questionIndex;
        this.currentSectionIndex = sectionIndex;
        this.questionsAnswered = answeredCount;
    }

    // Acknowledge warning
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

    // Stop heartbeat
    stop() {
        if (this.intervalId) {
            clearInterval(this.intervalId);
            this.intervalId = null;
        }
    }

    // Default handlers
    defaultWarningHandler(warning) {
        // Show warning modal
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
        // Show termination modal and redirect
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

        // Redirect after 3 seconds
        setTimeout(() => {
            if (data.redirect) {
                window.location.href = data.redirect;
            }
        }, 3000);
    }

    defaultMessageHandler(message) {
        // Show non-blocking notification
        const toast = document.createElement('div');
        toast.className = 'fixed bottom-4 right-4 z-50 max-w-sm p-4 bg-blue-600 text-white rounded-lg shadow-lg animate-slide-up';
        toast.innerHTML = `
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                </svg>
                <div>
                    <p class="font-semibold">Message from Proctor</p>
                    <p class="text-sm opacity-90">${message}</p>
                </div>
                <button onclick="this.closest('.fixed').remove();" class="ml-auto text-white/70 hover:text-white">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        `;
        document.body.appendChild(toast);

        // Auto remove after 10 seconds
        setTimeout(() => toast.remove(), 10000);
    }

    defaultSessionSupersededHandler(data) {
        document.querySelectorAll('.exam-blocking-modal').forEach(el => el.remove());

        const modal = document.createElement('div');
        modal.className = 'exam-blocking-modal fixed inset-0 z-[999] flex items-center justify-center p-4 bg-black/80';
        modal.innerHTML = `
            <div style="background:#fff;border-radius:2px;max-width:28rem;width:100%;overflow:hidden;box-shadow:0 25px 60px rgba(0,0,0,.4);">
                <div style="height:4px;background:linear-gradient(90deg,#dc2626,#f87171);"></div>
                <div style="padding:2rem;">
                    <div style="display:flex;align-items:center;gap:.75rem;margin-bottom:1.25rem;">
                        <div style="flex-shrink:0;width:2.5rem;height:2.5rem;border-radius:50%;background:#fee2e2;display:flex;align-items:center;justify-content:center;">
                            <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="#dc2626" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 style="margin:0;font-size:1rem;font-weight:700;color:#111;">Session Taken Over</h3>
                            <p style="margin:.25rem 0 0;font-size:.8125rem;color:#6b7280;">Your exam was opened on another device</p>
                        </div>
                    </div>
                    <p style="font-size:.875rem;color:#374151;margin:0 0 1.5rem;line-height:1.6;">
                        ${data.message || 'This exam session has been opened on another device.'}
                    </p>
                    <p style="font-size:.75rem;color:#9ca3af;margin:0;">Redirecting in <span id="superseded-countdown">5</span> seconds...</p>
                </div>
            </div>
        `;
        document.body.appendChild(modal);

        let secs = 5;
        const tick = setInterval(() => {
            secs--;
            const el = document.getElementById('superseded-countdown');
            if (el) el.textContent = secs;
            if (secs <= 0) {
                clearInterval(tick);
                window.location.href = data.redirect || '/examinations/join';
            }
        }, 1000);
    }

    // Dispatches a CustomEvent so every timer instance on the page receives
    // the update. Both desktop and mobile timers listen on document.
    defaultTimeSyncHandler(remainingSeconds, extraTimeMinutes) {
        document.dispatchEvent(new CustomEvent('exam:sync-time', {
            detail: {
                remaining:    remainingSeconds,
                extraMinutes: extraTimeMinutes ?? 0,
            },
        }));
    }
}

// Export for use
window.ExamHeartbeat = ExamHeartbeat;
