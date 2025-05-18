<div class="space-y-2">
    <label class="font-medium text-sm text-gray-700">Select Options</label>

    @foreach ($metafields_options as $key => $label)
        <div class="flex items-center space-x-2">
            <input type="checkbox" wire:model="selectedOptions" value="{{ $key }}" id="option-{{ $key }}"
                   class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-600" />
            <label for="option-{{ $key }}" class="text-sm text-gray-800">{{ $label }}</label>
        </div>
    @endforeach
</div>


@if (in_array('page', $selectedOptions))
    <div class="bg-slate-200 p-4 mt-4 rounded">
        <h4 class="py-2 font-semibold">Insert Blank Page</h4>
        <div class="flex items-center">
            <label class="text-sm mr-3 my-auto">Number of Pages</label>
            <x-form.input :has-label="false" class="max-w-[56px]" name="pages_count" type="number" wire:model="pagesCount" />
        </div>
    </div>
@endif

@if (in_array('image', $selectedOptions))
    <div class="bg-slate-200 p-4 mt-4 rounded">
        <h4 class="py-2 font-semibold">Insert Image</h4>
        <div>
            <input type="file" wire:model="image" accept="image/*" />

            @if ($image)
                <img src="{{ $image->temporaryUrl() }}" class="mt-2 max-w-xs rounded shadow" />
            @endif
        </div>
    </div>
@endif

@if (in_array('space', $selectedOptions))
    <div class="bg-slate-200 p-4 mt-4 rounded">
        <h4 class="py-2 font-semibold">Insert Empty Spaces</h4>
        <div class="flex items-center">
            <label class="text-sm mr-3 my-auto">Number of Spaces</label>
            <x-form.input :has-label="false" class="max-w-[56px]" name="spaces_count" type="number" wire:model="spacesCount" />
        </div>
    </div>
@endif

@if (in_array('external', $selectedOptions))
    <div class="bg-slate-200 p-4 mt-4 rounded">
        <h4 class="py-2 font-semibold">Insert Document</h4>
        <div>
            <input type="file" wire:model="file" />

            @if ($file)
                <iframe src="{{ $file->temporaryUrl() }}" class="w-full h-96 mt-2 border rounded"></iframe>
            @endif
        </div>
    </div>
@endif
