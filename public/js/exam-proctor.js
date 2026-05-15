/**
 * exam-proctor.js
 *
 * Initialised on the exam-taking pages when `exam.is_proctored` is true.
 * Communicates violations back to ProctoringController@storeEvent via fetch.
 *
 * Usage (injected by the section/start Blade views):
 *   <script>
 *     window.ExamProctorConfig = {
 *       eventUrl:       '/examinations-hub/take/{exam}/proctor/event',
 *       csrfToken:      '{{ csrf_token() }}',
 *       hardenedMode:   {{ $exam->hardened_mode ? 'true' : 'false' }},
 *       requireFullscreen: {{ $exam->require_fullscreen ? 'true' : 'false' }},
 *       autoSubmitUrl:  '{{ route("examinations-hub.take.submit", $exam) }}',
 *     };
 *   <\/script>
 *   <script src="{{ asset('js/exam-proctor.js') }}"><\/script>
 */

(function () {
    'use strict';

    const config = window.ExamProctorConfig || {};
    const MAX_RETRIES = 2;

    // ── Utility ──────────────────────────────────────────────────────────────

    async function sendEvent(eventType, eventData = {}) {
        if (!config.eventUrl) return;

        const payload = { event_type: eventType, event_data: eventData };

        for (let attempt = 0; attempt <= MAX_RETRIES; attempt++) {
            try {
                const res = await fetch(config.eventUrl, {
                    method:  'POST',
                    headers: {
                        'Content-Type':  'application/json',
                        'Accept':        'application/json',
                        'X-CSRF-TOKEN':  config.csrfToken || document.querySelector('meta[name="csrf-token"]')?.content,
                    },
                    body: JSON.stringify(payload),
                });

                if (!res.ok) break;

                const json = await res.json();

                // Server says this submission must be auto-submitted
                if (json.should_auto_submit && config.autoSubmitUrl) {
                    triggerAutoSubmit('violation_threshold_reached');
                }

                break;
            } catch (_) {
                // Network error – silently retry
            }
        }
    }

    function triggerAutoSubmit(reason) {
        // Prevent duplicate submissions
        if (window.__proctoringAutoSubmitted) return;
        window.__proctoringAutoSubmitted = true;

        showOverlay(`Your exam is being submitted due to a proctoring violation (${reason}).`);

        setTimeout(() => {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = config.autoSubmitUrl;
            const csrf  = document.createElement('input');
            csrf.type   = 'hidden';
            csrf.name   = '_token';
            csrf.value  = config.csrfToken || document.querySelector('meta[name="csrf-token"]')?.content;
            form.appendChild(csrf);
            document.body.appendChild(form);
            form.submit();
        }, 2500);
    }

    function showOverlay(message) {
        const el = document.createElement('div');
        el.id    = 'proctor-overlay';
        el.style.cssText = [
            'position:fixed', 'inset:0', 'z-index:99999',
            'background:rgba(0,0,0,0.85)', 'display:flex',
            'align-items:center', 'justify-content:center',
        ].join(';');
        el.innerHTML = `
            <div style="background:#fff;border-radius:12px;padding:32px;max-width:420px;text-align:center">
                <svg style="width:48px;height:48px;color:#ef4444;margin:0 auto 16px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                </svg>
                <p style="font-weight:600;font-size:1.1rem;color:#111;margin-bottom:8px">Proctoring Violation</p>
                <p style="color:#555;font-size:.9rem">${message}</p>
            </div>`;
        document.body.appendChild(el);
    }

    // ── Lockdown helpers ─────────────────────────────────────────────────────

    let tabSwitchCount = 0;

    function onVisibilityChange() {
        if (document.hidden) {
            tabSwitchCount++;
            sendEvent('tab_switch', { count: tabSwitchCount, timestamp: Date.now() });
        }
    }

    function onWindowBlur() {
        sendEvent('window_blur', { timestamp: Date.now() });
    }

    function onCopy(e) {
        if (config.hardenedMode) e.preventDefault();
        sendEvent('copy_attempt', { timestamp: Date.now() });
    }

    function onPaste(e) {
        if (config.hardenedMode) e.preventDefault();
        sendEvent('paste_attempt', { timestamp: Date.now() });
    }

    function onContextMenu(e) {
        if (config.hardenedMode) e.preventDefault();
        sendEvent('right_click', { timestamp: Date.now() });
    }

    function onKeyDown(e) {
        const blocked = [
            // DevTools
            { key: 'F12' },
            { ctrlKey: true, shiftKey: true, key: 'I' },
            { ctrlKey: true, shiftKey: true, key: 'J' },
            { ctrlKey: true, key: 'U' },
            // Print
            { ctrlKey: true, key: 'P' },
            // Screenshot shortcuts
            { key: 'PrintScreen' },
        ];

        const match = blocked.some(combo =>
            (combo.key      ? e.key      === combo.key      : true) &&
            (combo.ctrlKey  ? e.ctrlKey  === true           : !combo.ctrlKey  || !e.ctrlKey) &&
            (combo.shiftKey ? e.shiftKey === true           : !combo.shiftKey || !e.shiftKey)
        );

        if (match) {
            e.preventDefault();
            sendEvent('keyboard_shortcut', { key: e.key, ctrlKey: e.ctrlKey, shiftKey: e.shiftKey });
        }
    }

    // ── Fullscreen ───────────────────────────────────────────────────────────

    function requestFullscreen() {
        const el = document.documentElement;
        if (el.requestFullscreen)           el.requestFullscreen();
        else if (el.webkitRequestFullscreen) el.webkitRequestFullscreen();
        else if (el.mozRequestFullScreen)    el.mozRequestFullScreen();
    }

    function onFullscreenChange() {
        if (!document.fullscreenElement && !document.webkitFullscreenElement) {
            sendEvent('fullscreen_exit', { timestamp: Date.now() });

            // Prompt user to re-enter fullscreen
            if (config.requireFullscreen) {
                const banner = document.getElementById('fullscreen-banner');
                if (banner) banner.classList.remove('hidden');
            }
        } else {
            const banner = document.getElementById('fullscreen-banner');
            if (banner) banner.classList.add('hidden');
        }
    }

    // ── Initialise ───────────────────────────────────────────────────────────

    document.addEventListener('DOMContentLoaded', function () {
        document.addEventListener('visibilitychange', onVisibilityChange);
        window.addEventListener('blur', onWindowBlur);
        document.addEventListener('copy', onCopy);
        document.addEventListener('paste', onPaste);
        document.addEventListener('contextmenu', onContextMenu);
        document.addEventListener('keydown', onKeyDown);
        document.addEventListener('fullscreenchange', onFullscreenChange);
        document.addEventListener('webkitfullscreenchange', onFullscreenChange);

        if (config.requireFullscreen) {
            requestFullscreen();
        }
    });

    // Expose for manual integration (e.g. webcam face-detection callbacks)
    window.ExamProctor = { sendEvent, requestFullscreen };
})();
