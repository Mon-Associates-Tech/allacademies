<x-layouts.app title="Quiz Summary" :has-action="false">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Quizzes' => route('quizzes.index',['academic_subject' => $academicSubject, 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')]),
        ]" />
    </x-slot>

    <x-detail>
        <x-detail.data label="Academic Subject">{{ $academicSubject->name }}</x-detail.data>
        <x-detail.data label="Title">{{ $quiz->title }}</x-detail.data>
        <x-detail.data label="Duration">{{ $quiz->duration_in_minutes }} minutes</x-detail.data>
        <x-detail.data label="Time Spent">{{ $worksheet->duration }} minutes</x-detail.data>
        <x-detail.data label="Score">{{ $score['value'] }} / {{ $score['max'] }}</x-detail.data>

        <x-slot name="action">
            <x-link.secondary :to="route('quizzes.show', ['quiz' => $quiz, 'academic_subject' => $academicSubject, 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')])">Review Results</x-link.secondary>
            <x-link.primary :to="route('quizzes.index', ['academic_subject' => $academicSubject, 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')])">Back to Quizzes</x-link.primary>
        </x-slot>
    </x-detail>
</x-layouts.app>
