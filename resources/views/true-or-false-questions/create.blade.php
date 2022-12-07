<x-dashboard title="True or False Question" summary="Add true or false question">
    <div class="font-medium text-gray-500 tracking-wide">
        Add new true or false question
    </div>
    <form class="w-full max-w-md space-y-2" method="POST" action="{{ route('academic-topics.true-or-false-questions.store', ['academic_topic' => $academicTopic]) }}">
        @csrf
        <x-form.editor full name="question" />
        <x-form.select full name="answer" :options="[
            ['value' => '1', 'label' => 'True'],
            ['value' => '0', 'label' => 'False'],
        ]" />
        <div class="flex items-center justify-end">
            <x-button>Save</x-button>
        </div>
    </form>
</x-dashboard>