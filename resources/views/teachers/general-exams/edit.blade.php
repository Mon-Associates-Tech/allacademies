<x-layouts.app>
    <div class="py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-6">
                <a href="{{ route('teachers.general-exams.show', $assignment) }}" class="text-sm text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 flex items-center gap-1 mb-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    Back to Assignment
                </a>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Assignment</h1>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ $assignment->title }}</p>
            </div>

            <!-- Edit Form -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6">
                <form action="{{ route('teachers.general-exams.show', $assignment) }}" method="GET" id="editForm">
                    <!-- Basic Information -->
                    <div class="mb-8">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Basic Information</h2>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Title</label>
                                <input type="text" name="title" value="{{ $assignment->title }}" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white" disabled>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                                <textarea name="description" rows="3" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white" disabled>{{ $assignment->description }}</textarea>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Type</label>
                                    <input type="text" value="{{ ucfirst($assignment->type) }}" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white" disabled>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                                    <input type="text" value="{{ ucfirst($assignment->status) }}" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white" disabled>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Time & Configuration -->
                    <div class="mb-8">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Time & Configuration</h2>
                        <div class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Duration (minutes)</label>
                                    <input type="text" value="{{ $assignment->duration_in_minutes ?? 'No limit' }}" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white" disabled>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Max Attempts</label>
                                    <input type="text" value="{{ $assignment->max_attempts ?? 1 }}" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white" disabled>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Start Date</label>
                                    <input type="text" value="{{ $assignment->starts_at ? $assignment->starts_at->format('M d, Y H:i') : 'Not set' }}" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white" disabled>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">End Date</label>
                                    <input type="text" value="{{ $assignment->ends_at ? $assignment->ends_at->format('M d, Y H:i') : 'Not set' }}" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white" disabled>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Questions Summary -->
                    <div class="mb-8">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Questions ({{ $assignment->questions()->count() }})</h2>
                        <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                            <div class="grid grid-cols-3 gap-4 text-center">
                                <div>
                                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $assignment->questions()->count() }}</div>
                                    <div class="text-sm text-gray-500">Total Questions</div>
                                </div>
                                <div>
                                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $assignment->total_marks ?? 0 }}</div>
                                    <div class="text-sm text-gray-500">Total Marks</div>
                                </div>
                                <div>
                                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $assignment->questions()->distinct('type')->count('type') }}</div>
                                    <div class="text-sm text-gray-500">Question Types</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Notice -->
                    <div class="p-4 bg-amber-50 dark:bg-amber-900/30 border border-amber-200 dark:border-amber-800 rounded-xl mb-6">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-amber-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                            <div>
                                <h3 class="font-medium text-amber-800 dark:text-amber-300">Edit Mode Limited</h3>
                                <p class="text-sm text-amber-700 dark:text-amber-400 mt-1">
                                    @if($assignment->status === 'published')
                                        This assignment is published. To make changes, you may need to close it first or create a duplicate.
                                    @elseif($assignment->submissions()->count() > 0)
                                        This assignment has submissions. Some fields cannot be edited to maintain data integrity.
                                    @else
                                        Full editing is available through the create wizard. Use the button below to access all editing options.
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-between pt-6 border-t border-gray-200 dark:border-gray-700">
                        <a href="{{ route('teachers.general-exams.show', $assignment) }}" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                            Cancel
                        </a>
                        <div class="flex gap-3">
                            @if($assignment->status === 'draft')
                                <form action="{{ route('teachers.general-exams.publish', $assignment) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg">
                                        Publish Assignment
                                    </button>
                                </form>
                            @elseif($assignment->status === 'published')
                                <form action="{{ route('teachers.general-exams.close', $assignment) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-lg">
                                        Close Assignment
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </form>
            </div>

            <!-- Quick Actions -->
            <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                <a href="{{ route('teachers.general-exams.results', $assignment) }}" class="p-4 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 hover:border-indigo-500 transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-indigo-100 dark:bg-indigo-900/50 rounded-lg">
                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <div>
                            <div class="font-medium text-gray-900 dark:text-white">View Results</div>
                            <div class="text-sm text-gray-500">{{ $assignment->submissions()->count() }} submissions</div>
                        </div>
                    </div>
                </a>
                <a href="{{ route('general-exams.join.code', $assignment->access_code) }}" target="_blank" class="p-4 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 hover:border-indigo-500 transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-green-100 dark:bg-green-900/50 rounded-lg">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </div>
                        <div>
                            <div class="font-medium text-gray-900 dark:text-white">Preview</div>
                            <div class="text-sm text-gray-500">Test as participant</div>
                        </div>
                    </div>
                </a>
                <button onclick="navigator.clipboard.writeText('{{ route('general-exams.join.code', $assignment->access_code) }}')" class="p-4 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 hover:border-indigo-500 transition-colors text-left">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-purple-100 dark:bg-purple-900/50 rounded-lg">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path>
                            </svg>
                        </div>
                        <div>
                            <div class="font-medium text-gray-900 dark:text-white">Share Link</div>
                            <div class="text-sm text-gray-500">Copy join URL</div>
                        </div>
                    </div>
                </button>
            </div>
        </div>
    </div>
</x-layouts.app>
