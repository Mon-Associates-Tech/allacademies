<x-auth title="Create Subscription">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Subscriptions' => route('subscriptions.index'),
        ]" />
    </x-slot>

    <form method="POST" action="{{ route('subscriptions.store') }}">
        @csrf

        @livewire('subscription-form', ['academicGroups' => $academicGroups, 'teams' => $teams])

        <div class="flex justify-end mt-3">
            <x-button.primary class="ml-2">Create Subscription</x-button.primary>
        </div>
    </form>
</x-auth>
