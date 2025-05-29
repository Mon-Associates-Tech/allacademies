@php
    $options = [
        'institution' => 'Institution Only',
        'department' => 'Department Based',
        'faculty' => 'Faculty Based',
        'college' => 'College Based',
    ];
@endphp
<div>
    <h6 class="text-base font-semibold leading-7 text-gray-900">Institutional Information</h6>
    <p class="mt-1 text-sm leading-6 text-gray-600">This information will be used to generate examination headings and may require approval when edited.</p>

    <div class="grid grid-cols-1 md:grid-cols-6 gap-3 mt-3">
        <div class="col-span-2">
            <x-form.select name="type" wire:model.live="type" type="text" :options="$options" />
        </div>
        <div class="col-span-4">
            <x-form.input name="institution" wire:model.live="institution" type="text" label="Name" />
        </div>
        @if ('college' === $type)
        <div class="col-span-2">
            <x-form.input name="college" wire:model.live="college" type="text" label="College" />
        </div>
        <div class="col-span-2">
            <x-form.input name="school" wire:model.live="school" type="text" label="School" />
        </div>
        <div class="col-span-2">
            <x-form.input name="department" wire:model.live="department" type="text" label="Department" />
        </div>
        @endif
        @if ('faculty' === $type)
        <div class="col-span-3">
            <x-form.input name="faculty" wire:model.live="faculty" type="text" label="Faculty" />
        </div>
        <div class="col-span-3">
            <x-form.input name="department" wire:model.live="department" type="text" label="Department" />
        </div>
        @endif
        @if ('department' === $type)
        <div class="col-span-6">
            <x-form.input name="department" wire:model.live="department" type="text" label="Department" />
        </div>
        @endif
        <div class="col-span-6">
            <x-form.file name="logo" />
        </div>
    </div>
</div>
