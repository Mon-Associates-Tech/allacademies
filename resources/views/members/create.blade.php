<x-dashboard title="Member" summary="Add new member">
    <div class="font-medium text-gray-500 tracking-wide">
        Add new member
    </div>
    <form class="w-full max-w-md space-y-2" method="POST" action="{{ route('teams.members.store', ['team' => $team]) }}">
        @csrf
        <x-form.input full name="email" />
        <div class="flex items-center justify-end">
            <x-button>Save</x-button>
        </div>
    </form>
</x-dashboard>