<x-auth title="Academic Subject Details">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Academic Groups' => route('academic-groups.index'),
            $academicSubject->academicLevel->academicGroup->name => route('academic-groups.show', ['academic_group' => $academicSubject->academicLevel->academicGroup]),
            'Academic Levels' => route('academic-groups.academic-levels.index', ['academic_group' => $academicSubject->academicLevel->academicGroup]),
            $academicSubject->academicLevel->name => route('academic-levels.show', ['academic_level' => $academicSubject->academicLevel]),
            'Academic Subjects' => route('academic-levels.academic-subjects.index', ['academic_level' => $academicSubject->academicLevel]),
        ]" />
    </x-slot>

    <x-detail>
        <x-detail.data label="Name">{{ $academicSubject->name }}</x-detail.data>
        <x-detail.data label="Label">{{ $academicSubject->code }}</x-detail.data>

        <x-detail.data label="Academic Level">
            <x-anchor to="{{ route('academic-levels.show', ['academic_level' => $academicSubject->academicLevel]) }}">
                {{ $academicSubject->academicLevel->name }}
            </x-anchor>
        </x-detail.data>

        <x-detail.data label="Academic Topics">
            <x-anchor to="{{ route('academic-subjects.academic-topics.index', ['academic_subject' => $academicSubject]) }}">
                {{ $academicSubject->academic_topics_count }} academic {{ Str::plural('topic', $academicSubject->academic_topics_count) }}
            </x-anchor>
        </x-detail.data>

        @can('administrate')
        <x-slot name="action">
            <x-button.secondary type="buttton" x-data="{}" x-on:click="$store.deleteForm.show('Danger', 'Are you sure you want to delete {{ $academicSubject->name }}', '{{ route('academic-subjects.destroy', ['academic_subject' => $academicSubject]) }}')">Delete Academic Subject</x-button.secondary>
            <x-link.primary :to="route('academic-subjects.edit', ['academic_subject' => $academicSubject])">Edit Academic Subject</x-link.primary>
        </x-slot>
        @endcan
    </x-detail>
</x-auth>