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

            <div class="grid sm:grid-cols-6 gap-4 place-items-center">
                <input name="metadata" value="{{base64_encode(json_encode($metadata, JSON_THROW_ON_ERROR))}}"
                       type="hidden" hidden/>
                <div class="sm:col-span-5 text-start ms-auto">
                    <x-button.primary class="text-right">Preview Examination</x-button.primary>
                </div>
            </div>
        </form>
    </div>
</x-layouts.app>
