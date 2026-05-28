<x-layouts.app>
    <x-examination-hub.navigation active="manage" />

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 pb-16 pt-6"
         x-data="participantMonitoring({{ $exam->id }}, {{ $participant['submission_id'] ?? 'null' }})">
        {{-- Breadcrumb --}}
        <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 mb-6">
            <a href="{{ route('examination-hub.exams.show', $exam) }}" class="hover:text-indigo-600">{{ $exam->title }}</a>
            <x-heroicon-o-chevron-right class="w-4 h-4" />
            <a href="{{ route('examination-hub.live-monitoring.index', $exam) }}" class="hover:text-indigo-600">Live Monitoring</a>
            <x-heroicon-o-chevron-right class="w-4 h-4" />
            <span>{{ $participant['participant_name'] ?? 'Unknown Participant' }}</span>
        </div>

        {{-- Main Card --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                <div class="flex justify-between items-center">
                    <h2 class="text-base font-semibold text-gray-800 dark:text-gray-200">
                        Live Monitoring for {{ $participant['participant_name'] ?? 'Unknown' }}
                    </h2>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-medium
                        @if($participant['status'] === 'completed') bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-400
                        @elseif($participant['status'] === 'in-progress' || $participant['status'] === 'active') bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-400
                        @else bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-400 @endif">
                        {{ __('Exam Status: ') }}{{ $participant['status'] ?: 'Unknown' }}
                    </span>
                </div>
            </div>
            
            <div class="p-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-gray-50 dark:bg-slate-900/40 rounded-lg p-5">
                        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">Participant Details</h3>
                        <table class="w-full text-sm">
                            <tr>
                                <td class="py-2 pr-4 align-top"><span class="text-gray-500 dark:text-gray-400">Name:</span></td>
                                <td class="py-2 font-medium text-gray-800 dark:text-gray-200">{{ $participant['participant_name'] ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="py-2 pr-4 align-top"><span class="text-gray-500 dark:text-gray-400">Email:</span></td>
                                <td class="py-2 font-medium text-gray-800 dark:text-gray-200">{{ $participant['participant_email'] ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="py-2 pr-4 align-top"><span class="text-gray-500 dark:text-gray-400">Start Time:</span></td>
                                <td class="py-2 font-medium text-gray-800 dark:text-gray-200">{{ $participant['started_at'] ? \Carbon\Carbon::parse($participant['started_at'])->format('Y-m-d H:i:s') : 'Not started' }}</td>
                            </tr>
                            <tr>
                                <td class="py-2 pr-4 align-top"><span class="text-gray-500 dark:text-gray-400">Current Question:</span></td>
                                <td class="py-2 font-medium text-gray-800 dark:text-gray-200">{{ $participant['current_question'] ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="py-2 pr-4 align-top"><span class="text-gray-500 dark:text-gray-400">Progress:</span></td>
                                <td class="py-2 font-medium text-gray-800 dark:text-gray-200">{{ $participant['progress_percentage'] ?? '0' }}%</td>
                            </tr>
                            <tr>
                                <td class="py-2 pr-4 align-top"><span class="text-gray-500 dark:text-gray-400">Elapsed Time:</span></td>
                                <td class="py-2 font-medium text-gray-800 dark:text-gray-200" id="elapsed-time-display">{{ $participant['elapsed_seconds'] ? $participant['elapsed_seconds'] : 'N/A' }}</td>
                            </tr>
                        </table>
                    </div>
                    
                    <div class="bg-gray-50 dark:bg-slate-900/40 rounded-lg p-5">
                        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">Proctoring Violations</h3>
                        @if(count($violations) > 0)
                            <ul class="space-y-3">
                                @foreach($violations as $violation)
                                    <li class="border-l-4 border-red-500 pl-3 py-1 bg-red-50 dark:bg-red-900/20 rounded-r px-3 py-2">
                                        <div class="flex justify-between">
                                            <span class="font-medium text-red-700 dark:text-red-400">{{ str_replace('_', ' ', ucfirst($violation['event_type'])) }}</span>
                                            <small class="text-gray-500">{{ \Carbon\Carbon::parse($violation['occurred_at'])->format('Y-m-d H:i:s') }}</small>
                                        </div>
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $violation['event_data']['details'] ?? '' }}</p>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <div class="text-center py-4">
                                <x-heroicon-o-check-circle class="w-10 h-10 mx-auto text-green-500" />
                                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">No violations detected for this participant.</p>
                            </div>
                        @endif
                    </div>
                </div>
                
                {{-- Admin Message History --}}
                <div class="mt-8">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Admin Message History</h3>
                        <button @click="loadMessageHistory()" 
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300 transition-colors"
                                style="border-radius: 2px;">
                            <x-heroicon-o-arrow-path class="w-4 h-4" />
                            Refresh
                        </button>
                    </div>
                    
                    <div id="message-history-container" class="bg-gray-50 dark:bg-slate-900/40 rounded-lg p-5 min-h-[200px]">
                        <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                            <x-heroicon-o-chat-bubble-left class="w-12 h-12 mx-auto mb-3 opacity-50" />
                            <p class="text-sm">Click "Refresh" to load message history</p>
                        </div>
                    </div>
                </div>
                
                <div class="mt-8 flex justify-end space-x-3">
                    <button @click="showWarningModal = true" 
                            class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-yellow-600 hover:bg-yellow-700 transition-colors"
                            style="border-radius: 2px;">
                        <x-heroicon-o-exclamation-triangle class="w-4 h-4" />
                        Send Warning
                    </button>
                    
                    <button @click="showMessageModal = true"
                            class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 transition-colors"
                            style="border-radius: 2px;">
                        <x-heroicon-o-chat-bubble-left-right class="w-4 h-4" />
                        Send Message
                    </button>
                    
                    <button @click="showTerminateModal = true"
                            class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-red-600 hover:bg-red-700 transition-colors"
                            style="border-radius: 2px;">
                        <x-heroicon-o-x-circle class="w-4 h-4" />
                        Terminate Exam
                    </button>
                </div>
            </div>
        </div>

        {{-- ── MESSAGE MODAL ── --}}
    <div x-show="showMessageModal"
         x-transition:enter="ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
         @click.self="showMessageModal = false"
         style="display: none;">
            <div class="bg-white dark:bg-slate-900 w-full max-w-md overflow-hidden"
                 style="border-radius: 2px; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);">
                <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800">
                    <h3 class="font-bold text-slate-900 dark:text-white">Send Message</h3>
                    <p class="text-sm text-slate-500 mt-1">To: {{ $participant['participant_name'] ?? 'Unknown' }}</p>
                </div>
                <div class="px-6 py-4">
                    <textarea x-model="messageText"
                              rows="3"
                              placeholder="Type your message..."
                              class="w-full px-3 py-2 text-sm border border-slate-200 dark:border-slate-700 dark:bg-slate-800 focus:ring-2 focus:ring-indigo-500"
                              style="border-radius: 2px;"></textarea>
                </div>
                <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/50 flex justify-end gap-3">
                    <button @click="showMessageModal = false"
                            class="px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors"
                            style="border-radius: 2px;">
                        Cancel
                    </button>
                    <button @click="sendMessage()"
                            :disabled="!messageText.trim()"
                            class="px-4 py-2 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 transition-colors disabled:opacity-50"
                            style="border-radius: 2px;">
                        Send Message
                    </button>
                </div>
            </div>
        </div>

        {{-- ── WARNING MODAL ── --}}
    <div x-show="showWarningModal"
         x-transition:enter="ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
         @click.self="showWarningModal = false"
         style="display: none;">
            <div class="bg-white dark:bg-slate-900 w-full max-w-md overflow-hidden"
                 style="border-radius: 2px; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);">
                <div class="h-1 w-full" style="background: linear-gradient(90deg, #f59e0b, #fbbf24);"></div>
                <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800">
                    <h3 class="font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <x-heroicon-o-exclamation-triangle class="w-5 h-5 text-yellow-500" />
                        Send Warning
                    </h3>
                    <p class="text-sm text-slate-500 mt-1">To: {{ $participant['participant_name'] ?? 'Unknown' }}</p>
                </div>
                <div class="px-6 py-4">
                    <textarea x-model="warningText"
                              rows="3"
                              placeholder="Warning message..."
                              class="w-full px-3 py-2 text-sm border border-slate-200 dark:border-slate-700 dark:bg-slate-800 focus:ring-2 focus:ring-yellow-500"
                              style="border-radius: 2px;"></textarea>
                    <p class="text-xs text-slate-500 mt-2">This warning will be displayed prominently to the participant.</p>
                </div>
                <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/50 flex justify-end gap-3">
                    <button @click="showWarningModal = false"
                            class="px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors"
                            style="border-radius: 2px;">
                        Cancel
                    </button>
                    <button @click="sendWarning()"
                            :disabled="!warningText.trim()"
                            class="px-4 py-2 text-sm font-semibold text-white bg-yellow-600 hover:bg-yellow-700 transition-colors disabled:opacity-50"
                            style="border-radius: 2px;">
                        Send Warning
                    </button>
                </div>
            </div>
        </div>

        {{-- ── TERMINATE MODAL ── --}}
    <div x-show="showTerminateModal"
         x-transition:enter="ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
         @click.self="showTerminateModal = false"
         style="display: none;">
            <div class="bg-white dark:bg-slate-900 w-full max-w-md overflow-hidden"
                 style="border-radius: 2px; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);">
                <div class="h-1 w-full" style="background: linear-gradient(90deg, #ef4444, #f87171);"></div>
                <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800">
                    <h3 class="font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <x-heroicon-o-x-circle class="w-5 h-5 text-red-500" />
                        Terminate Exam
                    </h3>
                    <p class="text-sm text-slate-500 mt-1">Participant: {{ $participant['participant_name'] ?? 'Unknown' }}</p>
                </div>
                <div class="px-6 py-4">
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Termination Reason</label>
                    <textarea x-model="terminateReason"
                              rows="3"
                              placeholder="Enter reason for termination..."
                              class="w-full px-3 py-2 text-sm border border-slate-200 dark:border-slate-700 dark:bg-slate-800 focus:ring-2 focus:ring-red-500"
                              style="border-radius: 2px;"></textarea>
                    <p class="text-xs text-red-600 dark:text-red-400 mt-2">⚠ This action cannot be undone. The participant will see this reason before being redirected.</p>
                </div>
                <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/50 flex justify-end gap-3">
                    <button @click="showTerminateModal = false"
                            class="px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors"
                            style="border-radius: 2px;">
                        Cancel
                    </button>
                    <button @click="terminateExam()"
                            :disabled="!terminateReason.trim()"
                            class="px-4 py-2 text-sm font-semibold text-white bg-red-600 hover:bg-red-700 transition-colors disabled:opacity-50"
                            style="border-radius: 2px;">
                        Terminate Exam
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function participantMonitoring(examId, submissionId) {
            return {
                examId: examId,
                submissionId: submissionId,
                showMessageModal: false,
                showWarningModal: false,
                showTerminateModal: false,
                messageText: '',
                warningText: '',
                terminateReason: '',

                init() {
                    // Format the elapsed time after the page loads
                    this.$nextTick(() => {
                        const elapsedTimeDisplay = document.getElementById('elapsed-time-display');
                        if(elapsedTimeDisplay && elapsedTimeDisplay.textContent !== 'N/A') {
                            const seconds = parseInt(elapsedTimeDisplay.textContent);
                            if(!isNaN(seconds)) {
                                elapsedTimeDisplay.textContent = this.formatDuration(seconds);
                            }
                        }
                        
                        // Load message history on page load
                        this.loadMessageHistory();
                    });
                },

                async loadMessageHistory() {
                    const container = document.getElementById('message-history-container');
                    
                    if (!this.submissionId) {
                        container.innerHTML = `
                            <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                                <x-heroicon-o-exclamation-circle class="w-12 h-12 mx-auto mb-3 opacity-50" />
                                <p class="text-sm">No submission data available</p>
                            </div>
                        `;
                        return;
                    }
                    
                    // Show loading state
                    container.innerHTML = `
                        <div class="text-center py-8">
                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600 mx-auto"></div>
                            <p class="mt-3 text-sm text-gray-500 dark:text-gray-400">Loading messages...</p>
                        </div>
                    `;
                    
                    try {
                        const response = await fetch(`/examinations/exams/${this.examId}/live-monitoring/messages/${this.submissionId}`);
                        
                        if (!response.ok) {
                            throw new Error('Failed to load messages');
                        }
                        
                        const data = await response.json();
                        this.displayMessageHistory(data.messages, container);
                    } catch (error) {
                        console.error('Error loading message history:', error);
                        container.innerHTML = `
                            <div class="text-center py-8 text-red-500">
                                <x-heroicon-o-exclamation-circle class="w-12 h-12 mx-auto mb-3" />
                                <p class="text-sm">Failed to load message history</p>
                            </div>
                        `;
                    }
                },
                
                displayMessageHistory(messages, container) {
                    if (!messages || messages.length === 0) {
                        container.innerHTML = `
                            <div class="text-center py-8">
                                <x-heroicon-o-chat-bubble-left class="w-12 h-12 mx-auto mb-3 opacity-50 text-gray-400" />
                                <p class="text-sm text-gray-500 dark:text-gray-400">No messages sent yet</p>
                            </div>
                        `;
                        return;
                    }
                    
                    const html = messages.map(msg => {
                        const typeColors = {
                            'warning': 'border-yellow-500 bg-yellow-50 dark:bg-yellow-900/20',
                            'info': 'border-blue-500 bg-blue-50 dark:bg-blue-900/20',
                            'termination': 'border-red-500 bg-red-50 dark:bg-red-900/20',
                            'force_submit': 'border-orange-500 bg-orange-50 dark:bg-orange-900/20',
                            'time_extension': 'border-green-500 bg-green-50 dark:bg-green-900/20'
                        };
                        
                        const borderColor = typeColors[msg.message_type] || 'border-gray-500 bg-gray-50';
                        
                        return `
                            <div class="border-l-4 ${borderColor} pl-4 py-3 mb-3 rounded-r">
                                <div class="flex items-start justify-between mb-2">
                                    <div class="flex items-center gap-2">
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded text-xs font-medium ${this.getTypeBadgeClass(msg.message_type)}">
                                            ${msg.type_label}
                                        </span>
                                        ${msg.is_delivered ? '<span class="text-xs text-green-600 dark:text-green-400">✓ Delivered</span>' : ''}
                                        ${msg.is_acknowledged ? '<span class="text-xs text-blue-600 dark:text-blue-400">✓ Acknowledged</span>' : ''}
                                    </div>
                                    <small class="text-gray-500 dark:text-gray-400 text-xs">${this.formatDateTime(msg.sent_at)}</small>
                                </div>
                                <p class="text-sm text-gray-800 dark:text-gray-200 mb-2">${this.escapeHtml(msg.message)}</p>
                                <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                                    <span>Sent by: ${msg.sent_by ? this.escapeHtml(msg.sent_by.name) : 'Unknown'}</span>
                                    ${msg.delivered_at ? `<span>• Delivered: ${this.formatDateTime(msg.delivered_at)}</span>` : ''}
                                </div>
                            </div>
                        `;
                    }).join('');
                    
                    container.innerHTML = `<div class="space-y-2">${html}</div>`;
                },
                
                getTypeBadgeClass(type) {
                    const classes = {
                        'warning': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-300',
                        'info': 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300',
                        'termination': 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300',
                        'force_submit': 'bg-orange-100 text-orange-800 dark:bg-orange-900/40 dark:text-orange-300',
                        'time_extension': 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300'
                    };
                    return classes[type] || 'bg-gray-100 text-gray-800';
                },
                
                formatDateTime(isoString) {
                    if (!isoString) return 'N/A';
                    const date = new Date(isoString);
                    return date.toLocaleString('en-US', {
                        year: 'numeric',
                        month: 'short',
                        day: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit',
                        second: '2-digit'
                    });
                },
                
                escapeHtml(text) {
                    const div = document.createElement('div');
                    div.textContent = text;
                    return div.innerHTML;
                },
                
                async sendMessage() {
                    if (!this.messageText || !this.messageText.trim()) return;
                    
                    try {
                        const response = await fetch(`/examinations/exams/${this.examId}/live-monitoring/message/${this.submissionId}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({ message: this.messageText.trim() })
                        });
                        
                        if (response.ok) {
                            // Close modal and clear input
                            this.showMessageModal = false;
                            this.messageText = '';
                            
                            // Refresh message history
                            this.loadMessageHistory();
                        } else {
                            alert('Failed to send message');
                        }
                    } catch (error) {
                        console.error('Error sending message:', error);
                        alert('Error sending message');
                    }
                },
                
                async sendWarning() {
                    if (!this.warningText || !this.warningText.trim()) return;
                    
                    try {
                        const response = await fetch(`/examinations/exams/${this.examId}/live-monitoring/warn/${this.submissionId}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({ message: this.warningText.trim() })
                        });
                        
                        if (response.ok) {
                            // Close modal and clear input
                            this.showWarningModal = false;
                            this.warningText = '';
                            
                            // Refresh message history
                            this.loadMessageHistory();
                        } else {
                            alert('Failed to send warning');
                        }
                    } catch (error) {
                        console.error('Error sending warning:', error);
                        alert('Error sending warning');
                    }
                },
                
                async terminateExam() {
                    if (!this.terminateReason || !this.terminateReason.trim()) return;
                    
                    try {
                        const response = await fetch(`/examinations/exams/${this.examId}/live-monitoring/terminate/${this.submissionId}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({ reason: this.terminateReason.trim() })
                        });
                        
                        if (response.ok) {
                            // Close modal and clear input
                            this.showTerminateModal = false;
                            this.terminateReason = '';
                            
                            // Refresh message history
                            this.loadMessageHistory();
                            
                            alert('Exam terminated successfully');
                        } else {
                            alert('Failed to terminate exam');
                        }
                    } catch (error) {
                        console.error('Error terminating exam:', error);
                        alert('Error terminating exam');
                    }
                },
                
                // Helper function to format duration
                formatDuration(seconds) {
                    if(isNaN(seconds)) return 'N/A';
                    
                    const hrs = Math.floor(seconds / 3600);
                    const mins = Math.floor((seconds % 3600) / 60);
                    const secs = seconds % 60;
                    
                    let result = '';
                    if (hrs > 0) result += hrs + 'h ';
                    if (mins > 0) result += mins + 'm ';
                    result += secs + 's';
                    
                    return result.trim();
                }
            };
        }
    </script>
</x-layouts.app>