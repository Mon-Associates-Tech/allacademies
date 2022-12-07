<x-dashboard title="Essay Question" summary="Add essay question">
    <div class="font-medium text-gray-500 tracking-wide">
        Add new essay question
    </div>
    <form class="w-full max-w-md space-y-2" method="POST" action="{{ route('academic-topics.essay-questions.store', ['academic_topic' => $academicTopic]) }}">
        @csrf
        <x-form.editor full name="question" />
        <x-form.editor full name="answer" />
        <div class="flex items-center justify-end">
            <x-button>Save</x-button>
        </div>
    </form>
</x-dashboard>