<x-print title="Examination Details">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Examinations' => route('examinations.index', ['academic_subject' => $examination->academicSubject]),
        ]"/>
    </x-slot>

    @include('examinations.details', [
        'examination' => $examination,
        'sections' => $sections,
        'heading' => $heading,
    ])
</x-print>
