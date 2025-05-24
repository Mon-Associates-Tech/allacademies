<x-layouts.app title="Academic Levels" :page-name="'Academic Levels'">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Academic Groups' => route('academic-groups.index'),
            $academicGroup->name => route('academic-groups.show', ['academic_group' => $academicGroup]),
        ]" />
    </x-slot>
    @can('administrate')
    <x-slot name="action">
        <x-link.primary :to="route('academic-groups.academic-levels.create', ['academic_group' => $academicGroup])">New Academic Level</x-link.primary>
    </x-slot>
    @endcan

    @if ($academicLevels->count())
    <x-table>
        <x-slot name="head">
            <tr>
                <x-table.th>Name</x-table.th>
                <x-table.th>Label</x-table.th>
                <x-table.th><span class="sr-only">Actions</span></x-table.th>
            </tr>
        </x-slot>

        @foreach ($academicLevels as $academicLevel)
            <tr>
                <x-table.td bold>{{ $academicLevel->name }}</x-table.td>
                <x-table.td>{{ $academicLevel->label }}</x-table.td>
                <x-table.td action>
                    <x-action name="view" :to="route('academic-levels.show', ['academic_level' => $academicLevel])" />
                    @can('administrate')
                    <x-action name="edit" :to="route('academic-levels.edit', ['academic_level' => $academicLevel])" />
                    <x-action name="delete" :to="route('academic-levels.destroy', ['academic_level' => $academicLevel])">
                        Are you sure you want to delete {{ $academicLevel->name }}
                    </x-action>
                    @endcan
                </x-table.td>
            </tr>
        @endforeach
    </x-table>

    <div class="mt-3">
        {{ $academicLevels->links() }}
    </div>
    @else
    <x-blank />
    @endif
</x-layouts.app>
