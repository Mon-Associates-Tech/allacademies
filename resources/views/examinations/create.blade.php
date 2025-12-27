<x-layouts.app title="Create Examination" title-align-center="true" :has-action="false" class=""
               action-url="{{ route('examinations.index', ['academic_subject' => $academicSubject, 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')]) }}">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Academic Groups' => route('academic-groups.index'),
            $academicSubject->academicLevel->academicGroup->name => route('academic-groups.show', ['academic_group' => $academicSubject->academicLevel->academicGroup]),
            'Academic Levels' => route('academic-levels.index', ['academic_group' => $academicSubject->academicLevel->academicGroup]),
            $academicSubject->academicLevel->name => route('academic-levels.show', ['academic_level' => $academicSubject->academicLevel, 'academic_group' => getRouteParameter('academic_group')]),
            'Academic Subjects' => route('academic-subjects.index', ['academic_level' => $academicSubject->academicLevel, 'academic_group' => getRouteParameter('academic_group')]),
            $academicSubject->name => route('academic-subjects.show', ['academic_subject' => $academicSubject, 'academic_level'=>getRouteParameter('academic_level'), 'academic_group'=>getRouteParameter('academic_group')]),
            'Examinations' => route('examinations.index', ['academic_subject'=>getRouteParameter('academic_subject'), 'academic_level'=>getRouteParameter('academic_level'), 'academic_group'=>getRouteParameter('academic_group')]),
        ]" />
    </x-slot>

    <div class="">
        <!-- Display validation errors -->
        @if ($errors->any())
            <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex items-start space-x-3">
                    <svg class="h-5 w-5 text-red-500 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                    </svg>
                    <div class="flex-1">
                        <h3 class="text-sm font-medium text-red-800">There were some errors with your submission</h3>
                        <ul class="mt-2 text-sm text-red-700 list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <section class="bg-white max-w-3xl pb-4 rounded-xl">
            <form method="POST" enctype="multipart/form-data"
                  action="{{ route('examinations.generate-preview', ['academic_subject' => $academicSubject, 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')]) }}">
                @csrf
                <input type="hidden" name="team_id" value="{{ auth()->user()->current_team_id }}">
                <input type="hidden" name="creator_id" value="{{ auth()->id() }}">

                <div class="grid place-items-center">
                    <div class="w-full max-w-3xl">
                        @livewire('examination-heading', ['metadata' => $metadata])
                    </div>
                </div>

                <div class="grid place-items-center mx-auto">
                    <div class="w-full max-w-3xl">
                        @livewire('examination-sections', ['topics' => $topics])
                    </div>
                </div>

                <div class="grid sm:grid-cols-6 gap-4 place-items-end">
                    <input name="metadata" value="{{base64_encode(json_encode($metadata, JSON_THROW_ON_ERROR))}}"
                           type="hidden" hidden/>
                    <div class="sm:col-span-6 text-start ms-auto px-4">
                        <x-button.primary type="submit" class="text-right">Preview Examination</x-button.primary>
                    </div>
                </div>
            </form>
        </section>

    </div>
</x-layouts.app>
