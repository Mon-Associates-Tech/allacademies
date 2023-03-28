<x-auth title="Academic Level Details">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Academic Groups' => route('academic-groups.index'),
            $academicLevel->academicGroup->name => route('academic-groups.show', ['academic_group' => $academicLevel->academicGroup]),
            'Academic Levels' => route('academic-groups.academic-levels.index', ['academic_group' => $academicLevel->academicGroup]),
        ]" />
    </x-slot>

    <x-detail>
        <x-detail.data label="Name">{{ $academicLevel->name }}</x-detail.data>
        <x-detail.data label="Label">{{ $academicLevel->label }}</x-detail.data>

        <x-detail.data label="Academic Group">
            <x-anchor to="{{ route('academic-groups.show', ['academic_group' => $academicLevel->academicGroup]) }}">
                {{ $academicLevel->academicGroup->name }}
            </x-anchor>
        </x-detail.data>

        <x-detail.data label="Academic Subjects">
            <x-anchor to="{{ route('academic-levels.academic-subjects.index', ['academic_level' => $academicLevel]) }}">
                {{ $academicLevel->academic_subjects_count }} academic {{ Str::plural('subject', $academicLevel->academic_subjects_count) }}
            </x-anchor>
        </x-detail.data>

        @can('administrate')
        <x-slot name="action">
            <x-button.secondary type="buttton" x-data="{}" x-on:click="$store.deleteForm.show('Danger', 'Are you sure you want to delete {{ $academicLevel->name }}', '{{ route('academic-levels.destroy', ['academic_level' => $academicLevel]) }}')">Delete Academic Level</x-button.secondary>
            <x-link.primary :to="route('academic-levels.edit', ['academic_level' => $academicLevel])">Edit Academic Level</x-link.primary>
        </x-slot>
        @endcan
    </x-detail>
</x-auth>