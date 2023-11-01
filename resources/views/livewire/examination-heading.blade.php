{{-- @php
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
@endphp --}}

<div class="space-y-1">
    <label class="text-gray-800 font-medium text-sm">Heading</label>
    <div x-data="{ preview: false, down: @entangle('down'), up: @entangle('up') }"
        x-init="up = marked.parse(down)"
        x-effect="up = marked.parse(down)">
        <div class="bg-white border-x border-t border-gray-300 rounded-t-lg flex items-center justify-between">
            <div class="text-xs pl-3">
                <button x-bind:class="!preview && 'font-medium tracking-wide'" x-on:click="preview = false" type="button" class="py-2 px-1">Edit</button>
                <button x-bind:class="preview && 'font-medium tracking-wide'" x-on:click="preview = true" type="button" class="py-2 px-1">Preview</button>
            </div>
            <div class="p-1">
                <span class="inline-flex rounded-md">
                    <span class="inline-flex items-center rounded-l-md ring-1 ring-inset ring-gray-300 bg-gray-50 px-2 py-1 text-xs font-medium text-gray-600">Template</span>
                    <select wire:model="template" id="heading_template" name="heading[template]" class="-ml-px border-0 rounded-r-md ring-1 ring-inset ring-gray-300 bg-gray-50 pl-2 pr-7 py-1 focus:ring-2 text-xs font-medium text-gray-600">
                        <option value="twig">Twig</option>
                        <option value="pug">Pug</option>
                        @isset($metadata['institution'])
                        <option value="tera">Tera</option>
                        <option value="jinja">Jinja</option>
                        @endisset
                    </select>
                </span>
            </div>
        </div>

        <div x-show="!preview" class="grid sm:grid-cols-6 gap-4 px-4 py-2 text-gray-700 focus:outline-none focus:border-gray-700 bg-gray-50 border border-gray-300 rounded-b-lg">
            <div class="sm:col-span-4">
                <x-form.input wire:model="title" name="heading[title]" label="Title" type="text" />
            </div>
            <div class="sm:col-span-2">
                <x-form.input wire:model="duration" name="heading[duration]" label="Duration" type="text" />
            </div>
            <div class="sm:col-span-6">
                <x-form.textarea wire:model="instructions" name="heading[instructions]" label="Instructions" />
            </div>
        </div>
        <textarea x-model="down" id="heading_down" name="heading[down]" class="hidden"></textarea>

        <div x-html="up" x-show="preview" class="font-serif block px-4 py-2 text-gray-700 focus:outline-none focus:border-gray-700 bg-white border border-gray-300 rounded-b-lg"></div>
        <textarea x-model="up" id="heading_up" name="heading[up]" class="hidden"></textarea>
    </div>
    @error('heading.up')
        <p class="text-xs text-red-700">{{ $message }}</p>
    @enderror
</div>
