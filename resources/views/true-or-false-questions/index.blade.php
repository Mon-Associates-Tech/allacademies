<x-dashboard title="True or False Questions" summary="Available true or false questions">
    <table class="w-full divide-y divide-gray-300">
        <caption>
            <div class="flex items-center justify-between px-2 py-3">
                <div class="font-medium text-gray-500 tracking-wide">
                    List of available true or false questions
                </div>
                <div>
                </div>
            </div>
        </caption>
        <thead>
            <tr>
                <x-table.th>ID</x-table.th>
                <x-table.th>Question</x-table.th>
                <x-table.th>Score</x-table.th>
                <x-table.th>Difficulty Level</x-table.th>
                <x-table.th>Academic Topic</x-table.th>
                <x-table.th>Academic Subject</x-table.th>
                <x-table.th>Academic Level</x-table.th>
                <x-table.th><span class="sr-only">Actions</span></x-table.th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @foreach ($trueOrFalseQuestions as $trueOrFalseQuestion)
            <tr>
                <td class="p-2 text-sm text-gray-500">#{{ $trueOrFalseQuestion->id }}</td>
                <td class="p-2 text-sm text-gray-900 font-medium">{!! $trueOrFalseQuestion->question->up() !!}</td>
                <td class="p-2 text-sm text-gray-500">{{ $trueOrFalseQuestion->score }}</td>
                <td class="p-2 text-sm text-gray-500">{{ $trueOrFalseQuestion->difficulty_level }}</td>
                <td class="p-2 text-sm text-gray-500">{{ $trueOrFalseQuestion->academicTopic->name }}</td>
                <td class="p-2 text-sm text-gray-500">{{ $trueOrFalseQuestion->academicTopic->academicSubject->name }}</td>
                <td class="p-2 text-sm text-gray-500">{{ $trueOrFalseQuestion->academicTopic->academicSubject->academicLevel->name }}</td>
                <td class="p-2 text-sm text-primary-600 space-x-3">
                    <a href="{{ route('multiple-choice-questions.edit', ['multiple_choice_question' => $trueOrFalseQuestion]) }}">Edit</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</x-dashboard>