<x-auth title="Upload Image">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Back to Dashboard' => route('dashboard'),
        ]" />
    </x-slot>

    @livewire('image-upload')
</x-auth>