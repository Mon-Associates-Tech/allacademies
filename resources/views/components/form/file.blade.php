@props(['name', 'label' => null, 'required' => false, 'info' => null, 'infoPosition' => 'top'])

<div class="space-y-1">
    <label for="{{ $name }}" class="block text-sm tracking-wide font-medium text-gray-700 dark:text-gray-300">
        {{ $label ?? ucfirst($name) }}
        @if($required)
            <span class="text-red-500">*</span>
        @endif
    </label>
    @if(!empty($info) && $infoPosition === 'top')
        <p class="text-xs tracking-tight !-mt-0 pb-1 text-gray-500 dark:text-gray-400">{{ $info }}</p>
    @endif
    <input name="{{ $name }}" id="{{ $name }}" type="file" {{ $attributes->merge(['class' => "block text-sm tracking-wide font-medium bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm w-full leading-tight
        file:mr-4 file:py-2 file:px-4
        file:rounded-md file:border-0
        file:text-sm file:font-medium
        file:bg-primary-600 file:text-white
        hover:file:bg-primary-700
        dark:file:bg-primary-500 dark:hover:file:bg-primary-600
    "]) }} />
    @if(!empty($info) && $infoPosition === 'bottom')
        <p class="text-xs tracking-tight !-mt-0 pb-1 pt-1 text-gray-500 dark:text-gray-400">{{ $info }}</p>
    @endif
    @error($name)
    <div class="text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</div>
    @enderror
</div>
