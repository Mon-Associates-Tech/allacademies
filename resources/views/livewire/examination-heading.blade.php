<div class="space-y-2">
    <label class="block text-sm tracking-wide font-medium text-gray-700">Heading</label>
    <div class="rounded-xl bg-gray-200 p-2">
        @if($package)
            <div class="grid grid-cols-4 gap-3">
                <div>   
                    <input type="radio" name="heading_type" wire:model="heading_type" id="1" value="1" {{ old('heading_type') ? 'checked' : ''}} class="peer hidden" required="required" />
                    <label for="1" class="block cursor-pointer select-none rounded-xl p-2 text-center peer-checked:bg-primary-600 peer-checked:font-bold peer-checked:text-white text-sm font-medium">Basic</label>
                </div>

                <div>
                    <input type="radio" name="heading_type" wire:model="heading_type" id="2" value="2" {{ old('heading_type') ? 'checked' : ''}} class="peer hidden" />
                    <label for="2" class="block cursor-pointer select-none rounded-xl p-2 text-center peer-checked:bg-primary-600 peer-checked:font-bold peer-checked:text-white text-sm font-medium">Institutional</label>
                </div>

                <div>
                    <input type="radio"  name="heading_type" wire:model="heading_type" id="3" value="3" {{ old('heading_type') ? 'checked' : ''}} class="peer hidden" />
                    <label for="3" class="block cursor-pointer select-none rounded-xl p-2 text-center peer-checked:bg-primary-600 peer-checked:font-bold peer-checked:text-white text-sm font-medium">Advanced - Institutional</label>
                </div>
            </div>
        @elseif(!$package)
            <input checked type="radio" name="heading_type" id="4" value="1" class="peer hidden" />
        @endif
        
        @if($heading_type != null || $package == null)
            <div class="pt-4">
                <div x-data="{ preview: false}">
                    <div class="bg-white border-x border-t border-gray-300 rounded-t-lg">
                        <div class="text-xs pl-3">
                            <button x-bind:class="!preview && 'font-medium tracking-wide'" x-on:click="preview = false" type="button" class="py-2 px-1">Write</button>
                            <button x-bind:class="preview && 'font-medium tracking-wide'" x-on:click="preview = true" type="button" class="py-2 px-1">Preview</button>
                        </div>
                    </div>
                    <div x-show="!preview" id="write-heading" class="block px-4 py-2 text-gray-700 shadow-sm border border-gray-300 w-full rounded-b-lg">
                        @if($heading_type == "3")
                            <div class="bg-blue-50 border border-blue-200 text-sm text-blue-600 rounded-md p-4 mb-4" role="alert">
                                <span class="font-bold">Note:</span> Make sure to add institution and department name, and logo under 
                                <span class="font-bold">
                                    @if (($metaData->team)->owner->is(auth()->user()))
                                        <a class="hover:text-base" href="{{ route('teams.edit', ['team' => $metaData->team]) }}">Edit Team</a>
                                    @else
                                        <a>Edit Team</a>
                                    @endif
                                </span>section.
                            </div>
                            
                        @elseif($heading_type == "2")
                            <div class="bg-blue-50 border border-blue-200 text-sm text-blue-600 rounded-md p-4 mb-4" role="alert">
                                <span class="font-bold">Note:</span> Make sure to add institution name under 
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
                
                        @if($heading_type == "3")
                            <div class="sm:col-span-2">
                                <x-form.input name="examiners" wire:model="examiners" type="text" />
                            </div>
                        @endif
                    </div>
            
                    <div x-show="preview" class="px-4 py-2 text-gray-700 focus:outline-none focus:border-gray-700 bg-white border border-gray-300 rounded-b-lg">
                        @if($heading_type == "1" || $package == null)
                            {{-- heading 1 - individual --}}
                            <div class="text-center font-bold">
                                <h3>{{$academicSubject->code}} <span class="mr-4"></span> {{$academicSubject->name}}</h3>
                                <h3>{{is_null($title) ? "EXAM TITLE" : $title}}</h3>
                                <h3>{{is_null($date) ? "EXAM DATE" : Carbon\Carbon::parse($date)->format('d F, Y')}}</h3>
                                <h3>{{is_null($start) ? "EXAM START TIME" : Carbon\Carbon::parse($start)->format('g:i A')}} <span class="mr-1 ml-1"> — </span> {{is_null($end) ? "EXAM END TIME" : Carbon\Carbon::parse($end)->format('g:i A')}}</h3> 
                            </div>
                            <p cLass="font-bold underline">Exams Instructions</p>
                            <P class="text-justify mb-8"> {{is_null($instructions) ? "Exams instructions will be displayed here" : $instructions}} </P> 
                        @elseif ($heading_type == "2")
                            {{-- heading 2 for institutions --}}
                            <div class = "text-center font-bold">
                                <h2>{{ Str::ucfirst(is_null($metaData) ? null : $metaData->meta['school'] ?? 'INSTITUTION NAME') }}</h2>
                                <h3>{{ Str::ucfirst($academicLevel->academicGroup->name) }} {{ Str::ucfirst($academicLevel->name) }}</h3>    
                                <h3>{{ $academicSubject->code }}  <span class="mr-4"></span> {{ $academicSubject->name }}</h3>
                                <h3>{{is_null($title) ? "EXAM TITLE" : $title}}</h3>
                            </div>
                            <div class="font-bold">
                            <h3>{{is_null($date) ? "EXAM DATE" : 'Date: ' . Carbon\Carbon::parse($date)->format('d F, Y')}}</h3>
                            <h3>Duration: {{is_null($start) ? "EXAM START TIME" : Carbon\Carbon::parse($start)->format('g:i A')}} <span class="mr-1 ml-1"> — </span> {{is_null($end) ? "EXAM END TIME" : Carbon\Carbon::parse($end)->format('g:i A')}}</h3> 
                            </div>
                            <p cLass="font-bold underline mt-4">Exams Instructions</p>
                            <P class="text-justify mb-8"> {{is_null($instructions) ? "Exams instructions will be displayed here" : $instructions}} </P> 
                             @elseif($heading_type == "3")
                            {{-- heading 3 - institution with logo --}}
                            <div class="flex justify-center">
                                <img src="{{ asset('storage/' . $metaData->meta['logo']) }}" class="w-20" alt="" onerror="this.style.display='none'" />
                            </div>
                            <div class="text-center">
                                <h2 class="font-bold">{{ Str::upper(is_null($metaData) ? null : $metaData->meta['school'] ?? 'INSTITUTION NAME') }}</h2>
                                <h3>{{ Str::upper(is_null($metaData) ? null : $metaData->meta['department'] ?? 'DEPARTMENT') }}</h3>   
                                <h3>{{ Str::ucfirst($academicLevel->academicGroup->name) }} {{ Str::ucfirst($academicLevel->name) }}</h3>   
                                <h3>{{ $academicSubject->code }} <span class="mr-4"></span> {{ $academicSubject->name }} <span class="mr-4"></span> {{is_null($date) ? "EXAM DATE" : 'Date: ' . Carbon\Carbon::parse($date)->format('d F, Y')}}</h3>
                                <h3>{{is_null($title) ? "EXAM TITLE" : $title}}</h3>
                                <h3>{{is_null($examiners) ? "EXAMINERS" : "Examiner(s): " . $examiners}}</h3>
                                <h3>{{is_null($start) ? "EXAM START TIME" : Carbon\Carbon::parse($start)->format('g:i A')}} <span class="mr-1 ml-1"> — </span> {{is_null($end) ? "EXAM END TIME" : Carbon\Carbon::parse($end)->format('g:i A')}}</h3> 
                            </div>
                            <p cLass="font-bold underline mt-4">Exams Instructions</p>
                            <P class="text-justify mb-8"> {{is_null($instructions) ? "Exams instructions will be displayed here" : $instructions}} </P> 
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>