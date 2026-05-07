<x-layouts.app>
    <x-examinations-hub.navigation active="manage" />
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $exam->title }}</h1>
                <p class="text-sm text-gray-500">Code: {{ $exam->access_code }} · Duration: {{ $exam->duration_in_minutes ?? 0 }} mins · {{ $exam->questions_count }} questions</p>
            </div>
            <div class="flex items-center gap-2">
                @if(!$exam->starts_at || now()->lt($exam->starts_at))
                    <a href="{{ route('examinations-hub.exams.edit', $exam) }}" class="px-4 py-2 bg-emerald-600 text-white rounded-lg">Edit Exam</a>
                @endif
                <a href="{{ route('examinations-hub.submissions.index', $exam) }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg">View Submissions</a>
            </div>
        </div>

        @if(session('success'))
            <div class="p-3 rounded-lg bg-green-50 border border-green-200 text-green-700">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="p-3 rounded-lg bg-red-50 border border-red-200 text-red-700">{{ session('error') }}</div>
        @endif

        <div class="grid md:grid-cols-3 gap-4">
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                <div class="text-sm text-gray-600 dark:text-gray-400">Total Sections</div>
                <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $exam->sections_count }}</div>
            </div>
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                <div class="text-sm text-gray-600 dark:text-gray-400">Total Questions</div>
                <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $exam->questions_count }}</div>
            </div>
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                <div class="text-sm text-gray-600 dark:text-gray-400">Submissions</div>
                <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $exam->submissions_count }}</div>
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-6">
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                <h2 class="font-semibold mb-3">Section Navigator</h2>
                
                @if($exam->hardened_mode && $exam->starts_at && now()->lt($exam->starts_at))
                    <div class="p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
                        <p class="text-yellow-800 dark:text-yellow-200 font-medium">🔒 Hardened Mode Active</p>
                        <p class="text-sm text-yellow-700 dark:text-yellow-300 mt-1">Questions are hidden until exam starts on {{ $exam->starts_at->format('M d, Y \\a\\t h:i A') }}</p>
                    </div>
                    
                    <div class="space-y-3 mt-4">
                        @foreach($sectionNavigator as $section)
                            <div class="p-3 border border-gray-200 dark:border-gray-700 rounded-lg">
                                <div class="font-medium">{{ $section['index'] }}. {{ $section['title'] }}</div>
                                <div class="text-sm text-gray-500">{{ $section['question_count'] }} questions · {{ $section['time_limit_minutes'] ?? 'No' }} min section limit</div>
                                @if(!empty($section['instructions']))
                                    <div class="text-sm mt-1 text-gray-600 dark:text-gray-400">{{ $section['instructions'] }}</div>
                                @endif
                                <div class="mt-2 p-2 bg-gray-100 dark:bg-gray-700 rounded text-center">
                                    <p class="text-xs text-gray-600 dark:text-gray-400">🔒 Questions hidden</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach($sectionNavigator as $section)
                            <div class="p-3 border border-gray-200 dark:border-gray-700 rounded-lg">
                                <div class="font-medium">{{ $section['index'] }}. {{ $section['title'] }}</div>
                                <div class="text-sm text-gray-500">{{ $section['question_count'] }} questions · {{ $section['time_limit_minutes'] ?? 'No' }} min section limit</div>
                                @if(!empty($section['instructions']))
                                    <div class="text-sm mt-1 text-gray-600 dark:text-gray-400">{{ $section['instructions'] }}</div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="space-y-6">
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                    <h2 class="font-semibold mb-3 flex items-center gap-2">
                        <svg class="h-5 w-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        Email Invitations & Reminders
                    </h2>
                    
                    <div class="space-y-4">
                        <div class="p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                            <p class="text-sm text-blue-800 dark:text-blue-200">
                                <strong>📧 Send invitations</strong> to all configured participants with exam details and calendar file.
                            </p>
                        </div>

                        <form action="{{ route('examinations-hub.exams.send-invitations', $exam) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 flex items-center justify-center gap-2">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                                Send Invitations Now
                            </button>
                        </form>

                        <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                            <form action="{{ route('examinations-hub.exams.reminder-settings', $exam) }}" method="POST" class="space-y-3">
                                @csrf
                                <div class="flex items-center gap-2">
                                    <input type="checkbox" name="send_reminders" id="send_reminders" value="1" {{ $exam->send_reminders ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                    <label for="send_reminders" class="text-sm font-medium text-gray-700 dark:text-gray-300">Enable Automatic Reminders</label>
                                </div>
                                
                                <div>
                                    <label class="text-sm text-gray-600 dark:text-gray-400">Send reminder on:</label>
                                    <input type="datetime-local" name="reminder_datetime" value="{{ $exam->reminder_datetime?->format('Y-m-d\TH:i') }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white text-sm">
                                </div>

                                <button type="submit" class="w-full px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 text-sm">
                                    Save Reminder Settings
                                </button>
                            </form>
                        </div>

                        @if($exam->reminder_sent)
                            <div class="p-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                                <p class="text-sm text-green-800 dark:text-green-200">
                                    ✓ Reminder sent on {{ $exam->reminder_sent_at->format('M d, Y \a\t h:i A') }}
                                </p>
                            </div>
                        @endif

                        <form action="{{ route('examinations-hub.exams.send-reminder', $exam) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 flex items-center justify-center gap-2 text-sm">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                </svg>
                                Send Manual Reminder Now
                            </button>
                        </form>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-3">
                        <h2 class="font-semibold">Participant Settings</h2>
                        <span class="text-xs px-2 py-1 bg-blue-100 text-blue-800 rounded">{{ ucfirst($exam->participant_mode) }}</span>
                    </div>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Mode:</span>
                            <span class="font-medium">{{ ucfirst($exam->participant_mode) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Match Rule:</span>
                            <span class="font-medium">{{ ucfirst($exam->configured_match_mode ?? 'any') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Required Fields:</span>
                            <span class="font-medium">{{ implode(', ', $exam->participant_required_fields ?? []) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Configured Count:</span>
                            <span class="font-medium">{{ $configuredCount }}</span>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                    <h2 class="font-semibold mb-3">Add Participant</h2>
                    <form action="{{ route('examinations-hub.participants.configured.store', $exam) }}" method="POST" class="grid gap-3">
                        @csrf
                        <input name="name" placeholder="Name" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white" required>
                        <input name="email" type="email" placeholder="Email" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white" required>
                        <input name="unique_code" placeholder="Unique Code (optional)" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
                        <button class="px-4 py-2 bg-gray-900 text-white rounded-lg hover:bg-gray-800">Add Participant</button>
                    </form>
                </div>

                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                    <h2 class="font-semibold mb-3">Import Participants (CSV)</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">CSV format: name,email,unique_code</p>
                    <form action="{{ route('examinations-hub.participants.configured.import', $exam) }}" method="POST" enctype="multipart/form-data" class="grid gap-3">
                        @csrf
                        <input type="file" name="participants_csv" accept=".csv" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white" required>
                        <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">Import CSV</button>
                    </form>
                </div>
            </div>
        </div>

        @if($configuredParticipants->isNotEmpty())
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                <h2 class="font-semibold mb-3">Configured Participants ({{ $configuredCount }})</h2>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
                            <tr>
                                <th class="px-4 py-2 text-left font-medium text-gray-700 dark:text-gray-300">Name</th>
                                <th class="px-4 py-2 text-left font-medium text-gray-700 dark:text-gray-300">Email</th>
                                <th class="px-4 py-2 text-left font-medium text-gray-700 dark:text-gray-300">Unique Code</th>
                                <th class="px-4 py-2 text-left font-medium text-gray-700 dark:text-gray-300">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($configuredParticipants as $participant)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-900">
                                    <td class="px-4 py-2">{{ $participant->name }}</td>
                                    <td class="px-4 py-2">{{ $participant->email }}</td>
                                    <td class="px-4 py-2">{{ $participant->unique_code ?? '-' }}</td>
                                    <td class="px-4 py-2">
                                        <span class="text-xs px-2 py-1 rounded {{ $participant->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                            {{ $participant->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</x-layouts.app>
