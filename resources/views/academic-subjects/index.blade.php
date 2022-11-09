<x-dashboard title="Academic Subjects" summary="Available academic subjects">
    <table class="w-full divide-y divide-gray-300">
        <caption>
            <div class="flex items-center justify-between px-2 py-3">
                <div class="font-medium text-gray-500 tracking-wide">
                    List all available academic subjects
                </div>
                <div>
                </div>
            </div>
        </caption>
        <thead>
            <tr>
                <x-table.th>ID</x-table.th>
                <x-table.th>Name</x-table.th>
                <x-table.th>Code</x-table.th>
                <x-table.th>Academic Level</x-table.th>
                <x-table.th><span class="sr-only">Actions</span></x-table.th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @foreach ($academicSubjects as $academicSubject)
            <tr>
                <td class="p-2 text-sm text-gray-500">#{{ $academicSubject->id }}</td>
                <td class="p-2 text-sm text-gray-900 font-medium">{{ $academicSubject->name }}</td>
                <td class="p-2 text-sm text-gray-500">{{ $academicSubject->code }}</td>
                <td class="p-2 text-sm text-gray-500">{{ $academicSubject->academicLevel->name }}</td>
                <td class="p-2 text-sm text-primary-600 space-x-3">
                    <a href="{{ route('academic-subjects.academic-topics.create', ['academic_subject' => $academicSubject]) }}">Topic</a>
                    <a href="{{ route('academic-subjects.edit', ['academic_subject' => $academicSubject]) }}">Edit</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</x-dashboard>