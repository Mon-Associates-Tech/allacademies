@props(['name', 'value' => null, 'label' => null, 'options' => [], 'nullable' => false, 'required' => false])

<div class="space-y-1">
    <div class="flex">
        <label for="{{ $name }}" class="block text-sm tracking-wide font-medium text-gray-700 dark:text-gray-300">{{ $label ?? ucfirst($name) }}</label>
        @if($required)
            <span class="text-xs text-red-600 dark:text-red-400"> *</span>
        @endif
    </div>

    <div class="relative">
        <select name="{{ $name }}" id="{{ $name }}" {{ $attributes->merge(['class' => 'block border-gray-300 dark:border-gray-600 rounded-lg shadow-sm w-full leading-tight dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent']) }}>
            @if($nullable)
            <option value="" class="dark:bg-gray-700">{{ $nullable }}</option>
            @endif
            @foreach ($options as $key => $optionName)
                <option value="{{ $key }}" class="dark:bg-gray-700" @if(old($name, $value) == $key) selected @endif>{{ $optionName }}</option>
            @endforeach
        </select>
    </div>
    @error($name)
    <div class="text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</div>
    @enderror
</div>
