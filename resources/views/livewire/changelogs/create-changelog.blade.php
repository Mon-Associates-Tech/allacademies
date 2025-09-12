<div class="p-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
    @if (session()->has('message'))
        <div class="mb-4 p-4 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 rounded-lg">
            {{ session('message') }}
        </div>
    @endif

    <form wire:submit.prevent="save">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Title
                </label>
                <input
                    type="text"
                    id="title"
                    wire:model="title"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
                @error('title')
                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label for="task_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Task Name
                </label>
                <input
                    type="text"
                    id="task_name"
                    wire:model="task_name"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
                @error('task_name')
                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="mb-6">
            <label for="task_description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Task Description
            </label>
            <textarea
                id="task_description"
                wire:model="task_description"
                rows="4"
                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"></textarea>
            @error('task_description')
                <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-6">
            <label for="additional_info" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Additional Information
            </label>
            <textarea
                id="additional_info"
                wire:model="additional_info"
                rows="3"
                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"></textarea>
            @error('additional_info')
                <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-6">
            <div class="flex justify-between items-center mb-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Completed Items
                </label>
                <button
                    type="button"
                    wire:click="addCompletedItem"
                    class="inline-flex items-center px-3 py-1 text-sm font-medium rounded-lg text-blue-700 dark:text-blue-300 bg-blue-100 dark:bg-blue-900/30 hover:bg-blue-200 dark:hover:bg-blue-800 transition-colors duration-150">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Add Item
                </button>
            </div>

            @error('completed_items')
                <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
            @enderror

            <div class="space-y-3 mt-2">
                @foreach($completed_items as $index => $item)
                <div class="flex items-center">
                    <input
                        type="text"
                        wire:model="completed_items.{{ $index }}"
                        placeholder="Completed item {{ $index + 1 }}"
                        class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
                    <button
                        type="button"
                        wire:click="removeCompletedItem({{ $index }})"
                        class="ml-2 p-2 text-red-500 hover:text-red-700 dark:hover:text-red-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                </div>
                @endforeach
            </div>
        </div>

        <div class="flex justify-end">
            <button
                type="submit"
                class="px-6 py-2 bg-blue-600 hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-800 text-white font-medium rounded-lg transition-colors duration-150">
                Create Changelog Entry
            </button>
        </div>
    </form>
</div>
