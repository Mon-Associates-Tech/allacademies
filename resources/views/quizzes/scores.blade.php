<x-auth title="Quiz Results" :has-action="false">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Quizzes' => route('academic-subjects.quizzes.index', ['academic_subject' => $academicSubject]),
        ]" />
    </x-slot>

    @if ($worksheets->count())
        <div class="rounded-md bg-blue-50 p-4 mb-6">
            <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a.75.75 0 000 1.5h.253a.25.25 0 01.244.304l-.459 2.066A1.75 1.75 0 0010.747 15H11a.75.75 0 000-1.5h-.253a.25.25 0 01-.244-.304l.459-2.066A1.75 1.75 0 009.253 9H9z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-blue-700 text-sm">These are results for <strong>{{$quiz->title}}.</strong> The maximum score for this quiz is <strong>{{$score['max']}}</strong></p>
            </div>
            </div>
        </div>
    <x-table>
        <x-slot name="head">
            <tr>
                <x-table.th>Name</x-table.th>
                <x-table.th>Email</x-table.th>
                <x-table.th>Score</x-table.th>
            </tr>
        </x-slot>

        @foreach ($worksheets as $worksheet)
            <tr>
                <x-table.td bold>{{ $worksheet->user->name }}</x-table.td>
                <x-table.td>{{ $worksheet->user->email }}</x-table.td>
                <x-table.td bold>{{ $worksheet->score['value'] }}</x-table.td>
            </tr>
        @endforeach
    </x-table>

    <div class="mt-3">
        {{ $worksheets->links() }}
    </div>
    @else
    <x-blank />
    @endif
</x-auth>
