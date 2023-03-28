<x-auth title="Change User Role">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Settings' => route('settings.index'),
        ]" />
    </x-slot>

    <form method="POST" action="{{ route('settings.role') }}">
        @csrf
        <div class="grid sm:grid-cols-3 gap-4">
            <div class="sm:col-span-2">
                <x-form.input name="email" type="email" />
            </div>
            <div>
                <x-form.select name="role" :options="[
                    'subscriber' => 'Subscriber',
                    'moderator' => 'Moderator',
                    'admin' => 'Admin',
                ]" />
            </div>
        </div>
        <div class="flex justify-end mt-3">
            <x-button.primary class="ml-2">Change Role</x-button.primary>
        </div>
    </form>
</x-auth>
