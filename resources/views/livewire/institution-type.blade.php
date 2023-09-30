@php
    $options = [
        'institution_only' => 'Institution Only',
        'department_based' => 'Department Based',
        'faculty_based' => 'Faculty Based',
        'college_based' => 'College Based'
    ];

    $logo_path = is_null($metaData) ? null : $metaData['logo'] ?? '';  
@endphp
<div>
    <label class="block text-gray-800 font-medium text-sm">Institution</label>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-3 mt-3">
        <div class="col-span-1">
            <x-form.select name="type" wire:model="type" type="text" :options="$options"/>
        </div>
        <div class="col-span-1">
            <x-form.input name="institution" type="text" label="Name" :value="is_null($metaData) ? null : $metaData['name'] ?? ''"/>
        </div> 
    </div>
    @if($type == "college_based")
        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-3 mt-3">
            <div class="col-span-1">
                <x-form.input name="college" type="text" :value="is_null($metaData) ? null : $metaData['college'] ?? ''"/>
            </div>
            <div class="col-span-1">
                <x-form.input name="school" type="text" :value="is_null($metaData) ? null : $metaData['school'] ?? ''"/>
            </div>
        </div>
    @endif
    @if($type == "faculty_based")
        <div class="col-span-2 mt-3">
            <x-form.input name="faculty" type="text" :value="is_null($metaData) ? null : $metaData['faculty'] ?? ''"/>
        </div>
    @endif
    @if($type != "institution_only")
        <div class="col-span-2 mt-3">
            <x-form.input name="department" type="text" :value="is_null($metaData) ? null : $metaData['department'] ?? '' "/>
        </div>
    @endif
    <div class="grid grid-cols-1 md:grid-cols-6 gap-x-3 mt-3">
        @if($logo_path)
            <div class="col-span-1">
                <img src="{{ asset('storage/' . $logo_path) }}" class="w-15" alt="" onerror="this.style.display='none'"/>
            </div>
            <div class="col-span-5">
                <x-form.file-upload name="logo" class="block"/>
            </div>
        @else
            <div class="col-span-6">
                <x-form.file-upload name="logo"/>
            </div>
        @endif
    </div>
</div>

