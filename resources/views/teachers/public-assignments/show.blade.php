<x-layouts.app>
    <div class="py-6">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                <div>
                    <a href="{{ route('teachers.public-assignments.index') }}" class="text-sm text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 flex items-center gap-1 mb-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                        Back to Assignments
                    </a>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $assignment->title }}</h1>
                    <div class="flex items-center gap-3 mt-2">
                        <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full
                            @if($assignment->status === 'published') bg-green-100 text-green-700 dark:bg-green-900/50 dark:text-green-300
                            @elseif($assignment->status === 'draft') bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300
                            @elseif($assignment->status === 'closed') bg-amber-100 text-amber-700 dark:bg-amber-900/50 dark:text-amber-300
                            @else bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400
                            @endif">
                            {{ ucfirst($assignment->status) }}
                        </span>
                        <span class="text-sm text-gray-500 dark:text-gray-400">{{ ucfirst($assignment->type) }}</span>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('teachers.public-assignments.edit', $assignment) }}" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                        Edit
                    </a>
                    <a href="{{ route('teachers.public-assignments.results', $assignment) }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition-colors">
                        View Results
                    </a>
                </div>
            </div>

            <!-- Access Code Card -->
            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-2xl p-6 mb-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-medium opacity-90">Access Code</h2>
                        <p class="text-sm opacity-75 mt-1">Share this code with participants to join</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <code class="px-4 py-2 bg-white/20 rounded-lg text-2xl font-mono tracking-widest">{{ $assignment->access_code }}</code>
                        <button onclick="navigator.clipboard.writeText('{{ $assignment->access_code }}')" class="p-2 bg-white/20 hover:bg-white/30 rounded-lg transition-colors" title="Copy code">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-white/20">
                    <p class="text-sm opacity-75">Join URL: <span class="font-mono">{{ route('public-assignments.join.code', $assignment->access_code) }}</span></p>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $assignment->questions()->count() }}</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Questions</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $assignment->total_marks ?? 0 }}</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Total Marks</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $assignment->submissions()->count() }}</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Submissions</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $assignment->duration_in_minutes ?? '∞' }}</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Minutes</div>
                </div>
            </div>

            <!-- Details Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Basic Info -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h2 class="font-semibold text-gray-900 dark:text-white mb-4">Basic Information</h2>
                    <div class="space-y-4">
                        @if($assignment->description)
                            <div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">Description</div>
                                <div class="text-gray-900 dark:text-white">{{ $assignment->description }}</div>
                            </div>
                        @endif
                        @if($assignment->instructions)
                            <div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">Instructions</div>
                                <div class="text-gray-900 dark:text-white whitespace-pre-wrap">{{ $assignment->instructions }}</div>
                            </div>
                        @endif
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">Start Date</div>
                                <div class="text-gray-900 dark:text-white">{{ $assignment->starts_at ? $assignment->starts_at->format('M d, Y H:i') : 'Not set' }}</div>
                            </div>
                            <div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">End Date</div>
                                <div class="text-gray-900 dark:text-white">{{ $assignment->ends_at ? $assignment->ends_at->format('M d, Y H:i') : 'Not set' }}</div>
                            </div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Max Attempts</div>
                            <div class="text-gray-900 dark:text-white">{{ $assignment->max_attempts ?? 1 }}</div>
                        </div>
                    </div>
                </div>

                <!-- Settings -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h2 class="font-semibold text-gray-900 dark:text-white mb-4">Settings</h2>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                            <span class="text-gray-600 dark:text-gray-400">Randomize Questions</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ $assignment->is_randomized ? 'Yes' : 'No' }}</span>
                        </div>
                        <div class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                            <span class="text-gray-600 dark:text-gray-400">Result Visibility</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ ucfirst(str_replace('_', ' ', $assignment->result_visibility ?? 'immediate')) }}</span>
                        </div>
                        <div class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                            <span class="text-gray-600 dark:text-gray-400">Show Correct Answers</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ $assignment->show_correct_answers ? 'Yes' : 'No' }}</span>
                        </div>
                        <div class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                            <span class="text-gray-600 dark:text-gray-400">Proctoring</span>
                            <span class="font-medium {{ $assignment->proctoring_enabled ? 'text-amber-600' : 'text-gray-900 dark:text-white' }}">
                                {{ $assignment->proctoring_enabled ? 'Enabled' : 'Disabled' }}
                            </span>
                        </div>
                        @if($assignment->proctoring_enabled)
                            <div class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400">Restrict Navigation</span>
                                <span class="font-medium text-gray-900 dark:text-white">{{ $assignment->restrict_navigation ? 'Yes' : 'No' }}</span>
                            </div>
                            <div class="flex items-center justify-between py-2">
                                <span class="text-gray-600 dark:text-gray-400">Require Fullscreen</span>
                                <span class="font-medium text-gray-900 dark:text-white">{{ $assignment->require_fullscreen ? 'Yes' : 'No' }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Questions Preview -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="font-semibold text-gray-900 dark:text-white mb-4">Questions ({{ $assignment->questions()->count() }})</h2>
                <div class="space-y-4">
                    @forelse($assignment->questions()->orderBy('order')->get() as $index => $question)
                        <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-xl">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="text-sm font-medium text-gray-500">Q{{ $index + 1 }}</span>
                                        <span class="px-2 py-0.5 text-xs bg-gray-100 dark:bg-gray-700 rounded">{{ ucfirst(str_replace('_', ' ', $question->type)) }}</span>
                                        <span class="text-xs text-gray-500">{{ $question->marks }} marks</span>
                                    </div>
                                    <p class="text-gray-900 dark:text-white"><x-prose-content :content="$question->question" />   </p>
                                    @if($question->type === 'multiple_choice' && $question->options)
                                        <div class="mt-2 space-y-1">
                                            @foreach($question->options as $key => $option)
                                                <div class="text-sm flex items-center gap-1 {{ $key === $question->correct_answer ? 'text-green-600 font-medium' : 'text-gray-600 dark:text-gray-400' }}">
                                                    <span class="flex-shrink-0">{{ $key }}.</span>
                                                    <div class="flex-1">
                                                        <x-prose-content 
                                                            :content="$option" 
                                                            :textColor="$key === $question->correct_answer ? 'text-green-600 font-medium' : 'text-gray-600 dark:text-gray-400'" 
                                                        />
                                                    </div>
                                                    @if($key === $question->correct_answer)
                                                        <span class="flex-shrink-0 text-green-600">✓</span>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    @elseif($question->type === 'true_false')
                                        <div class="mt-2 text-sm text-green-600 font-medium">
                                            Correct: {{ ucfirst($question->correct_answer) }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                            <p>No questions added yet.</p>
                            <a href="{{ route('teachers.public-assignments.edit', $assignment) }}" class="text-indigo-600 hover:text-indigo-700 mt-2 inline-block">Add questions</a>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
