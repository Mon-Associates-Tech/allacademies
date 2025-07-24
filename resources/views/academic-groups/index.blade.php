<x-layouts.app title="Academic Groups" page-name="Academic Groups">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="['Academic Groups' => null]" />
    </x-slot>

    <!-- Header Section -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden mb-6 transition-colors duration-200">
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 dark:from-blue-700 dark:to-indigo-700 px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                <div class="flex items-center">
                    <div class="flex-shrink-0 w-14 h-14 sm:w-16 sm:h-16 bg-white/20 dark:bg-white/30 rounded-2xl flex items-center justify-center">
                        <svg class="w-7 h-7 sm:w-8 sm:h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-2M7 21H5m2 0h2m6-11V7h-2v3m0 0V7h2v3m-2 0h2v3m-2-3h-2v3"></path>
                        </svg>
                    </div>
                    <div class="ml-4 sm:ml-6">
                        <h1 class="text-2xl sm:text-3xl font-bold text-white mb-1 sm:mb-2">Academic Groups</h1>
                        <p class="text-blue-100 dark:text-blue-200 text-sm sm:text-base">Manage your educational hierarchies</p>
                    </div>
                </div>

                @can('administrate')
                    <div class="mt-4 lg:mt-0">
                        <a href="{{ route('academic-groups.create') }}"
                           class="inline-flex items-center px-4 sm:px-6 py-2 sm:py-3 border border-transparent text-sm sm:text-base font-medium rounded-xl text-blue-600 bg-white hover:bg-gray-50 dark:bg-gray-100 dark:hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white transition-all duration-200 shadow-sm">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            <span class="hidden sm:inline">New Academic Group</span>
                            <span class="sm:hidden">New Group</span>
                        </a>
                    </div>
                @endcan
            </div>
        </div>

        @if ($academicGroups->count())
            <!-- Quick Stats -->
            <div class="px-4 sm:px-6 lg:px-8 py-6 bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600 transition-colors duration-200">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-6">
                    <div class="flex items-center p-4 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-600 transition-all duration-200">
                        <div class="flex-shrink-0 w-10 h-10 sm:w-12 sm:h-12 bg-blue-100 dark:bg-blue-900 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-2M7 21H5m2 0h2m6-11V7h-2v3m0 0V7h2v3m-2 0h2v3m-2-3h-2v3"></path>
                            </svg>
                        </div>
                        <div class="ml-3 sm:ml-4">
                            <div class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">{{ $academicGroups->total() }}</div>
                            <div class="text-xs sm:text-sm text-gray-600 dark:text-gray-400">Total Groups</div>
                        </div>
                    </div>

                    <!-- Additional stats placeholders - can be populated with real data -->
                    <div class="flex items-center p-4 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-600 transition-all duration-200">
                        <div class="flex-shrink-0 w-10 h-10 sm:w-12 sm:h-12 bg-green-100 dark:bg-green-900 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-3 sm:ml-4">
                            <div class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">{{ $academicGroups->sum(function($group) { return $group->academicLevels()->count(); }) }}</div>
                            <div class="text-xs sm:text-sm text-gray-600 dark:text-gray-400">Total Levels</div>
                        </div>
                    </div>

                    <div class="flex items-center p-4 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-600 transition-all duration-200">
                        <div class="flex-shrink-0 w-10 h-10 sm:w-12 sm:h-12 bg-purple-100 dark:bg-purple-900 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <div class="ml-3 sm:ml-4">
                            <div class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">{{ $academicGroups->sum(function($group) { return $group->teachers()->count(); }) }}</div>
                            <div class="text-xs sm:text-sm text-gray-600 dark:text-gray-400">Total Teachers</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Search and Filter Section -->
            <div class="px-4 sm:px-6 lg:px-8 py-4 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-600 transition-colors duration-200">
                <div class="flex flex-col sm:flex-row gap-4 sm:items-center sm:justify-between">
                    <div class="flex-1 max-w-md">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <input type="text"
                                   class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg leading-5 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 text-sm"
                                   placeholder="Search groups..."
                                   id="search-input">
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <select class="block w-full sm:w-auto px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 text-sm">
                            <option value="">All Groups</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>

                        <button class="p-2 text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 transition-colors duration-200"
                                onclick="toggleView()"
                                title="Toggle view">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Academic Groups List -->
            <div class="p-4 sm:p-6 lg:p-8">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden transition-colors duration-200">
                    <div class="divide-y divide-gray-200 dark:divide-gray-700" id="groups-container">
                        @foreach ($academicGroups as $academicGroup)
                            <div class="p-4 sm:p-6 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200 group-item border-l-4 border-l-transparent hover:border-l-blue-500"
                                 data-name="{{ strtolower($academicGroup->name) }}">
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
                                    <!-- Group Info -->
                                    <div class="flex items-center space-x-3 sm:space-x-4 flex-1">
                                        <div class="flex-shrink-0">
                                            <!-- Unique Icon Avatar for each Academic Group -->
                                            <x-avatar
                                                max-initials="3"
                                                :name="$academicGroup->name"
                                                class="w-12 h-12 sm:w-14 sm:h-14 text-sm font-bold"
                                                text-size="text-sm"
                                            />
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center space-x-3 mb-2">
                                                <h3 class="text-lg sm:text-xl font-semibold text-gray-900 dark:text-white truncate">
                                                    {{ $academicGroup->name }}
                                                </h3>
                                                <!-- Status indicator -->
                                                <div class="flex items-center">
                                                    <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                                                    <span class="ml-1 text-xs text-green-600 dark:text-green-400 font-medium">Active</span>
                                                </div>
                                            </div>

                                            <div class="flex flex-wrap items-center gap-2 mb-3">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gradient-to-r from-blue-100 to-indigo-100 dark:from-blue-900 dark:to-indigo-900 text-blue-800 dark:text-blue-200 border border-blue-200 dark:border-blue-700">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-2M7 21H5m2 0h2m6-11V7h-2v3m0 0V7h2v3m-2 0h2v3m-2-3h-2v3"></path>
                            </svg>
                            Academic Group
                        </span>
                                                <span class="text-xs text-gray-500 dark:text-gray-400 flex items-center">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Created {{ $academicGroup->created_at->diffForHumans() }}
                        </span>
                                            </div>

                                            <!-- Enhanced stats for mobile with better icons -->
                                            <div class="flex items-center gap-4 mt-2 sm:hidden">
                                                <div class="flex items-center text-xs text-gray-600 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded-full">
                                                    <svg class="w-3 h-3 mr-1 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-2M7 21H5m2 0h2m6-11V7h-2v3m0 0V7h2v3m-2 0h2v3m-2-3h-2v3"></path>
                                                    </svg>
                                                    <span class="font-medium">{{ $academicGroup->academicLevels()->count() }}</span>
                                                    <span class="ml-1">levels</span>
                                                </div>
                                                <div class="flex items-center text-xs text-gray-600 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded-full">
                                                    <svg class="w-3 h-3 mr-1 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                    </svg>
                                                    <span class="font-medium">{{ $academicGroup->teachers()->count() }}</span>
                                                    <span class="ml-1">teachers</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Enhanced Quick Stats for Desktop -->
                                    <div class="hidden sm:flex items-center space-x-6 mr-6">
                                        <div class="text-center p-3 bg-purple-50 dark:bg-purple-900/20 rounded-xl border border-purple-200 dark:border-purple-700">
                                            <div class="flex items-center justify-center mb-1">
                                                <svg class="w-4 h-4 text-purple-600 dark:text-purple-400 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-2M7 21H5m2 0h2m6-11V7h-2v3m0 0V7h2v3m-2 0h2v3m-2-3h-2v3"></path>
                                                </svg>
                                                <span class="text-lg font-bold text-purple-600 dark:text-purple-400">{{ $academicGroup->academicLevels()->count() }}</span>
                                            </div>
                                            <div class="text-xs text-purple-600 dark:text-purple-400 font-medium">Levels</div>
                                        </div>
                                        <div class="text-center p-3 bg-green-50 dark:bg-green-900/20 rounded-xl border border-green-200 dark:border-green-700">
                                            <div class="flex items-center justify-center mb-1">
                                                <svg class="w-4 h-4 text-green-600 dark:text-green-400 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                </svg>
                                                <span class="text-lg font-bold text-green-600 dark:text-green-400">{{ $academicGroup->teachers()->count() }}</span>
                                            </div>
                                            <div class="text-xs text-green-600 dark:text-green-400 font-medium">Teachers</div>
                                        </div>
                                    </div>

                                    <!-- Enhanced Action Buttons -->
                                    <div class="flex items-center space-x-2 sm:ml-6 sm:space-x-3">
                                        <a href="{{ route('academic-groups.show', ['academic_group' => $academicGroup]) }}"
                                           class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-lg text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 group">
                                            <svg class="w-4 h-4 mr-1.5 group-hover:text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            <span class="hidden sm:inline">View</span>
                                            <span class="sm:hidden">👁</span>
                                        </a>

                                        @can('administrate')
                                            <a href="{{ route('academic-groups.edit', ['academic_group' => $academicGroup]) }}"
                                               class="inline-flex items-center px-3 py-2 border border-amber-300 dark:border-amber-600 text-sm font-medium rounded-lg text-amber-700 dark:text-amber-300 bg-white dark:bg-gray-700 hover:bg-amber-50 dark:hover:bg-amber-900/20 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 transition-all duration-200 group">
                                                <svg class="w-4 h-4 mr-1.5 group-hover:text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                                <span class="hidden sm:inline">Edit</span>
                                                <span class="sm:hidden">✏️</span>
                                            </a>

                                            <form method="post" action="{{ route('academic-groups.destroy', ['academic_group' => $academicGroup]) }}" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="page" value="{{ $academicGroups->currentPage() }}">
                                                <button type="submit"
                                                        onclick="return confirm('⚠️ Are you sure you want to delete this academic group?\n\nThis action cannot be undone and will affect all associated levels and teachers.')"
                                                        class="inline-flex items-center px-3 py-2 border border-red-300 dark:border-red-600 text-sm font-medium rounded-lg text-red-700 dark:text-red-400 bg-white dark:bg-gray-700 hover:bg-red-50 dark:hover:bg-red-900/20 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-200 group">
                                                    <svg class="w-4 h-4 mr-1.5 group-hover:text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                    <span class="hidden sm:inline">Delete</span>
                                                    <span class="sm:hidden">🗑️</span>
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Pagination -->
                @if($academicGroups->hasPages())
                    <div class="mt-6">
                        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4 transition-colors duration-200">
                            {{ $academicGroups->appends(request()->query())->links() }}
                        </div>
                    </div>
                @endif
            </div>
        @else
            <!-- Empty State -->
            <div class="p-8 sm:p-12 text-center">
                <div class="mx-auto w-20 h-20 sm:w-24 sm:h-24 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4 transition-colors duration-200">
                    <svg class="w-10 h-10 sm:w-12 sm:h-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-2M7 21H5m2 0h2m6-11V7h-2v3m0 0V7h2v3m-2 0h2v3m-2-3h-2v3"></path>
                    </svg>
                </div>
                <h3 class="text-lg sm:text-xl font-semibold text-gray-900 dark:text-white mb-2">No Academic Groups Yet</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-6 max-w-md mx-auto">Get started by creating your first academic group to organize your educational structure.</p>
                @can('administrate')
                    <a href="{{ route('academic-groups.create') }}"
                       class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 shadow-sm">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Create Academic Group
                    </a>
                @endcan
            </div>
        @endif
    </div>

    <!-- JavaScript for Enhanced Functionality -->
    <script>
        // Search functionality
        document.getElementById('search-input').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const groups = document.querySelectorAll('.group-item');

            groups.forEach(group => {
                const name = group.dataset.name;
                if (name.includes(searchTerm)) {
                    group.style.display = 'block';
                } else {
                    group.style.display = 'none';
                }
            });
        });

        // Toggle view functionality (placeholder)
        function toggleView() {
            // Implementation for grid/list view toggle
            console.log('Toggle view functionality can be implemented here');
        }

        // Enhanced delete confirmation
        document.querySelectorAll('form[method="post"]').forEach(form => {
            form.addEventListener('submit', function(e) {
                if (!confirm('Are you sure you want to delete this academic group? This action cannot be undone.')) {
                    e.preventDefault();
                }
            });
        });
    </script>
</x-layouts.app>
