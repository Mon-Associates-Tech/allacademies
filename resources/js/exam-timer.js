// Exam Timer Alpine.js Component
//
// Parameters (all passed from section.blade.php):
//   sectionStartTs          — ISO-8601 string of when the exam/section started
//                             (used as fallback if serverRemainingSeconds is absent)
//   timerDuration           — base duration in MINUTES (used as fallback only)
//   serverRemainingSeconds  — authoritative remaining time in SECONDS from the
//                             server at page-load time, already including any
//                             extra_time_minutes. Pass null when there is no limit.
//
// Globals exposed so other scripts can drive the timer:
//   window.examTimerSync(remainingSeconds)  — called by heartbeat on every poll
//   window.examTimerExtend(minutes)         — called by Echo real-time push

document.addEventListener('alpine:init', () => {
    Alpine.data('examTimer', (sectionStartTs, timerDuration, serverRemainingSeconds = null) => ({
        remaining: 0,
        lastKnownExtraMinutes: 0,
        pendingExtensionMinutes: 0,
        pendingExtensionTimer: null,
        timerStyle: {
            backgroundColor: 'rgb(248 250 252)', // slate-50
            borderColor: 'rgb(226 232 240)',      // slate-200
            borderWidth: '1px',
            borderStyle: 'solid',
            borderRadius: '2px',
        },
        timerIconClass: 'text-slate-500 dark:text-slate-400',
        timerTextClass: 'text-slate-800 dark:text-slate-300',
        display: '00:00',
        timerInterval: null,

        init() {
            const hasLimit = (serverRemainingSeconds !== null) || (timerDuration !== null);

            if (!hasLimit) {
                this.display = '--:--';
                return;
            }

            // ── Initialise remaining time ─────────────────────────────────────
            //
            // Priority order:
            //  1. serverRemainingSeconds  — PHP already called getRemainingTime()
            //     which includes extra_time_minutes; most accurate.
            //  2. Local calculation from sectionStartTs + timerDuration — fallback
            //     when the server value is not yet available (e.g. section.blade.php
            //     hasn't been updated to pass the third argument yet).
            if (serverRemainingSeconds !== null) {
                this.remaining = Math.max(0, Math.round(serverRemainingSeconds));
            } else if (sectionStartTs && timerDuration !== null) {
                const startTime = new Date(sectionStartTs).getTime();
                const elapsed   = Math.floor((Date.now() - startTime) / 1000);
                this.remaining  = Math.max(0, (timerDuration * 60) - elapsed);
            } else if (timerDuration !== null) {
                this.remaining = timerDuration * 60;
            }

            this.updateDisplay();
            this.updateStyles();

            // ── Expose globals for heartbeat / Echo ───────────────────────────
            //
            // window.examTimerSync(remainingSeconds)
            //   Called by ExamHeartbeat.defaultTimeSyncHandler on every poll.
            //   Accepts the server-authoritative remaining seconds and applies it
            //   only when the difference is meaningful (avoids timer jitter from
            //   small network latency differences).
            //
            // window.examTimerExtend(minutes)
            //   Called by the Echo extend_time handler when no remaining_seconds
            //   value is available in the push payload.
            window.examTimerSync   = (secs, extra) => this.syncFromServer(secs, extra);
            window.examTimerExtend = (mins) => this.extendByMinutes(mins);

            // Listen for document sync/extend events so heartbeat and Echo can drive the timer
            this._onSyncEvent   = (e) => this.syncFromServer(e.detail?.remaining, e.detail?.extraMinutes);
            this._onExtendEvent = (e) => this.extendByMinutes(e.detail?.minutes);
            document.addEventListener('exam:sync-time', this._onSyncEvent);
            document.addEventListener('exam:extend-time', this._onExtendEvent);

            // ── Start the countdown ───────────────────────────────────────────
            this.timerInterval = setInterval(() => {
                if (this.remaining > 0) {
                    this.remaining--;
                    this.updateDisplay();
                    this.updateStyles();
                } else {
                    this.display = 'EXPIRED';
                    this.timerTextClass = 'text-red-600 dark:text-red-400 font-bold animate-pulse';
                    clearInterval(this.timerInterval);
                    this.timerInterval = null;
                    this.triggerExpiry();
                }
            }, 1000);
        },

        // ── Server sync (called by heartbeat polling) ─────────────────────────
        //
        // Accepts the server's authoritative remaining seconds and applies them
        // as the single source of truth. Small client-side jitter will be ignored
        // because the server value is authoritative.
        syncFromServer(serverRemaining, serverExtraMinutes = 0) {
            if (serverRemaining === null || serverRemaining === undefined) return;

            serverRemaining = Math.max(0, Math.round(serverRemaining));
            serverExtraMinutes = serverExtraMinutes ?? 0;

            const prev = this.remaining;
            const diff = serverRemaining - prev; // positive = server has more

            // Authoritative server update: always apply server's remaining time
            this.remaining = serverRemaining;
            this.updateDisplay();
            this.updateStyles();

            if (serverExtraMinutes > this.lastKnownExtraMinutes) {
                // Genuine extension via admin
                const addedMinutes = serverExtraMinutes - this.lastKnownExtraMinutes;
                this.lastKnownExtraMinutes = serverExtraMinutes;
                this.showTimeExtendedBanner(addedMinutes);
            } else if (diff > 5) {
                // Server reports more time (but extraMinutes didn't change) — show banner
                const gainedMinutes = Math.ceil(diff / 60);
                this.showTimeExtendedBanner(gainedMinutes);
            }

            // Re-arm the interval in case the timer had already expired
            if (!this.timerInterval && this.remaining > 0) {
                window._timerExpiredCalled = false;
                this.timerInterval = setInterval(() => {
                    if (this.remaining > 0) {
                        this.remaining--;
                        this.updateDisplay();
                        this.updateStyles();
                    } else {
                        this.display = 'EXPIRED';
                        this.timerTextClass = 'text-red-600 dark:text-red-400 font-bold animate-pulse';
                        clearInterval(this.timerInterval);
                        this.timerInterval = null;
                        this.triggerExpiry();
                    }
                }, 1000);
            }
        },


        // ── Direct extension (called by Echo real-time push) ──────────────────
        extendByMinutes(minutes) {
            if (!minutes || minutes <= 0) return;

            // Debounce to avoid double-applying when a heartbeat sync arrives
            this.pendingExtensionMinutes = minutes;
            if (this.pendingExtensionTimer) clearTimeout(this.pendingExtensionTimer);
            this.pendingExtensionTimer = setTimeout(() => {
                this.remaining += Math.round(this.pendingExtensionMinutes * 60);
                this.lastKnownExtraMinutes += this.pendingExtensionMinutes;
                this.updateDisplay();
                this.updateStyles();
                this.showTimeExtendedBanner(this.pendingExtensionMinutes);

                // Re-arm if the timer had already fired
                if (!this.timerInterval && this.remaining > 0) {
                    window._timerExpiredCalled = false;
                    this.timerInterval = setInterval(() => {
                        if (this.remaining > 0) {
                            this.remaining--;
                            this.updateDisplay();
                            this.updateStyles();
                        } else {
                            this.display = 'EXPIRED';
                            clearInterval(this.timerInterval);
                            this.timerInterval = null;
                            this.triggerExpiry();
                        }
                    }, 1000);
                }

                this.pendingExtensionMinutes = 0;
                this.pendingExtensionTimer = null;
            }, 700);
        },


        // ── Notification banner shown to the candidate ────────────────────────
        showTimeExtendedBanner(minutes) {
            document.getElementById('exam-time-extended-banner')?.remove();

            const banner = document.createElement('div');
            banner.id = 'exam-time-extended-banner';
            banner.style.cssText = [
                'position:fixed', 'top:1rem', 'left:50%', 'transform:translateX(-50%)',
                'z-index:200', 'display:flex', 'align-items:center', 'gap:0.75rem',
                'padding:0.75rem 1.25rem',
                'background:linear-gradient(135deg,#059669,#34d399)',
                'color:#fff', 'font-size:0.875rem', 'font-weight:600',
                'border-radius:2px',
                'box-shadow:0 4px 20px rgba(5,150,105,0.45)',
                'pointer-events:none',
            ].join(';');

            banner.innerHTML = `
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>+${minutes} minute${minutes !== 1 ? 's' : ''} added to your time</span>
            `;

            document.body.appendChild(banner);

            // Fade out after 8 seconds
            setTimeout(() => {
                banner.style.transition = 'opacity 0.5s';
                banner.style.opacity = '0';
                setTimeout(() => banner.remove(), 500);
            }, 8000);
        },

        // ── Trigger Livewire auto-submit when time hits zero ──────────────────
        triggerExpiry() {
            if (window._timerExpiredCalled) return;
            window._timerExpiredCalled = true;

            const wireEl = document.querySelector('[wire:id]');
            if (wireEl) {
                const wire = Livewire.find(wireEl.getAttribute('wire:id'));
                if (wire) wire.call('handleTimerExpired');
            }
        },

        // ── Display & style helpers ───────────────────────────────────────────
        updateDisplay() {
            if (this.remaining < 0) return;
            const h = Math.floor(this.remaining / 3600);
            const m = Math.floor((this.remaining % 3600) / 60);
            const s = this.remaining % 60;

            this.display = h > 0
                ? `${String(h).padStart(2,'0')}:${String(m).padStart(2,'0')}:${String(s).padStart(2,'0')}`
                : `${String(m).padStart(2,'0')}:${String(s).padStart(2,'0')}`;
        },

        updateStyles() {
            if (this.remaining <= 60) {         // last minute — red pulse
                this.timerTextClass = 'text-red-600 dark:text-red-400 font-bold animate-pulse';
                this.timerStyle.backgroundColor = 'rgb(254 242 242)';  // red-50
                this.timerStyle.borderColor     = 'rgb(252 165 165)';  // red-300
            } else if (this.remaining <= 300) { // last 5 minutes — amber
                this.timerTextClass = 'text-amber-600 dark:text-amber-400 font-bold';
                this.timerStyle.backgroundColor = 'rgb(255 251 235)';  // amber-50
                this.timerStyle.borderColor     = 'rgb(252 211 77)';   // amber-300
            } else {                            // normal
                this.timerTextClass = 'text-slate-800 dark:text-slate-300';
                this.timerStyle.backgroundColor = 'rgb(248 250 252)';  // slate-50
                this.timerStyle.borderColor     = 'rgb(226 232 240)';  // slate-200
            }
        },

        // ── Cleanup ───────────────────────────────────────────────────────────
        destroy() {
            if (this.timerInterval) clearInterval(this.timerInterval);
            if (this._onSyncEvent)   document.removeEventListener('exam:sync-time',   this._onSyncEvent);
            if (this._onExtendEvent) document.removeEventListener('exam:extend-time', this._onExtendEvent);
            // Remove globals so they don't linger if the component is re-mounted
            window.examTimerSync   = null;
            window.examTimerExtend = null;
            document.getElementById('exam-time-extended-banner')?.remove();
        },

    }));
});