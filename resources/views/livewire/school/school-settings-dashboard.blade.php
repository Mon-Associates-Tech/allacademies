<section x-data="{
    showNotification: false,
    notificationMessage: '',
    activeTab: @entangle('activeTab'),
    showPeriodModal: @entangle('showPeriodModal'),
    showPeriodModal: @entangle('showPeriodModal'),
    showGroupLevelModal: @entangle('showGroupLevelModal'),
    showAcademicYearModal: @entangle('showAcademicYearModal'),

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

        <div class="bg-gradient-to-r rounded-t-xl mb-4 from-indigo-600 via-purple-600 to-pink-600">
            <div class="px-4 sm:px-6 lg:px-8 py-8">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                    <div class="flex items-center space-x-4">
                        <!-- School Logo/Avatar -->
                        @if($schoolLogo)
                        <div class="flex-shrink-0">
                            <img src="{{ $schoolLogo }}" alt="{{ $school->name }}"
                                class="h-16 w-16 rounded-xl object-cover border-2 border-white shadow-lg">
                        </div>
                        @else
                        <div class="flex-shrink-0">
                            <div class="h-16 w-16 rounded-xl bg-white bg-opacity-20 backdrop-blur-sm flex items-center justify-center border-2 border-white shadow-lg">
                                <span class="text-2xl font-bold text-white">{{ $schoolInitials }}</span>
                            </div>
                        </div>
                        @endif

                        <!-- School Name & Description -->
                        <div>
                            @if(!($isAccountant ?? false))
                            <h1 class="text-2xl md:text-3xl font-bold text-white">
                                School Settings & Configuration
                            </h1>
                            <p class="text-indigo-100 text-sm md:text-base mt-1">
                                {{ $school->name }} • Manage academic periods, fees, users, and system preferences
                            </p>
                            @else
                            <h1 class="text-2xl md:text-3xl font-bold text-white">
                                School Settings
                            </h1>
                            <p class="text-indigo-100 text-sm md:text-base mt-1">
                                {{ $school->name }} • Financial and Account Management
                            </p>
                            @endif
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="mt-4 md:mt-0 flexd hidden items-center space-x-3">
                        <button wire:click="refreshData"
                            class="inline-flex items-center px-4 py-2 bg-white bg-opacity-20 backdrop-blur-sm border border-white border-opacity-30 rounded-lg text-sm font-medium text-white hover:bg-opacity-30 transition-all duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            Refresh
                        </button>

                        <a href="{{ route('dashboard') }}"
                            class="inline-flex items-center px-4 py-2 bg-white text-indigo-600 rounded-lg text-sm font-medium hover:bg-indigo-50 transition-all duration-200 shadow-lg">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Setup Cards -->
        <section class="px-6">
            @if($school)
            <div class="mb-1">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-base font-semibold text-gray-900 dark:text-white">Quick Actions</h2>
                    <span class="text-xs text-gray-500 dark:text-gray-400">Common tasks & shortcuts</span>
                </div>

                <!-- Compact Grid Layout -->
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
                    <!-- Academic Periods -->
                    @if(!($isAccountant ?? false))
                    <button wire:click="setActiveTab('academic-periods')"
                        class="group bg-white dark:bg-gray-800 rounded-lg shadow-sm p-3 hover:shadow-md transition-all duration-200 border border-gray-200 dark:border-gray-700 hover:border-indigo-300 dark:hover:border-indigo-600">
                        <div class="flex flex-col items-center text-center">
                            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform duration-200 mb-2">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <span class="text-xs font-medium text-gray-900 dark:text-white">Periods</span>
                        </div>
                    </button>
                    @endif

                    <!-- Fee Structure -->
                    <button wire:click="setActiveTab('fee-structure')"
                        class="group bg-white dark:bg-gray-800 rounded-lg shadow-sm p-3 hover:shadow-md transition-all duration-200 border border-gray-200 dark:border-gray-700 hover:border-green-300 dark:hover:border-green-600">
                        <div class="flex flex-col items-center text-center">
                            <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-green-600 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform duration-200 mb-2">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <span class="text-xs font-medium text-gray-900 dark:text-white">Fees</span>
                        </div>
                    </button>

                    <!-- Academic Structure -->
                    @if(!($isAccountant ?? false))
                    <a href="#"
                        class="group hidden bg-white dark:bg-gray-800 rounded-lg shadow-sm p-3 hover:shadow-md transition-all duration-200 border border-gray-200 dark:border-gray-700 hover:border-purple-300 dark:hover:border-purple-600">
                        <div class="flex flex-col items-center text-center">
                            <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform duration-200 mb-2">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                </svg>
                            </div>
                            <span class="text-xs font-medium text-gray-900 dark:text-white">Structure</span>
                        </div>
                    </a>
                    @endif

                    <!-- Settings -->
                    @if(!($isAccountant ?? false))
                    <button type="button" wire:click="setActiveTab('system-settings')"
                        class="group bg-white dark:bg-gray-800 rounded-lg shadow-sm p-3 hover:shadow-md transition-all duration-200 border border-gray-200 dark:border-gray-700 hover:border-orange-300 dark:hover:border-orange-600">
                        <div class="flex flex-col items-center text-center">
                            <div class="w-10 h-10 bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform duration-200 mb-2">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                                </svg>
                            </div>
                            <span class="text-xs font-medium text-gray-900 dark:text-white">Settings</span>
                        </div>
                    </button>
                    @endif

                    <!-- Import Data -->
                    <button type="button" wire:click="openImportModal"
                        class="group bg-white dark:bg-gray-800 rounded-lg shadow-sm p-3 hover:shadow-md transition-all duration-200 border border-gray-200 dark:border-gray-700 hover:border-blue-300 dark:hover:border-blue-600">
                        <div class="flex flex-col items-center text-center">
                            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform duration-200 mb-2">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                </svg>
                            </div>
                            <span class="text-xs font-medium text-gray-900 dark:text-white">Import</span>
                        </div>
                    </button>

                    <!-- Templates -->
                    <button type="button" wire:click="openTemplateModal"
                        class="group bg-white dark:bg-gray-800 rounded-lg shadow-sm p-3 hover:shadow-md transition-all duration-200 border border-gray-200 dark:border-gray-700 hover:border-teal-300 dark:hover:border-teal-600">
                        <div class="flex flex-col items-center text-center">
                            <div class="w-10 h-10 bg-gradient-to-br from-teal-500 to-teal-600 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform duration-200 mb-2">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <span class="text-xs font-medium text-gray-900 dark:text-white">Templates</span>
                        </div>
                    </button>
                </div>
            </div>
            @endif
        </section>

        <!-- Stats Overview -->
        @if(!($isAccountant ?? false))
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
            @endif

            <div class="px-4 sm:px-6 lg:px-8 py-8 pt-0">
                <!-- Navigation Tabs -->
                <div class="mb-8 w-full ">
                    <livewire:scrollable-tabs
                        :tabs="$tabs"
                        :activeTab="$activeTab" />
                </div>

                <!-- Tab Content -->
                @if(!($isAccountant ?? false))
                @include('livewire.school.partials.overview-tab')
                @endif
                @include('livewire.school.partials.basic-info-tab')
                @include('livewire.school.partials.academic-periods-tab')
                {{-- @include('livewire.school.partials.system-settings-tab') --}}
                @include('livewire.school.partials.fee-structure-tab')
                <livewire:school-settings.letterhead-settings />
                @include('livewire.school.partials.financial-aid-tab')
                @include('livewire.school.partials.account-information-tab')
            </div>

            <!-- Academic Period Modal -->
            @include('livewire.school.partials.period-modal')
            {{-- @include('livewire.school.partials.academic-year-modal')--}}

            <!-- Import Modal -  -->
            @if($showImportModal)
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity z-50" wire:click="closeImportModal"></div>
            <div class="fixed inset-0 z-50 overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-lg bg-white dark:bg-gray-800 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-2xl" wire:click.stop>
                        <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="flex items-center justify-between mb-6">
                                <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white">Import Data</h3>
                                <button wire:click="closeImportModal" class="text-gray-400 hover:text-gray-500">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <form wire:submit="performImport">
                                <div class="space-y-6">
                                    <!-- Import Type -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Import Type *</label>
                                        <select wire:model="importType" class="block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                            @foreach($importTypes as $key => $label)
                                            <option value="{{ $key }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- File Upload -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Upload File (CSV/Excel) *</label>
                                        <input type="file" wire:model="importFile" accept=".csv,.xlsx,.xls" class="block w-full text-sm text-gray-500 dark:text-gray-400
                                    file:mr-4 file:py-2 file:px-4
                                    file:rounded-md file:border-0
                                    file:text-sm file:font-semibold
                                    file:bg-indigo-50 file:text-indigo-700
                                    hover:file:bg-indigo-100
                                    dark:file:bg-indigo-900 dark:file:text-indigo-300">
                                        @error('importFile') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                    </div>

                                    <!-- Default Password -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Default Password for New Users *</label>
                                        <input type="text" wire:model="defaultPassword" class="block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Users can change this password after first login</p>
                                        @error('defaultPassword') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>

                                    <!-- Import Options -->
                                    <div class="space-y-3">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Import Options</label>

                                        <div class="flex items-center">
                                            <input type="checkbox" wire:model="createMissingLevels" id="createLevels" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                            <label for="createLevels" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                                                Create missing academic levels
                                            </label>
                                        </div>

                                        <div class="flex items-center">
                                            <input type="checkbox" wire:model="createMissingGroups" id="createGroups" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                            <label for="createGroups" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                                                Create missing academic groups
                                            </label>
                                        </div>

                                        <div class="flex items-center">
                                            <input type="checkbox" wire:model="sendWelcomeEmail" id="sendEmail" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                            <label for="sendEmail" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                                                Send welcome emails to new users
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Help Text -->
                                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-md p-4">
                                        <div class="flex">
                                            <svg class="w-5 h-5 text-blue-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                            </svg>
                                            <div class="ml-3">
                                                <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">Import Guidelines</h3>
                                                <div class="mt-2 text-sm text-blue-700 dark:text-blue-300">
                                                    <ul class="list-disc list-inside space-y-1">
                                                        <li>File must be in CSV or Excel format</li>
                                                        <li>First row must contain column headers</li>
                                                        <li>Download templates to see required format</li>
                                                        <li>Maximum file size: 10MB</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-6 flex items-center justify-end space-x-3">
                                    <button type="button" wire:click="closeImportModal" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                                        Cancel
                                    </button>
                                    <button type="submit" class="px-4 py-2 bg-green-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-green-700">
                                        Start Import
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Templates Modal - NEW -->
            @if($showTemplateModal)
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity z-50" wire:click="closeTemplateModal"></div>
            <div class="fixed inset-0 z-50 overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-lg bg-white dark:bg-gray-800 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-4xl" wire:click.stop>
                        <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="flex items-center justify-between mb-6">
                                <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white">Download Import Templates</h3>
                                <button wire:click="closeTemplateModal" class="text-gray-400 hover:text-gray-500">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($importTypes as $key => $label)
                                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:shadow-md transition">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <h4 class="text-base font-semibold text-gray-900 dark:text-white">{{ $label }}</h4>
                                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">CSV Template</p>
                                        </div>
                                        <svg class="w-10 h-10 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </div>
                                    <button wire:click="downloadTemplate('{{ $key }}')" class="mt-4 w-full inline-flex items-center justify-center px-4 py-2 bg-blue-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-blue-700">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                        </svg>
                                        Download Template
                                    </button>
                                </div>
                                @endforeach
                            </div>

                            <div class="mt-6">
                                <a href="{{ route('school.import-formats') }}" target="_blank" class="inline-flex items-center text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-500">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    View detailed import format documentation
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            @if($showGroupLevelModal)
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity z-50" wire:click="$set('showGroupLevelModal', false)"></div>
            <div class="fixed inset-0 z-50 overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-lg bg-white dark:bg-gray-800 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-2xl" wire:click.stop>
                        <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                    <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white mb-4">
                                        Manage Academic Structure
                                    </h3>
                                    <div class="mt-2 max-h-[60vh] overflow-y-auto pr-2">
                                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                                            Select the academic groups and levels active for this school.
                                        </p>

                                        <div class="space-y-4">
                                            @foreach($allAcademicGroups as $group)
                                            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:bg-gray-50 dark:hover:bg-gray-750 transition-colors">
                                                <div class="flex items-center">
                                                    <input type="checkbox"
                                                        value="{{ $group->id }}"
                                                        wire:model.live="selectedGroups"
                                                        id="group_{{ $group->id }}"
                                                        class="h-5 w-5 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded cursor-pointer">
                                                    <label for="group_{{ $group->id }}" class="ml-3 block text-base font-medium text-gray-900 dark:text-white cursor-pointer">
                                                        {{ $group->name }}
                                                    </label>
                                                </div>

                                                @if(in_array((string)$group->id, $selectedGroups))
                                                <div class="mt-3 ml-8 pl-4 border-l-2 border-indigo-100 dark:border-gray-600 grid grid-cols-1 sm:grid-cols-2 gap-3 animate-fade-in-down">
                                                    @foreach($group->academicLevels as $level)
                                                    <div class="flex items-center">
                                                        <input type="checkbox"
                                                            value="{{ $level->id }}"
                                                            wire:model="selectedLevels"
                                                            id="level_{{ $level->id }}"
                                                            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded cursor-pointer">
                                                        <label for="level_{{ $level->id }}" class="ml-2 block text-sm text-gray-600 dark:text-gray-300 cursor-pointer">
                                                            {{ $level->name }}
                                                        </label>
                                                    </div>
                                                    @endforeach
                                                </div>
                                                @endif
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                            <button type="button" wire:click="saveAcademicGroupsAndLevels" class="inline-flex w-full justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 sm:ml-3 sm:w-auto">
                                Save Changes
                            </button>
                            <button type="button" wire:click="$set('showGroupLevelModal', false)" class="mt-3 inline-flex w-full justify-center rounded-md bg-white dark:bg-gray-800 px-3 py-2 text-sm font-semibold text-gray-900 dark:text-gray-300 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 sm:mt-0 sm:w-auto">
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            @if($showAcademicYearModal)
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity z-50" wire:click="$set('showAcademicYearModal', false)"></div>
            <div class="fixed inset-0 z-50 overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-lg bg-white dark:bg-gray-800 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg" wire:click.stop>
                        <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                    <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white mb-4">
                                        {{ $editingYearId ? 'Edit Academic Year' : 'Create Academic Year' }}
                                    </h3>

                                    <form wire:submit.prevent="saveAcademicYear">
                                        <div class="space-y-4">
                                            <!-- Name -->
                                            <div>
                                                <label for="yearName" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name *</label>
                                                <input type="text" wire:model="yearName" id="yearName"
                                                    placeholder="e.g. 2024/2025"
                                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                                @error('yearName') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                            </div>

                                            <!-- Dates Row -->
                                            <div class="grid grid-cols-1 gap-y-4 gap-x-4 sm:grid-cols-2">
                                                <div>
                                                    <label for="yearStartDate" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Start Date *</label>
                                                    <input type="date" wire:model="yearStartDate" id="yearStartDate"
                                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                                    @error('yearStartDate') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                                </div>
                                                <div>
                                                    <label for="yearEndDate" class="block text-sm font-medium text-gray-700 dark:text-gray-300">End Date *</label>
                                                    <input type="date" wire:model="yearEndDate" id="yearEndDate"
                                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                                    @error('yearEndDate') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                                </div>
                                            </div>

                                            <!-- Status -->
                                            <div>
                                                <label for="yearStatus" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status *</label>
                                                <select wire:model="yearStatus" id="yearStatus"
                                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                                    <option value="upcoming">Upcoming</option>
                                                    <option value="active">Active</option>
                                                    <option value="completed">Completed</option>
                                                </select>
                                                @error('yearStatus') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                            </div>

                                            <!-- Description -->
                                            <div>
                                                <label for="yearDescription" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                                                <textarea wire:model="yearDescription" id="yearDescription" rows="3"
                                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></textarea>
                                                @error('yearDescription') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                            <button type="button" wire:click="saveAcademicYear" class="inline-flex w-full justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 sm:ml-3 sm:w-auto">
                                {{ $editingYearId ? 'Update Year' : 'Create Year' }}
                            </button>
                            <button type="button" wire:click="$set('showAcademicYearModal', false)" class="mt-3 inline-flex w-full justify-center rounded-md bg-white dark:bg-gray-800 px-3 py-2 text-sm font-semibold text-gray-900 dark:text-gray-300 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 sm:mt-0 sm:w-auto">
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
</section>