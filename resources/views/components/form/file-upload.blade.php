@props(['name', 'label' => null])

<div class="space-y-1">
    <label for="{{ $name }}" class="block text-sm tracking-wide font-medium text-gray-700">{{ $label ?? ucfirst($name) }}</label>
    <input name="{{ $name }}" id="{{ $name }}" type="file" {{ $attributes->merge(['class' => "block text-sm tracking-wide font-medium bg-white text-gray-700 border border-gray-300 rounded-lg shadow-sm w-full leading-tight
    file:mr-4 file:py-2 file:px-4
    file:rounded-md file:border-0
    file:text-sm file:font-medium
    file:bg-primary-600 file:text-white
    hover:file:hover:bg-primary-700
    "]) }} />
    @error($name)
    <div class="text-xs font-medium text-red-600">{{ $message }}</div>
    @enderror
</div>