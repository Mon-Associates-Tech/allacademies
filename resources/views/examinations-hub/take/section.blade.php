<x-layouts.exam>
    @livewire('examinations.exam-section-taking', [
        'exam' => $exam,
        'submission' => $submission,
        'section' => $section,
        'sectionIndex' => $sectionIndex,
        'questions' => $questions,
    ])
</x-layouts.exam>
