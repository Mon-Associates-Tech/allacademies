<x-dashboard title="Change Role" summary="Change User Role">
    <div class="font-medium text-gray-500 tracking-wide">
        Change user role
    </div>
    <form class="w-full max-w-md space-y-2" method="POST" action="{{ route('settings.role') }}">
        @csrf
        <x-form.input full name="email" />
        <x-form.select full name="role" :options="[
            ['value' => 'subscriber', 'label' => 'Subscriber'],
            ['value' => 'moderator', 'label' => 'Moderator'],
            ['value' => 'admin', 'label' => 'Admin'],
        ]" />
        <div class="flex items-center justify-end">
            <x-button>Save</x-button>
        </div>
    </form>
</x-dashboard>