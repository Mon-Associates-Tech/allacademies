/**
 * Frontend Proctoring Module
 *
 * Exports a class that can be imported via Vite or attached to window
 * for legacy script tag usage.
 */
export default class ExamProctoring {
    constructor(config = {}) {
        this.sessionId = config.sessionId;
        this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        this.isInitialized = false;
        this.endpoint = config.endpoint || '/proctoring/violation';
        console.log('proctoring')
    }

    init() {
        if (this.isInitialized || !this.sessionId) return;
        this.bindEvents();
        this.enforceFullscreen();
        this.isInitialized = true;
        console.log('Proctoring initialized for session:', this.sessionId);
    }

    bindEvents() {
        document.addEventListener('visibilitychange', () => this.handleVisibilityChange());
        window.addEventListener('blur', () => this.report('window_blur'));
        document.addEventListener('fullscreenchange', () => this.handleFullscreenChange());
        document.addEventListener('keydown', (e) => this.handleKeydown(e));
        document.addEventListener('copy', (e) => { e.preventDefault(); this.report('copy_attempt'); });
        document.addEventListener('contextmenu', (e) => { e.preventDefault(); this.report('context_menu'); });
    }

    handleVisibilityChange() {
        if (document.hidden) this.report('tab_switch');
    }

    enforceFullscreen() {
        const elem = document.documentElement;
        if (!document.fullscreenElement && elem.requestFullscreen) {
            elem.requestFullscreen().catch(() => this.report('fullscreen_reject'));
        }
    }

    handleFullscreenChange() {
        if (!document.fullscreenElement) {
            this.report('fullscreen_exit');
            this.enforceFullscreen();
        }
    }

    handleKeydown(e) {
        if (e.ctrlKey || e.metaKey || ['PrintScreen', 'F12'].includes(e.key)) {
            e.preventDefault();
            this.report('keyboard_shortcut_blocked', { key: e.key });
        }
    }

    async report(type, metadata = {}) {
        if (!this.sessionId) return;

        try {
            const res = await fetch(this.endpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    proctoring_session_id: this.sessionId,
                    proctoring_violation: {
                        type,
                        metadata: { ...metadata, timestamp: new Date().toISOString() }
                    }
                })
            });

            if (!res.ok) throw new Error(`HTTP ${res.status}`);

            const data = await res.json();
            this.handleResponse(data);
        } catch (err) {
            console.warn('Proctoring report failed:', err);
            // Fail gracefully - don't block the exam
        }
    }

    handleResponse(data) {
        if (data.action === 'warn') {
            // Show non-blocking warning
            this.showToast(data.message, 'warning');
        }
        if (data.action === 'auto_submit' || data.action === 'suspend') {
            this.showToast(data.message, 'error');
            // Delay slightly to let user read message
            setTimeout(() => {
                document.querySelector('#exam-form')?.submit();
                window.location.href = '/dashboard';
            }, 2000);
        }
    }

    showToast(message, type = 'info') {
        // Simple toast - replace with your UI framework's notification
        const toast = document.createElement('div');
        toast.className = `proctor-toast proctor-toast--${type}`;
        toast.textContent = message;
        toast.style.cssText = `
            position: fixed; top: 20px; right: 20px;
            padding: 12px 20px; border-radius: 8px;
            background: ${type === 'error' ? '#ef4444' : type === 'warning' ? '#f59e0b' : '#3b82f6'};
            color: white; z-index: 9999; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
        `;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 5000);
    }
}


// Also attach to window for legacy script tag usage (optional)
if (typeof window !== 'undefined') {
    window.ExamProctoring = ExamProctoring;
}
