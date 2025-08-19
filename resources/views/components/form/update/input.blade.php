@props([
    'fieldId' => '',
    'label' => '',
    'model' => '',
    'type' => 'text',
    'placeholder' => '',
    'required' => false,
    'helpText' => ''
])

<div class="space-y-2">
    <label for="{{ $fieldId }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
        {{ $label }}
        @if($required)
            <span class="text-red-500">*</span>
        @endif
    </label>

    <div class="relative">
        @if($type === 'textarea')
            <textarea
                id="{{ $fieldId }}"
                wire:model="{{ $model }}"
                rows="4"
                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm
                       focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                       dark:bg-gray-700 dark:text-white dark:focus:ring-blue-400
                       transition-colors duration-200 resize-vertical
                       @error($model) border-red-500 focus:border-red-500 focus:ring-red-500 @enderror"
                placeholder="{{ $placeholder }}"
                {{ $attributes }}
            ></textarea>
        @else
            <input
                id="{{ $fieldId }}"
                type="{{ $type }}"
                wire:model="{{ $model }}"
                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm
                       focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                       dark:bg-gray-700 dark:text-white dark:focus:ring-blue-400
                       transition-colors duration-200
                       @error($model) border-red-500 focus:border-red-500 focus:ring-red-500 @enderror"
                placeholder="{{ $placeholder }}"
                {{ $attributes }}
            >
        @endif

        <!-- Success/Error Icons -->
        @error($model)
        <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
            <svg class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>
        </div>
        @enderror
    </div>

    @if($helpText)
        <p class="text-xs text-gray-500">{{ $helpText }}</p>
    @endif

    @error($model)
    <p class="text-red-500 text-sm flex items-center mt-1">
        <svg class="w-4 h-4 mr-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
        </svg>
        {{ $message }}
    </p>
    @enderror
</div>
