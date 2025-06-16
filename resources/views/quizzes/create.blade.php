<x-layouts.app title="Create Quiz" :has-action="false" :main-only="true">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Quizzes' => route('quizzes.index', ['academic_subject' => $academicSubject, 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group') ]),
        ]" />
    </x-slot>

    <div class="bg-white p-4 rounded-md border-slate-300 border w-full max-w-full  mx-auto
">
        <form class="max-w-2xl mx-auto bg-gray-100 p-4 rounded-md" method="POST" action="{{ route('quizzes.store', ['academic_subject' => $academicSubject, 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')]) }}" class="w-full">
            @csrf

            <input type="hidden" name="team_id" value="{{ auth()->user()->current_team_id }}">
            <input type="hidden" name="creator_id" value="{{ auth()->id() }}">

            <div class="grid sm:grid-cols-1 gap-4">
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
    </div>
</x-layouts.app>
