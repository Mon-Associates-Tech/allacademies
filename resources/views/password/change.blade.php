<x-auth title="Change Passsword">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Security' => route('security'),
        ]" />
    </x-slot>

    <form method="POST" action="{{ route('password.change') }}">
        @csrf
        <div class="grid sm:grid-cols-3 gap-4">
            <div class="sm:col-span-2">
                <x-form.input name="current_password" label="Current Password" type="password" />
            </div>
            <div class="sm:col-span-2">
                <x-form.input name="password" type="password" />
            </div>
            <div class="sm:col-span-2">
                <x-form.input name="password_confirmation" label="Confirm Password" type="password" />
            </div>
        </div>
        <div class="flex justify-end mt-3">
            <x-button.primary class="ml-2">Change Profile</x-button.primary>
        </div>
    </form>
</x-auth>