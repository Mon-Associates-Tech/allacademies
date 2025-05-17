@props(['name', 'type', 'value' => null, 'label' => null, 'error' => null, 'hasLabel' => true])

<div class="space-y-1">
    @if ($hasLabel ?? false)
        <label for="{{ $name }}" class="block text-sm tracking-wide font-medium text-gray-700">
            {{ $label ?? ucfirst($name) }}
        </label>
    @endif
    <input name="{{ $name }}" id="{{ $name }}" type="{{ $type }}" value="{{ old($name, $value) }}" {{ $attributes->merge(['class' => 'border-gray-300 rounded-lg shadow-sm w-full leading-tight']) }}>
    @error($error ?? $name)
    <div class="text-xs font-medium text-red-600">{{ $message }}</div>
    @enderror
</div>
