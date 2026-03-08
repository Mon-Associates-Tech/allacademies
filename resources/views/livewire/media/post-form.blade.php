<div class="max-w-4xl mx-auto bg-white shadow rounded-lg">
    <form wire:submit.prevent="save" class="space-y-6 p-6">
        <!-- Title -->
        <div>
            <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Title</label>
            <input
                type="text"
                id="title"
                wire:model="title"
                class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500"
                placeholder="Enter post title"
            >
            @error('title') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
        </div>

        <!-- Excerpt -->
        <div>
            <label for="excerpt" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Excerpt</label>
            <textarea
                id="excerpt"
                wire:model="excerpt"
                rows="3"
                class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500"
                placeholder="Brief description of the post"
            ></textarea>
            @error('excerpt') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
        </div>

        <!-- Featured Image -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-3">Featured Image</label>

            @if($featuredImage)
                <div class="relative inline-block">
                    <img
                        src="{{ $featuredImage->url }}"
                        alt="{{ $featuredImage->alt_text }}"
                        class="w-48 h-32 object-cover rounded-lg border border-gray-200"
                    >
                    <button
                        type="button"
                        wire:click="removeFeaturedImage"
                        class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center hover:bg-red-600"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                    <div class="mt-2">
                        <p class="text-sm text-gray-600">{{ $featuredImage->name }}</p>
                        <button
                            type="button"
                            wire:click="openFeaturedImagePicker"
                            class="text-blue-600 hover:text-blue-800 text-sm"
                        >
                            Change Image
                        </button>
                    </div>
                </div>
            @else
                <button
                    type="button"
                    wire:click="openFeaturedImagePicker"
                    class="w-48 h-32 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg flex items-center justify-center hover:border-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                >
                    <div class="text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 48 48">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                        </svg>
                        <p class="mt-2 text-sm text-gray-600">Select Featured Image</p>
                    </div>
                </button>
            @endif
        </div>

        <!-- Content -->
        <div>
            <label for="content" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Content</label>
            <textarea
                id="content"
                wire:model="content"
                rows="10"
                class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500"
                placeholder="Write your post content here..."
            ></textarea>
            @error('content') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
        </div>

        <!-- Gallery Images -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-3">Gallery Images</label>

            <div class="space-y-4">
                @if(!empty($galleryImages))
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                        @foreach($galleryImages as $image)
                            <div class="relative">
                                <img
                                    src="{{ $image['url'] }}"
                                    alt="{{ $image['alt_text'] ?? '' }}"
                                    class="w-full h-24 object-cover rounded-lg border border-gray-200"
                                >
                                <button
                                    type="button"
                                    wire:click="removeGalleryImage({{ $image['id'] }})"
                                    class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center hover:bg-red-600"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                                <p class="text-xs text-gray-600 mt-1 truncate">{{ $image['name'] }}</p>
                            </div>
                        @endforeach
                    </div>
                @endif

                <button
                    type="button"
                    wire:click="openGalleryPicker"
                    class="w-full h-24 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg flex items-center justify-center hover:border-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                >
                    <div class="text-center">
                        <svg class="mx-auto h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        <p class="mt-1 text-sm text-gray-600">
                            {{ empty($galleryImages) ? 'Add Gallery Images' : 'Add More Images' }}
                        </p>
                    </div>
                </button>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
            <button
                type="button"
                class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
            >
                Cancel
            </button>
            <button
                type="submit"
                class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700"
                wire:loading.attr="disabled"
            >
                <span wire:loading.remove>{{ $postId ? 'Update Post' : 'Create Post' }}</span>
                <span wire:loading>Saving...</span>
            </button>
        </div>
    </form>

    <!-- Media Pickers -->
    <div x-data="{
        featuredPickerOpen: false,
        galleryPickerOpen: false
    }">
        <!-- Featured Image Picker -->
        @livewire('media-picker', [
            'multiple' => false,
            'acceptedTypes' => ['image']
        ], key('featured-picker'))

        <!-- Gallery Picker -->
        @livewire('media-picker', [
            'multiple' => true,
            'acceptedTypes' => ['image']
        ], key('gallery-picker'))
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Listen for media picker events
        Livewire.on('openMediaPicker', function(type) {
            if (type === 'featured') {
                Livewire.emit('openFeaturedImagePicker');
            } else if (type === 'gallery') {
                Livewire.emit('openGalleryPicker');
            }
        });

        // Handle media selection
        Livewire.on('mediaSelected', function(media) {
            // This will be handled by the specific picker components
        });
    });
</script>
