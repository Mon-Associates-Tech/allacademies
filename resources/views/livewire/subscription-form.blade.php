<div class="grid sm:grid-cols-3 gap-4">
    <div class="sm:col-span-2">
        <x-form.select wire:model="durationInMonths" name="duration_in_months" label="Duration" :options="[
            '3' => '3 Months',
            '6' => '6 Months',
            '12' => '12 Months',
        ]" />
    </div>

    <input name="package" type="hidden" value="{{ $package }}">

    @if('institution:full' == $package)
    <div class="sm:col-span-2">
        <x-form.input wire:model="beneficiaries" name="beneficiaries" type="number" />
    </div>
    @endif

    <div class="sm:col-span-2">
        <label class="block text-gray-800 font-medium text-sm mt-2 mb-1">Subjects</label>
        <div class="overflow-hidden rounded-lg border border-gray-300 bg-white">
            <div class="bg-gray-50 p-2 flex items-center justify-between">
                <span class="inline-flex items-center gap-x-1.5 rounded-md px-2 py-1 text-xs font-medium text-gray-900 ring-1 ring-inset ring-gray-300 bg-white">
                    <svg class="h-1.5 w-1.5 {{ $this->subjects_count ?  'fill-green-500' : 'fill-red-500'}}" viewBox="0 0 6 6" aria-hidden="true">
                        <circle cx="3" cy="3" r="3" />
                    </svg>
                    {{ $this->subjects_count }} {{ Str::plural('Subject', $this->subjects_count) }} Selected
                </span>

                <span class="inline-flex rounded-md">
                    <span
                        class="inline-flex items-center rounded-l-md ring-1 ring-inset ring-gray-300 bg-white px-2 py-1 text-sm font-medium text-gray-600">
                        Academic Group
                    </span>
                    <select wire:model="academicGroupId" id="academic_group"
                        class="-ml-px border-0 rounded-r-md ring-1 ring-inset ring-gray-300 bg-white pl-2 pr-7 py-1 focus:ring-2 text-sm font-medium text-gray-600">
                        @foreach ($academicGroups as $academicGroup)
                            <option value="{{ $academicGroup['id'] }}">{{ $academicGroup['name'] }}</option>
                        @endforeach
                    </select>
                </span>
            </div>

            <ul role="list" class="divide-y divide-gray-200">
                @foreach ($academicLevels as $levelIndex => $academicLevel)
                    <li x-data="{ open:  false }" wire:key="academic_level_{{ $academicLevel['id'] }}"
                        class="p-2">
                        <p x-on:click="open = !open"
                            role="button" class="flex items-center justify-between cursor-pointer">
                            <span class="font-medium text-sm">{{ $academicLevel['name'] }}</span>
                            <span class="text-gray-500">
                                <svg x-cloak x-show="open" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                    class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3 4.5h14.25M3 9h9.75M3 13.5h5.25m5.25-.75L17.25 9m0 0L21 12.75M17.25 9v12" />
                                </svg>
                                <svg x-show="!open" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                    class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3 4.5h14.25M3 9h9.75M3 13.5h9.75m4.5-4.5v12m0 0l-3.75-3.75M17.25 21L21 17.25" />
                                </svg>
                            </span>
                        </p>
                        <div x-cloak x-show="open" x-transition class="pt-2">
                            <fieldset>
                                <div class="space-y-2">
                                    @foreach ($academicLevel['academic_subjects'] as $academicSubject)
                                        <div wire:key="academic_subject_{{ $academicSubject['id'] }}"
                                            class="relative flex items-center">
                                                <input value="{{ $academicSubject['id'] }}"
                                                    wire:model="academicSubjects"
                                                    name="academic_subject_ids[]" type="checkbox"
                                                    class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-600 ml-3">
                                            <div class="ml-2 text-sm leading-6">
                                                <label
                                                    class="font-medium text-gray-900">{{ $academicSubject['name'] }}</label>
                                                <span
                                                    class="text-gray-500">{{ $academicSubject['code'] }}</span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </fieldset>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    <div class="sm:col-span-2">
        <x-form.input value="{{ $this->amount }}" name="amount" type="number" readonly />
    </div>
</div>
