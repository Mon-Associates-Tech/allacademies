<script>
function liveMonitoring(initialData) {
    return {
        exam: initialData.exam,
        stats: initialData.stats,
        participants: initialData.participants,
        loading: false,
        actionLoading: false,
        lastUpdated: new Date().toLocaleTimeString(),
        activeFilter: 'all',
        searchQuery: '',
        pollingInterval: null,

        // Modals
        showMessageModal: false,
        showWarningModal: false,
        showExtendTimeModal: false,
        selectedParticipant: null,
        messageText: '',
        warningText: '',
        extendMinutes: 15,

        // Toasts
        toasts: [],
        toastId: 0,

        get statsCards() {
            return [
                { label: 'Total', value: this.stats.total_participants, gradient: 'linear-gradient(135deg,#6366f1,#818cf8)', iconPath: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z' },
                { label: 'Active', value: this.stats.active, gradient: 'linear-gradient(135deg,#10b981,#34d399)', iconPath: 'M5.636 18.364a9 9 0 010-12.728m12.728 0a9 9 0 010 12.728m-9.9-2.829a5 5 0 010-7.07m7.072 0a5 5 0 010 7.07M13 12a1 1 0 11-2 0 1 1 0 012 0z' },
                { label: 'Idle', value: this.stats.idle, gradient: 'linear-gradient(135deg,#f59e0b,#fbbf24)', iconPath: 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z' },
                { label: 'Away', value: this.stats.away, gradient: 'linear-gradient(135deg,#f97316,#fb923c)', iconPath: 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z' },
                { label: 'Disconnected', value: this.stats.disconnected, gradient: 'linear-gradient(135deg,#64748b,#94a3b8)', iconPath: 'M18.364 5.636a9 9 0 010 12.728m0 0l-2.829-2.829m2.829 2.829L21 21M15.536 8.464a5 5 0 010 7.072m0 0l-2.829-2.829m-4.243 2.829a4.978 4.978 0 01-1.414-2.83m-1.414 5.658a9 9 0 01-2.167-9.238m7.824 2.167a1 1 0 111.414 1.414m-1.414-1.414L3 3m8.293 8.293l1.414 1.414' },
                { label: 'Flagged', value: this.stats.flagged, gradient: 'linear-gradient(135deg,#ef4444,#f87171)', iconPath: 'M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9' },
                { label: 'Completed', value: this.stats.completed, gradient: 'linear-gradient(135deg,#3b82f6,#60a5fa)', iconPath: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' },
            ];
        },

        get filters() {
            return [
                { label: 'All', value: 'all', count: this.stats.total_participants },
                { label: 'Active', value: 'active', count: this.stats.active },
                { label: 'Idle/Away', value: 'idle', count: this.stats.idle + this.stats.away },
                { label: 'Disconnected', value: 'disconnected', count: this.stats.disconnected },
                { label: 'Flagged', value: 'flagged', count: this.stats.flagged },
                { label: 'Completed', value: 'completed', count: this.stats.completed },
            ];
        },

        get filteredParticipants() {
            let result = this.participants;

            if (this.activeFilter !== 'all') {
                if (this.activeFilter === 'idle') {
                    result = result.filter(p => p.status === 'idle' || p.status === 'away');
                } else if (this.activeFilter === 'flagged') {
                    result = result.filter(p => p.is_flagged);
                } else {
                    result = result.filter(p => p.status === this.activeFilter);
                }
            }

            if (this.searchQuery.trim()) {
                const query = this.searchQuery.toLowerCase();
                result = result.filter(p =>
                    (p.participant_name && p.participant_name.toLowerCase().includes(query)) ||
                    (p.participant_email && p.participant_email.toLowerCase().includes(query))
                );
            }

            return result;
        },

        init() {
            this.pollingInterval = setInterval(() => this.refreshData(), 10000);

            if (typeof Echo !== 'undefined') {
                Echo.channel('exam-monitoring.' + this.exam.id)
                    .listen('.participant.heartbeat', (data) => this.updateParticipant(data))
                    .listen('.participant.violation', (data) => {
                        this.updateParticipant(data.participant);
                        this.addToast('warning', `Violation: ${data.violation.event_type} (${data.participant.participant_name})`);
                    })
                    .listen('.participant.status_changed', (data) => this.updateParticipant(data.participant));
            }
        },

        async refreshData() {
            this.loading = true;
            try {
                const response = await fetch('{{ route("examination-hub.live-monitoring.api.participants", $exam) }}');
                const data = await response.json();
                this.stats = data.stats;
                this.participants = data.participants;
                this.lastUpdated = new Date().toLocaleTimeString();
            } catch (error) {
                console.error('Failed to refresh:', error);
            }
            this.loading = false;
        },

        updateParticipant(data) {
            const index = this.participants.findIndex(p => p.submission_id === data.submission_id);
            if (index !== -1) {
                this.participants[index] = data;
            } else {
                this.participants.push(data);
            }
            this.recalculateStats();
        },

        recalculateStats() {
            this.stats = {
                total_participants: this.participants.length,
                active: this.participants.filter(p => p.status === 'active').length,
                idle: this.participants.filter(p => p.status === 'idle').length,
                away: this.participants.filter(p => p.status === 'away').length,
                disconnected: this.participants.filter(p => p.status === 'disconnected').length,
                completed: this.participants.filter(p => p.status === 'completed').length,
                terminated: this.participants.filter(p => p.status === 'terminated').length,
                flagged: this.participants.filter(p => p.is_flagged).length,
            };
        },

        formatTime(seconds) {
            if (!seconds) return '—';
            const mins = Math.floor(seconds / 60);
            const secs = seconds % 60;
            return `${mins}m ${secs}s`;
        },

        // Modal handlers
        openMessageModal(participant) {
            this.selectedParticipant = participant;
            this.messageText = '';
            this.showMessageModal = true;
        },

        openWarningModal(participant) {
            this.selectedParticipant = participant;
            this.warningText = '';
            this.showWarningModal = true;
        },

        openExtendTimeModal(participant) {
            this.selectedParticipant = participant;
            this.extendMinutes = 15;
            this.showExtendTimeModal = true;
        },

        // Actions
        async sendMessage() {
            if (!this.messageText.trim() || !this.selectedParticipant) return;
            this.actionLoading = true;
            try {
                await fetch(`{{ url('examinations/exams/' . $exam->id . '/live-monitoring/message') }}/${this.selectedParticipant.submission_id}`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ message: this.messageText }),
                });
                this.showMessageModal = false;
                this.addToast('success', 'Message sent successfully');
            } catch (error) {
                this.addToast('error', 'Failed to send message');
            }
            this.actionLoading = false;
        },

        async sendWarning() {
            if (!this.warningText.trim() || !this.selectedParticipant) return;
            this.actionLoading = true;
            try {
                await fetch(`{{ url('examinations/exams/' . $exam->id . '/live-monitoring/warn') }}/${this.selectedParticipant.submission_id}`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ message: this.warningText }),
                });
                this.showWarningModal = false;
                this.addToast('success', 'Warning sent successfully');
            } catch (error) {
                this.addToast('error', 'Failed to send warning');
            }
            this.actionLoading = false;
        },

        async extendTime() {
            if (!this.extendMinutes || !this.selectedParticipant) return;
            this.actionLoading = true;
            try {
                await fetch(`{{ url('examinations/exams/' . $exam->id . '/live-monitoring/extend-time') }}/${this.selectedParticipant.submission_id}`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ minutes: parseInt(this.extendMinutes) }),
                });
                this.showExtendTimeModal = false;
                this.addToast('success', `Extended time by ${this.extendMinutes} minutes`);
                this.refreshData();
            } catch (error) {
                this.addToast('error', 'Failed to extend time');
            }
            this.actionLoading = false;
        },

        async confirmForceSubmit(participant) {
            if (!confirm(`Force submit exam for ${participant.participant_name}? This cannot be undone.`)) return;
            try {
                await fetch(`{{ url('examinations/exams/' . $exam->id . '/live-monitoring/force-submit') }}/${participant.submission_id}`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ reason: 'Admin force submission' }),
                });
                this.addToast('success', 'Exam force submitted');
                this.refreshData();
            } catch (error) {
                this.addToast('error', 'Failed to force submit');
            }
        },

        async confirmTerminate(participant) {
            const reason = prompt(`Reason for terminating ${participant.participant_name}'s session:`);
            if (!reason) return;
            try {
                await fetch(`{{ url('examinations/exams/' . $exam->id . '/live-monitoring/terminate') }}/${participant.submission_id}`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ reason }),
                });
                this.addToast('success', 'Session terminated');
                this.refreshData();
            } catch (error) {
                this.addToast('error', 'Failed to terminate');
            }
        },

        // Toast notifications
        addToast(type, message) {
            const id = ++this.toastId;
            this.toasts.push({ id, type, message, visible: true });
            setTimeout(() => this.removeToast(id), 5000);
        },

        removeToast(id) {
            const index = this.toasts.findIndex(t => t.id === id);
            if (index !== -1) {
                this.toasts[index].visible = false;
                setTimeout(() => {
                    this.toasts = this.toasts.filter(t => t.id !== id);
                }, 200);
            }
        },

        destroy() {
            if (this.pollingInterval) clearInterval(this.pollingInterval);
        },
    };
}
</script>
