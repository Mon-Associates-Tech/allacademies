@php
    $options = [
        'institution_only' => 'Institution Only',
        'department_based' => 'Department Based',
        'faculty_based' => 'Faculty Based',
        'college_based' => 'College Based'
    ];

    $logo_path = is_null($team->metaData) ? null : $team->metaData->meta['logo'] ?? '';  
@endphp
<div>
    <div class="bg-primary-50 border border-primary-300 text-sm text-gray-600 rounded-md p-4 mb-2 mt-2" role="alert">
        <span class="font-bold">Note!</span> 
        <span>Institution details will be used for examination heading. Please provide these details before creating an examination.</span>
    </div>
    <label class="block text-gray-800 font-medium text-sm">Institution</label>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-3 mt-3">
        <div class="col-span-1">
            <x-form.select name="institution_type" wire:model="institution_type" label="Type" type="text" :options="$options" />
        </div>
        <div class="col-span-1">
            <x-form.input name="institution_name" type="text" label="Name" :value="is_null($team->metaData) ? null : $team->metaData->meta['institution_name'] ?? '' " />
        </div> 
    </div>
    @if($institution_type == "college_based")
        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-3 mt-3">
            <div class="col-span-1">
                <x-form.input name="college" type="text" :value="is_null($team->metaData) ? null : $team->metaData->meta['college'] ?? '' "  />
            </div>
            <div class="col-span-1">
                <x-form.input name="school" type="text" :value="is_null($team->metaData) ? null : $team->metaData->meta['school'] ?? '' "  />
            </div>
        </div>
    @endif
    @if($institution_type == "faculty_based")
        <div class="col-span-2 mt-3">
            <x-form.input name="faculty" type="text" :value="is_null($team->metaData) ? null : $team->metaData->meta['faculty'] ?? '' "  />
        </div>
    @endif
    @if($institution_type != "institution_only")
        <div class="col-span-2 mt-3">
            <x-form.input name="department" type="text" :value="is_null($team->metaData) ? null : $team->metaData->meta['department'] ?? '' "  />
        </div>
    @endif
    <div class="grid grid-cols-1 md:grid-cols-6 gap-x-3 mt-3">
        @if($logo_path)
            <div class="col-span-1">
                <img src="{{ asset('storage/' . $logo_path) }}" class="w-15" alt="" onerror="this.style.display='none'" />
            </div>
            <div class="col-span-5">
                <x-form.file-upload name="logo" class="block"/>
            </div>
        @else
            <div class="col-span-6">
                <x-form.file-upload name="logo" class="block"/>
            </div>
        @endif
    </div>
</div>

