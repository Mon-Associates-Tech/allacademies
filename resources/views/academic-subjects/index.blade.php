<x-dashboard title="Academic Subjects" summary="Available academic subjects">
    <table class="w-full divide-y divide-gray-300">
        <caption>
            <div class="flex items-center justify-between px-2 py-3">
                <div class="font-medium text-gray-500 tracking-wide">
                    List of academic subjects
                </div>
                <div>
                    <x-button :to="route('academic-subjects.create')">Add new academic subject</x-button>
                </div>
            </div>
        </caption>
        <thead>
            <tr>
                <th class="px-2 py-4 text-left text-sm tracking-wider text-gray-500">ID</th>
                <th class="px-2 py-4 text-left text-sm tracking-wider text-gray-500">Name</th>
                <th class="px-2 py-4 text-left text-sm tracking-wider text-gray-500">Code</th>
                <th class="px-2 py-4 text-left text-sm tracking-wider text-gray-500"><span class="sr-only">Actions</span></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @foreach ($academicSubjects as $academicSubject)
            <tr>
                <td class="p-2 text-sm text-gray-500">#{{ $academicSubject->id }}</td>
                <td class="p-2 text-sm text-gray-900 font-medium">{{ $academicSubject->name }}</td>
                <td class="p-2 text-sm text-gray-500">{{ $academicSubject->code }}</td>
                <td class="p-2 text-sm text-primary-600 space-x-3">
                    <a href="{{ route('academic-subjects.edit', ['academic_subject' => $academicSubject]) }}">Edit</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</x-dashboard>