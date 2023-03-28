@props(['name', 'value' => null, 'label' => null])

<div class="space-y-1">
    <label for="{{ $name }}" class="block text-sm tracking-wide font-medium text-gray-700">{{ $label ?? ucfirst($name) }}</label>
    <textarea name="{{ $name }}" id="{{ $name }}" {{ $attributes->merge(['class' => 'border-gray-300 rounded-lg shadow-sm w-full leading-tight']) }}>{{ old($name, $value) }}</textarea>
    @error($name)
    <div class="text-xs font-medium text-red-600">{{ $message }}</div>
    @enderror
</div>