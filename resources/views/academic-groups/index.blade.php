<x-layouts.app title="Academic Groups" page-name="Academic Groups">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="['Academic Groups' => null]" />
    </x-slot>

    <!-- Header Section -->
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-8 py-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                <div class="flex items-center">
                    <div class="flex-shrink-0 w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-2M7 21H5m2 0h2m6-11V7h-2v3m0 0V7h2v3m-2 0h2v3m-2-3h-2v3"></path>
                        </svg>
                    </div>
                    <div class="ml-6">
                        <h1 class="text-3xl font-bold text-white mb-2">Academic Groups</h1>
                        <p class="text-blue-100">Manage your educational hierarchies</p>
                    </div>
                </div>

                @can('administrate')
                    <div class="mt-4 lg:mt-0">
                        <a href="{{ route('academic-groups.create') }}"
                           class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-xl text-blue-600 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white transition-all duration-200 shadow-sm">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            New Academic Group
                        </a>
                    </div>
                @endcan
            </div>
        </div>

        @if ($academicGroups->count())
            <!-- Quick Stats -->
            <div class="px-8 py-6 bg-gray-50 border-b border-gray-200">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-2M7 21H5m2 0h2m6-11V7h-2v3m0 0V7h2v3m-2 0h2v3m-2-3h-2v3"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <div class="text-2xl font-bold text-gray-900">{{ $academicGroups->total() }}</div>
                            <div class="text-sm text-gray-600">Total Groups</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Groups Grid -->
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($academicGroups as $academicGroup)
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-all duration-200 group overflow-hidden">
                            <div class="p-6">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center space-x-3">
                                        <div class="p-2 bg-blue-100 rounded-lg group-hover:bg-blue-200 transition-colors duration-200">
                                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-2M7 21H5m2 0h2m6-11V7h-2v3m0 0V7h2v3m-2 0h2v3m-2-3h-2v3"></path>
                                            </svg>
                                        </div>
                                        <h3 class="text-lg font-semibold text-gray-900 group-hover:text-blue-600 transition-colors duration-200">
                                            {{ $academicGroup->name }}
                                        </h3>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex flex-col gap-2 mt-4">
                                    <a href="{{ route('academic-groups.show', ['academic_group' => $academicGroup]) }}"
                                       class="inline-flex items-center justify-center px-4 py-2.5 border border-transparent text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        View Details
                                    </a>

                                    @can('administrate')
                                        <div class="flex gap-2">
                                            <a href="{{ route('academic-groups.edit', ['academic_group' => $academicGroup]) }}"
                                               class="flex-1 inline-flex items-center justify-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                                Edit
                                            </a>

                                            <button type="button"
                                                    x-data="{}"
                                                    x-on:click="$store.deleteForm.show('Danger', 'Are you sure you want to delete {{ $academicGroup->name }}?', '{{ route('academic-groups.destroy', ['academic_group' => $academicGroup]) }}')"
                                                    class="inline-flex items-center justify-center px-3 py-2 border border-red-300 text-sm font-medium rounded-lg text-red-700 bg-red-50 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-200">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </div>
                                    @endcan
                                </div>
                            </div>

                            <!-- Footer -->
                            <div class="px-6 py-3 bg-gray-50 border-t border-gray-200">
                                <div class="flex items-center justify-between text-xs text-gray-500">
                                    <span>ID: #{{ $academicGroup->id }}</span>
                                    <span>Created {{ $academicGroup->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Pagination -->
            @if($academicGroups->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $academicGroups->links() }}
                </div>
            @endif
        @else
            <!-- Empty State -->
            <div class="text-center py-16 px-6">
                <svg class="mx-auto h-20 w-20 text-gray-400 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-2M7 21H5m2 0h2m6-11V7h-2v3m0 0V7h2v3m-2 0h2v3m-2-3h-2v3"></path>
                </svg>

                <h3 class="text-2xl font-semibold text-gray-900 mb-3">No Academic Groups Found</h3>
                <p class="text-gray-600 mb-8 max-w-md mx-auto">
                    Start organizing your educational content by creating your first academic group.
                </p>

                @can('administrate')
                    <a href="{{ route('academic-groups.create') }}"
                       class="inline-flex items-center px-8 py-3 border border-transparent text-base font-medium rounded-xl text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 shadow-md hover:shadow-lg">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Create First Academic Group
                    </a>
                @endcan
            </div>
        @endif
    </div>
</x-layouts.app>
