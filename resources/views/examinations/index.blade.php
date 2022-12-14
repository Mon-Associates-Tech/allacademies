<x-dashboard title="Examinations" summary="Available Examinations">
    <table class="w-full divide-y divide-gray-300">
        <caption>
            <div class="flex items-center justify-between px-2 py-3">
                <div class="font-medium text-gray-500 tracking-wide">
                    List of examinations
                </div>
                <div>
                </div>
            </div>
        </caption>
        <thead>
            <tr>
                <x-table.th>ID</x-table.th>
                <x-table.th>Title</x-table.th>
                <x-table.th>Academic Subject</x-table.th>
                <x-table.th>Academic Level</x-table.th>
                {{-- <x-table.th><span class="sr-only">Actions</span></x-table.th> --}}
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @foreach ($examinations as $examination)
            <tr>
                <td class="p-2 text-sm text-gray-500">#{{ $examination->id }}</td>
                <td class="p-2 text-sm text-gray-900 font-medium">{{ $examination->title }}</td>
                <td class="p-2 text-sm text-gray-500">{{ $examination->academicSubject->name }}</td>
                <td class="p-2 text-sm text-gray-500">{{ $examination->academicSubject->academicLevel->name }}</td>
                <td class="p-2 text-sm text-primary-600 space-x-3">
                    <a href="{{ route('examinations.show', ['examination' => $examination]) }}">Show</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</x-dashboard>