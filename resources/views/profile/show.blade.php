<x-auth title="Profile">
    <x-slot name="breadcrumb">
        <x-breadcrumb />
    </x-slot>

    <div class="flex">
        <div class="mr-5">
            <img class="w-20 h-20 md:w-24 md:h-24 lg:w-28 lg:h-28 object-cover rounded-lg shadow" src="{{ $user->profile_avatar_url }}">
        </div>

        <div class="flex-1">
            <x-detail>
                <x-detail.data label="Name">{{ $user->name }}</x-detail.data>
                <x-detail.data label="Email">{{ $user->email }}</x-detail.data>
                <x-detail.data label="Joined On">{{ $user->created_at->format('F d, Y') }}</x-detail.data>

                <x-slot name="action">
                    <x-link.primary :to="route('profile.edit')">Edit Profile</x-link.primary>
                </x-slot>
            </x-detail>
        </div>
    </div>
</x-auth>