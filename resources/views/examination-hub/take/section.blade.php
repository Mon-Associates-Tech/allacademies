<x-layouts.exam>
    @livewire('examination-hub.exam-section-taking', [
        'exam' => $exam,
        'submission' => $submission,
        'section' => $section,
        'sectionIndex' => $sectionIndex,
        'questions' => $questions,
    ])
</x-layouts.exam>
