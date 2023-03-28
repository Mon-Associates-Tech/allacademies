<x-auth title="Create Examination">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Examinations' => route('academic-subjects.examinations.index', ['academic_subject' => $academicSubject]),
        ]" />
    </x-slot>

    <form method="POST" action="{{ route('academic-subjects.examinations.store', ['academic_subject' => $academicSubject]) }}">
        @csrf

        <input type="hidden" name="team_id" value="{{ auth()->user()->current_team_id }}">
        <input type="hidden" name="creator_id" value="{{ auth()->id() }}">

        <div class="grid sm:grid-cols-3 gap-4">
            <div class="sm:col-span-2">
                <x-form.input name="title" type="text" />
            </div>
            <div class="sm:col-span-2">
                <x-form.editor name="heading" />
            </div>
            <div class="sm:col-span-2">
                @livewire('examination-sections', ['topics' => $topics])
            </div>
            <div class="sm:col-span-2">
                <x-form.input name="examiners" type="text" />
            </div>
        </div>

        {{-- @error('package')
        <div class="text-xs font-medium text-red-600 pt-4">{{ $message }}</div>
        @enderror --}}

        <div class="flex justify-end mt-3">
            <x-button.primary class="ml-2">Create Examination</x-button.primary>
        </div>
    </form>
</x-auth>

{{-- <x-dashboard title="Examination" summary="Set examination">
    <div class="font-medium text-gray-500 tracking-wide">
        New examination
    </div>
    <form class="w-full max-w-md space-y-2" method="POST" action="{{ route('academic-subjects.examinations.store', ['academic_subject' => $academicSubject]) }}">
        @csrf
        <input type="hidden" name="team_id" value="{{ auth()->user()->current_team_id }}">
        <input type="hidden" name="creator_id" value="{{ auth()->id() }}">
        <x-form.input full name="title" />
        <x-form.editor full name="heading" />
        @livewire('examination-sections', ['topics' => $topics])
        <x-form.input full name="examiners" />
        <div class="flex items-center justify-end">
            <x-button>Save</x-button>
        </div>
    </form>
</x-dashboard> --}}