<x-dashboard title="Academic Groups" summary="All educational groups">
    <table class="w-full divide-y divide-gray-300">
        <caption>
            <div class="flex items-center justify-between px-2 py-3">
                <div class="font-medium text-gray-500 tracking-wide">
                    List all academic groups
                </div>
                <div>
                    @can('administrate')
                    <x-button :to="route('academic-groups.create')">Add new academic group</x-button>
                    @endcan
                </div>
            </div>
        </caption>
        <thead>
            <tr>
                <x-table.th>ID</x-table.th>
                <x-table.th>Name</x-table.th>
                @can('administrate')
                <x-table.th><span class="sr-only">Actions</span></x-table.th>
                @endcan
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @foreach ($academicGroups as $academicGroup)
            <tr>
                <td class="p-2 text-sm text-gray-500">#{{ $academicGroup->id }}</td>
                <td class="p-2 text-sm text-gray-900 font-medium">{{ $academicGroup->name }}</td>
                @can('administrate')
                <td class="p-2 text-sm text-primary-600 space-x-3">
                    <a href="{{ route('academic-groups.academic-levels.create', ['academic_group' => $academicGroup]) }}">Level</a>
                    <a href="{{ route('academic-groups.edit', ['academic_group' => $academicGroup]) }}">Edit</a>
                </td>
                @endcan
            </tr>
            @endforeach
        </tbody>
    </table>
</x-dashboard>