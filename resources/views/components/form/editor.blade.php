@props(['name', 'label' => null, 'value' => ''])

@php
    $up = null;
    $down = null;

    $mark = old($name, $value);

    if ($mark instanceof \App\Support\Mark) {
        $down = $mark->down;
        $up = $mark->up;
    }

    if (is_string($down)) {
        $down = addslashes($down);
    }
@endphp


<div class="space-y-1">
    <label class="text-gray-800 font-medium text-sm">{{ $label ?? ucfirst($name) }}</label>
    <div x-data="{ preview: false, down: '{{ $down }}', up: '{{ $up }}' }"
        x-effect="up = marked.parse(down)">
        <div class="bg-white border-x border-t border-gray-300 rounded-t-lg">
            <div class="text-xs pl-3">
                <button x-bind:class="!preview && 'font-medium tracking-wide'" x-on:click="preview = false" type="button" class="py-2 px-1">Write</button>
                <button x-bind:class="preview && 'font-medium tracking-wide'" x-on:click="preview = true" type="button" class="py-2 px-1">Preview</button>
            </div>
        </div>

        <textarea x-model.lazy="down" x-show="!preview" id="{{ $name }}_down" name="{{ $name }}[down]" class="block px-4 py-2 text-gray-700 shadow-sm border border-gray-300 w-full rounded-b-lg"></textarea>

        <div x-html="up" x-show="preview" class="block px-4 py-2 text-gray-700 focus:outline-none focus:border-gray-700 bg-white border border-gray-300 rounded-b-lg"></div>
        <textarea x-model="up" id="{{ $name }}_up" name="{{ $name }}[up]" class="hidden"></textarea>
    </div>
    @error($name.'.up')
        <p class="text-xs text-red-700">{{ $message }}</p>
    @enderror
</div>