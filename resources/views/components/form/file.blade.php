@props(['name', 'label' => null])

<div class="space-y-1">
    <label for="{{ $name }}" class="block text-sm tracking-wide font-medium text-gray-700">{{ $label ?? ucfirst($name) }}</label>
    <input name="{{ $name }}" id="{{ $name }}" type="file" {{ $attributes->merge(['class' => 'w-full leading-tight']) }}>
    @error($name)
    <div class="text-xs font-medium text-red-600">{{ $message }}</div>
    @enderror
</div>