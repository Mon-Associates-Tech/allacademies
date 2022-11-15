<x-dashboard title="Examination" summary="Set examination">
    <div class="font-medium text-gray-500 tracking-wide">
        New examination
    </div>
    <form class="w-full max-w-md space-y-2" method="POST" action="{{ route('academic-subjects.examinations.store', ['academic_subject' => $academicSubject]) }}">
        @csrf
        <x-form.input full name="title" />
        <x-form.editor full name="heading" />
        @livewire('examination-sections', ['topics' => $topics])
        <x-form.input full name="examiners" />
        <div class="flex items-center justify-end">
            <x-button>Save</x-button>
        </div>
    </form>
</x-dashboard>