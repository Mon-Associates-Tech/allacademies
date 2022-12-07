<x-dashboard title="Academic Topics" summary="Available academic topics">
    <table class="w-full divide-y divide-gray-300">
        <caption>
            <div class="flex items-center justify-between px-2 py-3">
                <div class="font-medium text-gray-500 tracking-wide">
                    List of academic topics
                </div>
                <div>
                </div>
            </div>
        </caption>
        <thead>
            <tr>
                <x-table.th>ID</x-table.th>
                <x-table.th>Name</x-table.th>
                <x-table.th>Academic Subject</x-table.th>
                <x-table.th>Academic Level</x-table.th>
                <x-table.th><span class="sr-only">Actions</span></x-table.th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @foreach ($academicTopics as $academicTopic)
            <tr>
                <td class="p-2 text-sm text-gray-500">#{{ $academicTopic->id }}</td>
                <td class="p-2 text-sm text-gray-900 font-medium">{{ $academicTopic->name }}</td>
                <td class="p-2 text-sm text-gray-500">{{ $academicTopic->academicSubject->name }}</td>
                <td class="p-2 text-sm text-gray-500">{{ $academicTopic->academicSubject->academicLevel->name }}</td>
                <td class="p-2 text-sm text-primary-600 space-x-3">
                    <a href="{{ route('academic-topics.multiple-choice-questions.create', ['academic_topic' => $academicTopic]) }}">MCQ</a>
                    <a href="{{ route('academic-topics.essay-questions.create', ['academic_topic' => $academicTopic]) }}">Essay</a>
                    <a href="{{ route('academic-topics.true-or-false-questions.create', ['academic_topic' => $academicTopic]) }}">T/F Q</a>
                    @can('administrate')
                    <a href="{{ route('academic-topics.edit', ['academic_topic' => $academicTopic]) }}">Edit</a>
                    @endcan
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</x-dashboard>