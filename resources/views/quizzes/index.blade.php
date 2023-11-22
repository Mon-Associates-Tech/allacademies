<x-auth title="Quizzes">
    <x-slot name="breadcrumb">
        <x-breadcrumb />
    </x-slot>
    @can('privileged', Auth::user()->currentTeam)
        <x-slot name="action">
            <x-link.primary :to="route('academic-subjects.quizzes.create', ['academic_subject' => $academicSubject])">New Quiz</x-link.primary>
        </x-slot>
    @endcan

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
                    <a class="text-primary-600 hover:text-primary-900" href="{{ route('quizzes.start', ['quiz' => $quiz]) }}">Quiz</a>
                    @can('privileged', Auth::user()->currentTeam)
                        <a class="text-primary-600 hover:text-primary-900" href="{{ route('quizzes.scores', ['quiz' => $quiz]) }}">Scores</a>
                    @endcan
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
