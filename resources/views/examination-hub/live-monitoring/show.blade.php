<x-layouts.app>
    <x-examination-hub.navigation active="manage" />

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 pb-16 pt-6">
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
                
                <div class="mt-8 flex justify-end space-x-3">
                    <button @click="warnParticipant()" 
                            class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-yellow-600 hover:bg-yellow-700 transition-colors"
                            style="border-radius: 2px;">
                        <x-heroicon-o-exclamation-triangle class="w-4 h-4" />
                        Send Warning
                    </button>
                    
                    <button @click="terminateExam()"
                            class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-red-600 hover:bg-red-700 transition-colors"
                            style="border-radius: 2px;">
                        <x-heroicon-o-x-circle class="w-4 h-4" />
                        Terminate Exam
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Format the elapsed time after the page loads
            const elapsedTimeDisplay = document.getElementById('elapsed-time-display');
            if(elapsedTimeDisplay && elapsedTimeDisplay.textContent !== 'N/A') {
                const seconds = parseInt(elapsedTimeDisplay.textContent);
                if(!isNaN(seconds)) {
                    elapsedTimeDisplay.textContent = formatDuration(seconds);
                }
            }
        });
        
        function warnParticipant() {
            // Implement warning logic
            alert("Warning functionality would be implemented here");
        }
        
        function terminateExam() {
            // Implement termination logic
            alert("Termination functionality would be implemented here");
        }
        
        // Helper function to format duration
        function formatDuration(seconds) {
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
    </script>
</x-layouts.app>