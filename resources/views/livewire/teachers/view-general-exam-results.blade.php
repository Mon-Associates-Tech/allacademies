<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <a href="{{ route('teachers.general-exams.index') }}" class="text-sm text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 flex items-center gap-1 mb-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    Back to Exams
                </a>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $assignment->title }}</h1>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Submission Results</p>
            </div>
            <div class="flex items-center gap-3">
                @if(!$assignment->results_released)
                    <button wire:click="releaseResults" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors">
                        Release Results
                    </button>
                @else
                    <span class="px-3 py-1 bg-green-100 dark:bg-green-900/50 text-green-700 dark:text-green-300 text-sm rounded-full">
                        Results Released
                    </span>
                    <button wire:click="resendResultNotifications" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition-colors">
                        Resend Result Emails
                    </button>
                @endif
                <button wire:click="exportResults" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                    Export CSV
                </button>
            </div>
        </div>

        <!-- Stats Cards -->
        @php
            $defaults = [
                'total' => 0,
                'completed' => 0,
                'in_progress' => 0,
                'average_score' => 0,
                'needs_grading' => 0,
            ];
            $stats = array_merge($defaults, $stats ?? []);
        @endphp
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total'] }}</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">Total Submissions</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="text-2xl font-bold text-green-600">{{ $stats['completed'] }}</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">Completed</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="text-2xl font-bold text-amber-600">{{ $stats['in_progress'] }}</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">In Progress</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="text-2xl font-bold text-indigo-600">{{ number_format($stats['average_score'], 1) }}%</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">Average Score</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['needs_grading'] }}</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">Needs Grading</div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 mb-6">
            <div class="flex flex-col md:flex-row gap-4">
                <div class="flex-1">
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search by name or email..."
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-indigo-500">
                </div>
                <select wire:model.live="statusFilter" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
                    <option value="">All Statuses</option>
                    <option value="completed">Completed</option>
                    <option value="in_progress">In Progress</option>
                    <option value="graded">Graded</option>
                    <option value="needs_grading">Needs Grading</option>
                </select>
                <select wire:model.live="sortBy" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
                    <option value="submitted_at">Submission Date</option>
                    <option value="score">Score</option>
                    <option value="participant_name">Name</option>
                </select>
            </div>
        </div>

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 rounded-lg text-green-700 dark:text-green-300">
                {{ session('success') }}
            </div>
        @endif

        <!-- Submissions List -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            @if($submissions->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Participant</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Score</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Time Spent</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Submitted</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($submissions as $submission)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <td class="px-6 py-4">
                                        <div class="font-medium text-gray-900 dark:text-white">{{ $submission->getParticipantName() }}</div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $submission->getParticipantEmail() }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 text-xs rounded-full
                                            {{ $submission->participant_type === 'App\\Models\\Student' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/50 dark:text-blue-300' : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300' }}">
                                            {{ $submission->participant_type === 'App\\Models\\Student' ? 'Student' : 'Guest' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full
                                            @if($submission->status === 'completed') bg-green-100 text-green-700 dark:bg-green-900/50 dark:text-green-300
                                            @elseif($submission->status === 'in_progress') bg-amber-100 text-amber-700 dark:bg-amber-900/50 dark:text-amber-300
                                            @elseif($submission->status === 'graded') bg-indigo-100 text-indigo-700 dark:bg-indigo-900/50 dark:text-indigo-300
                                            @else bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300
                                            @endif">
                                            {{ ucfirst(str_replace('_', ' ', $submission->status)) }}
                                        </span>
                                        @if($submission->needs_manual_grading)
                                            <span class="ml-1 px-2 py-1 text-xs bg-red-100 text-red-700 dark:bg-red-900/50 dark:text-red-300 rounded-full">
                                                Needs Grading
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        @php
                                            $percent = $submission->percentage ?? (($submission->total_marks > 0 && $submission->score !== null) ? ($submission->score / $submission->total_marks * 100) : null);
                                        @endphp
                                        <div class="font-medium text-gray-900 dark:text-white">
                                            {{ $percent !== null ? number_format($percent, 1).'%' : '—' }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            @if($submission->score !== null && $submission->total_marks !== null)
                                                {{ $submission->score }}/{{ $submission->total_marks }}
                                            @else
                                                &nbsp;
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                        {{ $this->formatTimeSpent($submission->time_spent_seconds) }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                        @if($submission->submitted_at)
                                            {{ $submission->submitted_at->format('M d, Y H:i') }}
                                        @else
                                            <span class="text-gray-400">Not submitted</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <button wire:click="viewSubmission({{ $submission->id }})" class="p-2 text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 rounded-lg" title="View Details">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                            </button>
                                            @if($submission->status === 'completed' || $submission->needs_manual_grading)
                                                <a href="{{ route('teachers.general-exams.grade-submission', $submission) }}" class="p-2 text-green-600 hover:bg-green-50 dark:hover:bg-green-900/30 rounded-lg" title="Grade">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $submissions->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="w-16 h-16 mx-auto text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No submissions yet</h3>
                    <p class="text-gray-500 dark:text-gray-400">Share the access code with participants to start receiving submissions.</p>
                    <div class="mt-4 flex items-center justify-center gap-2">
                        <code class="px-3 py-2 bg-gray-100 dark:bg-gray-700 rounded-lg text-lg font-mono">{{ $assignment->access_code }}</code>
                        <button wire:click="copyAccessCode" class="p-2 text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Submission Detail Modal -->
    @if($viewingSubmission ?? false)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="fixed inset-0 bg-black/50" wire:click="closeSubmissionView"></div>
                <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-xl max-w-3xl w-full p-6 max-h-[90vh] overflow-y-auto">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Submission Details</h2>
                        <button wire:click="closeSubmissionView" class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Participant Info -->
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                            <div class="text-sm text-gray-500 dark:text-gray-400">Participant</div>
                            <div class="font-medium text-gray-900 dark:text-white">{{ $viewingSubmission->getParticipantName() }}</div>
                            <div class="text-sm text-gray-500">{{ $viewingSubmission->getParticipantEmail() }}</div>
                        </div>
                        <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                            <div class="text-sm text-gray-500 dark:text-gray-400">Score</div>
                            <div class="text-2xl font-bold text-gray-900 dark:text-white">
                                {{ $viewingSubmission->score !== null ? number_format($viewingSubmission->score, 1) . '%' : 'Not graded' }}
                            </div>
                        </div>
                        <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                            <div class="text-sm text-gray-500 dark:text-gray-400">Started</div>
                            <div class="font-medium text-gray-900 dark:text-white">{{ $viewingSubmission->started_at?->format('M d, Y H:i') ?? 'N/A' }}</div>
                        </div>
                        <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                            <div class="text-sm text-gray-500 dark:text-gray-400">Submitted</div>
                            <div class="font-medium text-gray-900 dark:text-white">{{ $viewingSubmission->submitted_at?->format('M d, Y H:i') ?? 'Not submitted' }}</div>
                        </div>
                    </div>

                    <!-- Proctoring Info -->
                    @if($viewingSubmission->proctoringSession)
                        <div class="mb-6 p-4 bg-amber-50 dark:bg-amber-900/30 border border-amber-200 dark:border-amber-800 rounded-xl">
                            <h3 class="font-medium text-amber-800 dark:text-amber-300 mb-2">Proctoring Summary</h3>
                            <div class="grid grid-cols-3 gap-4 text-sm">
                                <div>
                                    <span class="text-amber-600 dark:text-amber-400">Tab Switches:</span>
                                    <span class="font-medium text-amber-800 dark:text-amber-200">{{ $viewingSubmission->proctoringSession->tab_switch_count ?? 0 }}</span>
                                </div>
                                <div>
                                    <span class="text-amber-600 dark:text-amber-400">Violations:</span>
                                    <span class="font-medium text-amber-800 dark:text-amber-200">{{ $viewingSubmission->proctoringSession->violation_count ?? 0 }}</span>
                                </div>
                                <div>
                                    <span class="text-amber-600 dark:text-amber-400">Status:</span>
                                    <span class="font-medium {{ $viewingSubmission->proctoringSession->is_valid ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $viewingSubmission->proctoringSession->is_valid ? 'Valid' : 'Flagged' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Responses Summary -->
                    <h3 class="font-medium text-gray-900 dark:text-white mb-4">Responses</h3>
                    <div class="space-y-3 max-h-64 overflow-y-auto">
                        @foreach($assignment->questions as $index => $question)
                            @php
                                $response = $viewingSubmission->responses[$question->id] ?? null;
                                $isCorrect = $response['is_correct'] ?? null;
                                $question = Str::limit($question->question, 50)
                            @endphp
                            <div class="p-3 border border-gray-200 dark:border-gray-700 rounded-lg">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300 flex items-start gap-2">
                                        <span class="flex-shrink-0 mt-0.5">Q{{ $index + 1 }}:</span>
                                        <span class="flex-1">
                                            <x-prose-content :content="$question" class="prose-sm dark:prose-invert prose-p:my-0 prose-ul:my-0 prose-ol:my-0 prose-li:my-0" />
                                        </span>
                                    </span>
                                    @if($isCorrect === true)
                                        <span class="text-green-600">✓ Correct</span>
                                    @elseif($isCorrect === false)
                                        <span class="text-red-600">✗ Incorrect</span>
                                    @else
                                        <span class="text-gray-400">Pending</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="flex gap-3 mt-6">
                        <button wire:click="closeSubmissionView" class="flex-1 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300">
                            Close
                        </button>
                        <a href="{{ route('teachers.general-exams.grade-submission', $viewingSubmission) }}" class="flex-1 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-center">
                            Grade Submission
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
