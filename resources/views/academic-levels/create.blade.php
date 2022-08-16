<x-dashboard title="Academic Levels" summary="Available educational levels">
    <div class="font-medium text-gray-500 tracking-wide">
        Create new academic level
    </div>
    <form class="w-full max-w-md space-y-2" method="POST" action="{{ route('academic-levels.store') }}">
        @csrf
        <x-form.input full name="name" />
        <x-form.input full name="label" />
        <div class="flex items-center justify-end">
            <x-button>Save</x-button>
        </div>
    </form>
</x-dashboard>