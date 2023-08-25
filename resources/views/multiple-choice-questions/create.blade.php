<x-auth title="New Multiple Choice Question">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Academic Groups' => route('academic-groups.index'),
            $academicTopic->academicSubject->academicLevel->academicGroup->name => route('academic-groups.show', ['academic_group' => $academicTopic->academicSubject->academicLevel->academicGroup]),
            'Academic Levels' => route('academic-groups.academic-levels.index', ['academic_group' => $academicTopic->academicSubject->academicLevel->academicGroup]),
            $academicTopic->academicSubject->academicLevel->name => route('academic-levels.show', ['academic_level' => $academicTopic->academicSubject->academicLevel]),
            'Academic Subjects' => route('academic-levels.academic-subjects.index', ['academic_level' => $academicTopic->academicSubject->academicLevel]),
            $academicTopic->academicSubject->name => route('academic-subjects.show', ['academic_subject' => $academicTopic->academicSubject]),
            'Academic Topics' => route('academic-subjects.academic-topics.index', ['academic_subject' => $academicTopic->academicSubject]),
            $academicTopic->name => route('academic-topics.show', ['academic_topic' => $academicTopic]),
            'Multiple Choice Questions' => route('academic-topics.multiple-choice-questions.index', ['academic_topic' => $academicTopic]),
        ]" />
    </x-slot>

    <div class="grid sm:grid-cols-3 gap-12">
        <div class="sm:col-span-2">
            <form method="POST" action="{{ route('academic-topics.multiple-choice-questions.store', ['academic_topic' => $academicTopic]) }}">
                @csrf
                <x-form.editor full name="question" />
                <x-form.editor full name="option_a" label="Option A" />
                <x-form.editor full name="option_b" label="Option B" />
                <x-form.editor full name="option_c" label="Option C" />
                <x-form.editor full name="option_d" label="Option D" />
                <x-form.editor full name="option_e" label="Option E" />
                <x-form.select full name="answer" :options="[
                    'a' => 'Option A',
                    'b' => 'Option B',
                    'c' => 'Option C',
                    'd' => 'Option D',
                    'e' => 'Option E',
                ]" />
            
                <div class="flex justify-end mt-3">
                    <x-button.primary class="ml-2">Create Multiple Choice Question</x-button.primary>
                </div>
            </form>
        </div>
        <div class="sm:col-span-1 space-y-2">
            @livewire('image-upload')
            @livewire('show-images')
        </div>
    </div>
</x-auth>