<x-auth title="Quiz Summary" :has-action="false">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Quizzes' => route('academic-subjects.quizzes.index', ['academic_subject' => $academicSubject]),
        ]" />
    </x-slot>

    <x-detail>
        <x-detail.data label="Academic Subject">{{ $academicSubject->name }}</x-detail.data>
        <x-detail.data label="Title">{{ $quiz->title }}</x-detail.data>
        <x-detail.data label="Duration">{{ $quiz->duration_in_minutes }} minutes</x-detail.data>
        <x-detail.data label="Time Spent">{{ $worksheet->duration }} minutes</x-detail.data>
        <x-detail.data label="Score">{{ $score['value'] }} / {{ $score['max'] }}</x-detail.data>

        <x-slot name="action">
            <x-link.secondary :to="route('quizzes.show', ['quiz' => $quiz])">Review Results</x-link.secondary>
            <x-link.primary :to="route('academic-subjects.quizzes.index', ['academic_subject' => $academicSubject])">Back to Quizzes</x-link.primary>
        </x-slot>
    </x-detail>
</x-auth>
