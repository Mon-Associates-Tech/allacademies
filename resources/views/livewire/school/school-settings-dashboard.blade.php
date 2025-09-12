<section>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    animation: {
                        'fade-in': 'fadeIn 0.2s ease-in-out',
                        'slide-up': 'slideUp 0.3s ease-out'
                    }
                }
            }
        }
    </script>
    <style>
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>


    <div class="min-h-screen bg-gray-50 dark:bg-gray-900"
         x-data="{
        showNotification: false,
        notificationMessage: '',
        activeTab: 'overview',
        showPeriodModal: false,
        showDeleteConfirm: false,
        editingPeriod: null,
        settingGroups: {
            'general': {
                'school_motto': { key: 'school_motto', type: 'text', label: 'School Motto', value: 'Excellence in Education' },
                'academic_calendar_type': { key: 'academic_calendar_type', type: 'select', label: 'Academic Calendar', value: 'semester', options: ['semester', 'trimester', 'quarter'] },
                'default_language': { key: 'default_language', type: 'select', label: 'Default Language', value: 'en', options: ['en', 'fr', 'es'] }
            },
            'academic': {
                'grading_system': { key: 'grading_system', type: 'select', label: 'Grading System', value: 'letter', options: ['letter', 'numeric', 'percentage'] },
                'passing_grade': { key: 'passing_grade', type: 'number', label: 'Passing Grade', value: '60' },
                'max_absences': { key: 'max_absences', type: 'number', label: 'Max Allowed Absences', value: '10' }
            },
            'system': {
                'maintenance_mode': { key: 'maintenance_mode', type: 'boolean', label: 'Maintenance Mode', value: false },
                'allow_registration': { key: 'allow_registration', type: 'boolean', label: 'Allow New Registrations', value: true },
                'backup_frequency': { key: 'backup_frequency', type: 'select', label: 'Backup Frequency', value: 'daily', options: ['daily', 'weekly', 'monthly'] }
            }
        },
        periods: [
            {
                id: 1,
                title: 'Fall Semester 2024',
                type: 'semester',
                sequence: 1,
                academic_year: '2024/2025',
                start_date: '2024-09-01',
                end_date: '2025-01-15',
                status: 'active',
                is_current: true,
                progress: 65,
                weeks: 18,
                description: 'Fall semester for academic year 2024-2025'
            },
            {
                id: 2,
                title: 'Spring Semester 2025',
                type: 'semester',
                sequence: 2,
                academic_year: '2024/2025',
                start_date: '2025-01-20',
                end_date: '2025-05-30',
                status: 'upcoming',
                is_current: false,
                progress: 0,
                weeks: 16,
                description: 'Spring semester for academic year 2024-2025'
            }
        ],
        stats: {
            total_students: 1250,
            active_students: 1180,
            total_teachers: 85,
            active_teachers: 82,
            current_period: 'Fall Semester 2024',
            current_period_progress: 65,
            academic_levels: 12,
            academic_groups: 4
        },
        darkMode: false,

        // Methods
        toggleDarkMode() {
            this.darkMode = !this.darkMode;
        },

        setActiveTab(tab) {
            this.activeTab = tab;
        },

        showNotify(message) {
            this.notificationMessage = message;
            this.showNotification = true;
            setTimeout(() => {
                this.showNotification = false;
            }, 3000);
        },

        createPeriod() {
            this.editingPeriod = null;
            this.showPeriodModal = true;
        },

        editPeriod(period) {
            this.editingPeriod = period;
            this.showPeriodModal = true;
        },

        deletePeriod(periodId) {
            if (confirm('Are you sure you want to delete this academic period?')) {
                this.periods = this.periods.filter(p => p.id !== periodId);
                this.showNotify('Academic period deleted successfully!');
            }
        },

        setCurrentPeriod(periodId) {
            this.periods.forEach(p => p.is_current = p.id === periodId);
            this.showNotify('Current academic period updated!');
        },

        updateSetting(key, value, group) {
            if (this.settingGroups[group] && this.settingGroups[group][key]) {
                this.settingGroups[group][key].value = value;
                this.showNotify('Setting updated successfully!');
            }
        },

        formatDate(dateStr) {
            return new Date(dateStr).toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });
        }
     }">

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

        <!-- Header -->
        <div class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
            <div class="px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-6">
                    <div class="flex items-center space-x-4">
                        <div class="h-10 w-10 rounded-lg bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center">
                            <span class="text-white font-bold text-sm">SA</span>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Springfield Academy</h1>
                            <p class="text-sm text-gray-500 dark:text-gray-400">School Administration Dashboard</p>
                        </div>
                    </div>

                    <div class="flex items-center space-x-4">
                        <!-- Dark mode toggle -->
                        <button @click="toggleDarkMode()"
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
                            <p class="text-2xl font-bold text-gray-900 dark:text-white" x-text="stats.total_students.toLocaleString()"></p>
                            <p class="text-sm text-green-600 dark:text-green-400"><span x-text="stats.active_students.toLocaleString()"></span> active</p>
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
                            <p class="text-2xl font-bold text-gray-900 dark:text-white" x-text="stats.total_teachers"></p>
                            <p class="text-sm text-green-600 dark:text-green-400"><span x-text="stats.active_teachers"></span> active</p>
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
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Current Period</p>
                            <p class="text-lg font-bold text-gray-900 dark:text-white truncate" x-text="stats.current_period"></p>
                            <div class="mt-2">
                                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                    <div class="bg-indigo-600 h-2 rounded-full transition-all duration-300"
                                         :style="`width: ${stats.current_period_progress}%`"></div>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1"><span x-text="stats.current_period_progress"></span>% complete</p>
                            </div>
                        </div>
                        <div class="h-12 w-12 bg-indigo-100 dark:bg-indigo-900 rounded-lg flex items-center justify-center">
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
                            <p class="text-2xl font-bold text-gray-900 dark:text-white" x-text="stats.academic_levels"></p>
                            <p class="text-sm text-purple-600 dark:text-purple-400"><span x-text="stats.academic_groups"></span> groups</p>
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
                    <button @click="setActiveTab('overview')"
                            :class="activeTab === 'overview' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600'"
                            class="py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                        Overview
                    </button>
                    <button @click="setActiveTab('basic-info')"
                            :class="activeTab === 'basic-info' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600'"
                            class="py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                        Basic Information
                    </button>
                    <button @click="setActiveTab('academic-periods')"
                            :class="activeTab === 'academic-periods' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600'"
                            class="py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                        Academic Periods
                    </button>
                    <button @click="setActiveTab('system-settings')"
                            :class="activeTab === 'system-settings' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600'"
                            class="py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                        System Settings
                    </button>
                </nav>
            </div>

            <!-- Tab Content -->
            <div class="space-y-8">
                <!-- Overview Tab -->
                <div x-show="activeTab === 'overview'" class="animate-fade-in">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <!-- Quick Actions -->
                        <div class="lg:col-span-1">
                            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Quick Actions</h3>
                                <div class="space-y-4">
                                    <button @click="createPeriod()"
                                            class="w-full flex items-center justify-between p-4 bg-gradient-to-r from-indigo-500 to-purple-600 text-white rounded-lg hover:from-indigo-600 hover:to-purple-700 transition-all transform hover:scale-[1.02]">
                                        <span class="font-medium">Add Academic Period</span>
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                        </svg>
                                    </button>

                                    <button @click="setActiveTab('basic-info')"
                                            class="w-full flex items-center justify-between p-4 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-all">
                                        <span class="font-medium">Update School Info</span>
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </button>

                                    <button @click="setActiveTab('system-settings')"
                                            class="w-full flex items-center justify-between p-4 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-all">
                                        <span class="font-medium">Configure Settings</span>
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Activity -->
                        <div class="lg:col-span-2">
                            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Academic Periods Overview</h3>

                                <div class="space-y-4">
                                    <template x-for="period in periods" :key="period.id">
                                        <div class="flex items-center justify-between p-4 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                            <div class="flex items-center space-x-4">
                                                <div class="h-10 w-10 rounded-lg flex items-center justify-center"
                                                     :class="period.status === 'active' ? 'bg-green-100 dark:bg-green-900' : period.status === 'upcoming' ? 'bg-blue-100 dark:bg-blue-900' : 'bg-gray-100 dark:bg-gray-700'">
                                                    <div class="h-3 w-3 rounded-full"
                                                         :class="period.status === 'active' ? 'bg-green-500 animate-pulse' : period.status === 'upcoming' ? 'bg-blue-500' : 'bg-gray-500'"></div>
                                                </div>
                                                <div>
                                                    <p class="font-medium text-gray-900 dark:text-white" x-text="period.title"></p>
                                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                                        <span x-text="formatDate(period.start_date)"></span> - <span x-text="formatDate(period.end_date)"></span>
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="flex items-center space-x-2">
                                            <span class="px-3 py-1 text-xs font-medium rounded-full"
                                                  :class="period.status === 'active' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : period.status === 'upcoming' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200'"
                                                  x-text="period.status.charAt(0).toUpperCase() + period.status.slice(1)"></span>
                                                <span x-show="period.is_current" class="px-2 py-1 text-xs font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200 rounded-full">Current</span>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Academic Periods Tab -->
                <div x-show="activeTab === 'academic-periods'" class="space-y-6 animate-fade-in">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Academic Periods</h3>
                        <button @click="createPeriod()"
                                class="px-4 py-2 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                            Add New Period
                        </button>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                                <tr>
                                    <th class="px-6 py-4 text-left text-sm font-medium text-gray-900 dark:text-white">Period</th>
                                    <th class="px-6 py-4 text-left text-sm font-medium text-gray-900 dark:text-white">Type</th>
                                    <th class="px-6 py-4 text-left text-sm font-medium text-gray-900 dark:text-white">Duration</th>
                                    <th class="px-6 py-4 text-left text-sm font-medium text-gray-900 dark:text-white">Status</th>
                                    <th class="px-6 py-4 text-left text-sm font-medium text-gray-900 dark:text-white">Progress</th>
                                    <th class="px-6 py-4 text-right text-sm font-medium text-gray-900 dark:text-white">Actions</th>
                                </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                                <template x-for="period in periods" :key="period.id">
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center">
                                                <div>
                                                    <p class="font-medium text-gray-900 dark:text-white" x-text="period.title"></p>
                                                    <p class="text-sm text-gray-500 dark:text-gray-400" x-text="period.academic_year"></p>
                                                </div>
                                                <span x-show="period.is_current" class="ml-2 px-2 py-1 text-xs font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200 rounded-full">Current</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="text-sm text-gray-900 dark:text-white capitalize" x-text="`${period.type} ${period.sequence}`"></span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-900 dark:text-white">
                                                <span x-text="formatDate(period.start_date)"></span>
                                                <span class="text-gray-500 dark:text-gray-400"> - </span>
                                                <span x-text="formatDate(period.end_date)"></span>
                                            </div>
                                            <p class="text-xs text-gray-500 dark:text-gray-400"><span x-text="period.weeks"></span> weeks</p>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="px-3 py-1 text-xs font-medium rounded-full capitalize"
                                                  :class="period.status === 'active' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : period.status === 'upcoming' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200'"
                                                  x-text="period.status"></span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div x-show="period.status === 'active'">
                                                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                                    <div class="bg-indigo-600 h-2 rounded-full transition-all duration-300" :style="`width: ${period.progress}%`"></div>
                                                </div>
                                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1"><span x-text="period.progress"></span>%</p>
                                            </div>
                                            <span x-show="period.status !== 'active'" class="text-sm text-gray-400 dark:text-gray-500">-</span>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <div class="flex items-center justify-end space-x-2">
                                                <button x-show="!period.is_current && period.status !== 'completed'"
                                                        @click="setCurrentPeriod(period.id)"
                                                        class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 text-sm font-medium">
                                                    Set Current
                                                </button>
                                                <button @click="editPeriod(period)"
                                                        class="text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-300">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                    </svg>
                                                </button>
                                                <button @click="deletePeriod(period.id)"
                                                        class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- System Settings Tab -->
                <div x-show="activeTab === 'system-settings'" class="space-y-6 animate-fade-in">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">System Settings</h3>
                        <button class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-medium rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                            Export Settings
                        </button>
                    </div>

                    <template x-for="(group, groupName) in settingGroups" :key="groupName">
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                            <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-6 capitalize" x-text="groupName + ' Settings'"></h4>

                            <div class="space-y-6">
                                <template x-for="(setting, key) in group" :key="key">
                                    <div class="flex items-center justify-between py-4 border-b border-gray-100 dark:border-gray-700 last:border-b-0">
                                        <div class="flex-1 mr-6">
                                            <label class="text-sm font-medium text-gray-900 dark:text-white" x-text="setting.label"></label>
                                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Configure <span x-text="setting.label.toLowerCase()"></span> for your school</p>
                                        </div>

                                        <!-- Text Input -->
                                        <div x-show="setting.type === 'text'" class="w-64">
                                            <input type="text"
                                                   :value="setting.value"
                                                   @change="updateSetting(key, $event.target.value, groupName)"
                                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white text-sm">
                                        </div>

                                        <!-- Number Input -->
                                        <div x-show="setting.type === 'number'" class="w-32">
                                            <input type="number"
                                                   :value="setting.value"
                                                   @change="updateSetting(key, $event.target.value, groupName)"
                                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white text-sm">
                                        </div>

                                        <!-- Select Input -->
                                        <div x-show="setting.type === 'select'" class="w-48">
                                            <select :value="setting.value"
                                                    @change="updateSetting(key, $event.target.value, groupName)"
                                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white text-sm">
                                                <template x-for="option in setting.options" :key="option">
                                                    <option :value="option" x-text="option.charAt(0).toUpperCase() + option.slice(1)"></option>
                                                </template>
                                            </select>
                                        </div>

                                        <!-- Boolean Toggle -->
                                        <div x-show="setting.type === 'boolean'" class="flex items-center">
                                            <button @click="updateSetting(key, !setting.value, groupName)"
                                                    :class="setting.value ? 'bg-indigo-600' : 'bg-gray-200 dark:bg-gray-700'"
                                                    class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2">
                                            <span :class="setting.value ? 'translate-x-5' : 'translate-x-0'"
                                                  class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"></span>
                                            </button>
                                            <span class="ml-3 text-sm text-gray-900 dark:text-white" x-text="setting.value ? 'Enabled' : 'Disabled'"></span>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Basic Information Tab -->
                <div x-show="activeTab === 'basic-info'" class="max-w-4xl animate-fade-in">
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Basic Information</h3>

                        <form @submit.prevent="showNotify('School information updated successfully!')" class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">School Name *</label>
                                    <input type="text" value="Springfield Academy" required
                                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white transition-colors">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">School Code</label>
                                    <input type="text" value="SA001"
                                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white transition-colors">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email</label>
                                    <input type="email" value="admin@springfieldacademy.edu"
                                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white transition-colors">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Phone</label>
                                    <input type="tel" value="+1 (555) 123-4567"
                                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white transition-colors">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Website</label>
                                    <input type="url" value="https://springfieldacademy.edu"
                                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white transition-colors">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">School Type</label>
                                    <select class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white transition-colors">
                                        <option value="">Select Type</option>
                                        <option value="public">Public</option>
                                        <option value="private" selected>Private</option>
                                        <option value="charter">Charter</option>
                                        <option value="international">International</option>
                                        <option value="religious">Religious</option>
                                    </select>
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Address</label>
                                    <textarea rows="3" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white transition-colors">123 Education Street, Springfield, ST 12345</textarea>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Student Capacity</label>
                                    <input type="number" value="1500"
                                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white transition-colors">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Timezone</label>
                                    <select class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white transition-colors">
                                        <option value="America/New_York" selected>Eastern Time</option>
                                        <option value="America/Chicago">Central Time</option>
                                        <option value="America/Denver">Mountain Time</option>
                                        <option value="America/Los_Angeles">Pacific Time</option>
                                    </select>
                                </div>
                            </div>

                            <div class="flex justify-end">
                                <button type="submit"
                                        class="px-6 py-3 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition-colors">
                                    Update School Information
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Academic Period Modal -->
        <div x-show="showPeriodModal"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center p-4 z-50"
             @click.self="showPeriodModal = false">

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform scale-95"
                 x-transition:enter-end="opacity-100 transform scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 transform scale-100"
                 x-transition:leave-end="opacity-0 transform scale-95">

                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                        <span x-text="editingPeriod ? 'Edit Academic Period' : 'Add New Academic Period'"></span>
                    </h3>
                </div>

                <form @submit.prevent="showPeriodModal = false; showNotify(editingPeriod ? 'Academic period updated successfully!' : 'Academic period created successfully!')" class="p-6">
                    <div class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Period Title *</label>
                                <input type="text" required
                                       :value="editingPeriod ? editingPeriod.title : ''"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Type *</label>
                                <select required
                                        :value="editingPeriod ? editingPeriod.type : 'semester'"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white">
                                    <option value="semester">Semester</option>
                                    <option value="term">Term</option>
                                    <option value="quarter">Quarter</option>
                                    <option value="trimester">Trimester</option>
                                    <option value="session">Session</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Sequence *</label>
                                <input type="number" min="1" max="10" required
                                       :value="editingPeriod ? editingPeriod.sequence : 1"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status *</label>
                                <select required
                                        :value="editingPeriod ? editingPeriod.status : 'upcoming'"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white">
                                    <option value="upcoming">Upcoming</option>
                                    <option value="active">Active</option>
                                    <option value="completed">Completed</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Start Date *</label>
                                <input type="date" required
                                       :value="editingPeriod ? editingPeriod.start_date : ''"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">End Date *</label>
                                <input type="date" required
                                       :value="editingPeriod ? editingPeriod.end_date : ''"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description</label>
                            <textarea rows="3"
                                      :value="editingPeriod ? editingPeriod.description : ''"
                                      class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"></textarea>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end space-x-3">
                        <button type="button" @click="showPeriodModal = false"
                                class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            Cancel
                        </button>
                        <button type="submit"
                                class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                            <span x-text="editingPeriod ? 'Update Period' : 'Create Period'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

</section>
