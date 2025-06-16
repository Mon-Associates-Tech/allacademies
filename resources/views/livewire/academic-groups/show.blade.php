<x-layouts.app title="Academic Group Details" :has-action="false">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Academic Groups' => route('academic-groups.index'),
        ]" />
    </x-slot>

    <x-detail>
        <x-detail.data label="Name">{{ $academicGroup->name }}</x-detail.data>

        <x-detail.data label="Academic Levels">
            <x-anchor to="{{ route('academic-groups.academic-levels.index', ['academic_group' => $academicGroup]) }}">
                {{ $academicGroup->academic_levels_count }} academic {{ Str::plural('level', $academicGroup->academic_levels_count) }}
            </x-anchor>
        </x-detail.data>

        @can('administrate')
        <x-slot name="action">
            <x-button.secondary type="button" x-data="{}" x-on:click="$store.deleteForm.show('Danger', 'Are you sure you want to delete {{ $academicGroup->name }}', '{{ route('academic-groups.destroy', ['academic_group' => $academicGroup]) }}')">Delete Academic Group</x-button.secondary>
            <x-link.primary :to="route('academic-groups.edit', ['academic_group' => $academicGroup])">Edit Academic Group</x-link.primary>
        </x-slot>
        @endcan
    </x-detail>
</x-layouts.app>
