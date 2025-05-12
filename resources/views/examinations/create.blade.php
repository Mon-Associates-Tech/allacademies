<x-auth title="Create Examination">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Examinations' => route('academic-subjects.examinations.index', ['academic_subject' => $academicSubject]),
        ]" />
    </x-slot>

    <div class="flex">
        <form method="POST"
              action="{{ route('academic-subjects.examinations.store', ['academic_subject' => $academicSubject]) }}">
            @csrf
            <input type="hidden" name="team_id" value="{{ auth()->user()->current_team_id }}">
            <input type="hidden" name="creator_id" value="{{ auth()->id() }}">

            <div class="grid sm:grid-cols-5 gap-4 place-items-center">
                <div class="sm:col-span-1"></div>
                <div class="sm:col-span-3">
                    @livewire('examination-heading', ['metadata' => $metadata])
                </div>
                <div class="sm:col-span-1"></div>
            </div>

            <div class="grid sm:grid-cols-5 gap-4 place-items-center">
                <div class="sm:col-span-1"></div>
                <div class="sm:col-span-3">
                    @livewire('examination-sections', ['topics' => $topics, 'subtopics' => $subtopics])
                </div>
                <div class="sm:col-span-1"></div>
            </div>

            <div class="flex justify-center mt-3">
                <x-button.primary class="ml-2">Create Examination</x-button.primary>
            </div>
        </form>
    </div>

</x-auth>
