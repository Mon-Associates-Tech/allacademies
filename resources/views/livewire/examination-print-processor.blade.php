<div class="bg-gray-50 px-4 py-4 sm:px-6 flex items-center justify-end print:hidden">
    <x-link.secondary class="mr-4" :to="route('academic-subjects.examinations.create', ['academic_subject' => $academicSubject])"> Go back</x-link.secondary>
    <x-button.primary wire:click="saveExamination()">Print</x-button.primary>
</div>
