// Exam Timer Alpine.js Component
document.addEventListener('alpine:init', () => {
    Alpine.data('examTimer', (sectionStartTs, timerDuration) => ({
        remaining: 0,
        timerStyle: {
            backgroundColor: 'rgb(248 250 252)', // slate-50
            borderColor: 'rgb(226 232 240)',    // slate-200
            borderWidth: '1px',
            borderStyle: 'solid',
            borderRadius: '0.375rem',           // rounded
        },
        timerIconClass: 'text-slate-500 dark:text-slate-400',
        timerTextClass: 'text-slate-800 dark:text-slate-300',
        display: '00:00',
        timerInterval: null,

        init() {
            if (timerDuration !== null) {
                // Calculate remaining time based on start timestamp and duration
                if (sectionStartTs) {
                    const startTime = new Date(sectionStartTs).getTime();
                    const now = Date.now();
                    const elapsed = Math.floor((now - startTime) / 1000); // in seconds
                    this.remaining = Math.max(0, (timerDuration * 60) - elapsed); // convert minutes to seconds
                } else {
                    this.remaining = timerDuration * 60; // convert minutes to seconds
                }

                // Start the timer
                this.updateDisplay();
                this.timerInterval = setInterval(() => {
                    if (this.remaining > 0) {
                        this.remaining--;
                        this.updateDisplay();

                        // Update styles based on remaining time
                        if (this.remaining <= 60) { // 1 minute
                            this.timerTextClass = 'text-red-600 dark:text-red-400 font-bold';
                            this.timerStyle.animation = 'pulse 1s infinite';
                        } else if (this.remaining <= 300) { // 5 minutes
                            this.timerTextClass = 'text-amber-600 dark:text-amber-400 font-bold';
                        }
                    } else {
                        this.display = 'EXPIRED';
                        this.timerTextClass = 'text-red-600 dark:text-red-400 font-bold';
                        clearInterval(this.timerInterval);

                        // Only call Livewire once even if the interval fires again
                        if (!window._timerExpiredCalled) {
                            window._timerExpiredCalled = true;

                            // Ask the Livewire component to perform the authoritative
                            // server-side auto-submit and dispatch examAutoSubmitted.
                            const wireEl = document.querySelector('[wire:id]');
                            if (wireEl) {
                                const wire = Livewire.find(wireEl.getAttribute('wire:id'));
                                if (wire) {
                                    wire.call('handleTimerExpired');
                                }
                            }
                        }
                    }
                }, 1000);
            } else {
                // No time limit - display a dash or similar
                this.display = '--:--';
            }
        },

        updateDisplay() {
            if (this.remaining >= 0) {
                const minutes = Math.floor(this.remaining / 60);
                const seconds = this.remaining % 60;
                this.display = `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
            }
        },

        // Clean up interval when component is destroyed
        destroy() {
            if (this.timerInterval) {
                clearInterval(this.timerInterval);
            }
        }
    }));
});
