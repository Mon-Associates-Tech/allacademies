<div class="bg-gray-50 px-4 py-4 sm:px-6 flex items-center justify-end print:hidden">
    <x-link.secondary class="mr-4" :to="route('examinations.create', ['academic_subject' => $academicSubject, 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')])"> Go back</x-link.secondary>
    <x-button.primary wire:click="saveExamination()">Print</x-button.primary>
</div>
