<x-auth title="Start Quiz">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Quizzes' => route('academic-subjects.quizzes.index', ['academic_subject' => $academicSubject]),
        ]" />
    </x-slot>

    <x-detail>
        <x-detail.data label="Academic Subject">{{ $academicSubject->name }}</x-detail.data>
        <x-detail.data label="Title">{{ $quiz->title }}</x-detail.data>
        <x-detail.data label="Duration">{{ $quiz->duration_in_minutes }} minutes</x-detail.data>
        <x-detail.data label="Notice">When you start this quiz, you will have to finish within the given duration.</x-detail.data>

        <x-slot name="action">
            <x-link.primary :to="route('quizzes.take', ['quiz' => $quiz])">Start</x-link.primary>
            <x-link.secondary :to="route('academic-subjects.quizzes.index', ['academic_subject' => $academicSubject])">Not Now</x-link.secondary>
        </x-slot>
    </x-detail>
</x-auth>
