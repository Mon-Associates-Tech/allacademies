<x-layouts.app title="Create Examination" :has-action="true" class="" action="Back" action-url="{{ route('academic-subjects.examinations.index', ['academic_subject' => $academicSubject]) }}">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Examinations' => route('academic-subjects.examinations.index', ['academic_subject' => $academicSubject]),
        ]" />
    </x-slot>

    <div class="bg-white shadow-sm rounded-lg ring-1 ring-slate-200 p-4">
        <form method="POST" enctype="multipart/form-data"
              action="{{ route('academic-subjects.examinations.store', ['academic_subject' => $academicSubject]) }}">
            @csrf
            <input type="hidden" name="team_id" value="{{ auth()->user()->current_team_id }}">
            <input type="hidden" name="creator_id" value="{{ auth()->id() }}">

            <div class="grid place-items-center w-full">
            <div class="grid place-items-center max-w-xl">

                <div class="sm:col-span-1"></div>
                <div class="sm:col-span-6">
                    @livewire('examination-heading', ['metadata' => $metadata])
                </div>
                <div class="sm:col-span-1"></div>
            </div>
            </div>

            <div class="grid">
            <div class="grid max-w-xl place-items-center mx-auto">
                <div class="sm:col-span-1"></div>
                <div class="sm:col-span-6">
                    @livewire('examination-sections', ['topics' => $topics])
                </div>
                <div class="sm:col-span-1"></div>
            </div>
            </div>

            <div class="grid sm:grid-cols-6 gap-4 place-items-center">
                <div class="sm:col-span-5 text-start ms-auto">
                    <x-button.primary class="text-right">Create Examination</x-button.primary>
                </div>
            </div>
        </form>
    </div>

</x-layouts.app>
