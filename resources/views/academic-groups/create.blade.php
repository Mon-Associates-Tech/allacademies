<x-dashboard title="Academic Groups" summary="Add new educational group">
    <div class="font-medium text-gray-500 tracking-wide">
        Add new academic group
    </div>
    <form class="w-full max-w-md space-y-2" method="POST" action="{{ route('academic-groups.store') }}">
        @csrf
        <x-form.input full name="name" />
        <div class="flex items-center justify-end">
            <x-button>Save</x-button>
        </div>
    </form>
</x-dashboard>