<x-layouts.app title="Academic Topics" :page-name="$academicSubject->name">
        <x-slot name="breadcrumb">
            <x-breadcrumb :paths="[
                'Academic Groups' => route('academic-groups.index'),
                $academicSubject->academicLevel->academicGroup->name => route('academic-groups.show', ['academic_group' => $academicSubject->academicLevel->academicGroup]),
                'Academic Levels' => route('academic-levels.index', ['academic_group' => $academicSubject->academicLevel->academicGroup]),
                $academicSubject->academicLevel->name => route('academic-levels.show', ['academic_level' => $academicSubject->academicLevel, 'academic_group' => getRouteParameter('academic_group')]),
                'Academic Subjects' => route('academic-subjects.index', ['academic_level' => $academicSubject->academicLevel, 'academic_group'=>getRouteParameter('academic_group')]),
                $academicSubject->name => route('academic-subjects.show', ['academic_subject' => $academicSubject, 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')]),
            ]" />
        </x-slot>

        <div class="space-y-6">
            <!-- Header Section -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="p-3 bg-blue-100 rounded-full">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">Topics for {{ $academicSubject->name }}</h1>
                            <p class="text-gray-600">{{ $academicSubject->description }}</p>
                        </div>
                    </div>

                    @can('administrate')
                        <x-link.primary :to="route('academic-topics.create', ['academic_subject' => $academicSubject, 'academic_level' => $academicSubject->academicLevel, 'academic_group' => $academicSubject->academicLevel->academicGroup])" class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            New Topic
                        </x-link.primary>
                    @endcan
                </div>
            </div>

            <!-- Topics List -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                @if ($academicTopics->count())
                    <div class="overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Topic Name</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($academicTopics as $academicTopic)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="text-sm font-medium text-gray-900">{{ $academicTopic->name }}</div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex items-center justify-end space-x-3">
                                                <x-link.secondary :to="route('academic-topics.show', ['academic_topic' => $academicTopic, 'academic_subject' => getRouteParameter('academic_subject'), 'academic_level' => getRouteParameter('academic_level'), 'academic_group'=>getRouteParameter('academic_group')])">
                                                    View
                                                </x-link.secondary>

                                                @can('administrate')
                                                    <x-link.secondary :to="route('academic-topics.edit', ['academic_topic' => $academicTopic, 'academic_subject' => getRouteParameter('academic_subject'), 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')])">
                                                        Edit
                                                    </x-link.secondary>

                                                    <x-button.secondary
                                                        x-data="{}"
                                                        x-on:click="$store.deleteForm.show('Delete Topic', 'Are you sure you want to delete {{ $academicTopic->name }}?', '{{ route('academic-topics.destroy', ['academic_topic' => $academicTopic, 'academic_subject' => getRouteParameter('academic_subject'), 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')]) }}')"
                                                        class="text-red-600 hover:text-red-700"
                                                    >
                                                        Delete
                                                    </x-button.secondary>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($academicTopics->hasPages())
                        <div class="px-6 py-4 border-t border-gray-200">
                            {{ $academicTopics->links() }}
                        </div>
                    @endif
                @else
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No topics</h3>
                        <p class="mt-1 text-sm text-gray-500">Get started by creating a new topic.</p>
                        @can('administrate')
                            <div class="mt-6">
                                <x-link.primary :to="route('academic-topics.create', ['academic_subject' => $academicSubject, 'academic_level' => $academicSubject->academicLevel, 'academic_group' => $academicSubject->academicLevel->academicGroup])">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                    </svg>
                                    New Topic
                                </x-link.primary>
                            </div>
                        @endcan
                    </div>
                @endif
            </div>
        </div>
    </x-layouts.app>
