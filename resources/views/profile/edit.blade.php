<x-auth title="Edit Profile">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Profile' => route('profile.show'),
        ]" />
    </x-slot>

    <form method="POST" action="{{ route('profile.update') }}">
        @csrf
        @method('PATCH')
        <div class="grid sm:grid-cols-3 gap-4">
            <div class="sm:col-span-1">
                <x-form.file name="avatar" />
            </div>
            <div>
                <x-form.checkbox name="force_update_avatar" label="Force Update Avatar" description="If checked, previous avatar is deleted even without selecting a new one"  />
            </div>
            <div class="sm:col-span-2">
                <x-form.input name="name" type="text" :value="$user->name" />
            </div>
            <div class="sm:col-span-2">
                <x-form.input name="email" type="email" :value="$user->email" />
            </div>
        </div>
        <div class="flex justify-end mt-3">
            <x-button.primary class="ml-2">Update Profile</x-button.primary>
        </div>
    </form>
</x-auth>