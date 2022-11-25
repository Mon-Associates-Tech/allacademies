<x-dashboard title="Team members" summary="All team members">
    <table class="w-full divide-y divide-gray-300">
        <caption>
            <div class="flex items-center justify-between px-2 py-3">
                <div class="font-medium text-gray-500 tracking-wide">
                    Your Team
                </div>
                <div>
                    {{-- <x-button :to="route('payments.create')">Add new payment</x-button> --}}
                </div>
            </div>
        </caption>
        <thead>
            <tr>
                <x-table.th>ID</x-table.th>
                <x-table.th>Name</x-table.th>
                <x-table.th><span class="sr-only">Actions</span></x-table.th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            <tr>
                <td class="p-2 text-sm text-gray-500">#{{ $team->id }}</td>
                <td class="p-2 text-sm text-gray-900 font-medium">{{ $team->name }}</td>
                <td class="p-2 text-sm text-primary-600 space-x-3">
                    <a href="{{ route('teams.members.create', ['team' => $team]) }}">Member</a>
                </td>
            </tr>
        </tbody>
    </table>

    <table class="w-full divide-y divide-gray-300 mt-10">
        <caption>
            <div class="flex items-center justify-between px-2 py-3">
                <div class="font-medium text-gray-500 tracking-wide">
                    List team members
                </div>
                <div>
                    {{-- <x-button :to="route('payments.create')">Add new payment</x-button> --}}
                </div>
            </div>
        </caption>
        <thead>
            <tr>
                <x-table.th>ID</x-table.th>
                <x-table.th>Name</x-table.th>
                <x-table.th>Email</x-table.th>
                <x-table.th><span class="sr-only">Actions</span></x-table.th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @foreach ($team->members as $member)
            <tr>
                <td class="p-2 text-sm text-gray-500">#{{ $member->id }}</td>
                <td class="p-2 text-sm text-gray-900 font-medium">{{ $member->name }}</td>
                <td class="p-2 text-sm text-gray-500">{{ $member->email }}</td>
                <td class="p-2 text-sm text-primary-600 space-x-3">
                    @if(! $team->owner->is($member))
                    <form method="POST" action="{{ route('teams.members.destroy', ['team' => $team, 'member' => $member]) }}">
                        @csrf
                        @method('DELETE')
                        <button>Remove</button>
                    </form>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</x-dashboard>