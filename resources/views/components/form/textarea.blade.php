@props(['name', 'value' => null, 'label' => null, 'required' => false])

<div class="space-y-1">
    <div class="flex">
        <label for="{{ $name }}" class="block text-sm tracking-wide font-medium text-gray-700">{{ $label ?? ucfirst($name) }}</label>
        @if($required)
            <span class="text-xs text-red-600"> *</span>
        @endif
    </div>
    <textarea name="{{ $name }}" id="{{ $name }}" {{ $attributes->merge(['class' => 'border-gray-300 rounded-lg shadow-sm w-full leading-tight']) }}>{{ old($name, $value) }}</textarea>
    @error($name)
    <div class="text-xs font-medium text-red-600">{{ $message }}</div>
    @enderror
</div>
