<x-auth title="Upload Image">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Back to Dashboard' => route('dashboard'),
        ]" />
    </x-slot>
    <div class="space-y-3 max-w-xl">   
        @livewire('image-upload')
        @livewire('show-images')
    </div>
</x-auth>