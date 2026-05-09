<div class="p-6">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Generate Examination</h2>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Select a subject to create an examination or view your recent examinations</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Left Column: Subject Selection -->
        <div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Select Subject</h3>
            
            @if(empty($groupedSubjects))
                <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                    <p class="text-yellow-800 dark:text-yellow-200">No subjects available. Please set up academic groups, levels, and subjects first.</p>
                </div>
            @else
                <div class="space-y-4" x-data="{ expanded: {} }">
                    @foreach($groupedSubjects as $groupIndex => $group)
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                            <!-- Group Header -->
                            <div class="bg-blue-50 dark:bg-blue-900/20 p-4 cursor-pointer hover:bg-blue-100 dark:hover:bg-blue-900/30 transition"
                                 @click="expanded['group_{{ $groupIndex }}'] = !expanded['group_{{ $groupIndex }}']"
                                 x-init="expanded['group_{{ $groupIndex }}'] = {{ $groupIndex === 0 ? 'true' : 'false' }}">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <span class="text-lg" x-text="expanded['group_{{ $groupIndex }}'] ? '▼' : '▶'"></span>
                                        <div>
                                            <h3 class="text-lg font-semibold text-blue-900 dark:text-blue-200">{{ $group['name'] }}</h3>
                                            @if(isset($group['tag']))
                                                <p class="text-sm text-blue-600 dark:text-blue-400">{{ $group['tag'] }}</p>
                                            @endif
                                        </div>
                                    </div>
                                    <span class="text-sm text-blue-600 dark:text-blue-400">
                                        {{ count($group['levels']) }} {{ Str::plural('Level', count($group['levels'])) }}
                                    </span>
                                </div>
                            </div>

                            <!-- Levels -->
                            <div x-show="expanded['group_{{ $groupIndex }}']" x-collapse>
                                @foreach($group['levels'] as $levelIndex => $level)
                                    <div class="border-t border-gray-200 dark:border-gray-700">
                                        <!-- Level Header -->
                                        <div class="bg-green-50 dark:bg-green-900/20 p-3 pl-8 cursor-pointer hover:bg-green-100 dark:hover:bg-green-900/30 transition"
                                             @click="expanded['level_{{ $groupIndex }}_{{ $levelIndex }}'] = !expanded['level_{{ $groupIndex }}_{{ $levelIndex }}']"
                                             x-init="expanded['level_{{ $groupIndex }}_{{ $levelIndex }}'] = {{ $groupIndex === 0 && $levelIndex === 0 ? 'true' : 'false' }}">
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center gap-3">
                                                    <span x-text="expanded['level_{{ $groupIndex }}_{{ $levelIndex }}'] ? '▼' : '▶'"></span>
                                                    <div>
                                                        <h4 class="font-medium text-green-900 dark:text-green-200">{{ $level['name'] }}</h4>
                                                        @if(isset($level['label']))
                                                            <p class="text-sm text-green-600 dark:text-green-400">{{ $level['label'] }}</p>
                                                        @endif
                                                    </div>
                                                </div>
                                                <span class="text-sm text-green-600 dark:text-green-400">
                                                    {{ count($level['subjects']) }} {{ Str::plural('Subject', count($level['subjects'])) }}
                                                </span>
                                            </div>
                                        </div>

                                        <!-- Subjects -->
                                        <div x-show="expanded['level_{{ $groupIndex }}_{{ $levelIndex }}']" x-collapse>
                                            @if(empty($level['subjects']))
                                                <div class="p-4 pl-12 text-sm text-gray-500 dark:text-gray-400">
                                                    No subjects available for this level
                                                </div>
                                            @else
                                                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                                                    @foreach($level['subjects'] as $subject)
                                                        <a href="{{ $subject['create_url'] }}"
                                                           class="block p-4 pl-12 hover:bg-purple-50 dark:hover:bg-purple-900/20 transition group">
                                                            <div class="flex items-center justify-between">
                                                                <div class="flex items-center gap-3">
                                                                    <svg class="w-5 h-5 text-purple-500 dark:text-purple-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor">
                                                                        <path d="M5 0h8a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2 2 2 0 0 1-2 2H3a2 2 0 0 1-2-2h1a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1H3a1 1 0 0 0-1 1H1a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v9a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H5a1 1 0 0 0-1 1H3a2 2 0 0 1 2-2z"/>
                                                                    </svg>
                                                                    <div>
                                                                        <h5 class="font-medium text-gray-900 dark:text-white group-hover:text-purple-600 dark:group-hover:text-purple-400">
                                                                            {{ $subject['name'] }}
                                                                        </h5>
                                                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                                                            Code: {{ $subject['code'] }}
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                                <svg class="w-5 h-5 text-gray-400 group-hover:text-purple-600 dark:group-hover:text-purple-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor">
                                                                    <path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z"/>
                                                                </svg>
                                                            </div>
                                                        </a>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Right Column: Recent Examinations -->
        <div>
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Your Recent Examinations</h3>
            </div>

            <!-- Filters -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 mb-4">
                <div class="space-y-3">
                    <!-- Search -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Search</label>
                        <input type="text" 
                               wire:model.live.debounce.300ms="searchTerm" 
                               placeholder="Search by title or subject..."
                               class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <!-- Subject Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Filter by Subject</label>
                        <select wire:model.live="filterSubject"
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">All Subjects</option>
                            @foreach($subjectsForFilter as $subject)
                                <option value="{{ $subject->id }}">
                                    {{ $subject->academicLevel->academicGroup->name }} - {{ $subject->academicLevel->name }} - {{ $subject->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    @if($searchTerm || $filterSubject)
                        <button wire:click="clearFilters" 
                                class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300">
                            Clear Filters
                        </button>
                    @endif
                </div>
            </div>

            <!-- Examinations List -->
            <div class="space-y-3">
                @forelse($recentExaminations as $examination)
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 hover:shadow-md transition">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-900 dark:text-white mb-1">
                                    {{ $examination->title }}
                                </h4>
                                <div class="space-y-1 text-sm text-gray-600 dark:text-gray-400">
                                    <p>
                                        <span class="font-medium">Subject:</span> 
                                        {{ $examination->academicSubject->name }} ({{ $examination->academicSubject->code }})
                                    </p>
                                    <p>
                                        <span class="font-medium">Level:</span> 
                                        {{ $examination->academicSubject->academicLevel->name }}
                                    </p>
                                    <p>
                                        <span class="font-medium">Group:</span> 
                                        {{ $examination->academicSubject->academicLevel->academicGroup->name }}
                                    </p>
                                    <p>
                                        <span class="font-medium">Created:</span> 
                                        {{ $examination->created_at->diffForHumans() }}
                                    </p>
                                    @if($examination->duration)
                                        <p>
                                            <span class="font-medium">Duration:</span> 
                                            {{ $examination->duration }} minutes
                                        </p>
                                    @endif
                                </div>
                            </div>
                            <div class="flex flex-col gap-2 ml-4">
                                <a href="{{ route('examinations.show', [
                                    'academic_group' => $examination->academicSubject->academicLevel->academicGroup->id,
                                    'academic_level' => $examination->academicSubject->academicLevel->id,
                                    'academic_subject' => $examination->academicSubject->id,
                                    'examination' => $examination->id
                                ]) }}" 
                                   class="inline-flex items-center px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded transition">
                                    View
                                </a>
                                <a href="{{ route('examinations.answers', [
                                    'academic_group' => $examination->academicSubject->academicLevel->academicGroup->id,
                                    'academic_level' => $examination->academicSubject->academicLevel->id,
                                    'academic_subject' => $examination->academicSubject->id,
                                    'examination' => $examination->id
                                ]) }}" 
                                   class="inline-flex items-center px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded transition">
                                    Answers
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-8 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No examinations found</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            @if($searchTerm || $filterSubject)
                                No examinations match your filters. Try adjusting your search.
                            @else
                                You haven't created any examinations yet. Select a subject to get started.
                            @endif
                        </p>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($recentExaminations->hasPages())
                <div class="mt-4">
                    {{ $recentExaminations->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
