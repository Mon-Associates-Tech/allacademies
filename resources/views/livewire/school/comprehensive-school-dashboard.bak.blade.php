<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- Header Section -->
    <div class="bg-white dark:bg-gray-800 shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <!-- School Logo/Initials -->
                    <div class="flex-shrink-0">
                        @if($schoolLogo)
                            <img src="{{ $schoolLogo }}" alt="{{ $schoolName }}" class="h-16 w-16 rounded-full object-cover">
                        @else
                            <div class="h-16 w-16 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-xl font-bold">
                                {{ $schoolInitials }}
                            </div>
                        @endif
                    </div>

                    <!-- School Info -->
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $schoolName }}</h1>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $schoolCode }} • {{ $schoolType }}</p>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center space-x-3">
                    <button wire:click="refreshData" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Refresh
                    </button>
                    <a href="{{ route('school-settings.index') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-indigo-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Settings
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Overview -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Students -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 font-medium">Students</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $stats['active_students'] }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">of {{ $stats['total_students'] }} total</p>
                    </div>
                    <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                        <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Teachers -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 font-medium">Teachers</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $stats['active_teachers'] }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">of {{ $stats['total_teachers'] }} total</p>
                    </div>
                    <div class="p-3 bg-green-100 dark:bg-green-900/30 rounded-lg">
                        <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Academic Groups -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 font-medium">Academic Groups</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $stats['academic_groups'] }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">{{ $stats['academic_levels'] }} levels</p>
                    </div>
                    <div class="p-3 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
                        <svg class="w-8 h-8 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Subjects -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 font-medium">Subjects</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $stats['total_subjects'] }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">across all levels</p>
                    </div>
                    <div class="p-3 bg-orange-100 dark:bg-orange-900/30 rounded-lg">
                        <svg class="w-8 h-8 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="border-b border-gray-200 dark:border-gray-700">
                <nav class="flex space-x-8 px-6" aria-label="Tabs">
                    <button wire:click="setActiveTab('overview')" class="@if($activeTab === 'overview') border-indigo-500 text-indigo-600 dark:text-indigo-400 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300 @endif whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Overview
                    </button>
                    <button wire:click="setActiveTab('groups')" class="@if($activeTab === 'groups') border-indigo-500 text-indigo-600 dark:text-indigo-400 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300 @endif whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Academic Groups ({{ count($academicGroups) }})
                    </button>
                    <button wire:click="setActiveTab('levels')" class="@if($activeTab === 'levels') border-indigo-500 text-indigo-600 dark:text-indigo-400 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300 @endif whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Academic Levels ({{ count($academicLevels) }})
                    </button>
                    <button wire:click="setActiveTab('subjects')" class="@if($activeTab === 'subjects') border-indigo-500 text-indigo-600 dark:text-indigo-400 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300 @endif whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Subjects ({{ $stats['total_subjects'] }})
                    </button>
                    <button wire:click="setActiveTab('periods')" class="@if($activeTab === 'periods') border-indigo-500 text-indigo-600 dark:text-indigo-400 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300 @endif whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Academic Periods ({{ count($periods) }})
                    </button>
                </nav>
            </div>

            <div class="p-6">
                <!-- Overview Tab -->
                @if($activeTab === 'overview')
                    <div class="space-y-6">
                        <!-- School Details -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">School Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Email</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $schoolEmail ?: 'N/A' }}</dd>
                                </div>
                                <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Phone</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $schoolPhone ?: 'N/A' }}</dd>
                                </div>
                                <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Website</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $schoolWebsite ?: 'N/A' }}</dd>
                                </div>
                                <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Student Capacity</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $studentCapacity ?: 'N/A' }}</dd>
                                </div>
                            </div>
                        </div>

                        <!-- Current Period -->
                        @if($currentPeriod)
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Current Academic Period</h3>
                                <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-lg p-6 text-white">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <h4 class="text-xl font-bold">{{ $currentPeriod->getDisplayName() }}</h4>
                                            <p class="text-indigo-100 mt-1">
                                                {{ $currentPeriod->start_date->format('M d, Y') }} - {{ $currentPeriod->end_date->format('M d, Y') }}
                                            </p>
                                        </div>
                                        <div class="text-right">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-white/20 backdrop-blur-sm">
                                                {{ round($currentPeriod->getProgressPercentage()) }}% Complete
                                            </span>
                                        </div>
                                    </div>
                                    <div class="mt-4 bg-white/20 rounded-full h-2">
                                        <div class="bg-white rounded-full h-2 transition-all" style="width: {{ $currentPeriod->getProgressPercentage() }}%"></div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Academic Groups Tab -->
                @if($activeTab === 'groups')
                    <div class="space-y-4">
                        @forelse($academicGroups as $group)
                            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-6 hover:shadow-md transition">
                                <div class="flex items-center justify-between mb-4">
                                    <div>
                                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $group['name'] }}</h4>
                                        @if($group['tag'])
                                            <span class="inline-block px-2 py-1 text-xs font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200 rounded-full mt-1">
                                                {{ $group['tag'] }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $group['students_count'] }} Students</p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $group['teachers_count'] }} Teachers</p>
                                    </div>
                                </div>

                                @if(count($group['levels']) > 0)
                                    <div class="mt-4">
                                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Levels ({{ count($group['levels']) }}):</p>
                                        <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                                            @foreach($group['levels'] as $level)
                                                <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-3">
                                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $level['name'] }}</p>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                        {{ $level['students_count'] }} students • {{ $level['subjects_count'] }} subjects
                                                    </p>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="text-center py-12">
                                <p class="text-gray-500 dark:text-gray-400">No academic groups found</p>
                            </div>
                        @endforelse
                    </div>
                @endif

                <!-- Academic Levels Tab -->
                @if($activeTab === 'levels')
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @forelse($academicLevels as $level)
                            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-5 hover:shadow-md transition">
                                <div class="flex items-start justify-between mb-3">
                                    <div>
                                        <h4 class="text-base font-semibold text-gray-900 dark:text-white">{{ $level['name'] }}</h4>
                                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $level['group_name'] }}</p>
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-gray-600 dark:text-gray-400">Students:</span>
                                        <span class="font-medium text-gray-900 dark:text-white">{{ $level['students_count'] }}</span>
                                    </div>
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-gray-600 dark:text-gray-400">Subjects:</span>
                                        <span class="font-medium text-gray-900 dark:text-white">{{ $level['subjects_count'] }}</span>
                                    </div>
                                </div>

                                @if(count($level['subjects']) > 0)
                                    <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-700">
                                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-2">Subjects:</p>
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($level['subjects'] as $subject)
                                                <span class="inline-block px-2 py-1 text-xs bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded">
                                                    {{ $subject['code'] ?? $subject['name'] }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="col-span-3 text-center py-12">
                                <p class="text-gray-500 dark:text-gray-400">No academic levels found</p>
                            </div>
                        @endforelse
                    </div>
                @endif

                <!-- Subjects Tab -->
                @if($activeTab === 'subjects')
                    <div class="space-y-6">
                        @forelse($academicSubjects as $levelId => $levelData)
                            <div>
                                <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">
                                    {{ $levelData['level_name'] }} ({{ $levelData['group_name'] }})
                                </h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                    @foreach($levelData['subjects'] as $subject)
                                        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:shadow-sm transition">
                                            <div class="flex items-start justify-between">
                                                <div class="flex-1">
                                                    <h5 class="font-medium text-gray-900 dark:text-white">{{ $subject['name'] }}</h5>
                                                    @if($subject['code'])
                                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Code: {{ $subject['code'] }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                            @if($subject['description'])
                                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-2 line-clamp-2">{{ $subject['description'] }}</p>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-12">
                                <p class="text-gray-500 dark:text-gray-400">No subjects found</p>
                            </div>
                        @endforelse
                    </div>
                @endif

                <!-- Academic Periods Tab -->
                @if($activeTab === 'periods')
                    <div class="space-y-4">
                        @forelse($periods as $period)
                            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-5 @if($period['is_current']) ring-2 ring-indigo-500 @endif hover:shadow-md transition">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <div class="flex items-center space-x-2">
                                            <h4 class="text-base font-semibold text-gray-900 dark:text-white">{{ $period['title'] }}</h4>
                                            @if($period['is_current'])
                                                <span class="inline-flex items-center px-2 py-1 text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 rounded-full">
                                                    Current
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                            {{ $period['start_date'] }} - {{ $period['end_date'] }}
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <span class="inline-block px-3 py-1 text-xs font-medium rounded-full
                                            @if($period['status'] === 'active') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                            @elseif($period['status'] === 'upcoming') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                            @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300
                                            @endif">
                                            {{ ucfirst($period['status']) }}
                                        </span>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $period['weeks'] }} weeks</p>
                                    </div>
                                </div>

                                @if($period['status'] === 'active' || $period['is_current'])
                                    <div class="mt-4">
                                        <div class="flex items-center justify-between text-sm mb-2">
                                            <span class="text-gray-600 dark:text-gray-400">Progress</span>
                                            <span class="font-medium text-gray-900 dark:text-white">{{ $period['progress'] }}%</span>
                                        </div>
                                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-full h-2 transition-all" style="width: {{ $period['progress'] }}%"></div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="text-center py-12">
                                <p class="text-gray-500 dark:text-gray-400">No academic periods found</p>
                            </div>
                        @endforelse
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
