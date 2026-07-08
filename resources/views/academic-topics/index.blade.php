<x-layouts.app title="Academic Topics" :show-title-area="false" :page-name="$academicSubject->name">
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
        <!-- Unified container for header and content -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <!-- Header Section using academic-header component -->
            <x-academic-header>
                <x-slot name="headerContent">
                    <div class="flex items-center space-x-4">
                        <div>
                            <h1 class="text-xl sm:text-2xl font-bold text-gray-800 dark:text-white">Topics for {{ $academicSubject->name }}</h1>
                            <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">{{ $academicSubject->description ?? 'Manage topics for this subject' }}</p>
                        </div>
                    </div>
                </x-slot>

                @can('administrate')
                    <x-slot name="headerActions">
                        <x-link.primary :to="route('academic-topics.create', ['academic_subject' => $academicSubject, 'academic_level' => $academicSubject->academicLevel, 'academic_group' => $academicSubject->academicLevel->academicGroup])"
                                        class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200 hover:scale-105">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            New Topic
                        </x-link.primary>
                    </x-slot>
                @endcan
            </x-academic-header>

            <!-- Topics List -->
            <div class="border-t border-gray-200 dark:border-gray-700">
                @if ($academicTopics->count())
                    <div class="overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700/50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Topic Name</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach ($academicTopics as $academicTopic)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center">
                                                    <span class="text-white font-bold text-sm">{{ strtoupper(substr($academicTopic->name, 0, 1)) }}</span>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $academicTopic->name }}</div>
                                                    @if($academicTopic->subtopics_count > 0)
                                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                                            {{ $academicTopic->subtopics_count }} {{ Str::plural('subtopic', $academicTopic->subtopics_count) }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex items-center justify-end space-x-2">
                                                <x-link.secondary :to="route('academic-topics.show', ['academic_topic' => $academicTopic, 'academic_subject' => getRouteParameter('academic_subject'), 'academic_level' => getRouteParameter('academic_level'), 'academic_group'=>getRouteParameter('academic_group')])"
                                                                  class="inline-flex items-center px-3 py-1.5 text-sm font-medium rounded-lg transition-all duration-200 hover:scale-105">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                    </svg>
                                                    View
                                                </x-link.secondary>

                                                @can('administrate')
                                                    <x-link.secondary :to="route('academic-topics.edit', ['academic_topic' => $academicTopic, 'academic_subject' => getRouteParameter('academic_subject'), 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')])"
                                                                      class="inline-flex items-center px-3 py-1.5 text-sm font-medium rounded-lg transition-all duration-200 hover:scale-105">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                        </svg>
                                                        Edit
                                                    </x-link.secondary>

                                                    <button type="button"
                                                            x-data="{}"
                                                            x-on:click="$store.deleteForm.show('Delete Topic', 'Are you sure you want to delete {{ $academicTopic->name }}?', '{{ route('academic-topics.destroy', ['academic_topic' => $academicTopic, 'academic_subject' => getRouteParameter('academic_subject'), 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')]) }}')"
                                                            class="inline-flex items-center px-3 py-1.5 text-sm font-medium rounded-lg text-red-700 dark:text-red-400 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 hover:bg-red-100 dark:hover:bg-red-900/30 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition-all duration-200 hover:scale-105">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                        </svg>
                                                        Delete
                                                    </button>
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
                        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                            <div class="flex justify-center">
                                {{ $academicTopics->links() }}
                            </div>
                        </div>
                    @endif
                @else
                    <div class="text-center py-16 px-6">
                        <div class="mx-auto w-20 h-20 sm:w-24 sm:h-24 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center mb-6 shadow-lg">
                            <svg class="w-10 h-10 sm:w-12 sm:h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">No topics found</h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-8 max-w-md mx-auto">
                            Get started by creating your first topic for <span class="font-semibold text-indigo-600 dark:text-indigo-400">{{ $academicSubject->name }}</span>.
                        </p>
                        @can('administrate')
                            <div class="space-y-4">
                                <x-link.primary :to="route('academic-topics.create', ['academic_subject' => $academicSubject, 'academic_level' => $academicSubject->academicLevel, 'academic_group' => $academicSubject->academicLevel->academicGroup])"
                                                class="inline-flex items-center px-6 py-3 text-base font-medium rounded-lg transition-all duration-200 hover:scale-105">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                    </svg>
                                    Create First Topic
                                </x-link.primary>
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    <p>✨ Start building your curriculum content</p>
                                </div>
                            </div>
                        @endcan
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.app>
