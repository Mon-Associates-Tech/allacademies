<x-auth title="Academic Groups" :main-only="true">
    <x-slot name="breadcrumb">
        <x-breadcrumb />
    </x-slot>
    @can('administrate')
    <x-slot name="action">
        <x-link.primary :to="route('academic-groups.create')">New Academic Group</x-link.primary>
    </x-slot>
    @endcan

    @if ($academicGroups->count())
    <x-table>
        <x-slot name="head">
            <tr>
                <x-table.th>Name</x-table.th>
                <x-table.th><span class="sr-only">Actions</span></x-table.th>
            </tr>
        </x-slot>

        @foreach ($academicGroups as $academicGroup)
            <tr>
                <x-table.td bold>{{ $academicGroup->name }}</x-table.td>
                <x-table.td action>
                    <x-action name="view" :to="route('academic-groups.show', ['academic_group' => $academicGroup])" />
                    @can('administrate')
                    <x-action name="edit" :to="route('academic-groups.edit', ['academic_group' => $academicGroup])" />
                    <x-action name="delete" :to="route('academic-groups.destroy', ['academic_group' => $academicGroup])">
                        Are you sure you want to delete {{ $academicGroup->name }}
                    </x-action>
                    @endcan
                </x-table.td>
            </tr>
        @endforeach
    </x-table>

    <div class="mt-3">
        {{ $academicGroups->links() }}
    </div>
    @else
    <x-blank />
    @endif
</x-auth>
