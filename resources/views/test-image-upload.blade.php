<x-auth title="Upload Image">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Back to Dashboard' => route('dashboard'),
        ]" />
    </x-slot>
    <div class="space-y-6 max-w-2xl">   
        @livewire('image-upload')
    </div>
</x-auth>