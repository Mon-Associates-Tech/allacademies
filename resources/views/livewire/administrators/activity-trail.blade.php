<section class="p-4 min-h-screen bg-gradient-to-br from-gray-50 via-blue-50 to-indigo-100 dark:from-gray-900 dark:via-blue-900 dark:to-indigo-900">
    <div class="max-w-7xl mx-auto space-y-8">
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div class="space-y-2">
                <div class="flex items-center gap-4">
                    <h1 class="text-3xl font-bold bg-gradient-to-r from-indigo-600 via-purple-600 to-blue-600 dark:from-indigo-400 dark:via-purple-400 dark:to-blue-400 bg-clip-text text-transparent">
                        Activity Trail
                    </h1>
                    <!-- Focus Toggle -->
                    <button
                        wire:click="toggleQuestionFocus"
                        class="inline-flex items-center px-3 py-1 text-xs font-medium rounded-full transition-all duration-200 {{ $showOnlyQuestions
                            ? 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-300'
                            : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' }}"
                    >
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            @if($showOnlyQuestions)
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            @else
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            @endif
                        </svg>
                        {{ $showOnlyQuestions ? 'Questions Only' : 'All Modules' }}
                    </button>
                </div>
                <p class="text-gray-600 dark:text-gray-300">
                    {{ $showOnlyQuestions
                        ? 'Monitor question management activities and track changes across all question types'
                        : 'Track and monitor all system activities across your entire platform' }}
                </p>
            </div>

            <!-- Quick Stats -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">{{ number_format($stats['total']) }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">Total Activities</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ number_format($stats['created']) }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">Created</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ number_format($stats['updated']) }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">Updated</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="text-2xl font-bold text-red-600 dark:text-red-400">{{ number_format($stats['deleted']) }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">Deleted</div>
                </div>
            </div>
        </div>

        <!-- Module Stats (when not in question-only mode) -->
        @if(!$showOnlyQuestions && !empty($moduleStats))
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Activity by Module</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                        @foreach($moduleStats as $module => $count)
                            <div class="text-center p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                <div class="text-xl font-bold text-indigo-600 dark:text-indigo-400">{{ number_format($count) }}</div>
                                <div class="text-xs text-gray-600 dark:text-gray-400 capitalize">{{ str_replace('_', ' ', $module) }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <!-- Filters Section -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                        </svg>
                        Filters & Search
                    </h2>
                    <button
                        wire:click="resetFilters"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors duration-200"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Reset Filters
                    </button>
                </div>
            </div>

            <div class="p-6 space-y-6">
                <!-- Search and Date Range -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                    <div class="relative">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Search</label>
                        <div class="relative">
                            <input
                                type="text"
                                wire:model.live.debounce.300ms="search"
                                placeholder="Search activities, users, modules..."
                                class="w-full pl-10 pr-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-transparent text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 transition-all duration-200"
                            >
                            <svg class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 transform -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">From Date</label>
                        <input
                            type="date"
                            wire:model.live="dateFrom"
                            class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-transparent text-gray-900 dark:text-white transition-all duration-200"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">To Date</label>
                        <input
                            type="date"
                            wire:model.live="dateTo"
                            class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-transparent text-gray-900 dark:text-white transition-all duration-200"
                        >
                    </div>
                </div>

                <!-- Filter Dropdowns -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">User</label>
                        <select
                            wire:model.live="selectedUser"
                            class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-transparent text-gray-900 dark:text-white transition-all duration-200"
                        >
                            <option value="">All Users</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                            @endforeach
                        </select>
                    </div>

                    @if($showOnlyQuestions)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Question Type</label>
                            <select
                                wire:model.live="selectedQuestionType"
                                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-transparent text-gray-900 dark:text-white transition-all duration-200"
                            >
                                <option value="">All Question Types</option>
                                @foreach($questionTypes as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    @else
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Module</label>
                            <select
                                wire:model.live="selectedModule"
                                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-transparent text-gray-900 dark:text-white transition-all duration-200"
                            >
                                <option value="">All Modules</option>
                                @foreach($modules as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Action</label>
                        <select
                            wire:model.live="selectedAction"
                            class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-transparent text-gray-900 dark:text-white transition-all duration-200"
                        >
                            <option value="">All Actions</option>
                            @foreach($actionTypes as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Per Page</label>
                        <select
                            wire:model.live="perPage"
                            class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-transparent text-gray-900 dark:text-white transition-all duration-200"
                        >
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Results Section -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
            <!-- Table Header -->
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a4 4 0 01-4-4V5a4 4 0 014-4h10a4 4 0 014 4v12a4 4 0 01-4 4z"></path>
                        </svg>
                        Activity Results ({{ $activities->total() }} total)
                    </h3>
                    @if($showOnlyQuestions)
                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            Showing question management activities only
                        </div>
                    @else
                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            Showing all system activities
                        </div>
                    @endif
                </div>
            </div>

            <!-- Activity Table -->
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-700/30">
                    <tr>
                        <th class="px-6 py-4 text-left">
                            <button
                                wire:click="sortBy('created_at')"
                                class="flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors duration-200"
                            >
                                Date & Time
                                @if($sortBy === 'created_at')
                                    <svg class="w-4 h-4 {{ $sortDirection === 'asc' ? 'transform rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                @endif
                            </button>
                        </th>
                        <th class="px-6 py-4 text-left text-sm font-medium text-gray-700 dark:text-gray-300">User</th>
                        <th class="px-6 py-4 text-left text-sm font-medium text-gray-700 dark:text-gray-300">Action</th>
                        <th class="px-6 py-4 text-left text-sm font-medium text-gray-700 dark:text-gray-300">
                            @if($showOnlyQuestions) Question Type @else Module @endif
                        </th>
                        <th class="px-6 py-4 text-left text-sm font-medium text-gray-700 dark:text-gray-300">Details</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($activities as $activity)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-200">
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                        <span class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $activity->created_at->format('M d, Y') }}
                                        </span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $activity->created_at->format('h:i A') }}
                                        </span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <x-avatar class="w-8 h-8" name="{{$activity->causer->name ?? 'System'}}" avatar="{{$activity->causer->avatar ?? ''}}">

                                    </x-avatar>
                                    <div class="flex flex-col">
                                            <span class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $activity->causer->name ?? 'System User' }}
                                            </span>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ $activity->causer->email ?? '' }}
                                            </span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $actionClass = match($activity->description) {
                                        'created' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                                        'updated' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
                                        'deleted' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
                                        default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'
                                    };
                                @endphp
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $actionClass }}">
                                        {{ $actionTypes[$activity->description] ?? ucfirst($activity->description) }}
                                    </span>
                            </td>
                            <td class="px-6 py-4">
                                @if($showOnlyQuestions)
                                    @php
                                        $questionType = data_get($activity->properties, 'question_type', 'unknown');
                                        if ($questionType === 'unknown'){
                                            $questionType = $activity->subject_type;
                                        }
                                        $typeClass = match($questionType) {
                                            'essay' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
                                            'multiple_choice' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300',
                                            'true_or_false' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-300',
                                            default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'
                                        };
                                    @endphp
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $typeClass }}">
                                            {{ $questionTypes[$questionType] ?? ucwords(str_replace('_', ' ', $questionType)) }}
                                        </span>
                                @else
                                    @php
                                        $module = $activity->log_name ?: data_get($activity->properties, 'module', 'system');
                                        $moduleClass = match($module) {
                                            'question_management', 'questions' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
                                            'users', 'user_management' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                                            'authentication' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
                                            'system' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                                            default => 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300'
                                        };
                                    @endphp
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $moduleClass }}">
                                            {{ $modules[$module] ?? ucwords(str_replace('_', ' ', $module)) }}
                                        </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="space-y-1">
                                    @if($showOnlyQuestions)
                                        @if(data_get($activity->properties, 'difficulty_level'))
                                            <div class="text-xs text-gray-600 dark:text-gray-400">
                                                <span class="font-medium">Difficulty:</span>
                                                {{ data_get($activity->properties, 'difficulty_level') }}
                                            </div>
                                        @endif
                                        @if(data_get($activity->properties, 'score'))
                                            <div class="text-xs text-gray-600 dark:text-gray-400">
                                                <span class="font-medium">Score:</span>
                                                {{ data_get($activity->properties, 'score') }}
                                            </div>
                                        @endif
                                    @endif

                                    <!-- Show change summary if available -->
                                    @if($activity->description === 'updated')
                                        @if(data_get($activity->properties, 'change_summary'))
                                            <div class="text-xs text-blue-600 dark:text-blue-400 font-medium">
                                                {{ data_get($activity->properties, 'change_summary') }}
                                            </div>
                                        @elseif(data_get($activity->properties, 'changed_fields_readable'))
                                            <div class="text-xs text-blue-600 dark:text-blue-400">
                                                <span class="font-medium">Modified:</span>
                                                {{ implode(', ', array_slice(data_get($activity->properties, 'changed_fields_readable', []), 0, 2)) }}
                                                @if(count(data_get($activity->properties, 'changed_fields_readable', [])) > 2)
                                                    <span class="text-gray-500">+ {{ count(data_get($activity->properties, 'changed_fields_readable', [])) - 2 }} more</span>
                                                @endif
                                            </div>
                                        @elseif(data_get($activity->properties, 'changes'))
                                            <div class="text-xs text-gray-600 dark:text-gray-400">
                                                <span class="font-medium">Changes:</span>
                                                {{ count(data_get($activity->properties, 'changes', [])) }} field(s)
                                            </div>
                                        @endif
                                    @endif

                                    @if($activity->subject_type)
                                        <div class="text-xs text-gray-500 dark:text-gray-500">
                                            {{ class_basename($activity->subject_type) }} #{{ $activity->subject_id }}
                                        </div>
                                    @endif

                                    @if(data_get($activity->properties, 'metadata.subtopic_name'))
                                        <div class="text-xs text-gray-500 dark:text-gray-500">
                                            <span class="font-medium">Subtopic:</span>
                                            {{ data_get($activity->properties, 'metadata.subtopic_name') }}
                                        </div>
                                    @endif

                                    <!-- View Details Button -->
                                    <div class="mt-2">
                                        <button
                                            wire:click="showActivityDetails({{ $activity->id }})"
                                            class="inline-flex items-center px-2 py-1 text-xs font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 rounded-md transition-colors duration-200"
                                        >
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                            View Details
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center gap-4">
                                    <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                        </svg>
                                    </div>
                                    <div class="text-center">
                                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No activities found</h3>
                                        <p class="text-gray-500 dark:text-gray-400">
                                            @if($showOnlyQuestions)
                                                No question activities match your current filters. Try adjusting your search criteria or date range.
                                            @else
                                                No system activities match your current filters. Try adjusting your search criteria or date range.
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($activities->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="text-sm text-gray-700 dark:text-gray-300">
                            Showing {{ $activities->firstItem() }} to {{ $activities->lastItem() }} of {{ $activities->total() }} results
                        </div>
                        <div class="flex items-center gap-2">
                            {{ $activities->links('custom.pagination') }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Activity Details Modal - Replace the existing modal -->
    @if($showActivityModal && $selectedActivityId)
        @php
            $selectedActivity = \Spatie\Activitylog\Models\Activity::with(['causer', 'subject'])->find($selectedActivityId);
            $properties = $selectedActivity?->properties ?? [];

            // Initialize formatted properties array
            $formattedProperties = [];

            // Add basic question properties
            if (isset($properties['question_type'])) {
                $formattedProperties['Question Type'] = match($properties['question_type']) {
                    'essay' => 'Essay Question',
                    'multiple_choice' => 'Multiple Choice Question',
                    'true_or_false' => 'True or False Question',
                    default => ucwords(str_replace('_', ' ', $properties['question_type']))
                };
            }

            if (isset($properties['difficulty_level'])) {
                $formattedProperties['Difficulty Level'] = $properties['difficulty_level'];
            }

            if (isset($properties['score'])) {
                $formattedProperties['Score'] = $properties['score'];
            }

            if (isset($properties['academic_topic_id'])) {
                $formattedProperties['Academic Topic ID'] = $properties['academic_topic_id'];
            }

            if (isset($properties['academic_subtopic_id'])) {
                $formattedProperties['Academic Subtopic ID'] = $properties['academic_subtopic_id'];
            }

            if (isset($properties['metadata'])) {
                $metadata = $properties['metadata'];
                if (isset($metadata['subtopic_name'])) {
                    $formattedProperties['Subtopic'] = $metadata['subtopic_name'];
                }
                if (isset($metadata['topic_name'])) {
                    $formattedProperties['Topic'] = $metadata['topic_name'];
                }
            }

            // Always add raw properties for debugging
            $formattedProperties['Raw Properties'] = $properties;
        @endphp

        @if($selectedActivity)

            <x-modal-component  name="activity-trail-details-modal" size="3xl">
                <x-slot:header>
                    <div class="px-6 py-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a4 4 0 01-4-4V5a4 4 0 014-4h10a4 4 0 014 4v12a4 4 0 01-4 4z"></path>
                                    </svg>
                                </div>
                                Activity Details
                            </h3>
                        </div>
                    </div>
                </x-slot:header>
                    <div class="px-6 py-6 max-h-96 overflow-y-auto">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <!-- Basic Information -->
                            <div class="space-y-4">
                                <h4 class="text-lg font-medium text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2">
                                    Basic Information
                                </h4>

                                <div class="space-y-3">
                                    <div class="flex justify-between">
                                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Date & Time:</span>
                                        <span class="text-sm text-gray-900 dark:text-white">
                                        {{ $selectedActivity->created_at->format('M d, Y - h:i A') }}
                                    </span>
                                    </div>

                                    <div class="flex justify-between">
                                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">User:</span>
                                        <div class="text-right">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $selectedActivity->causer->name ?? 'System User' }}
                                            </div>
                                            @if($selectedActivity->causer?->email)
                                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                                    {{ $selectedActivity->causer->email }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="flex justify-between">
                                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Action:</span>
                                        @php
                                            $actionClass = match($selectedActivity->description) {
                                                'created' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                                                'updated' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
                                                'deleted' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
                                                default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'
                                            };
                                        @endphp
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $actionClass }}">
                                        {{ $actionTypes[$selectedActivity->description] ?? ucfirst($selectedActivity->description) }}
                                    </span>
                                    </div>

                                    @if($selectedActivity->subject_type)
                                        <div class="flex justify-between">
                                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Subject:</span>
                                            <span class="text-sm text-gray-900 dark:text-white">
                                            {{ class_basename($selectedActivity->subject_type) }} #{{ $selectedActivity->subject_id }}
                                        </span>
                                        </div>
                                    @endif

                                    <div class="flex justify-between">
                                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Log Name:</span>
                                        <span class="text-sm text-gray-900 dark:text-white">
                                        {{ $selectedActivity->log_name }}
                                    </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Properties & Details -->
                            <div class="space-y-4">
                                <h4 class="text-lg font-medium text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2">
                                    Properties & Details
                                </h4>

                                <div class="space-y-4">
                                    <!-- Change Summary -->
                                    @if(isset($properties['change_summary']))
                                        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-lg p-3">
                                            <div class="flex items-center gap-2">
                                                <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                <span class="text-sm font-medium text-blue-800 dark:text-blue-300">Change Summary</span>
                                            </div>
                                            <p class="text-sm text-blue-700 dark:text-blue-300 mt-1">{{ $properties['change_summary'] }}</p>
                                        </div>
                                    @endif

                                    <!-- Changed Fields -->
                                    @if(isset($properties['changed_fields_readable']) && !empty($properties['changed_fields_readable']))
                                        <div>
                                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400 block mb-2">Fields Modified:</span>
                                            <div class="flex flex-wrap gap-2">
                                                @foreach($properties['changed_fields_readable'] as $fieldName)
                                                    <span class="inline-flex px-2 py-1 text-xs font-medium bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-300 rounded-full">
                                                    {{ $fieldName }}
                                                </span>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Detailed Changes -->
                                    @if(isset($properties['changes']) && is_array($properties['changes']))
                                        <div>
                                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400 block mb-3">Detailed Changes:</span>
                                            <div class="space-y-3">
                                                @foreach($properties['changes'] as $field => $change)
                                                    @if(is_array($change) && (isset($change['old']) || isset($change['new'])))
                                                        <div class="bg-white dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-lg p-4">
                                                            <!-- Field Name -->
                                                            <div class="flex items-center gap-2 mb-3">
                                                                <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                                                                </svg>
                                                                <span class="text-sm font-semibold text-gray-900 dark:text-white">
                                                                {{ ucfirst(str_replace('_', ' ', $field)) }}
                                                            </span>
                                                            </div>

                                                            <!-- Before/After Comparison -->
                                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                                <!-- Old Value -->
                                                                <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 rounded-lg p-3">
                                                                    <div class="flex items-center gap-2 mb-2">
                                                                        <svg class="w-3 h-3 text-red-600 dark:text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                                                        </svg>
                                                                        <span class="text-xs font-medium text-red-800 dark:text-red-300">Previous Value</span>
                                                                    </div>
                                                                    <div class="text-sm text-red-700 dark:text-red-200 break-words">
                                                                        @if(isset($change['old']) && $change['old'] !== null && $change['old'] !== '')
                                                                            @if(is_string($change['old']) && strlen($change['old']) > 100)
                                                                                <div class="max-h-20 overflow-y-auto text-xs">{{ $change['old'] }}</div>
                                                                            @else
                                                                                {{ is_array($change['old']) ? json_encode($change['old'], JSON_PRETTY_PRINT) : $change['old'] }}
                                                                            @endif
                                                                        @else
                                                                            <em class="text-red-500 dark:text-red-400">Empty or null</em>
                                                                        @endif
                                                                    </div>
                                                                </div>

                                                                <!-- New Value -->
                                                                <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 rounded-lg p-3">
                                                                    <div class="flex items-center gap-2 mb-2">
                                                                        <svg class="w-3 h-3 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                                        </svg>
                                                                        <span class="text-xs font-medium text-green-800 dark:text-green-300">New Value</span>
                                                                    </div>
                                                                    <div class="text-sm text-green-700 dark:text-green-200 break-words">
                                                                        @if(isset($change['new']) && $change['new'] !== null && $change['new'] !== '')
                                                                            @if(is_string($change['new']) && strlen($change['new']) > 100)
                                                                                <div class="max-h-20 overflow-y-auto text-xs">{{ $change['new'] }}</div>
                                                                            @else
                                                                                {{ is_array($change['new']) ? json_encode($change['new'], JSON_PRETTY_PRINT) : $change['new'] }}
                                                                            @endif
                                                                        @else
                                                                            <em class="text-green-500 dark:text-green-400">Empty or null</em>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Other Properties -->
                                    @foreach($formattedProperties as $key => $value)
                                        @if($key !== 'Raw Properties' && !is_array($value))
                                            <div class="flex justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                                                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ $key }}:</span>
                                                <span class="text-sm text-gray-900 dark:text-white">{{ $value }}</span>
                                            </div>
                                        @endif
                                    @endforeach

                                    <!-- Question Data for Deleted Items -->
                                    @if(isset($properties['question_data']) && is_array($properties['question_data']))
                                        <div class="border-t border-gray-200 dark:border-gray-600 pt-4">
                                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400 block mb-2">Question Data (Deleted):</span>
                                            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3 space-y-2">
                                                @foreach($properties['question_data'] as $dataKey => $dataValue)
                                                    @if($dataValue && !in_array($dataKey, ['created_at', 'updated_at']))
                                                        <div class="flex justify-between text-xs">
                                                        <span class="font-medium text-gray-700 dark:text-gray-300">
                                                            {{ ucfirst(str_replace('_', ' ', $dataKey)) }}:
                                                        </span>
                                                            <span class="text-gray-600 dark:text-gray-400 max-w-xs truncate">
                                                            {{ is_string($dataValue) && strlen($dataValue) > 50 ? substr($dataValue, 0, 50) . '...' : (is_string($dataValue) ? $dataValue : json_encode($dataValue)) }}
                                                        </span>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Debug Section -->
                                    <div class="border-t border-gray-200 dark:border-gray-600 pt-3 mt-4">
                                        <details class="group">
                                            <summary class="text-sm font-medium text-gray-500 dark:text-gray-400 cursor-pointer hover:text-gray-700 dark:hover:text-gray-300 flex items-center gap-2">
                                                <svg class="w-4 h-4 transform group-open:rotate-90 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                </svg>
                                                Raw Properties (Debug)
                                            </summary>
                                            <div class="mt-2 bg-gray-900 dark:bg-gray-800 rounded-lg p-3">
                                                <pre class="text-xs text-green-400 overflow-x-auto whitespace-pre-wrap">{{ json_encode($properties, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                                            </div>
                                        </details>
                                    </div>

                                    <!-- Show if no properties at all -->
                                    @if(empty($properties))
                                        <div class="text-sm text-gray-500 dark:text-gray-400 italic text-center py-4">
                                            No properties data available for this activity.
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                <x-slot:footer>

                </x-slot:footer>
            </x-modal-component>
            <div class="fixed inset-0 hidden z-50 overflow-y-auto" wire:keydown.escape="closeActivityModal">
                <!-- Backdrop -->
                <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity"
                     wire:click="closeActivityModal"></div>

                <!-- Modal Container -->
                <div class="flex min-h-full items-center justify-center p-4">
                    <div class="relative w-full max-w-4xl bg-white dark:bg-gray-800 rounded-2xl shadow-2xl transform transition-all">
                        <!-- Modal Header -->
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <div class="flex items-center justify-between">
                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a4 4 0 01-4-4V5a4 4 0 014-4h10a4 4 0 014 4v12a4 4 0 01-4 4z"></path>
                                        </svg>
                                    </div>
                                    Activity Details
                                </h3>
                                <button
                                    wire:click="closeActivityModal"
                                    class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Modal Content -->
                        <div class="px-6 py-6 max-h-96 overflow-y-auto">
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <!-- Basic Information -->
                                <div class="space-y-4">
                                    <h4 class="text-lg font-medium text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2">
                                        Basic Information
                                    </h4>

                                    <div class="space-y-3">
                                        <div class="flex justify-between">
                                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Date & Time:</span>
                                            <span class="text-sm text-gray-900 dark:text-white">
                                        {{ $selectedActivity->created_at->format('M d, Y - h:i A') }}
                                    </span>
                                        </div>

                                        <div class="flex justify-between">
                                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">User:</span>
                                            <div class="text-right">
                                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ $selectedActivity->causer->name ?? 'System User' }}
                                                </div>
                                                @if($selectedActivity->causer?->email)
                                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                                        {{ $selectedActivity->causer->email }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="flex justify-between">
                                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Action:</span>
                                            @php
                                                $actionClass = match($selectedActivity->description) {
                                                    'created' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                                                    'updated' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
                                                    'deleted' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
                                                    default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'
                                                };
                                            @endphp
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $actionClass }}">
                                        {{ $actionTypes[$selectedActivity->description] ?? ucfirst($selectedActivity->description) }}
                                    </span>
                                        </div>

                                        @if($selectedActivity->subject_type)
                                            <div class="flex justify-between">
                                                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Subject:</span>
                                                <span class="text-sm text-gray-900 dark:text-white">
                                            {{ class_basename($selectedActivity->subject_type) }} #{{ $selectedActivity->subject_id }}
                                        </span>
                                            </div>
                                        @endif

                                        <div class="flex justify-between">
                                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Log Name:</span>
                                            <span class="text-sm text-gray-900 dark:text-white">
                                        {{ $selectedActivity->log_name }}
                                    </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Properties & Details -->
                                <div class="space-y-4">
                                    <h4 class="text-lg font-medium text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2">
                                        Properties & Details
                                    </h4>

                                    <div class="space-y-4">
                                        <!-- Change Summary -->
                                        @if(isset($properties['change_summary']))
                                            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-lg p-3">
                                                <div class="flex items-center gap-2">
                                                    <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    <span class="text-sm font-medium text-blue-800 dark:text-blue-300">Change Summary</span>
                                                </div>
                                                <p class="text-sm text-blue-700 dark:text-blue-300 mt-1">{{ $properties['change_summary'] }}</p>
                                            </div>
                                        @endif

                                        <!-- Changed Fields -->
                                        @if(isset($properties['changed_fields_readable']) && !empty($properties['changed_fields_readable']))
                                            <div>
                                                <span class="text-sm font-medium text-gray-500 dark:text-gray-400 block mb-2">Fields Modified:</span>
                                                <div class="flex flex-wrap gap-2">
                                                    @foreach($properties['changed_fields_readable'] as $fieldName)
                                                        <span class="inline-flex px-2 py-1 text-xs font-medium bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-300 rounded-full">
                                                    {{ $fieldName }}
                                                </span>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif

                                        <!-- Detailed Changes -->
                                        @if(isset($properties['changes']) && is_array($properties['changes']))
                                            <div>
                                                <span class="text-sm font-medium text-gray-500 dark:text-gray-400 block mb-3">Detailed Changes:</span>
                                                <div class="space-y-3">
                                                    @foreach($properties['changes'] as $field => $change)
                                                        @if(is_array($change) && (isset($change['old']) || isset($change['new'])))
                                                            <div class="bg-white dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-lg p-4">
                                                                <!-- Field Name -->
                                                                <div class="flex items-center gap-2 mb-3">
                                                                    <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                                                                    </svg>
                                                                    <span class="text-sm font-semibold text-gray-900 dark:text-white">
                                                                {{ ucfirst(str_replace('_', ' ', $field)) }}
                                                            </span>
                                                                </div>

                                                                <!-- Before/After Comparison -->
                                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                                    <!-- Old Value -->
                                                                    <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 rounded-lg p-3">
                                                                        <div class="flex items-center gap-2 mb-2">
                                                                            <svg class="w-3 h-3 text-red-600 dark:text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                                                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                                                            </svg>
                                                                            <span class="text-xs font-medium text-red-800 dark:text-red-300">Previous Value</span>
                                                                        </div>
                                                                        <div class="text-sm text-red-700 dark:text-red-200 break-words">
                                                                            @if(isset($change['old']) && $change['old'] !== null && $change['old'] !== '')
                                                                                @if(is_string($change['old']) && strlen($change['old']) > 100)
                                                                                    <div class="max-h-20 overflow-y-auto text-xs">{{ $change['old'] }}</div>
                                                                                @else
                                                                                    {{ is_array($change['old']) ? json_encode($change['old'], JSON_PRETTY_PRINT) : $change['old'] }}
                                                                                @endif
                                                                            @else
                                                                                <em class="text-red-500 dark:text-red-400">Empty or null</em>
                                                                            @endif
                                                                        </div>
                                                                    </div>

                                                                    <!-- New Value -->
                                                                    <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 rounded-lg p-3">
                                                                        <div class="flex items-center gap-2 mb-2">
                                                                            <svg class="w-3 h-3 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                                            </svg>
                                                                            <span class="text-xs font-medium text-green-800 dark:text-green-300">New Value</span>
                                                                        </div>
                                                                        <div class="text-sm text-green-700 dark:text-green-200 break-words">
                                                                            @if(isset($change['new']) && $change['new'] !== null && $change['new'] !== '')
                                                                                @if(is_string($change['new']) && strlen($change['new']) > 100)
                                                                                    <div class="max-h-20 overflow-y-auto text-xs">{{ $change['new'] }}</div>
                                                                                @else
                                                                                    {{ is_array($change['new']) ? json_encode($change['new'], JSON_PRETTY_PRINT) : $change['new'] }}
                                                                                @endif
                                                                            @else
                                                                                <em class="text-green-500 dark:text-green-400">Empty or null</em>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif

                                        <!-- Other Properties -->
                                        @foreach($formattedProperties as $key => $value)
                                            @if($key !== 'Raw Properties' && !is_array($value))
                                                <div class="flex justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                                                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ $key }}:</span>
                                                    <span class="text-sm text-gray-900 dark:text-white">{{ $value }}</span>
                                                </div>
                                            @endif
                                        @endforeach

                                        <!-- Question Data for Deleted Items -->
                                        @if(isset($properties['question_data']) && is_array($properties['question_data']))
                                            <div class="border-t border-gray-200 dark:border-gray-600 pt-4">
                                                <span class="text-sm font-medium text-gray-500 dark:text-gray-400 block mb-2">Question Data (Deleted):</span>
                                                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3 space-y-2">
                                                    @foreach($properties['question_data'] as $dataKey => $dataValue)
                                                        @if($dataValue && !in_array($dataKey, ['created_at', 'updated_at']))
                                                            <div class="flex justify-between text-xs">
                                                        <span class="font-medium text-gray-700 dark:text-gray-300">
                                                            {{ ucfirst(str_replace('_', ' ', $dataKey)) }}:
                                                        </span>
                                                                <span class="text-gray-600 dark:text-gray-400 max-w-xs truncate">
                                                            {{ is_string($dataValue) && strlen($dataValue) > 50 ? substr($dataValue, 0, 50) . '...' : (is_string($dataValue) ? $dataValue : json_encode($dataValue)) }}
                                                        </span>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif

                                        <!-- Debug Section -->
                                        <div class="border-t border-gray-200 dark:border-gray-600 pt-3 mt-4">
                                            <details class="group">
                                                <summary class="text-sm font-medium text-gray-500 dark:text-gray-400 cursor-pointer hover:text-gray-700 dark:hover:text-gray-300 flex items-center gap-2">
                                                    <svg class="w-4 h-4 transform group-open:rotate-90 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                    </svg>
                                                    Raw Properties (Debug)
                                                </summary>
                                                <div class="mt-2 bg-gray-900 dark:bg-gray-800 rounded-lg p-3">
                                                    <pre class="text-xs text-green-400 overflow-x-auto whitespace-pre-wrap">{{ json_encode($properties, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                                                </div>
                                            </details>
                                        </div>

                                        <!-- Show if no properties at all -->
                                        @if(empty($properties))
                                            <div class="text-sm text-gray-500 dark:text-gray-400 italic text-center py-4">
                                                No properties data available for this activity.
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Footer -->
                        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50 rounded-b-2xl">
                            <div class="flex justify-end">
                                <button
                                    wire:click="closeActivityModal"
                                    class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors duration-200"
                                >
                                    Close
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endif
    <style>
        /* Custom scrollbar for dark mode */
        .dark ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        .dark ::-webkit-scrollbar-track {
            background: rgb(55 65 81);
        }

        .dark ::-webkit-scrollbar-thumb {
            background: rgb(107 114 128);
            border-radius: 4px;
        }

        .dark ::-webkit-scrollbar-thumb:hover {
            background: rgb(156 163 175);
        }

        /* Loading animations */
        [wire\:loading] {
            opacity: 0.6;
            pointer-events: none;
        }

        /* Smooth transitions for all interactive elements */
        button, select, input {
            transition: all 0.2s ease-in-out;
        }
    </style>
</section>
