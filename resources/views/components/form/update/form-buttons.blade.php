@props([
    'isEditing' => false,
    'submitText' => '',
    'loadingText' => '',
    'submitTarget' => '',
    'cancelAction' => 'hideForm',
    'resetAction' => 'resetForm',
    'showReset' => true
])

<div class="flex items-center justify-between">
    @if($isEditing)
        <button
            type="button"
            wire:click="{{ $cancelAction }}"
            class="px-6 py-3 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700
                   border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600
                   focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500
                   disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200"
        >
            Cancel
        </button>
    @endif

    <div class="flex items-center space-x-3 {{ $isEditing ? '' : 'ml-auto' }}">
        @if(!$isEditing && $showReset)
            <button
                type="button"
                wire:click="{{ $resetAction }}"
                class="px-6 py-3 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700
                       border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600
                       focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500
                       transition-colors duration-200"
            >
                Reset
            </button>
        @endif

        <x-button.primary
            type="submit"
            wire:loading.attr="disabled"
            wire:target="{{ $submitTarget }}"
            class="px-8 py-3 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-lg
                   hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500
                   disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200
                   flex items-center space-x-2"
        >
            <!-- Loading Spinner -->
            <svg wire:loading wire:target="{{ $submitTarget }}"
                 class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>

            <span wire:loading.remove wire:target="{{ $submitTarget }}">
                {{ $submitText ?: ($isEditing ? 'Update Author' : 'Create Author') }}
            </span>
            <span wire:loading wire:target="{{ $submitTarget }}">
                {{ $loadingText ?: ($isEditing ? 'Updating...' : 'Creating...') }}
            </span>
        </x-button.primary>
    </div>
</div>
