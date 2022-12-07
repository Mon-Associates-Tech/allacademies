<x-dashboard title="Multiple Choice Question" summary="Add multiple choice question">
    <div class="font-medium text-gray-500 tracking-wide">
        Add new multiple choice question
    </div>
    <form class="w-full max-w-md space-y-2" method="POST" action="{{ route('academic-topics.multiple-choice-questions.store', ['academic_topic' => $academicTopic]) }}">
        @csrf
        <x-form.editor full name="question" />
        <x-form.editor full name="option_a" label="Option A" />
        <x-form.editor full name="option_b" label="Option B" />
        <x-form.editor full name="option_c" label="Option C" />
        <x-form.editor full name="option_d" label="Option D" />
        <x-form.editor full name="option_e" label="Option E" />
        <x-form.select full name="answer" :options="[
            ['value' => 'a', 'label' => 'Option A'],
            ['value' => 'b', 'label' => 'Option B'],
            ['value' => 'c', 'label' => 'Option C'],
            ['value' => 'd', 'label' => 'Option D'],
            ['value' => 'e', 'label' => 'Option E'],
        ]" />
        <div class="flex items-center justify-end">
            <x-button>Save</x-button>
        </div>
    </form>
</x-dashboard>