@props([
    'timeRemaining'    => 0,
    'extraTimeMinutes' => 0,
    'isMobile'         => false,
])

@php
    $seconds  = (int) $timeRemaining;
    $alertId  = $isMobile ? 'time-alert-mobile' : 'time-alert';
@endphp

<div
    x-data="examCountdown({{ $seconds }}, {{ (int) $extraTimeMinutes }})"
    id="{{ $alertId }}"
    class="flex items-center gap-2 px-3 py-1.5 transition-all duration-300 border"
    style="border-radius: 2px;"
    :class="{
        'bg-red-600 text-white animate-pulse border-red-600':                                                          state === 'expired',
        'bg-red-100 dark:bg-red-900/30 border-red-300 dark:border-red-700 text-red-700 dark:text-red-300':           state === 'critical',
        'bg-amber-100 dark:bg-amber-900/30 border-amber-300 dark:border-amber-700 text-amber-700 dark:text-amber-300': state === 'warning',
        'bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700':                                       state === 'normal'
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
Alpine.data('examCountdown', (initialSeconds, initialExtraMinutes) => ({
    remaining:              Math.max(0, Math.round(initialSeconds)),
    lastKnownExtraMinutes:  initialExtraMinutes ?? 0,
    state:                  initialSeconds <= 0   ? 'expired'
                          : initialSeconds <= 300 ? 'critical'
                          : initialSeconds <= 600 ? 'warning'
                          : 'normal',
    display:       '--:--:--',
    interval:      null,
    expiredCalled: false,
    _onSyncEvent:  null,
    _onExtendEvent: null,

    init() {
        this.updateDisplay();

        // If there is no remaining time at init, show a placeholder rather
        // than immediately triggering expiry. The authoritative remaining
        // seconds will arrive via heartbeat (exam:sync-time) or Echo, and
        // rearmInterval() will start the ticking when appropriate.
        if (this.remaining > 0) {
            this.interval = setInterval(() => this.tick(), 1000);
        } else {
            this.display = '--:--:--';
        }

        // Use CustomEvent on document instead of window globals.
        // This fixes two bugs:
        //  1. Both desktop + mobile timer instances receive every event —
        //     no single instance "owns" a global and blocks the other.
        //  2. The heartbeat class stays decoupled from the timer implementation.
        this._onSyncEvent   = (e) => this.handleSyncEvent(e.detail);
        this._onExtendEvent = (e) => this.extendByMinutes(e.detail.minutes);
        document.addEventListener('exam:sync-time',   this._onSyncEvent);
        document.addEventListener('exam:extend-time', this._onExtendEvent);
    },

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

    // Heartbeat polling path — called every ~15 s.
    // Two cases:
    //   A) extra_time_minutes increased → genuine admin extension
    //      → update remaining, show banner for the added minutes
    //   B) extra_time_minutes unchanged → normal drift correction
    //      → silently snap remaining only when drift > 30 s; never show banner
    handleSyncEvent({ remaining: serverRemaining, extraMinutes: serverExtraMinutes }) {
        if (serverRemaining === null || serverRemaining === undefined) return;

        serverRemaining    = Math.max(0, Math.round(serverRemaining));
        serverExtraMinutes = serverExtraMinutes ?? 0;

        // If client has no local remaining value (e.g. initial load), accept
        // server's authoritative remaining so UI reflects current state.
        if (this.remaining === 0 && serverRemaining > 0) {
            this.remaining = serverRemaining;
            this.updateDisplay();
            this.updateState();
            this.rearmInterval();
            return;
        }

        if (serverExtraMinutes > this.lastKnownExtraMinutes) {
            // Case A — genuine extension
            const addedMinutes         = serverExtraMinutes - this.lastKnownExtraMinutes;
            this.lastKnownExtraMinutes = serverExtraMinutes;
            this.remaining             = serverRemaining;
            this.updateDisplay();
            this.updateState();
            this.showExtensionBanner(addedMinutes);
            this.rearmInterval();
        } else {
            // Case B — silent drift correction (no banner)
            // Only accept server corrections that reduce remaining time.
            if (serverRemaining < this.remaining - 30) {
                this.remaining = serverRemaining;
                this.updateDisplay();
                this.updateState();
                this.rearmInterval();
            }
        }
    },

    // Echo real-time path — called immediately on admin push.
    extendByMinutes(minutes) {
        if (!minutes || minutes <= 0) return;
        this.remaining            += Math.round(minutes * 60);
        // Advance lastKnownExtraMinutes so the next heartbeat sync
        // does not double-count the same extension via Case A above.
        this.lastKnownExtraMinutes += minutes;
        this.updateDisplay();
        this.updateState();
        this.showExtensionBanner(minutes);
        this.rearmInterval();
    },

    // Re-arm the interval if the timer had already expired.
    rearmInterval() {
        if (!this.interval && this.remaining > 0) {
            this.expiredCalled = false;
            this.state = this.remaining <= 300 ? 'critical'
                       : this.remaining <= 600 ? 'warning'
                       : 'normal';
            this.interval = setInterval(() => this.tick(), 1000);
        }
    },

    // Green banner shown once per genuine extension.
    // The id guard means only one banner is visible even with two timer instances.
    showExtensionBanner(minutes) {
        document.getElementById('exam-time-extended-banner')?.remove();

        const banner = document.createElement('div');
        banner.id    = 'exam-time-extended-banner';
        banner.style.cssText = [
            'position:fixed','top:1rem','left:50%','transform:translateX(-50%)',
            'z-index:200','display:flex','align-items:center','gap:.75rem',
            'padding:.75rem 1.25rem',
            'background:linear-gradient(135deg,#059669,#34d399)',
            'color:#fff','font-size:.875rem','font-weight:600',
            'border-radius:2px',
            'box-shadow:0 4px 20px rgba(5,150,105,.45)',
            'pointer-events:none','white-space:nowrap',
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

        setTimeout(() => {
            banner.style.transition = 'opacity .5s';
            banner.style.opacity    = '0';
            setTimeout(() => banner.remove(), 500);
        }, 8000);
    },

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

    updateDisplay() {
        const h = Math.floor(this.remaining / 3600);
        const m = Math.floor((this.remaining % 3600) / 60);
        const s = this.remaining % 60;
        this.display = `${String(h).padStart(2,'0')}:${String(m).padStart(2,'0')}:${String(s).padStart(2,'0')}`;
    },

    updateState() {
        if      (this.remaining <= 0)   this.state = 'expired';
        else if (this.remaining <= 300) this.state = 'critical';
        else if (this.remaining <= 600) this.state = 'warning';
        else                            this.state = 'normal';
    },

    destroy() {
        if (this.interval)      clearInterval(this.interval);
        if (this._onSyncEvent)   document.removeEventListener('exam:sync-time',   this._onSyncEvent);
        if (this._onExtendEvent) document.removeEventListener('exam:extend-time', this._onExtendEvent);
        document.getElementById('exam-time-extended-banner')?.remove();
    },
}));
</script>
@endscript
