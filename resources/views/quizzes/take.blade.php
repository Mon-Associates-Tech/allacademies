<x-auth title="Quizzing">
    {{-- <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Quizzes' => route('academic-subjects.quizzes.index', ['academic_subject' => $academicSubject]),
        ]" />
    </x-slot> --}}

    <form method="POST" action="{{ route('quizzes.take', ['quiz' => $quiz]) }}">
        @csrf
        <x-detail>
            <x-detail.data expand label="Question">{!! $question->question->up !!}</x-detail.data>

            @if ($question instanceof \App\Models\MultipleChoiceQuestion)
            <input type="hidden" name="type" value="multiple_choice_questions">
            <x-detail.data label="Option A">{!! $question->option_a->up !!}</x-detail.data>
            <x-detail.data label="Option B">{!! $question->option_b->up !!}</x-detail.data>
            <x-detail.data label="Option C">{!! $question->option_c->up !!}</x-detail.data>
            <x-detail.data label="Option D">{!! $question->option_d->up !!}</x-detail.data>
            <x-detail.data label="Option E">{!! $question->option_e->up !!}</x-detail.data>

            <x-form.select full name="answer" :options="[
                'a' => 'Option A',
                'b' => 'Option B',
                'c' => 'Option C',
                'd' => 'Option D',
                'e' => 'Option E',
            ]" />
            @endif
            @if ($question instanceof \App\Models\TrueOrFalseQuestion)
            <input type="hidden" name="type" value="true_or_false_questions">
            <x-detail.data label="Option">True or False</x-detail.data>

            <x-form.select full name="answer" :options="[
                '1' => 'True',
                '0' => 'False',
            ]" :value="1" />
            @endif

            <x-slot name="action">
                <x-button.primary>Save and Continue</x-button.primary>
            </x-slot>
        </x-detail>
    </form>
</x-auth>
