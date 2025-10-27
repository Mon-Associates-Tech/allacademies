<section x-data="{
    showNotification: false,
    notificationMessage: '',
    activeTab: @entangle('activeTab'),
    showPeriodModal: @entangle('showPeriodModal'),
    editingPeriod: @entangle('editingPeriod'),
    periods: @js($periods),
    stats: @js($stats),
    settingGroups: @js($settingGroups),
    darkMode: @entangle('darkMode'),

    showNotify(message) {
        this.notificationMessage = message;
        this.showNotification = true;
        setTimeout(() => {
            this.showNotification = false;
        }, 3000);
    },

    formatDate(dateStr) {
        return new Date(dateStr).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        });
    }
}">
    <!-- Flash Messages Listener -->
    <div x-init="
        Livewire.on('notify', message => showNotify(message));
        @if(session()->has('success'))
            showNotify('{{ session('success') }}');
        @endif
    "></div>

    <!-- Notification Toast -->
    <div x-show="showNotification"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform translate-y-2"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 transform translate-y-0"
         x-transition:leave-end="opacity-0 transform translate-y-2"
         class="fixed top-4 right-4 z-50 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg">
        <span x-text="notificationMessage"></span>
    </div>

    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <!-- Header -->
        <div class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
            <div class="px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-6">
                    <div class="flex items-center space-x-4">
                        @if(isset($schoolLogo) && $schoolLogo)
                            <img src="{{ $schoolLogo }}" alt="{{ $school->name }}" class="h-10 w-10 rounded-lg object-cover">
                        @else
                            <div class="h-10 w-10 rounded-lg bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center">
                                <span class="text-white font-bold text-sm">{{ $schoolInitials }}</span>
                            </div>
                        @endif
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $school->name }}</h1>
                            <p class="text-sm text-gray-500 dark:text-gray-400">School Administration Dashboard</p>
                        </div>
                    </div>

                    <div class="flex items-center space-x-4">
                        <!-- Dark mode toggle -->
                        <button wire:click="toggleDarkMode"
                                class="p-2 rounded-lg bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                            <svg x-show="!darkMode" class="w-5 h-5 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                            </svg>
                            <svg x-show="darkMode" class="w-5 h-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"></path>
                            </svg>
                        </button>

                        <!-- Notifications indicator -->
                        <div class="relative">
                            <div class="h-8 w-8 rounded-full bg-green-100 dark:bg-green-900 flex items-center justify-center">
                                <div class="h-2 w-2 rounded-full bg-green-500 animate-pulse"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Overview -->
        <div class="px-4 sm:px-6 lg:px-8 py-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Students</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['total_students'] ?? 0) }}</p>
                            <p class="text-sm text-green-600 dark:text-green-400">{{ number_format($stats['active_students'] ?? 0) }} active</p>
                        </div>
                        <div class="h-12 w-12 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Teachers</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total_teachers'] ?? 0 }}</p>
                            <p class="text-sm text-green-600 dark:text-green-400">{{ $stats['active_teachers'] ?? 0 }} active</p>
                        </div>
                        <div class="h-12 w-12 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Current Period</p>
                            <p class="text-lg font-bold text-gray-900 dark:text-white truncate">{{ $stats['current_period'] ?? 'No active period' }}</p>
                            <div class="mt-2">
                                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                    <div class="bg-indigo-600 h-2 rounded-full transition-all duration-300"
                                         style="width: {{ $stats['current_period_progress'] ?? 0 }}%"></div>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $stats['current_period_progress'] ?? 0 }}% complete</p>
                            </div>
                        </div>
                        <div class="h-12 w-12 bg-indigo-100 dark:bg-indigo-900 rounded-lg flex items-center justify-center ml-4">
                            <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Academic Levels</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['academic_levels'] ?? 0 }}</p>
                            <p class="text-sm text-purple-600 dark:text-purple-400">{{ $stats['academic_groups'] ?? 0 }} groups</p>
                        </div>
                        <div class="h-12 w-12 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Navigation Tabs -->
            <div class="mb-8">
                <nav class="flex space-x-8 border-b border-gray-200 dark:border-gray-700">
                    <button wire:click="setActiveTab('overview')"
                            :class="activeTab === 'overview' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600'"
                            class="py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                        Overview
                    </button>
                    <button wire:click="setActiveTab('basic-info')"
                            :class="activeTab === 'basic-info' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600'"
                            class="py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                        Basic Information
                    </button>
                    <button wire:click="setActiveTab('academic-periods')"
                            :class="activeTab === 'academic-periods' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600'"
                            class="py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                        Academic Periods
                    </button>
                    <button wire:click="setActiveTab('system-settings')"
                            :class="activeTab === 'system-settings' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600'"
                            class="py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                        System Settings
                    </button>
                </nav>
            </div>

            <!-- Tab Content -->
            @include('livewire.school.partials.overview-tab')
            @include('livewire.school.partials.basic-info-tab')
            @include('livewire.school.partials.academic-periods-tab')
            @include('livewire.school.partials.system-settings-tab')
        </div>

        <!-- Academic Period Modal -->
        @include('livewire.school.partials.period-modal')
    </div>
</section>
