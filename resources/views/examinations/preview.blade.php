<x-print title="Examination Preview">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Examinations' => route('academic-subjects.examinations.index', ['academic_subject' => $academicSubject]),
        ]"/>
    </x-slot>

    <x-slot:action>
        <div class="text-right">
            <x-link.secondary
                :to="route('academic-subjects.examinations.create', ['academic_subject' => $academicSubject])"> Go back
            </x-link.secondary>
        </div>
    </x-slot:action>

    @include('examinations.details', [
         'sections' => $sections,
         'heading' => $heading,
     ])
</x-print>
