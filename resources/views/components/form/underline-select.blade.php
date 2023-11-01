@props(['name', 'value' => null, 'label' => null, 'options' => [], 'nullable' => false])

<div class="space-y-1">
    <label for="{{ $name }}"
        class="block text-sm tracking-wide font-medium text-gray-700">{{ $label ?? ucfirst($name) }}</label>
    <div class="relative">
        <select name="{{ $name }}" id="{{ $name }}"
            {{ $attributes->merge(['class' => 'block py-2.5 px-0 bg-transparent border-0 border-b-2 border-gray-300 w-full appearance-none focus:outline-none focus:ring-0 focus:border-gray-200']) }}>
            @if ($nullable)
                <option>{{ $nullable }}</option>
            @endif
            @foreach ($options as $key => $name)
                <option value="{{ $key }}" @if (old($name, $value) == $key) selected @endif>{{ $name }}
                </option>
            @endforeach
        </select>
    </div>
    @error($name)
        <div class="text-xs font-medium text-red-600">{{ $message }}</div>
    @enderror
</div>


{{-- <label for="underline_select" class="sr-only">Underline select</label>
<select id="underline_select"
    class="block py-2.5 px-0 w-full text-sm text-gray-500 bg-transparent border-0 border-b-2 border-gray-200 appearance-none dark:text-gray-400 dark:border-gray-700 focus:outline-none focus:ring-0 focus:border-gray-200 peer">
    <option selected>Choose a country</option>
    <option value="US">United States</option>
    <option value="CA">Canada</option>
    <option value="FR">France</option>
    <option value="DE">Germany</option>
</select> --}}
