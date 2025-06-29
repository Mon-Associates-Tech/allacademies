@props([
    'label' => '',
    'model' => '',
    'accept' => '',
    'maxSize' => '2048',
    'preview' => false,
    'helpText' => '',
    'required' => false
])

<div class="space-y-2">
    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
        {{ $label }}
        @if($required)
            <span class="text-red-500">*</span>
        @endif
    </label>

    <div class="flex items-center space-x-4">
        <!-- File Input -->
        <div class="flex-1">
            <input
                type="file"
                wire:model="{{ $model }}"
                accept="{{ $accept }}"
                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4
                       file:rounded-lg file:border-0 file:text-sm file:font-medium
                       file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100
                       file:cursor-pointer cursor-pointer
                       @error($model) border-red-500 @enderror"
            >
        </div>

        <!-- Preview -->
        @if($preview && $this->$model)
            <div class="flex-shrink-0">
                @if(is_string($this->$model))
                    <img src="{{ Storage::url($this->$model) }}" alt="Preview"
                         class="w-16 h-16 object-cover rounded-lg border">
                @else
                    <img src="{{ $this->$model->temporaryUrl() }}" alt="Preview"
                         class="w-16 h-16 object-cover rounded-lg border">
                @endif
            </div>
        @endif
    </div>

    @if($helpText)
        <p class="text-xs text-gray-500">{{ $helpText }}</p>
    @endif

    <!-- Upload Progress -->
    <div wire:loading wire:target="{{ $model }}" class="w-full bg-gray-200 rounded-full h-2">
        <div class="bg-blue-600 h-2 rounded-full animate-pulse" style="width: 45%"></div>
    </div>

    @error($model)
    <p class="text-red-500 text-sm flex items-center mt-1">
        <svg class="w-4 h-4 mr-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
        </svg>
        {{ $message }}
    </p>
    @enderror
</div>
