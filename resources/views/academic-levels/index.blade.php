<x-layouts.app title="Academic Levels" :page-name="'Academic Levels'">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Academic Groups' => route('academic-groups.index'),
            $academicGroup->name => route('academic-groups.show', ['academic_group' => $academicGroup]),
            'Academic Levels' => '#'
        ]" />
    </x-slot>

    <div class="max-w-7xl mx-auto space-y-6">
        <!-- Header Section -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-8 py-6">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                        <div class="ml-6">
                            <h1 class="text-3xl font-bold text-white mb-2">Academic Levels</h1>
                            <p class="text-indigo-100 text-lg">{{ $academicGroup->name }}</p>
                            <div class="flex items-center mt-2">
                                <span class="text-indigo-200 text-sm">{{ $academicLevels->count() }} {{ Str::plural('level', $academicLevels->count()) }} total</span>
                            </div>
                        </div>
                    </div>

                    @can('administrate')
                        <div class="mt-4 lg:mt-0">
                            <a href="{{ route('academic-levels.create', ['academic_group' => $academicGroup]) }}"
                               class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-xl text-indigo-600 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white transition-all duration-200 shadow-sm">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                New Academic Level
                            </a>
                        </div>
                    @endcan
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="px-8 py-6 bg-gray-50 border-b border-gray-200">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <div class="text-2xl font-bold text-gray-900">{{ $academicLevels->count() }}</div>
                            <div class="text-sm text-gray-600">Academic {{ Str::plural('Level', $academicLevels->count()) }}</div>
                        </div>
                    </div>

                    <div class="flex items-center">
                        <div class="flex-shrink-0 w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <div class="text-2xl font-bold text-gray-900">{{ $academicLevels->sum('academic_subjects_count') ?? 0 }}</div>
                            <div class="text-sm text-gray-600">Total Subjects</div>
                        </div>
                    </div>

                    <div class="flex items-center">
                        <div class="flex-shrink-0 w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <div class="text-2xl font-bold text-gray-900">{{ $academicGroup->name }}</div>
                            <div class="text-sm text-gray-600">Parent Group</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if ($academicLevels->count())
            <!-- Academic Levels Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($academicLevels as $academicLevel)
                    <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 overflow-hidden group">
                        <!-- Card Header -->
                        <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center group-hover:bg-indigo-200 transition-colors duration-200">
                                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-lg font-semibold text-gray-900 group-hover:text-indigo-600 transition-colors duration-200">
                                            {{ $academicLevel->name }}
                                        </h3>
                                        @if($academicLevel->label)
                                            <p class="text-sm text-gray-600">{{ $academicLevel->label }}</p>
                                        @endif
                                    </div>
                                </div>

                                <!-- Status Badge -->
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            Active
                        </span>
                            </div>
                        </div>

                        <!-- Card Content -->
                        <div class="p-6">
                            <!-- Level Info -->
                            <div class="space-y-3 mb-6">
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-600">Academic Subjects</span>
                                    <span class="font-semibold text-gray-900">{{ $academicLevel->academic_subjects_count ?? 0 }}</span>
                                </div>

                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-600">Created</span>
                                    <span class="font-semibold text-gray-900">{{ $academicLevel->created_at->format('M Y') }}</span>
                                </div>

                                @if($academicLevel->description)
                                    <div class="mt-3">
                                        <p class="text-sm text-gray-600 line-clamp-2">{{ $academicLevel->description }}</p>
                                    </div>
                                @endif
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex flex-col gap-2">
                                <!-- Primary Action -->
                                <a href="{{ route('academic-levels.show', ['academic_level' => $academicLevel, 'academic_group' => $academicGroup]) }}"
                                   class="inline-flex items-center justify-center px-4 py-2.5 border border-transparent text-sm font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 group-hover:shadow-md">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    View Details
                                </a>

                                @can('administrate')
                                    <!-- Secondary Actions -->
                                    <div class="flex gap-2">
                                        <a href="{{ route('academic-levels.edit', ['academic_level' => $academicLevel, 'academic_group' => $academicGroup]) }}"
                                           class="flex-1 inline-flex items-center justify-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                            Edit
                                        </a>

                                        <button type="button"
                                                x-data="{}"
                                                x-on:click="$store.deleteForm.show('Danger', 'Are you sure you want to delete {{ $academicLevel->name }}?', '{{ route('academic-levels.destroy', ['academic_level' => $academicLevel, 'academic_group' => $academicGroup]) }}')"
                                                class="inline-flex items-center justify-center px-3 py-2 border border-red-300 text-sm font-medium rounded-lg text-red-700 bg-red-50 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-200">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </div>
                                @endcan
                            </div>
                        </div>

                        <!-- Quick Access Footer -->
                        <div class="px-6 py-3 bg-gray-50 border-t border-gray-200">
                            <div class="flex items-center justify-between text-xs text-gray-500">
                                <span>Level ID: #{{ $academicLevel->id }}</span>
                                <span>Updated {{ $academicLevel->updated_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($academicLevels->hasPages())
                <div class="bg-white px-4 py-3 border-t border-gray-200 rounded-2xl shadow-sm">
                    {{ $academicLevels->links() }}
                </div>
            @endif

        @else
            <!-- Empty State -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="text-center py-16 px-6">
                    <svg class="mx-auto h-20 w-20 text-gray-400 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>

                    <div class="max-w-md mx-auto">
                        <h3 class="text-2xl font-semibold text-gray-900 mb-3">No Academic Levels Found</h3>
                        <p class="text-gray-600 mb-8">
                            The <strong>{{ $academicGroup->name }}</strong> group doesn't have any academic levels yet.
                            Create your first level to start organizing your educational content.
                        </p>

                        @can('administrate')
                            <div class="space-y-4">
                                <a href="{{ route('academic-levels.create', ['academic_group' => $academicGroup]) }}"
                                   class="inline-flex items-center px-8 py-3 border border-transparent text-base font-medium rounded-xl text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 shadow-md hover:shadow-lg">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                    </svg>
                                    Create First Academic Level
                                </a>

                                <div class="text-sm text-gray-500">
                                    <p>Or <a href="{{ route('academic-groups.show', ['academic_group' => $academicGroup]) }}" class="text-indigo-600 hover:text-indigo-500 font-medium">go back to group overview</a></p>
                                </div>
                            </div>
                        @else
                            <div class="space-y-4">
                                <p class="text-sm text-gray-500">Contact an administrator to create academic levels for this group.</p>
                                <a href="{{ route('academic-groups.show', ['academic_group' => $academicGroup]) }}"
                                   class="inline-flex items-center px-6 py-2 border border-gray-300 text-base font-medium rounded-xl text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                                    </svg>
                                    Back to Group
                                </a>
                            </div>
                        @endcan
                    </div>

                    <!-- Helpful Tips -->
                    <div class="mt-12 pt-8 border-t border-gray-200">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-left">
                            <div class="flex items-start">
                                <div class="flex-shrink-0 w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h4 class="text-sm font-medium text-gray-900">Organize by Levels</h4>
                                    <p class="text-sm text-gray-600 mt-1">Academic levels help structure your content by grade or difficulty.</p>
                                </div>
                            </div>

                            <div class="flex items-start">
                                <div class="flex-shrink-0 w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h4 class="text-sm font-medium text-gray-900">Add Subjects</h4>
                                    <p class="text-sm text-gray-600 mt-1">Each level can contain multiple subjects and courses.</p>
                                </div>
                            </div>

                            <div class="flex items-start">
                                <div class="flex-shrink-0 w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h4 class="text-sm font-medium text-gray-900">Quick Setup</h4>
                                    <p class="text-sm text-gray-600 mt-1">Get started quickly with our guided level creation process.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Back to Group Link -->
        <div class="text-center">
            <a href="{{ route('academic-groups.show', ['academic_group' => $academicGroup]) }}"
               class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to {{ $academicGroup->name }}
            </a>
        </div>
    </div>
</x-layouts.app>
