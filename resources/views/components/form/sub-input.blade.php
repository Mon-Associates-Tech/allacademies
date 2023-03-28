@props(['name', 'type', 'value' => null, 'label' => null])

<div>
    <div class="relative border border-gray-300 bg-white rounded-md px-3 py-2 shadow-sm focus-within:ring-1 focus-within:ring-indigo-600 focus-within:border-indigo-600">
        <label for="{{ $name }}" class="absolute -top-2 left-2 -mt-px inline-block px-1 bg-gray-100 text-xs font-medium text-gray-900">{{ $label ?? ucfirst($name) }}</label>
        <input name="{{ $name }}" id="{{ $name }}" type="{{ $type }}" value="{{ old($name, $value) }}" {{ $attributes->merge(['class' => 'block w-full border-0 p-0 text-gray-900 placeholder-gray-500 focus:ring-0 sm:text-sm']) }}>
    </div>
    @error($name)
    <div class="text-xs font-medium text-red-600">{{ $message }}</div>
    @enderror
</div>