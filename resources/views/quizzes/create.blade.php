<x-auth title="Create Quiz">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Quizzes' => route('academic-subjects.quizzes.index', ['academic_subject' => $academicSubject]),
        ]" />
    </x-slot>

    <form method="POST" action="{{ route('academic-subjects.quizzes.store', ['academic_subject' => $academicSubject]) }}">
        @csrf

        <input type="hidden" name="team_id" value="{{ auth()->user()->current_team_id }}">
        <input type="hidden" name="creator_id" value="{{ auth()->id() }}">

        <div class="grid sm:grid-cols-6 gap-4">
            <div class="sm:col-span-3">
                <x-form.input name="title" type="text" />
            </div>
            <div class="sm:col-span-1">
                <x-form.input name="duration_in_minutes" type="number" label="Duration In Minutes" />
            </div>
            <br>
            {{-- <div class="sm:col-span-2">
                <x-form.input name="starts_at" type="datetime-local" label="Starts At" />
            </div>
            <div class="sm:col-span-2">
                <x-form.input name="ends_at" type="datetime-local" label="Ends At" />
            </div> --}}
            <div class="sm:col-span-4">
                @livewire('quiz-sections', ['topics' => $topics])
            </div>
        </div>

        <div class="flex justify-end mt-3">
            <x-button.primary class="ml-2">Create Quiz</x-button.primary>
        </div>
    </form>
</x-auth>