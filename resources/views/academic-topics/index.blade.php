<x-auth title="Academic Topics">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Academic Groups' => route('academic-groups.index'),
            $academicSubject->academicLevel->academicGroup->name => route('academic-groups.show', ['academic_group' => $academicSubject->academicLevel->academicGroup]),
            'Academic Levels' => route('academic-groups.academic-levels.index', ['academic_group' => $academicSubject->academicLevel->academicGroup]),
            $academicSubject->academicLevel->name => route('academic-levels.show', ['academic_level' => $academicSubject->academicLevel]),
            'Academic Subjects' => route('academic-levels.academic-subjects.index', ['academic_level' => $academicSubject->academicLevel]),
            $academicSubject->name => route('academic-subjects.show', ['academic_subject' => $academicSubject]),
        ]" />
    </x-slot>
    @can('administrate')
    <x-slot name="action">
        <x-link.primary :to="route('academic-subjects.academic-topics.create', ['academic_subject' => $academicSubject])">New Academic Topic</x-link.primary>
    </x-slot>
    @endcan

    @if ($academicTopics->count())
    <x-table>
        <x-slot name="head">
            <tr>
                <x-table.th>Name</x-table.th>
                <x-table.th><span class="sr-only">Actions</span></x-table.th>
            </tr>
        </x-slot>

        @foreach ($academicTopics as $academicTopic)
            <tr>
                <x-table.td bold>{{ $academicTopic->name }}</x-table.td>
                <x-table.td action>
                    <x-action name="view" :to="route('academic-topics.show', ['academic_topic' => $academicTopic])" />
                    @can('administrate')
                    <x-action name="edit" :to="route('academic-topics.edit', ['academic_topic' => $academicTopic])" />
                    <x-action name="delete" :to="route('academic-topics.destroy', ['academic_topic' => $academicTopic])">
                        Are you sure you want to delete {{ $academicTopic->name }}
                    </x-action>
                    @endcan
                </x-table.td>
            </tr>
        @endforeach
    </x-table>

    <div class="mt-3">
        {{ $academicTopics->links() }}
    </div>
    @else
    <x-blank />
    @endif
</x-auth>
