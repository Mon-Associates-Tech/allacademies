@props(['name', 'value' => null, 'label' => null, 'required' => false, 'info' => null, 'infoPosition' => 'top'])

<div class="space-y-1">
    <div class="flex">
        <label for="{{ $name }}" class="block text-sm tracking-wide font-medium text-gray-700 dark:text-gray-300">{{ $label ?? ucfirst($name) }}</label>
        @if($required)
            <span class="text-xs text-red-600 dark:text-red-400"> *</span>
        @endif
    </div>
    @if(!empty($info) && $infoPosition === 'top')
        <p class="text-xs tracking-tight !-mt-0 pb-1 text-gray-500 dark:text-gray-400">{{ $info }}</p>
    @endif
    <textarea name="{{ $name }}" id="{{ $name }}" {{ $attributes->merge(['class' => 'border-gray-300 dark:border-gray-600 rounded-lg shadow-sm w-full leading-tight dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent']) }}>{{ old($name, $value) }}</textarea>
    @if(!empty($info) && $infoPosition === 'bottom')
        <p class="text-xs tracking-tight !-mt-0 pb-1 pt-1 text-gray-500 dark:text-gray-400">{{ $info }}</p>
    @endif
    @error($name)
    <div class="text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</div>
    @enderror
</div>
