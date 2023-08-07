<x-auth title="Quizzes">
    <x-slot name="breadcrumb">
        <x-breadcrumb />
    </x-slot>
    <x-slot name="action">
        <x-link.primary :to="route('academic-subjects.quizzes.create', ['academic_subject' => $academicSubject])">New Quiz</x-link.primary>
    </x-slot>

    @if ($quizzes->count())
    <x-table>
        <x-slot name="head">
            <tr>
                <x-table.th>Title</x-table.th>
                <x-table.th><span class="sr-only">Actions</span></x-table.th>
            </tr>
        </x-slot>

        @foreach ($quizzes as $quiz)
            <tr>
                <x-table.td bold>{{ $quiz->title }}</x-table.td>
                <x-table.td action>
                    <a class="text-primary-600 hover:text-primary-900" href="{{ route('quizzes.start', ['quiz' => $quiz]) }}">Take Quiz</a>
                    {{-- <a class="text-primary-600 hover:text-primary-900" href="{{ route('examinations.answers', ['examination' => $examination]) }}">Answer Scheme</a> --}}
                </x-table.td>
            </tr>
        @endforeach
    </x-table>

    <div class="mt-3">
        {{ $quizzes->links() }}
    </div>
    @else
    <x-blank />
    @endif
</x-auth>
