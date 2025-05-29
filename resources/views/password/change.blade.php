<x-layouts.app title="Change Password" :title-align-center="true" :has-action="true" action-link-text="Back">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Security' => route('security'),
        ]" />
    </x-slot>

    <div class=" max-w-xl mx-auto bg-white rounded-lg shadow-md p-6">
        <form method="POST" action="{{ route('password.change') }}">
            @csrf
            <div class="grid gap-4 w-full">
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
            <div class="flex justify-end mt-5">
                <x-button.primary class="ml-2">Change Password</x-button.primary>
            </div>
        </form>
    </div>

</x-layouts.app>
