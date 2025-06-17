<x-layouts.app
        :main-only="true"
        title="Subtopics - {{ $academic_topic->name }}"
        page-name="Academic Subtopics"
        :has-action="true"
        action-link-text="Add Subtopic"
        :action_link="route('subtopics.create', ['academic_topic' => $academic_topic, 'academic_subject' => getRouteParameter('academic_subject'), 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')])"
    >
        <x-slot name="breadcrumb">
            <x-breadcrumb :paths="[
                'Academic Groups' => route('academic-groups.index'),
                $academic_topic?->academicSubject->academicLevel->academicGroup->name => route('academic-groups.show', ['academic_group' => $academicSubject->academicLevel->academicGroup]),
                'Academic Levels' => route('academic-levels.index', ['academic_group' => $academicSubject->academicLevel->academicGroup]),
                $academic_topic?->academicSubject->academicLevel->name => route('academic-levels.show', ['academic_level' => $academicSubject->academicLevel, 'academic_group' => getRouteParameter('academic_group')]),
                'Academic Subjects' => route('academic-subjects.index', ['academic_level' => $academicSubject->academicLevel, 'academic_group' => getRouteParameter('academic_group')]),
                $academic_topic?->academicSubject->name => route('academic-subjects.show', ['academic_subject' => $academicSubject, 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')]),
                $academic_topic->name => route('academic-topics.show', ['academic_topic' => $academic_topic, 'academic_subject' => $academicSubject, 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group') ]),
                'Subtopics' => null,
            ]" />
        </x-slot>

        <!-- Topic Header -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">{{ $academic_topic->name }}</h2>
                    <p class="text-sm text-gray-600 mt-1">
                        Subject: {{ $academic_topic->academicSubject->name }}
                    </p>
                </div>
                <a href="{{ route('academic-topics.show', ['academic_topic' => $academic_topic, 'academic_subject' => $academicSubject, 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')]) }}"
                   class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                    View Topic Details →
                </a>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            @if ($subtopics->count())
                <x-table>
                    <x-slot name="head">
                        <tr>
                            <x-table.th>Subtopic Name</x-table.th>
                            <x-table.th>Created</x-table.th>
                            <x-table.th class="text-right">Actions</x-table.th>
                        </tr>
                    </x-slot>

                    @foreach ($subtopics as $subtopic)
                        <tr class="hover:bg-gray-50">
                            <x-table.td>
                                <a href="{{ route('subtopics.show', ['academic_topic' => $academic_topic, 'subtopic' => $subtopic, 'academic_subject' => getRouteParameter('academic_subject'), 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')]) }}"
                                   class="font-medium text-blue-600 hover:text-blue-800">
                                    {{ $subtopic->name }}
                                </a>
                            </x-table.td>
                            <x-table.td>
                                {{ $subtopic->created_at->diffForHumans() }}
                            </x-table.td>
                            <x-table.td action>
                                <div class="flex justify-end space-x-3">
                                    <x-action name="view"
                                        :to="route('subtopics.show', ['academic_topic' => $academic_topic, 'subtopic' => $subtopic, 'academic_subject' => getRouteParameter('academic_subject'), 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')])"
                                    />
                                    @can('administrate')
                                        <x-action name="edit"
                                            :to="route('subtopics.edit', ['academic_topic' => $academic_topic, 'subtopic' => $subtopic, 'academic_subject' => getRouteParameter('academic_subject'), 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')])"
                                        />
                                        <x-action name="delete"
                                            :to="route('subtopics.destroy', ['academic_topic' => $academic_topic, 'subtopic' => $subtopic, 'academic_subject' => getRouteParameter('academic_subject'), 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')])"
                                        >
                                            Are you sure you want to delete {{ $subtopic->name }}?
                                        </x-action>
                                    @endcan
                                </div>
                            </x-table.td>
                        </tr>
                    @endforeach
                </x-table>

                <div class="p-4 border-t border-gray-200">
                    {{ $subtopics->links() }}
                </div>
            @else
                <x-blank>
                    No subtopics have been created for this topic yet.
                </x-blank>
            @endif
        </div>
    </x-layouts.app>
