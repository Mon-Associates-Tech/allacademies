<x-dashboard title="Academic Levels" summary="All available educational levels">
    <table class="w-full divide-y divide-gray-300">
        <caption>
            <div class="flex items-center justify-between px-2 py-3">
                <div class="font-medium text-gray-500 tracking-wide">
                    List all available academic levels
                </div>
                <div>
                    <x-button :to="route('academic-levels.create')">Add new academic level</x-button>
                </div>
            </div>
        </caption>
        <thead>
            <tr>
                <x-table.th>ID</x-table.th>
                <x-table.th>Name</x-table.th>
                <x-table.th>Label</x-table.th>
                <x-table.th><span class="sr-only">Actions</span></x-table.th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @foreach ($academicLevels as $academicLevel)
            <tr>
                <td class="p-2 text-sm text-gray-500">#{{ $academicLevel->id }}</td>
                <td class="p-2 text-sm text-gray-900 font-medium">{{ $academicLevel->name }}</td>
                <td class="p-2 text-sm text-gray-500">{{ $academicLevel->label }}</td>
                <td class="p-2 text-sm text-primary-600 space-x-3">
                    <a href="{{ route('academic-levels.academic-subjects.create', ['academic_level' => $academicLevel]) }}">Subject</a>
                    <a href="{{ route('academic-levels.edit', ['academic_level' => $academicLevel]) }}">Edit</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</x-dashboard>