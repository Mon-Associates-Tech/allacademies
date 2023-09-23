@php
    $logo_path = is_null($metaData) ? null : $metaData->meta['logo'] ?? '';
    if($logo_path){
        $logo_path = asset('storage/' . $logo_path);
    }else{
        $logo_path = URL::to('/img/logo.png');
    }
    $institution_type = is_null($metaData) ? null : $metaData->meta['institution_type'] ?? '';
    $institution_name = is_null($metaData) ? 'INSTITUTION NAME' : Str::upper($metaData->meta['institution_name']) ?? 'INSTITUTION NAME';
    $school = is_null($metaData) ? 'SCHOOL' : Str::ucfirst($metaData->meta['school']) ?? 'SCHOOL';
    $faculty = is_null($metaData) ? 'FACULTY' : Str::ucfirst($metaData->meta['faculty']) ?? 'FACULTY';
    $college = is_null($metaData) ? 'COLLEGE' : Str::ucfirst($metaData->meta['college']) ?? 'COLLEGE';
    $department = is_null($metaData) ? 'DEPARTMENT' : Str::ucfirst($metaData->meta['department']) ?? 'DEPARTMENT';

    $academic_group = Str::ucfirst($academicLevel->academicGroup->name);
    $academic_level = Str::ucfirst($academicLevel->name);
    $subject_code = $academicSubject->code;
    $subject_name = Str::ucfirst($academicSubject->name);
    $exam_date = is_null($date) ? "EXAM DATE" : Carbon\Carbon::parse($date)->format('d F, Y');
    $exam_title = is_null($title) ? "EXAM TITLE" : Str::ucfirst($title);
    $start_time = is_null($start) ? "EXAM START TIME" : Carbon\Carbon::parse($start)->format('g:i A');
    $end_time =  is_null($end) ? "EXAM END TIME" : Carbon\Carbon::parse($end)->format('g:i A'); 
    $examiners = is_null($examiners) ? "EXAMINERS" : "Examiner(s): " . Str::ucfirst($examiners);
    $instructions  = is_null($instructions) ? "Exams instructions will be displayed here." : Str::ucfirst($instructions);
@endphp
<div class="space-y-2">
    <label class="block text-sm tracking-wide font-medium text-gray-700">Heading</label>
    <div class="rounded-xl bg-gray-200 p-2">
        @if($package)
            <div class="grid grid-cols-4 gap-3">
                <div>   
                    <input type="radio" name="heading_type" wire:model="heading_type" id="1" value="basic" class="peer hidden" required="required" />
                    <label for="1" class="block cursor-pointer select-none rounded-xl p-2 text-center peer-checked:bg-primary-600 peer-checked:font-bold peer-checked:text-white text-sm font-medium">Basic</label>
                </div>

                <div>
                    <input type="radio" name="heading_type" wire:model="heading_type" id="2" value="institutional" class="peer hidden" />
                    <label for="2" class="block cursor-pointer select-none rounded-xl p-2 text-center peer-checked:bg-primary-600 peer-checked:font-bold peer-checked:text-white text-sm font-medium">Institutional</label>
                </div>

                <div>
                    <input type="radio"  name="heading_type" wire:model="heading_type" id="3" value="institutional_advanced" class="peer hidden" />
                    <label for="3" class="block cursor-pointer select-none rounded-xl p-2 text-center peer-checked:bg-primary-600 peer-checked:font-bold peer-checked:text-white text-sm font-medium">Advanced - Institutional</label>
                </div>
            </div>
        @elseif(!$package)
            <input checked type="radio" name="heading_type" id="4" value="individual" class="peer hidden" />
        @endif
        <div class="pt-4">
            <div x-data="{ preview: false}">
                <div class="bg-white border-x border-t border-gray-300 rounded-t-lg">
                    <div class="text-xs pl-3">
                        <button x-bind:class="!preview && 'font-medium tracking-wide'" x-on:click="preview = false" type="button" class="py-2 px-1">Write</button>
                        <button x-bind:class="preview && 'font-medium tracking-wide'" x-on:click="preview = true" type="button" class="py-2 px-1">Preview</button>
                    </div>
                </div>
                <div x-show="!preview" class="block px-4 py-2 text-gray-700 shadow-sm border border-gray-300 w-full rounded-b-lg">
                    @if($package)
                        <div class="bg-primary-50 border border-primary-300 text-sm text-gray-600 rounded-md p-4 mb-4" role="alert">
                            <span class="font-bold">Note!</span> Make sure to add institution details under 
                            <span class="font-bold">
                                @if (($metaData->team)->owner->is(auth()->user()))
                                    <a class="hover:text-base" href="{{ route('teams.edit', ['team' => $metaData->team]) }}">Edit Team</a>
                                @else
                                    <a>Edit Team</a>
                                @endif
                            </span>section.
                        </div>
                    @endif
                    <div class="sm:col-span-2">
                        <x-form.input name="title" wire:model="title" type="text"/>
                    </div> 
                    <div class="grid md:grid-cols-4 gap-x-3 py-2">
                        <div class="col-span-2">
                            <x-form.input name="date" wire:model="date" type="date" />
                        </div>
                        <div class="col-span-1">
                            <x-form.input name="start" wire:model="start" type="time" />
                        </div>
                        <div class="col-span-1">
                            <x-form.input name="end" wire:model="end" type="time" />
                        </div>
                    </div>
                    <div class="sm:col-span-2">
                        <x-form.textarea name="instructions" wire:model="instructions" type="text" /> 
                    </div>
            
                    @if($heading_type == "institutional_advanced")
                        <div class="sm:col-span-2">
                            <x-form.input name="examiners" wire:model="examiners" type="text" />
                        </div>
                    @endif
                </div>
        
                <div x-show="preview" class="px-4 py-2 text-gray-700 focus:outline-none focus:border-gray-700 bg-white border border-gray-300 rounded-b-lg">
                    @if($heading_type == "basic" || $package == null)
                        {{-- heading 1 - individual --}}
                        @if(!$package)
                            <div class="flex justify-center">
                                <img src="{{URL::to('/img/logo.png')}}" class="w-20" alt="" onerror="this.style.display='none'" />
                            </div>
                        @endif
                        <div class="text-center">
                            @if($package)
                                <h3 class="font-bold">{{ $institution_name }}</h3>
                            @endif
                            <h3>{{ $subject_code }} <span class="mr-4"></span> {{ $subject_name }}</h3>
                            <h3>{{ $exam_title }}</h3>
                            <h3>{{ $exam_date }}</h3>
                            <h3>{{ $start_time }} <span class="mr-1 ml-1"> — </span> {{ $end_time }}</h3> 
                        </div>
                        <p cLass="font-bold underline mt-4">Exams Instructions</p>
                        <P class="text-justify mb-8">{{ $instructions }}</P>
                        
                    @elseif ($heading_type == "institutional")
                        {{-- heading 2 for institution_names --}}
                        <div class="flex justify-center">
                            <img src="{{$logo_path}}" class="w-20" alt="" onerror="this.style.display='none'" />
                        </div>
                        <div class = "text-center">
                            <h3 class="font-bold">{{ $institution_name }}</h3>
                            <h3>{{ $academic_group }} <span class="mr-4"></span> {{ $academic_level }}</h3>
                            <h3>{{ $subject_code . " " . $subject_name }}</h3>
                            <h3>{{ $exam_title }}</h3>
                        </div>
                        <div class="font-bold mt-4">
                            <p>Date: {{ $exam_date }}</p>
                            <p>Duration: {{ $start_time }} <span class="mr-1 ml-1"> — </span> {{ $end_time }}</h3> 
                        </div>
                        <p cLass="font-bold underline mt-4">Exams Instructions</p>
                        <P class="text-justify mb-8">{{ $instructions }}</P> 

                    @elseif($heading_type == "institutional_advanced")
                        {{-- heading 3 - institution_name with logo --}}
                        <div class="flex justify-center">
                            <img src="{{$logo_path}}" class="w-20" alt="" onerror="this.style.display='none'" />
                        </div>
                        <div class="text-center">
                            <h3 class="font-bold">{{ $institution_name }}</h3>
                            @if($institution_type == "faculty_based")
                                <h2>{{ $faculty }}</h2>
                            @elseif($institution_type == "college_based")
                                <h2>{{ $college}} <span class="mr-4"></span> {{$school }}</h2>
                            @endif
                            @if($institution_type != "institution_only")
                                <h3>{{ $department }}</h3>
                            @endif   
                            <h3>{{ $academic_group }} <span class="mr-4"></span> {{$academic_level }}</h3> 
                            <h3>{{ $exam_title }}</h3>  
                            <h3>{{ $subject_code . " " . $subject_name }} <span class="mr-4"></span> {{ $exam_date }}</h3>
                            <h3>{{ $start_time }} <span class="mr-1 ml-1"> — </span> {{ $end_time }}</h3>   
                        </div>
                        <p cLass="font-bold mb-4 mt-4">{{ $examiners }}</p>
                        <p cLass="font-bold underline mt-4">Exams Instructions</p>
                        <P class="text-justify mb-8">{{ $instructions }}</P> 
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>