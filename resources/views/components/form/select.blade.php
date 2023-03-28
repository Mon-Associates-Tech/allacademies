@props(['name', 'value' => null, 'label' => null, 'options' => [], 'nullable' => false])

<div class="space-y-1">
    <label for="{{ $name }}" class="block text-sm tracking-wide font-medium text-gray-700">{{ $label ?? ucfirst($name) }}</label>
    <div class="relative">
        <select name="{{ $name }}" id="{{ $name }}" {{ $attributes->merge(['class' => 'block border-gray-300 rounded-lg shadow-sm w-full leading-tight']) }}>
            @if($nullable)
            <option>{{ $nullable }}</option>
            @endif
            @foreach ($options as $key => $name)
                <option value="{{ $key }}" @if(old($name, $value) == $key) selected @endif>{{ $name }}</option>
            @endforeach
        </select>
    </div>
    @error($name)
    <div class="text-xs font-medium text-red-600">{{ $message }}</div>
    @enderror
</div>