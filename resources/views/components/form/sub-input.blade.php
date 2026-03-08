@props(['name', 'type', 'value' => null, 'label' => null])

<div>
    <div class="relative border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 rounded-md px-3 py-2 shadow-sm focus-within:ring-1 focus-within:ring-indigo-600 dark:focus-within:ring-indigo-400 focus-within:border-indigo-600 dark:focus-within:border-indigo-400">
        <label for="{{ $name }}" class="absolute -top-2 left-2 -mt-px inline-block px-1 bg-gray-100 dark:bg-gray-800 text-xs font-medium text-gray-900 dark:text-gray-200">{{ $label ?? ucfirst($name) }}</label>
        <input name="{{ $name }}" id="{{ $name }}" type="{{ $type }}" value="{{ old($name, $value) }}" {{ $attributes->merge(['class' => 'block w-full border-0 p-0 text-gray-900 dark:text-white dark:bg-gray-700 placeholder-gray-500 dark:placeholder-gray-400 focus:ring-0 sm:text-sm']) }}>
    </div>
    @error($name)
    <div class="text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</div>
    @enderror
</div>
