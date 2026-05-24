<x-layouts.exam>
    @livewire('examination-hub.exam-section-taking', [
        'exam' => $exam,
        'submission' => $submission,
        'section' => $section,
        'sectionIndex' => $sectionIndex,
        'questions' => $questions,
        'initialQuestionIndex' => session('restored_question', request()->query('q', 0)),
    ])

    <script>
        (function () {
            const proctoringEnabled = @json($proctoringEnabled ?? false);
            const sessionId         = @json($proctoringSessionId ?? null);
            const endpoint          = @json(route('examination-hub.take.proctor.event', ['exam' => $exam]));

            if (!proctoringEnabled) return;

            // ── Fullscreen gate ──────────────────────────────────────────────
            function isFullscreen() {
                return !!(document.fullscreenElement || document.webkitFullscreenElement || document.mozFullScreenElement);
            }

            function requestFullscreen() {
                const el = document.documentElement;
                const fn = el.requestFullscreen || el.webkitRequestFullscreen || el.mozRequestFullScreen || el.msRequestFullscreen;
                return fn ? fn.call(el) : Promise.resolve();
            }

            function showGate() {
                const g = document.getElementById('fullscreen-gate');
                if (g) g.style.display = 'flex';
            }

            function hideGate() {
                const g = document.getElementById('fullscreen-gate');
                if (g) g.style.display = 'none';
            }

            // Gate is visible by default in HTML.
            // Hide it only if already in fullscreen (e.g. user navigated from start page button).
            // Otherwise the user must click the button to enter fullscreen.
            if (isFullscreen()) {
                document.addEventListener('DOMContentLoaded', hideGate);
            }

            // Also check once DOM is ready
            document.addEventListener('DOMContentLoaded', () => {
                if (isFullscreen()) { hideGate(); }
            });

            // On fullscreen exit: show gate and report violation
            document.addEventListener('fullscreenchange', () => {
                if (!isFullscreen()) {
                    showGate();
                    window._examProctor?.report('fullscreen_exit');
                } else {
                    hideGate();
                }
            });
            document.addEventListener('webkitfullscreenchange', () => {
                if (!isFullscreen()) { showGate(); } else { hideGate(); }
            });

            // Resume button
            document.addEventListener('DOMContentLoaded', () => {
                const btn = document.getElementById('fullscreen-resume-btn');
                if (btn) {
                    btn.addEventListener('click', () => {
                        requestFullscreen().then(hideGate).catch(hideGate);
                    });
                }
            });

            // ── Proctoring boot (only when sessionId available) ──────────────────────
            if (sessionId) {
                function boot() {
                    if (!window.ExamProctoring) { setTimeout(boot, 100); return; }
                    const proctor = new window.ExamProctoring({
                        sessionId,
                        endpoint,
                        violations: @json($exam->resolvedViolationSettings()),
                    });
                    proctor.init();
                    proctor.report('exam_enter');
                    window._examProctor = proctor;
                }
                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', boot, { once: true });
                } else {
                    boot();
                }
            }
        })();
    </script>
    <script>
        /**
         * exam-autosave.js (inline)
         *
         * Watches every answer input on the page and POSTs to saveResponse
         * 800 ms after the user stops changing an answer.
         * Shows a subtle status indicator so the user knows their work is safe.
         */
        (function () {
            'use strict';

            const SAVE_URL     = '{{ route('examination-hub.take.save-response', $exam) }}';
            const CSRF_TOKEN   = '{{ csrf_token() }}';
            const EXAM_ID      = {{ $exam->id }};
            const SUBMISSION_ID = {{ $submission->id }};
            const SECTION_IDX  = {{ $sectionIndex }};
            const DEBOUNCE_MS  = 800;

            // ── Status indicator ────────────────────────────────────────────────────
            const indicator = document.getElementById('autosave-indicator');
            // Offline indicator (created dynamically if missing)
            let offlineIndicator = document.getElementById('offline-indicator');
            if (!offlineIndicator) {
                offlineIndicator = document.createElement('div');
                offlineIndicator.id = 'offline-indicator';
                offlineIndicator.style.cssText = 'font-size:0.75rem; color:#b91c1c; margin-left:0.5rem; display:none;';
                if (indicator && indicator.parentNode) {
                    indicator.parentNode.insertBefore(offlineIndicator, indicator.nextSibling);
                } else {
                    document.addEventListener('DOMContentLoaded', () => {
                        const el = document.getElementById('autosave-indicator');
                        if (el && el.parentNode) el.parentNode.insertBefore(offlineIndicator, el.nextSibling);
                    });
                }
            }

            function setStatus(state) {
                if (!indicator) return;
                const states = {
                    saving: { text: 'Saving…',  cls: 'text-amber-500' },
                    saved:  { text: '✓ Saved',  cls: 'text-emerald-600' },
                    error:  { text: '⚠ Not saved — retrying…', cls: 'text-red-500' },
                };
                const s = states[state] ?? states.saved;
                indicator.textContent = s.text;
                indicator.className   = `text-xs font-medium transition-all ${s.cls}`;
            }

            // ── Core save function ──────────────────────────────────────────────────
            async function saveResponse(questionId, response, retries = 2) {
                setStatus('saving');

                const payload = { question_id: questionId, response: response, section_index: SECTION_IDX };

                // If offline, enqueue the save and show offline status
                if (!navigator.onLine) {
                    enqueueSave(payload);
                    setStatus('error');
                    setOffline(true);
                    return;
                }

                try {
                    const res = await fetch(SAVE_URL, {
                        method:  'POST',
                        headers: {
                            'Content-Type':  'application/json',
                            'Accept':        'application/json',
                            'X-CSRF-TOKEN':  CSRF_TOKEN,
                        },
                        body: JSON.stringify(payload),
                    });

                    if (!res.ok) throw new Error(`HTTP ${res.status}`);

                    const json = await res.json();

                    if (json.status === 'already_submitted') {
                        window.location.reload();
                        return;
                    }

                    setStatus('saved');
                } catch (err) {
                    // On failure, enqueue for retry and mark as error
                    enqueueSave(payload);
                    setStatus('error');
                    console.error('Auto-save failed and queued:', err);
                }
            }

            // --- Offline save queue helpers ---
            const QUEUE_KEY = `exam_autosave_queue_${EXAM_ID}_${SUBMISSION_ID}`;

            function enqueueSave(item) {
                try {
                    const raw = localStorage.getItem(QUEUE_KEY);
                    const arr = raw ? JSON.parse(raw) : [];
                    arr.push(item);
                    localStorage.setItem(QUEUE_KEY, JSON.stringify(arr));
                } catch (e) {
                    console.error('Failed to enqueue save', e);
                }
            }

            async function flushQueue() {
                try {
                    const raw = localStorage.getItem(QUEUE_KEY);
                    if (!raw) return;
                    const arr = JSON.parse(raw);
                    if (!Array.isArray(arr) || arr.length === 0) return;

                    for (const item of arr) {
                        try {
                            const res = await fetch(SAVE_URL, {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
                                body: JSON.stringify(item),
                            });
                            if (!res.ok) throw new Error(`HTTP ${res.status}`);
                        } catch (e) {
                            console.warn('Flushing queued save failed, will retry later', e);
                            return; // Stop processing to preserve order
                        }
                    }

                    // All flushed successfully
                    localStorage.removeItem(QUEUE_KEY);
                    setStatus('saved');
                    setOffline(false);
                } catch (e) {
                    console.error('Failed to flush queue', e);
                }
            }

            function setOffline(isOffline) {
                if (!offlineIndicator) return;
                offlineIndicator.style.display = isOffline ? 'inline-block' : 'none';
                offlineIndicator.textContent = isOffline ? 'Offline — responses queued' : '';
            }

            window.addEventListener('online', () => { setOffline(false); flushQueue(); });
            window.addEventListener('offline', () => { setOffline(true); });

            // Try to flush any queued saves on load if online
            if (navigator.onLine) {
                flushQueue();
            } else {
                setOffline(true);
            }

            // ── Debounce helper ─────────────────────────────────────────────────────
            const timers = {};
            function debouncedSave(questionId, response) {
                clearTimeout(timers[questionId]);
                timers[questionId] = setTimeout(() => saveResponse(questionId, response), DEBOUNCE_MS);
            }

            // ── Wire up all answer inputs ───────────────────────────────────────────
            document.addEventListener('DOMContentLoaded', function () {

                // Radio buttons (multiple choice, true/false)
                document.querySelectorAll('input[type="radio"][data-question-id]').forEach(function (radio) {
                    radio.addEventListener('change', function () {
                        if (this.checked) {
                            debouncedSave(this.dataset.questionId, this.value);
                        }
                    });
                });

                // Textareas (essay, short answer)
                document.querySelectorAll('textarea[data-question-id]').forEach(function (textarea) {
                    textarea.addEventListener('input', function () {
                        debouncedSave(this.dataset.questionId, this.value);
                    });
                });

                // Text inputs (short answer fallback)
                document.querySelectorAll('input[type="text"][data-question-id]').forEach(function (input) {
                    input.addEventListener('input', function () {
                        debouncedSave(this.dataset.questionId, this.value);
                    });
                });
            });
        })();
    </script>

    {{-- Add this section at the bottom of the file, before </x-layouts.exam> --}}
    <script>
    (function () {
        const HEARTBEAT_URL   = '{{ route('examination-hub.take.heartbeat', $exam) }}';
        const INIT_URL        = '{{ route('examination-hub.take.heartbeat.init', $exam) }}';
        const COMPLETED_URL   = '{{ route('examination-hub.take.completed', $exam) }}';
        const CSRF            = document.querySelector('meta[name="csrf-token"]')?.content;
        const INTERVAL_MS     = 15000;
        let sectionIndex      = {{ $sectionIndex }};
        let isFocused         = true;
        let questionsAnswered = 0;
        let heartbeatTimer    = null;

        async function post(url, body) {
            return fetch(url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
                body: JSON.stringify(body),
            });
        }

        async function initSession() {
            try {
                const ua = navigator.userAgent;
                let browser = 'Unknown', os = 'Unknown';
                if (ua.includes('Firefox'))     browser = 'Firefox';
                else if (ua.includes('Chrome')) browser = 'Chrome';
                else if (ua.includes('Safari')) browser = 'Safari';
                else if (ua.includes('Edge'))   browser = 'Edge';
                if (ua.includes('Windows'))     os = 'Windows';
                else if (ua.includes('Mac'))    os = 'macOS';
                else if (ua.includes('Linux'))  os = 'Linux';
                else if (ua.includes('Android')) os = 'Android';

                await post(INIT_URL, { browser, os, screen_width: screen.width, screen_height: screen.height });
            } catch (e) {
                console.error('Heartbeat init failed:', e);
            }
        }

        function showAdminNotification(type, message) {
            window.dispatchEvent(new CustomEvent('admin-notification', { detail: { type, message } }));
        }

        async function sendHeartbeat() {
            try {
                const answeredCount = document.querySelectorAll(
                    'input[type="radio"]:checked, textarea:not([value=""]), input[type="text"]'
                ).length;
                questionsAnswered = answeredCount;

                const res  = await post(HEARTBEAT_URL, {
                    is_focused:             isFocused,
                    current_question_index: sectionIndex,
                    current_section_index:  sectionIndex,
                    questions_answered:     questionsAnswered,
                });
                const data = await res.json();

                if (data.status === 'terminated') {
                    clearInterval(heartbeatTimer);
                    alert(data.message || 'Your session has been terminated.');
                    window.location.href = data.redirect || COMPLETED_URL;
                } else if (data.warning) {
                    showAdminNotification('warning', data.warning.message);
                } else if (data.admin_message) {
                    showAdminNotification('message', data.admin_message);
                }
            } catch (e) {
                console.error('Heartbeat failed:', e);
            }
        }

        window.addEventListener('focus', () => { isFocused = true; });
        window.addEventListener('blur',  () => { isFocused = false; });
        document.addEventListener('visibilitychange', () => {
            isFocused = document.visibilityState === 'visible';
        });
        window.addEventListener('beforeunload', () => {
            window._examProctor?.reportPageLeave('exam_exit');
        });

        initSession().then(() => {
            sendHeartbeat();
            heartbeatTimer = setInterval(sendHeartbeat, INTERVAL_MS);
        });
    })();

    function examTimer(startedAtTimestamp, durationMinutes) {
        return {
            startedAt: startedAtTimestamp,
            duration: durationMinutes,
            elapsed: 0,
            remaining: null,
            display: '--:--',
            ticker: null,
            get isCountdown() { return this.duration !== null && this.duration > 0; },
            get isWarning()   { return this.isCountdown && this.remaining !== null && this.remaining <= 300; },
            get timerStyle() {
                if (this.isCountdown) {
                    return this.isWarning
                        ? 'background:rgba(239,68,68,0.12);border:1px solid rgba(239,68,68,0.25);'
                        : 'background:rgba(16,185,129,0.1);border:1px solid rgba(16,185,129,0.2);';
                }
                return 'background:rgba(99,102,241,0.1);border:1px solid rgba(99,102,241,0.2);';
            },
            get timerIconClass() {
                return this.isCountdown ? (this.isWarning ? 'text-red-400' : 'text-emerald-400') : 'text-indigo-400';
            },
            get timerTextClass() {
                return this.isCountdown ? (this.isWarning ? 'text-red-400' : 'text-emerald-400') : 'text-indigo-400';
            },
            format(s) {
                const t = Math.max(0, Math.round(s));
                const h = Math.floor(t / 3600), m = Math.floor((t % 3600) / 60), sec = t % 60;
                return h > 0
                    ? `${String(h).padStart(2,'0')}:${String(m).padStart(2,'0')}:${String(sec).padStart(2,'0')}`
                    : `${String(m).padStart(2,'0')}:${String(sec).padStart(2,'0')}`;
            },
            tick() {
                const now = Math.floor(Date.now() / 1000);
                this.elapsed = this.startedAt ? now - this.startedAt : 0;
                if (this.isCountdown) {
                    if (!this.startedAt) {
                        // Not started: show full duration but don't auto-submit
                        this.remaining = this.duration * 60;
                        this.display = this.format(this.remaining);
                    } else {
                        this.remaining = Math.max(0, this.startedAt + this.duration * 60 - now);
                        this.display   = this.format(this.remaining);
                        if (this.remaining <= 0) { clearInterval(this.ticker); document.getElementById('exam-submit-form')?.submit(); }
                    }
                } else {
                    this.display = this.format(this.elapsed);
                }
            },
            init() { this.tick(); this.ticker = setInterval(() => this.tick(), 1000); },
        };
    }
    </script>

    {{-- Fullscreen gate (proctored exams only) --}}
    @if($proctoringEnabled ?? false)
    <div id="fullscreen-gate"
         style="display:flex; position:fixed; inset:0; z-index:9999; background:#0f172a; flex-direction:column; align-items:center; justify-content:center; padding:2rem;">
        <div style="max-width:28rem; width:100%; background:#1e293b; border-radius:4px; overflow:hidden; box-shadow:0 25px 50px -12px rgba(0,0,0,0.6);">
            <div style="height:4px; background:linear-gradient(90deg,#f59e0b,#fbbf24);"></div>
            <div style="padding:2rem;">
                <div style="display:flex; align-items:flex-start; gap:1rem; margin-bottom:1.5rem;">
                    <div style="flex-shrink:0; width:3rem; height:3rem; background:rgba(245,158,11,0.15); border-radius:50%; display:flex; align-items:center; justify-content:center;">
                        <svg style="width:1.5rem;height:1.5rem;color:#f59e0b;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
                    </div>
                    <div>
                        <p style="font-weight:700; color:#f1f5f9; font-size:1.125rem; margin:0 0 0.5rem;">Fullscreen Required</p>
                        <p style="color:#94a3b8; font-size:0.875rem; line-height:1.6; margin:0;">This exam must be taken in fullscreen mode. Exiting fullscreen is recorded as a violation. Click the button below to continue.</p>
                    </div>
                </div>
                <button id="fullscreen-resume-btn"
                        style="width:100%; padding:0.75rem 1.5rem; background:linear-gradient(135deg,#b45309,#d97706); color:#fff; font-weight:600; font-size:0.875rem; border:none; border-radius:2px; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:0.5rem;">
                    <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
                    Re-enter Fullscreen & Continue
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- Admin notification overlay --}}
    <div x-data="{
            show: false,
            type: 'message',
            text: '',
            acknowledgeUrl: '{{ route('examination-hub.take.heartbeat.acknowledge-warning', $exam) }}',
            csrfToken: '{{ csrf_token() }}',
            init() {
                window.addEventListener('admin-notification', (e) => {
                    this.type = e.detail.type;
                    this.text = e.detail.message;
                    this.show = true;
                });
            },
            async dismiss() {
                if (this.type === 'warning') {
                    await fetch(this.acknowledgeUrl, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': this.csrfToken, 'Content-Type': 'application/json' },
                    });
                }
                this.show = false;
                this.text = '';
            },
         }"
         x-show="show"
         x-transition
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
         style="display:none;">
        <div class="w-full max-w-md overflow-hidden"
             :class="type === 'warning' ? 'bg-amber-50 dark:bg-amber-950' : 'bg-white dark:bg-slate-900'"
             style="border-radius:2px;box-shadow:0 25px 50px -12px rgba(0,0,0,0.4);">
            <div class="h-1 w-full"
                 :style="type==='warning' ? 'background:linear-gradient(90deg,#f59e0b,#fbbf24)' : 'background:linear-gradient(90deg,#6366f1,#818cf8)'"></div>
            <div class="px-6 py-5">
                <p class="font-bold text-slate-900 dark:text-white mb-2"
                   x-text="type === 'warning' ? 'Warning from Invigilator' : 'Message from Invigilator'"></p>
                <p class="text-sm text-slate-700 dark:text-slate-300" x-text="text"></p>
                <div class="mt-5 flex justify-end">
                    <button @click="dismiss()"
                            class="px-5 py-2 text-sm font-semibold text-white"
                            :style="type==='warning' ? 'background:#d97706;border-radius:2px;' : 'background:#6366f1;border-radius:2px;'">
                        Acknowledge
                    </button>
                </div>
            </div>
        </div>
    </div>

</x-layouts.exam>
