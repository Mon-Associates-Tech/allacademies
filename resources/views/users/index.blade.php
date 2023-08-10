<x-auth title="Users">
    <x-slot name="breadcrumb">
        <x-breadcrumb />
    </x-slot>

    @if ($users->count())
    <x-table>
        <x-slot name="head">
            <tr>
                <x-table.th>Name</x-table.th>
                <x-table.th>Role</x-table.th>
                <x-table.th><span class="sr-only">Actions</span></x-table.th>
            </tr>
        </x-slot>

        @foreach ($users as $user)
            <tr>
                <x-table.td bold>{{ $user->name }}</x-table.td>
                <x-table.td bold>{{ $user->role }}</x-table.td>
                <x-table.td action>
                    <x-action name="view" :to="route('users.show', ['user' => $user])" />
                </x-table.td>
            </tr>
        @endforeach
    </x-table>

    <div class="mt-3">
        {{ $users->links() }}
    </div>
    @else
    <x-blank />
    @endif
</x-auth>
