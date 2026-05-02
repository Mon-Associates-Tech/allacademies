<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">General Exams</h1>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Create and manage code-based exams for anyone to take</p>
            </div>
            <a href="{{ route('teachers.general-exams.create') }}"
               class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Create Exam
            </a>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $statusCounts['draft'] }}</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">Drafts</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="text-2xl font-bold text-green-600">{{ $statusCounts['published'] }}</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">Published</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="text-2xl font-bold text-amber-600">{{ $statusCounts['closed'] }}</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">Closed</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="text-2xl font-bold text-gray-400">{{ $statusCounts['archived'] }}</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">Archived</div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 mb-6">
            <div class="flex flex-col md:flex-row gap-4">
                <div class="flex-1">
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search assignments..."
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-indigo-500">
                </div>
                <select wire:model.live="statusFilter" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
                    <option value="">All Statuses</option>
                    <option value="draft">Draft</option>
                    <option value="published">Published</option>
                    <option value="closed">Closed</option>
                    <option value="archived">Archived</option>
                </select>
                <select wire:model.live="typeFilter" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
                    <option value="">All Types</option>
                    <option value="quiz">Quiz</option>
                    <option value="examination">Examination</option>
                    <option value="practice">Practice</option>
                </select>
            </div>
        </div>

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 rounded-lg text-green-700 dark:text-green-300">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 rounded-lg text-red-700 dark:text-red-300">
                {{ session('error') }}
            </div>
        @endif

        <!-- Assignments List -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            @if($assignments->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider cursor-pointer" wire:click="sortBy('title')">
                                    Title
                                    @if($sortBy === 'title')
                                        <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                    @endif
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Access Code</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider cursor-pointer" wire:click="sortBy('questions_count')">
                                    Questions
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider cursor-pointer" wire:click="sortBy('submissions_count')">
                                    Submissions
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider cursor-pointer" wire:click="sortBy('created_at')">
                                    Created
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($assignments as $assignment)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <td class="px-6 py-4">
                                        <a href="{{ route('teachers.general-exams.show', $assignment) }}" class="font-medium text-gray-900 dark:text-white hover:text-indigo-600 dark:hover:text-indigo-400">
                                            {{ $assignment->title }}
                                        </a>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ ucfirst($assignment->type) }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="space-y-2">
                                            <x-copyable-text :text="$assignment->access_code"
                                                             :show-text="$assignment->access_code"
                                                             class="text-sm" tooltip="Copy code" />
                                            <x-copyable-text :text="route('general-exams.join.code', $assignment->access_code)"
                                                             show-text="Copy Link"
                                                             class="text-sm text-gray-500 dark:text-gray-400"
                                                             tooltip="Copy join URL" />
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full
                                            @if($assignment->status === 'published') bg-green-100 text-green-700 dark:bg-green-900/50 dark:text-green-300
                                            @elseif($assignment->status === 'draft') bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300
                                            @elseif($assignment->status === 'closed') bg-amber-100 text-amber-700 dark:bg-amber-900/50 dark:text-amber-300
                                            @else bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400
                                            @endif">
                                            {{ ucfirst($assignment->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-gray-600 dark:text-gray-400">{{ $assignment->questions_count }}</td>
                                    <td class="px-6 py-4 text-gray-600 dark:text-gray-400">{{ $assignment->submissions_count }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $assignment->created_at->format('M d, Y') }}</td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            @if($assignment->status === 'draft')
                                                <button wire:click="publishAssignment({{ $assignment->id }})" class="p-2 text-green-600 hover:bg-green-50 dark:hover:bg-green-900/30 rounded-lg" title="Publish">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                </button>
                                            @elseif($assignment->status === 'published')
                                                <button wire:click="closeAssignment({{ $assignment->id }})" class="p-2 text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-900/30 rounded-lg" title="Close">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                </button>
                                            @elseif($assignment->status === 'closed')
                                                <button wire:click="reopenAssignment({{ $assignment->id }})" class="p-2 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/30 rounded-lg" title="Reopen">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                                    </svg>
                                                </button>
                                            @endif

                                            <a href="{{ route('teachers.general-exams.results', $assignment) }}" class="p-2 text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 rounded-lg" title="View Results">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                                </svg>
                                            </a>

                                            @if($assignment->delivery_type === 'print')
                                                <a href="{{ route('teachers.general-exams.answer-sheet', $assignment) }}"
                                                   target="_blank"
                                                   class="p-2 text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-900/30 rounded-lg"
                                                   title="Download Answer Sheet">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                                    </svg>
                                                </a>
                                            @endif

                                            <a href="{{ route('teachers.general-exams.edit', $assignment) }}" class="p-2 text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg" title="Edit">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </a>

                                            <button wire:click="duplicateAssignment({{ $assignment->id }})" class="p-2 text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg" title="Duplicate">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                                </svg>
                                            </button>

                                            <button wire:click="confirmDelete({{ $assignment->id }})" class="p-2 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-lg" title="Delete">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $assignments->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="w-16 h-16 mx-auto text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No Exams found</h3>
                    <p class="text-gray-500 dark:text-gray-400 mb-4">Get started by creating your first general exam.</p>
                    <a href="{{ route('teachers.general-exams.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg">
                        Create Exams
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    @if($confirmingDelete)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="fixed inset-0 bg-black/50" wire:click="cancelDelete"></div>
                <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-xl max-w-md w-full p-6 text-center">
                    <div class="w-16 h-16 mx-auto mb-4 bg-red-100 dark:bg-red-900/50 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Delete Exam?</h2>
                    <p class="text-gray-600 dark:text-gray-400 mb-6">This will permanently delete the exam and all its submissions. This action cannot be undone.</p>
                    <div class="flex gap-3">
                        <button wire:click="cancelDelete" class="flex-1 py-3 border border-gray-300 dark:border-gray-600 rounded-xl text-gray-700 dark:text-gray-300">
                            Cancel
                        </button>
                        <button wire:click="deleteAssignment" class="flex-1 py-3 bg-red-600 hover:bg-red-700 text-white rounded-xl">
                            Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Stats Modal -->
    @if($viewingAssignment && $assignmentStats)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="fixed inset-0 bg-black/50" wire:click="closeStats"></div>
                <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-xl max-w-lg w-full p-6">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Exam Statistics</h2>
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                                <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $assignmentStats['total_submissions'] ?? 0 }}</div>
                                <div class="text-sm text-gray-500">Total Submissions</div>
                            </div>
                            <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                                <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($assignmentStats['average_score'] ?? 0, 1) }}%</div>
                                <div class="text-sm text-gray-500">Average Score</div>
                            </div>
                            <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                                <div class="text-2xl font-bold text-green-600">{{ $assignmentStats['completed'] ?? 0 }}</div>
                                <div class="text-sm text-gray-500">Completed</div>
                            </div>
                            <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                                <div class="text-2xl font-bold text-amber-600">{{ $assignmentStats['in_progress'] ?? 0 }}</div>
                                <div class="text-sm text-gray-500">In Progress</div>
                            </div>
                        </div>
                    </div>
                    <button wire:click="closeStats" class="mt-6 w-full py-3 border border-gray-300 dark:border-gray-600 rounded-xl text-gray-700 dark:text-gray-300">
                        Close
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>

@script
<div id="copy-toast" class="hidden fixed bottom-4 right-4 bg-gray-900 text-white px-4 py-2 rounded-lg shadow-lg text-sm z-50 opacity-0 transition-opacity duration-200">
    Copied to clipboard
</div>

@once
<script>
    (function () {
        if (window.__copyAssignment) return;

        const toast = document.getElementById('copy-toast');
        let toastTimer = null;

        function copyText(text) {
            if (!text) return Promise.reject('No text');

            if (navigator.clipboard && window.isSecureContext) {
                return navigator.clipboard.writeText(text);
            }

            return new Promise((resolve, reject) => {
                const textarea = document.createElement('textarea');
                textarea.value = text;
                textarea.style.position = 'fixed';
                textarea.style.top = '-9999px';
                document.body.appendChild(textarea);
                textarea.focus();
                textarea.select();
                try {
                    document.execCommand('copy');
                    resolve();
                } catch (err) {
                    reject(err);
                } finally {
                    document.body.removeChild(textarea);
                }
            });
        }

        function showToast() {
            if (!toast) return;
            toast.classList.remove('hidden', 'opacity-0');
            toast.classList.add('opacity-100');
            clearTimeout(toastTimer);
            toastTimer = setTimeout(() => {
                toast.classList.add('opacity-0');
                setTimeout(() => toast.classList.add('hidden'), 200);
            }, 1400);
        }

        window.__copyAssignment = function (text) {
            copyText(text).then(showToast).catch(() => {});
        };
    })();
</script>
@endonce
@endscript
