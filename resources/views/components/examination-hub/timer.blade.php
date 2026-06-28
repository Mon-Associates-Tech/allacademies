@props([
    'timeRemaining' => 0,
    'isMobile' => false,
])

@php
    $seconds = (int) $timeRemaining;
    $alertId = $isMobile ? 'time-alert-mobile' : 'time-alert';
@endphp

<div
    x-data="examCountdown({{ $seconds }})"
    id="{{ $alertId }}"
    class="flex items-center gap-2 px-3 py-1.5 transition-all duration-300 border"
    style="border-radius: 2px;"
    :class="{
        'bg-red-600 text-white animate-pulse border-red-600':                                                  state === 'expired',
        'bg-red-100 dark:bg-red-900/30 border-red-300 dark:border-red-700 text-red-700 dark:text-red-300':   state === 'critical',
        'bg-amber-100 dark:bg-amber-900/30 border-amber-300 dark:border-amber-700 text-amber-700 dark:text-amber-300': state === 'warning',
        'bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700':                               state === 'normal'
    }"
>
    <svg class="w-4 h-4 flex-shrink-0 transition-colors"
         :class="state === 'expired' ? 'text-white' : 'text-slate-500 dark:text-slate-400'"
         fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    <span class="{{ $isMobile ? 'text-xs' : 'text-sm' }} font-bold tabular-nums"
          :class="state === 'expired' ? 'text-white' : 'text-slate-800 dark:text-slate-200'"
          x-text="display">--:--:--</span>
</div>

@script
<script>
Alpine.data('examCountdown', (initialSeconds) => ({
    remaining:     Math.max(0, Math.round(initialSeconds)),
    state:         initialSeconds <= 0   ? 'expired'
                 : initialSeconds <= 300 ? 'critical'
                 : initialSeconds <= 600 ? 'warning'
                 : 'normal',
    display:       '--:--:--',
    interval:      null,
    expiredCalled: false,

    init() {
        this.updateDisplay();

        if (this.remaining > 0) {
            this.interval = setInterval(() => this.tick(), 1000);
        } else {
            this.triggerExpire();
        }

        // ── Expose globals so the heartbeat and Echo handler can reach us ──
        //
        // window.examTimerSync(remainingSeconds)
        //   Called by ExamHeartbeat.defaultTimeSyncHandler on every heartbeat
        //   response that carries remaining_seconds.  Corrects clock drift and
        //   picks up admin-granted extensions within one polling cycle (~15 s).
        //
        // window.examTimerExtend(minutes)
        //   Called by the Echo extend_time handler for instant (real-time)
        //   extension delivery when Laravel Echo / WebSockets is available.
        //
        // Both are set here so they always point to the live Alpine instance.
        // If Livewire re-mounts this component, init() runs again and the
        // globals are updated to the new instance automatically.
        window.examTimerSync   = (secs) => this.syncFromServer(secs);
        window.examTimerExtend = (mins) => this.extendByMinutes(mins);
    },

    // ── Countdown tick ────────────────────────────────────────────────────────
    tick() {
        if (this.remaining <= 0) {
            this.remaining = 0;
            this.state     = 'expired';
            clearInterval(this.interval);
            this.interval  = null;
            this.triggerExpire();
            return;
        }
        this.remaining--;
        this.updateDisplay();
        this.updateState();
    },

    // ── Server sync (heartbeat polling path) ──────────────────────────────────
    //
    // Called every ~15 s when the heartbeat response includes remaining_seconds.
    // We apply the server value only when the difference is meaningful:
    //
    //  diff > +5 s  → server has MORE time than client = extension was granted
    //  diff < -30 s → server has significantly LESS = server-side correction
    //
    // Small differences (−30 s to +5 s) are normal network/processing jitter
    // and are intentionally ignored to prevent the display from jumping around.
    syncFromServer(serverRemaining) {
        if (serverRemaining === null || serverRemaining === undefined) return;

        serverRemaining     = Math.max(0, Math.round(serverRemaining));
        const diff          = serverRemaining - this.remaining; // +ve = server has more

        if (diff > 5) {
            // Extension: server has more time than the client countdown thinks
            const gainedMinutes = Math.ceil(diff / 60);
            this.remaining      = serverRemaining;
            this.updateDisplay();
            this.updateState();
            this.showExtensionBanner(gainedMinutes);
            this.rearmInterval();
        } else if (diff < -30) {
            // Server-side correction (e.g. resumed submission with different started_at)
            this.remaining = serverRemaining;
            this.updateDisplay();
            this.updateState();
        }
        // Ignore small differences — they are just heartbeat jitter
    },

    // ── Direct extension (Echo real-time path) ────────────────────────────────
    //
    // Called immediately when the admin grants extra time and Echo delivers the
    // event to the candidate's browser without waiting for the next heartbeat.
    extendByMinutes(minutes) {
        if (!minutes || minutes <= 0) return;

        this.remaining += Math.round(minutes * 60);
        this.updateDisplay();
        this.updateState();
        this.showExtensionBanner(minutes);
        this.rearmInterval();
    },

    // ── Re-arm the countdown interval ────────────────────────────────────────
    //
    // If the timer had already reached zero and fired triggerExpire(), the
    // interval is cleared.  An extension after expiry must restart it.
    rearmInterval() {
        if (!this.interval && this.remaining > 0) {
            this.expiredCalled = false; // allow triggerExpire() to fire again if needed
            this.state         = this.remaining <= 300 ? 'critical'
                               : this.remaining <= 600 ? 'warning'
                               : 'normal';
            this.interval = setInterval(() => this.tick(), 1000);
        }
    },

    // ── Banner shown to the candidate when time is extended ───────────────────
    showExtensionBanner(minutes) {
        document.getElementById('exam-time-extended-banner')?.remove();

        const banner       = document.createElement('div');
        banner.id          = 'exam-time-extended-banner';
        banner.style.cssText = [
            'position:fixed', 'top:1rem', 'left:50%', 'transform:translateX(-50%)',
            'z-index:200',
            'display:flex', 'align-items:center', 'gap:.75rem',
            'padding:.75rem 1.25rem',
            'background:linear-gradient(135deg,#059669,#34d399)',
            'color:#fff', 'font-size:.875rem', 'font-weight:600',
            'border-radius:2px',
            'box-shadow:0 4px 20px rgba(5,150,105,.45)',
            'pointer-events:none',
            'white-space:nowrap',
        ].join(';');

        banner.innerHTML = `
            <svg width="18" height="18" fill="none" viewBox="0 0 24 24"
                 stroke="currentColor" stroke-width="2" style="flex-shrink:0;">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>+${minutes} minute${minutes !== 1 ? 's' : ''} added to your time</span>
        `;

        document.body.appendChild(banner);

        // Fade out after 8 s, then remove from DOM
        setTimeout(() => {
            banner.style.transition = 'opacity .5s';
            banner.style.opacity    = '0';
            setTimeout(() => banner.remove(), 500);
        }, 8000);
    },

    // ── Trigger Livewire auto-submit when time reaches zero ───────────────────
    triggerExpire() {
        if (this.expiredCalled) return;
        this.expiredCalled = true;

        if (window.examSectionComponent) {
            try {
                window.examSectionComponent.call('handleTimerExpired');
            } catch (e) {
                console.warn('Could not trigger auto-submit:', e.message);
            }
        } else {
            console.warn('Livewire component reference not found for auto-submit.');
        }
    },

    // ── Display helpers ───────────────────────────────────────────────────────
    updateDisplay() {
        const h = Math.floor(this.remaining / 3600);
        const m = Math.floor((this.remaining % 3600) / 60);
        const s = this.remaining % 60;
        this.display = `${String(h).padStart(2,'0')}:${String(m).padStart(2,'0')}:${String(s).padStart(2,'0')}`;
    },

    updateState() {
        if (this.remaining <= 0)        this.state = 'expired';
        else if (this.remaining <= 300) this.state = 'critical';
        else if (this.remaining <= 600) this.state = 'warning';
        else                            this.state = 'normal';
    },

    // ── Cleanup ───────────────────────────────────────────────────────────────
    destroy() {
        if (this.interval) clearInterval(this.interval);

        // Only clear the globals if they still point to this instance.
        // (Protects against a race where a new instance has already registered.)
        if (window.examTimerSync   === ((secs) => this.syncFromServer(secs)))   window.examTimerSync   = null;
        if (window.examTimerExtend === ((mins) => this.extendByMinutes(mins))) window.examTimerExtend = null;

        document.getElementById('exam-time-extended-banner')?.remove();
    },
}));
</script>
@endscript
