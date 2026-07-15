/**
 * Exam Session Synchronization Module
 * Handles cross-browser data persistence and session continuity
 */

class ExamSessionSync {
    constructor(examId, submissionId, userId) {
        this.examId = examId;
        this.submissionId = submissionId;
        this.userId = userId;
        this.syncEndpoint = `/examinations/${examId}`;
        this.heartbeatInterval = null;
        this.lastSyncTime = Date.now();
        this.syncDebounceTimer = null;
    }

    /**
     * Initialize synchronization functionality
     */
    init() {
        // Listen for beforeunload to sync critical data
        window.addEventListener('beforeunload', () => this.syncImmediate());

        // Set up periodic heartbeat to maintain session
        this.startHeartbeat();

        // Listen for storage events to sync across tabs
        window.addEventListener('storage', (event) => this.handleStorageUpdate(event));

        // Attempt to restore session from shared storage
        this.restoreFromSharedStorage();

        // Set up debounced sync for response changes
        this.setupResponseSync();
    }

    /**
     * Start periodic heartbeat to maintain session
     */
    startHeartbeat() {
        this.heartbeatInterval = setInterval(() => {
            this.sendHeartbeat();
        }, 30000); // Every 30 seconds
    }

    /**
     * Send periodic heartbeat to server
     */
    async sendHeartbeat() {
        try {
            await fetch(`${this.syncEndpoint}/heartbeat`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                },
                body: JSON.stringify({
                    timestamp: Date.now(),
                    exam_id: this.examId,
                    submission_id: this.submissionId
                })
            });
        } catch (error) {
            console.error('Heartbeat failed:', error);
        }
    }

    /**
     * Sync data immediately
     */
    async syncImmediate() {
        // Collect all exam data
        const examData = this.collectExamData();
        try {


            // Send to server
            await fetch(this.syncEndpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                },
                body: JSON.stringify({
                    exam_id: this.examId,
                    submission_id: this.submissionId,
                    user_id: this.userId,
                    data: examData,
                    sync_type: 'immediate'
                })
            });
        } catch (error) {
            console.error('Immediate sync failed:', error);
            // Store locally as fallback
            this.saveToLocalBackup(examData);
        }
    }

    /**
     * Collect all exam data for synchronization
     */
    collectExamData() {
        // This would need to integrate with the actual exam component data
        // In a real implementation, this would extract data from the Livewire component
        return {
            responses: this.getCurrentResponses(),
            currentQuestion: this.getCurrentQuestionIndex(),
            flags: this.getFlaggedQuestions(),
            timeSpent: this.getTimeSpent(),
            sessionInfo: {
                userAgent: navigator.userAgent,
                platform: navigator.platform,
                language: navigator.language,
                timezone: Intl.DateTimeFormat().resolvedOptions().timeZone,
                timestamp: Date.now()
            }
        };
    }

    /**
     * Get current responses (would integrate with Livewire)
     */
    getCurrentResponses() {
        // This would integrate with the actual response data
        // For now, returning empty - in real implementation would extract from form fields
        return {};
    }

    /**
     * Get current question index
     */
    getCurrentQuestionIndex() {
        // Would integrate with actual exam state
        return 0;
    }

    /**
     * Get flagged questions
     */
    getFlaggedQuestions() {
        // Would integrate with actual flagging system
        return [];
    }

    /**
     * Calculate time spent on exam
     */
    getTimeSpent() {
        return Date.now() - this.lastSyncTime;
    }

    /**
     * Save data to local backup in case of sync failure
     */
    saveToLocalBackup(data) {
        const key = `exam_backup_${this.examId}_${this.submissionId}`;
        const backup = {
            data: data,
            timestamp: Date.now(),
            examId: this.examId,
            submissionId: this.submissionId
        };

        localStorage.setItem(key, JSON.stringify(backup));
    }

    /**
     * Restore from local backup if available
     */
    restoreFromLocalBackup() {
        const key = `exam_backup_${this.examId}_${this.submissionId}`;
        const backup = localStorage.getItem(key);

        if (backup) {
            try {
                const parsed = JSON.parse(backup);
                // Only use backup if it's recent (within last hour)
                if (Date.now() - parsed.timestamp < 3600000) {
                    return parsed.data;
                } else {
                    // Remove expired backup
                    localStorage.removeItem(key);
                }
            } catch (error) {
                console.error('Failed to parse backup:', error);
                localStorage.removeItem(key);
            }
        }
        return null;
    }

    /**
     * Setup response synchronization with debouncing
     */
    setupResponseSync() {
        // This would listen to response changes and debounce sync calls
        // In a real implementation, would integrate with Livewire events
        document.addEventListener('exam-response-change', (e) => {
            this.debouncedSync();
        });
    }

    /**
     * Debounced sync to avoid excessive server calls
     */
    debouncedSync() {
        if (this.syncDebounceTimer) {
            clearTimeout(this.syncDebounceTimer);
        }

        this.syncDebounceTimer = setTimeout(() => {
            this.syncIncremental();
        }, 2000); // Sync after 2 seconds of inactivity
    }

    /**
     * Sync incremental changes
     */
    async syncIncremental() {
        try {
            const examData = this.collectExamData();

            await fetch(`${this.syncEndpoint}/incremental`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                },
                body: JSON.stringify({
                    exam_id: this.examId,
                    submission_id: this.submissionId,
                    user_id: this.userId,
                    data: examData,
                    sync_type: 'incremental'
                })
            });

            this.lastSyncTime = Date.now();
        } catch (error) {
            console.error('Incremental sync failed:', error);
            this.saveToLocalBackup(this.collectExamData());
        }
    }

    /**
     * Handle storage updates from other tabs
     */
    handleStorageUpdate(event) {
        if (event.key === `exam_sync_${this.examId}`) {
            try {
                const data = JSON.parse(event.newValue);
                if (data.submissionId === this.submissionId) {
                    this.applyRemoteChanges(data);
                }
            } catch (error) {
                console.error('Failed to handle storage update:', error);
            }
        }
    }

    /**
     * Apply changes received from other tabs/browsers
     */
    applyRemoteChanges(data) {
        // This would update the local exam state with remote changes
        console.log('Applying remote changes:', data);
    }

    /**
     * Restore session from shared storage
     */
    restoreFromSharedStorage() {
        const key = `exam_sync_${this.examId}`;
        const stored = localStorage.getItem(key);

        if (stored) {
            try {
                const data = JSON.parse(stored);
                if (data.submissionId === this.submissionId) {
                    this.applyRemoteChanges(data);
                    return true;
                }
            } catch (error) {
                console.error('Failed to restore from shared storage:', error);
            }
        }
        return false;
    }

    /**
     * Cleanup resources
     */
    destroy() {
        if (this.heartbeatInterval) {
            clearInterval(this.heartbeatInterval);
        }

        if (this.syncDebounceTimer) {
            clearTimeout(this.syncDebounceTimer);
        }
    }
}

// Export for use in modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = ExamSessionSync;
}

// Make available globally for legacy use
window.ExamSessionSync = ExamSessionSync;
