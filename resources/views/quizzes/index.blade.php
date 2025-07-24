<x-layouts.app app-name="Quizzes" :has-action="false">
    <x-slot name="breadcrumb">
        <x-breadcrumb/>
    </x-slot>
    @can('privileged', $currentTeam)
        <div class="text-right mb-4">
            <x-link.primary
                :to="route('quizzes.create', ['academic_subject' => $academicSubject, 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')])">
                New Quiz
            </x-link.primary>
        </div>
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
                        <a class="text-primary-600 hover:text-primary-900"
                           href="{{ route('quizzes.start', ['academic_subject' => $academicSubject, 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group'), 'quiz' => $quiz]) }}">Quiz</a>
                        @can('privileged', $currentTeam)
                            <a class="text-primary-600 hover:text-primary-900"
                               href="{{ route('quizzes.scores', ['academic_subject' => $academicSubject, 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group'), 'quiz' => $quiz]) }}">Scores</a>
                        @endcan
                    </x-table.td>
                </tr>
            @endforeach
        </x-table>

        <div class="mt-3">
            {{ $quizzes->links() }}
        </div>
    @else
        <x-blank/>
    @endif
</x-layouts.app>
