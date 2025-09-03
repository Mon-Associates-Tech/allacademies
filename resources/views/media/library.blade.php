<x-layouts.app>
    <div class="container mx-auto px-4 py-6">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Media Library</h1>
            <p class="text-gray-600">Manage your files and folders</p>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            @livewire('media.media-library', ['folderId' => $folderId])
        </div>
    </div>
</x-layouts.app>
