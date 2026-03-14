<x-layouts.app :has-action="false" page-name="Edit Resource">
    <div class="container mx-auto px-4 py-6">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="mb-6">
                <a href="{{ route('educational-resources.show', $resource) }}" class="inline-flex items-center text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors mb-4">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Media
                </a>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Media</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Update the details of "{{ $resource->title }}"</p>
            </div>
            <!-- Edit Form Card -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <livewire:resources.resource-edit :resource="$resource" />
            </div>
        </div>
    </div>
</x-layouts.app>
