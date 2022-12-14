<x-dashboard title="Academic Levels" summary="Add new educational level">
    <div class="font-medium text-gray-500 tracking-wide">
        Add new academic level
    </div>
    <form class="w-full max-w-md space-y-2" method="POST" action="{{ route('academic-groups.academic-levels.store', ['academic_group' => $academicGroup]) }}">
        @csrf
        <x-form.input full label="Academic Group" name="academic_group" value="{{ $academicGroup->name }}" readonly />
        <x-form.input full name="name" />
        <x-form.input full name="label" />
        <div class="flex items-center justify-end">
            <x-button>Save</x-button>
        </div>
    </form>
</x-dashboard>