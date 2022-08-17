<x-dashboard title="Academic Subjects" summary="Add academic subject">
    <div class="font-medium text-gray-500 tracking-wide">
        Create new academic subject
    </div>
    <form class="w-full max-w-md space-y-2" method="POST" action="{{ route('academic-subjects.store') }}">
        @csrf
        <x-form.input full name="name" />
        <x-form.input full name="code" />
        <div class="flex items-center justify-end">
            <x-button>Save</x-button>
        </div>
    </form>
</x-dashboard>