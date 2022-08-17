<x-dashboard title="Academic Topics" summary="Add academic topic">
    <div class="font-medium text-gray-500 tracking-wide">
        Create new academic topic
    </div>
    <form class="w-full max-w-md space-y-2" method="POST" action="{{ route('academic-topics.store') }}">
        @csrf
        <x-form.input full name="name" />
        <x-form.select full name="academic_level_id" label="Academic level" :options="$academicLevels" />
        <x-form.select full name="academic_subject_id" label="Academic Subject" :options="$academicSubjects" />
        <div class="flex items-center justify-end">
            <x-button>Save</x-button>
        </div>
    </form>
</x-dashboard>