<x-auth :main-only="true" title="Subtopics" page-name="Academic Subtopics" :has-action="true" action-link-text="Add Subtopic"
:action_link="route('academic-topics.subtopics.create', ['academic_topic' => $academic_topic])"
>
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Academic Groups' => route('academic-groups.index'),
            $academic_topic?->academicSubject->academicLevel->academicGroup->name => route('academic-groups.show', ['academic_group' => $academicSubject->academicLevel->academicGroup]),
            'Academic Levels' => route('academic-groups.academic-levels.index', ['academic_group' => $academicSubject->academicLevel->academicGroup]),
            $academic_topic?->academicSubject->academicLevel->name => route('academic-levels.show', ['academic_level' => $academicSubject->academicLevel]),
            'Academic Subjects' => route('academic-levels.academic-subjects.index', ['academic_level' => $academicSubject->academicLevel]),
            $academic_topic?->academicSubject->name => route('academic-subjects.show', ['academic_subject' => $academicSubject]),
        ]" />
    </x-slot>
    <div>
        @if ($subtopics->count())
            <x-table>
                <x-slot name="head">
                    <tr>
                        <x-table.th>Sub Topic</x-table.th>
                        <x-table.th>Topic</x-table.th>
                        <x-table.th><span class="sr-only">Actions</span></x-table.th>
                    </tr>
                </x-slot>

                @foreach ($subtopics as $subtopic)
                    <tr>
                        <x-table.td bold>{{ $subtopic->name }}</x-table.td>
                        <x-table.td bold>{{ $academic_topic->name }}</x-table.td>
                        <x-table.td action>
                            <x-action name="view"
                                      :to="route('academic-topics.subtopics.show', ['academic_topic' => $academic_topic, 'subtopic' => $subtopic])"/>
                            @can('administrate')
                                <x-action name="edit"
                                          :to="route('academic-topics.subtopics.edit', ['academic_topic' => $academic_topic, 'subtopic' => $subtopic])"/>
                                <x-action name="delete"
                                          :to="route('academic-topics.subtopics.destroy', ['academic_topic' => $academic_topic, 'subtopic' => $subtopic])">
                                    Are you sure you want to delete {{ $subtopic->name }}
                                </x-action>
                            @endcan
                        </x-table.td>
                    </tr>
                @endforeach
            </x-table>

            <div class="mt-3">
                {{ $subtopics->links() }}
            </div>
        @else
            <x-blank/>
        @endif
    </div>
</x-auth>

