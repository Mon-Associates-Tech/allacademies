<x-layouts.app title="Academic Subjects" page-name="Academic Subjects">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Academic Groups' => route('academic-groups.index'),
            $academicLevel->academicGroup->name => route('academic-groups.show', ['academic_group' => $academicLevel->academicGroup]),
            'Academic Levels' => route('academic-levels.index', ['academic_group' => $academicLevel->academicGroup]),
            $academicLevel->name => route('academic-levels.show', ['academic_level' => $academicLevel, 'academic_group' => Route::getCurrentRoute()->parameter('academic_group')]),
        ]" />
    </x-slot>

    <!-- Header Section -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="p-3 bg-indigo-100 rounded-full">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Academic Subjects</h1>
                    <p class="text-gray-600">{{ $academicLevel->name }} - {{ $academicLevel->academicGroup->name }}</p>
                </div>
            </div>
            @can('administrate')
                <x-link.primary :to="route('academic-subjects.create', ['academic_level' => $academicLevel, 'academic_group' => $academicLevel->academicGroup])">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    New Academic Subject
                </x-link.primary>
            @endcan
        </div>
    </div>

    @if ($academicSubjects->count())
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <ul class="divide-y divide-gray-200">
                @foreach ($academicSubjects as $academicSubject)
                    <li class="hover:bg-gray-50 transition-colors duration-200">
                        <div class="px-6 py-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-4">
                                    <div class="flex-shrink-0 w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                                        <span class="text-indigo-600 font-semibold">{{ strtoupper(substr($academicSubject->code, 0, 2)) }}</span>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900">{{ $academicSubject->name }}</h3>
                                        <p class="text-sm text-gray-600">Code: {{ $academicSubject->code }}</p>
                                    </div>
                                </div>

                                <div class="flex items-center space-x-2">
                                    <x-link.secondary :to="route('academic-subjects.show', ['academic_subject' => $academicSubject, 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')])" class="inline-flex items-center px-3 py-1">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        View
                                    </x-link.secondary>

                                    @can('administrate')
                                        <x-link.secondary :to="route('academic-subjects.edit', ['academic_subject' => $academicSubject, 'academic_level' => $academicLevel, 'academic_group' => getRouteParameter('academic_group')])" class="inline-flex items-center px-3 py-1">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                            Edit
                                        </x-link.secondary>

                                        <button type="button"
                                                x-data="{}"
                                                x-on:click="$store.deleteForm.show('Delete Subject', 'Are you sure you want to delete {{ $academicSubject->name }}?', '{{ route('academic-subjects.destroy', ['academic_subject' => $academicSubject, 'academic_level' => $academicLevel, 'academic_group' => getRouteParameter('academic_group')]) }}')"
                                                class="inline-flex items-center px-3 py-1 border border-red-300 text-sm font-medium rounded-md text-red-700 bg-red-50 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-200">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                            Delete
                                        </button>
                                    @endcan
                                </div>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="mt-6">
            {{ $academicSubjects->links() }}
        </div>
    @else
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="text-center py-16 px-6">
                <svg class="mx-auto h-20 w-20 text-gray-400 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
                <h3 class="text-2xl font-semibold text-gray-900 mb-3">No Subjects Found</h3>
                <p class="text-gray-600 mb-6">There are no academic subjects for {{ $academicLevel->name }} yet.</p>
                @can('administrate')
                    <x-link.primary :to="route('academic-subjects.create', ['academic_level' => $academicLevel, 'academic_group' => $academicLevel->academicGroup])">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Create First Subject
                    </x-link.primary>
                @endcan
            </div>
        </div>
    @endif
</x-layouts.app>
