<x-auth title="Upload Image">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Back to Dashboard' => route('dashboard'),
        ]" />
    </x-slot>
    <div class="space-y-10 max-w-2xl">   
        @livewire('image-upload')
        @livewire('show-images')
    </div>
</x-auth>