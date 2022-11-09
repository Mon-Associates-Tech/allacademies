<x-dashboard title="Academic Subjects" summary="Add new educational subject">
    <div class="font-medium text-gray-500 tracking-wide">
        Add new academic subject
    </div>
    <form class="w-full max-w-md space-y-2" method="POST" action="{{ route('academic-levels.academic-subjects.store', ['academic_level' => $academicLevel]) }}">
        @csrf
        <x-form.input full label="Academic Level" name="academic_level" value="{{ $academicLevel->name }}" readonly />
        <x-form.input full name="name" />
        <x-form.input full name="code" />
        <div class="flex items-center justify-end">
            <x-button>Save</x-button>
        </div>
    </form>
</x-dashboard>