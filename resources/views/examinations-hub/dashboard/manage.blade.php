<x-layouts.app>
    <x-examinations-hub.navigation active="manage" />
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Manage Examinations</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">View, filter, and manage all your examinations</p>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 mb-6">
            <form method="GET" class="grid md:grid-cols-4 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Search</label>
                    <input 
                        type="text" 
                        name="search" 
                        value="{{ $filters['search'] ?? '' }}"
                        placeholder="Search by title or access code..."
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white"
                    >
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                    <select name="status" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
                        <option value="">All Statuses</option>
                        <option value="draft" {{ ($filters['status'] ?? '') === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="published" {{ ($filters['status'] ?? '') === 'published' ? 'selected' : '' }}>Published</option>
                    </select>
                </div>

                <div class="flex items-end">
                    <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                        Apply Filters
                    </button>
                </div>
            </form>
        </div>

        @forelse($exams as $exam)
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm hover:shadow-xl hover:border-indigo-300 dark:hover:border-indigo-600 transition-all duration-300 overflow-hidden mb-4">
                <div class="bg-gradient-to-r from-indigo-50 to-purple-50 dark:from-gray-700 dark:to-gray-750 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1 min-w-0">
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-1 truncate">{{ $exam->title }}</h3>
                            @if($exam->description)
                                <p class="text-sm text-gray-600 dark:text-gray-300 line-clamp-2">{{ $exam->description }}</p>
                            @endif
                        </div>
                        <div class="flex items-center gap-3 flex-shrink-0">
                            <span class="px-4 py-1.5 text-xs font-semibold rounded-full shadow-sm
                                {{ $exam->status === 'published' ? 'bg-green-500 text-white' : 'bg-gray-400 text-white' }}">
                                {{ ucfirst($exam->status) }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
                        <div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 border border-blue-200 dark:border-blue-700 rounded-lg p-4 text-center">
                            <div class="text-xs font-medium text-blue-600 dark:text-blue-400 uppercase tracking-wide mb-2">Access Code</div>
                            <div class="text-lg font-mono font-bold text-blue-900 dark:text-blue-100">{{ $exam->access_code }}</div>
                        </div>
                        <div class="bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20 border border-purple-200 dark:border-purple-700 rounded-lg p-4 text-center">
                            <div class="text-xs font-medium text-purple-600 dark:text-purple-400 uppercase tracking-wide mb-2">Sections</div>
                            <div class="text-2xl font-bold text-purple-900 dark:text-purple-100">{{ $exam->sections_count }}</div>
                        </div>
                        <div class="bg-gradient-to-br from-amber-50 to-amber-100 dark:from-amber-900/20 dark:to-amber-800/20 border border-amber-200 dark:border-amber-700 rounded-lg p-4 text-center">
                            <div class="text-xs font-medium text-amber-600 dark:text-amber-400 uppercase tracking-wide mb-2">Questions</div>
                            <div class="text-2xl font-bold text-amber-900 dark:text-amber-100">{{ $exam->questions_count }}</div>
                        </div>
                        <div class="bg-gradient-to-br from-emerald-50 to-emerald-100 dark:from-emerald-900/20 dark:to-emerald-800/20 border border-emerald-200 dark:border-emerald-700 rounded-lg p-4 text-center">
                            <div class="text-xs font-medium text-emerald-600 dark:text-emerald-400 uppercase tracking-wide mb-2">Submissions</div>
                            <div class="text-2xl font-bold text-emerald-900 dark:text-emerald-100">{{ $exam->submissions_count }}</div>
                        </div>
                    </div>

                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                        @if($exam->starts_at)
                            <div class="flex items-center gap-3 text-sm">
                                <div class="flex items-center justify-center w-10 h-10 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg">
                                    <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 font-medium">Scheduled</div>
                                    <div class="font-semibold text-gray-900 dark:text-white">{{ $exam->starts_at->format('M d, Y \a\t h:i A') }}</div>
                                    @if($exam->ends_at)
                                        <div class="text-xs text-gray-500 dark:text-gray-400">Ends: {{ $exam->ends_at->format('M d, Y \a\t h:i A') }}</div>
                                    @endif
                                </div>
                            </div>
                        @else
                            <div class="flex items-center gap-3 text-sm">
                                <div class="flex items-center justify-center w-10 h-10 bg-gray-100 dark:bg-gray-700 rounded-lg">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 font-medium">Schedule</div>
                                    <div class="font-semibold text-gray-400 dark:text-gray-500">Not scheduled</div>
                                </div>
                            </div>
                        @endif

                        <div class="flex flex-wrap gap-2">
                            <a href="{{ route('examinations-hub.exams.show', $exam) }}" 
                               class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 shadow-md hover:shadow-lg transition-all text-sm font-semibold">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                View Details
                            </a>
                            @if(!$exam->starts_at || now()->lt($exam->starts_at))
                                <a href="{{ route('examinations-hub.exams.edit', $exam) }}" 
                                   class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 shadow-md hover:shadow-lg transition-all text-sm font-semibold">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    Edit
                                </a>
                            @endif
                            <a href="{{ route('examinations-hub.submissions.index', $exam) }}" 
                               class="inline-flex items-center gap-2 px-5 py-2.5 bg-white dark:bg-gray-700 border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 hover:border-gray-400 dark:hover:border-gray-500 shadow-md hover:shadow-lg transition-all text-sm font-semibold">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Submissions
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-12">
                <div class="flex flex-col items-center">
                    <svg class="h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <p class="mt-4 text-lg font-medium text-gray-900 dark:text-white">No examinations found</p>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Get started by creating your first examination</p>
                    <a href="{{ route('examinations-hub.create') }}" class="mt-6 px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium">
                        Create Your First Exam
                    </a>
                </div>
            </div>
        @endforelse

        @if($exams->hasPages())
            <div class="mt-6">
                {{ $exams->links() }}
            </div>
        @endif
    </div>
</x-layouts.app>
