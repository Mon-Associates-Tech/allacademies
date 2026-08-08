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
    class="flex items-center gap-2 px-3 py-1.5 rounded-lg transition-all duration-300 border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800"
    :class="{
        'bg-red-600 text-white animate-pulse border-red-600': state === 'expired',
        'bg-red-100 dark:bg-red-900/30 border-red-300 dark:border-red-700 text-red-700 dark:text-red-300': state === 'critical',
        'bg-amber-100 dark:bg-amber-900/30 border-amber-300 dark:border-amber-700 text-amber-700 dark:text-amber-300': state === 'warning'
    }"
>
    <svg class="w-4 h-4 transition-colors" :class="state === 'expired' ? 'text-white' : 'text-slate-500 dark:text-slate-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
    </svg>
    <span class="{{ $isMobile ? 'text-xs' : 'text-sm' }} font-bold tabular-nums" :class="state === 'expired' ? 'text-white' : 'text-slate-800 dark:text-slate-200'" x-text="display">--:--:--</span>
</div>

@script
<script>
Alpine.data('examCountdown', (initialSeconds) => ({
    remaining: initialSeconds,
    state: initialSeconds <= 0 ? 'expired' : (initialSeconds <= 300 ? 'critical' : (initialSeconds <= 600 ? 'warning' : 'normal')),
    display: '--:--:--',
    interval: null,
    expiredCalled: false,

    init() {
        this.updateDisplay();
        if (this.remaining > 0) {
            this.interval = setInterval(() => this.tick(), 1000);
        } else {
            this.triggerExpire();
        }
    },

    tick() {
        if (this.remaining <= 0) {
            this.remaining = 0;
            this.state = 'expired';
            clearInterval(this.interval);
            this.triggerExpire();
            return;
        }
        this.remaining--;
        this.updateDisplay();
        this.updateState();
    },

    updateDisplay() {
        const h = Math.floor(this.remaining / 3600);
        const m = Math.floor((this.remaining % 3600) / 60);
        const s = this.remaining % 60;
        this.display = `${String(h).padStart(2, '0')}:${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`;
    },

    updateState() {
        if (this.remaining <= 300) {
            this.state = 'critical';
        } else if (this.remaining <= 600) {
            this.state = 'warning';
        }
    },

triggerExpire() {
    if (!this.expiredCalled) {
        this.expiredCalled = true;
        
        // ✅ FIXED: Only use the global reference. Removed dangerous Livewire.all() fallback.
        if (window.examSectionComponent) {
            try {
                window.examSectionComponent.call('handleTimerExpired');
            } catch (e) {
                console.warn('Could not trigger auto-submit (component may have been destroyed):', e.message);
            }
        } else {
            console.warn('Livewire component reference not found for auto-submit.');
        }
    }
},

    destroy() {
        if (this.interval) clearInterval(this.interval);
    }
}));
</script>
@endscript