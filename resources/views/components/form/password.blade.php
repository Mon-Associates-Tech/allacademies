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
    <input name="{{ $name }}" id="{{ $name }}" type="password" {{ $attributes->merge(['class' => 'border-gray-300 dark:border-gray-600 rounded-lg shadow-sm w-full leading-tight dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent']) }}>
    @if(!empty($info) && $infoPosition === 'bottom')
        <p class="text-xs tracking-tight !-mt-0 pb-1 pt-1 text-gray-500 dark:text-gray-400">{{ $info }}</p>
    @endif
    @error($name)
    <div class="text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</div>
    @enderror
</div>
