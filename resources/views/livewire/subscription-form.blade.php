<div>
    <div class="grid sm:grid-cols-3 gap-x-3 mb-4">
        <div class="sm:col-span-1">
            <x-form.select wire:model="team" name="team" :options="$teamsOptions" />
        </div>
        <div class="sm:col-span-1">
            <x-form.select wire:model="package" name="package" :options="[
                'individual:full' => 'Individual (Full Option)',
                'institution:full' => 'Institution (Full Option)',
            ]" readonly />
        </div>
    </div>
    <div class="grid sm:grid-cols-3 gap-4">
        <div class="sm:col-span-2">
            <x-form.select wire:model="duration" name="duration" :options="[
                '3' => '3 Months',
                '6' => '6 Months',
                '12' => '12 Months',
            ]" />
        </div>
        @if ('institution:full' == $package)
            <div class="sm:col-span-2">
                <x-form.input wire:model="beneficiaries" name="beneficiaries" type="number" />
            </div>
        @endif

        <div class="sm:col-span-2">
            <div class="space-y-1">
                <label class="block text-gray-800 font-medium text-sm mt-2">Subjects</label>
                <div class="overflow-hidden rounded-lg border border-gray-300 bg-white">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-x-3 p-2 relative">
                        <div class="sm:col-span-2">
                            <span class="inline-flex rounded-md">
                                <span
                                    class="inline-flex items-center rounded-l-md ring-1 ring-inset ring-gray-300 bg-gray-50 px-2 py-1 text-sm font-medium text-gray-600">
                                    Academic Group
                                </span>
                                <select wire:model="selectedAcademicGroupId" id="academic_group"
                                    class="-ml-px border-0 rounded-r-md ring-1 ring-inset ring-gray-300 bg-gray-50 pl-2 pr-7 py-1 focus:ring-2 text-sm font-medium text-gray-600">
                                    @foreach ($academicGroups as $groupIndex => $academicGroup)
                                        <option value="{{ $academicGroup['id'] }}">{{ $academicGroup['name'] }}</option>
                                    @endforeach
                                </select>
                            </span>
                        </div>
                        <div class="sm:col-span-1">
                        </div>
                        <div class="sm:col-span-1 absolute top-2 right-2">
                            <span
                                class="inline-flex items-center rounded-md ring-1 ring-inset ring-gray-300 bg-gray-50 px-2 py-1 text-sm font-medium text-gray-600">
                                {{ $countSelectedSubjects }}
                                {{ $countSelectedSubjects > 0 ? Str::plural('subject', $countSelectedSubjects) : 'subject' }}
                                selected
                            </span>
                        </div>
                    </div>
                    <hr class="h-px my-1 bg-gray-200 border-0 dark:bg-gray-300">
                    @if ($academicLevels)
                        <div class="sm:col-span-2 m-2">
                            <ul role="list" class="divide-y divide-gray-200 m-2">
                                @foreach ($academicLevels as $levelIndex => $academicLevel)
                                    <li wire:key="academic_level_{{ $academicLevel['id'] }}"
                                        class="py-3 first:pt-0 last:pb-0">
                                        <p wire:click="$toggle('academicGroups.{{ $selectedAcademicGroupId - 1 }}.academic_levels.{{ $levelIndex }}.is_open')"
                                            role="button" class="flex items-center justify-between cursor-pointer">
                                            <span class="font-medium text-sm">{{ $academicLevel['name'] }}</span>
                                            <span class="text-gray-500">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                    class="w-5 h-5">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="@if ($academicLevel['is_open']) M4.5 12.75l7.5-7.5 7.5 7.5m-15 6l7.5-7.5 7.5 7.5 @else M19.5 5.25l-7.5 7.5-7.5-7.5m15 6l-7.5 7.5-7.5-7.5 @endif" />
                                                </svg>
                                            </span>
                                        </p>
                                        @if ($academicLevel['is_open'])
                                            <div class="pt-4">
                                                <fieldset>
                                                    <div class="space-y-5">
                                                        @foreach ($academicLevel['academic_subjects'] as $subjectIndex => $academicSubject)
                                                            <div wire:key="academic_subject_{{ $academicSubject['id'] }}"
                                                                class="relative flex items-start">
                                                                <div class="flex h-6 items-center">
                                                                    <input value="{{ $academicSubject['id'] }}"
                                                                        wire:model="academicSubjects"
                                                                        name="academic_subject_ids[]" type="checkbox"
                                                                        class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600 ml-4">
                                                                </div>
                                                                <div class="ml-3 text-sm leading-6">
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
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="sm:col-span-2">
            <x-form.input value="{{ $this->amount }}" name="amount" type="number" readonly />
        </div>
    </div>
</div>
