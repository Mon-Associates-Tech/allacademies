@php
    $options = [];
    foreach ($teams as $team) {
        $options = Arr::add($options, $team->id, $team->name);
    }
@endphp
<div class="grid sm:grid-cols-3 gap-4">
    <div class="sm:col-span-2">
        <x-form.select wire:model="team" name="team" :options="$options" />
    </div>
    <div class="sm:col-span-2">
        <x-form.select wire:model="package" name="package" :options="[
            'individual:full' => 'Individual (Full Option)',
            'institution:full' => 'Institution (Full Option)',
        ]" />
    </div>
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
                <ul role="list" class="divide-y divide-gray-300">
                    @foreach ($academicGroups as $groupIndex => $academicGroup)
                        <li wire:key="academic_group_{{ $academicGroup['id'] }}" class="p-3">
                            <p wire:click="$toggle('academicGroups.{{ $groupIndex }}.is_open')" role="button"
                                class="flex items-center justify-between cursor-pointer">
                                <span>{{ $academicGroup['name'] }}</span>
                                <span class="text-gray-500">
                                    @if ($academicGroup['is_open'])
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M3 4.5h14.25M3 9h9.75M3 13.5h5.25m5.25-.75L17.25 9m0 0L21 12.75M17.25 9v12" />
                                        </svg>
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M3 4.5h14.25M3 9h9.75M3 13.5h9.75m4.5-4.5v12m0 0l-3.75-3.75M17.25 21L21 17.25" />
                                        </svg>
                                    @endif
                                </span>
                            </p>
                            @if ($academicGroup['is_open'])
                                <div class="mt-3 pt-3 px-3 border-t border-gray-300">
                                    <ul role="list" class="divide-y divide-gray-200">
                                        @foreach ($academicGroup['academic_levels'] as $levelIndex => $academicLevel)
                                            <li wire:key="academic_level_{{ $academicLevel['id'] }}"
                                                class="py-3 first:pt-0 last:pb-0">
                                                <p wire:click="$toggle('academicGroups.{{ $groupIndex }}.academic_levels.{{ $levelIndex }}.is_open')"
                                                    role="button"
                                                    class="flex items-center justify-between cursor-pointer">
                                                    <span
                                                        class="font-medium text-sm">{{ $academicLevel['name'] }}</span>
                                                    <span class="text-gray-500">
                                                        @if ($academicLevel['is_open'])
                                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                viewBox="0 0 24 24" stroke-width="1.5"
                                                                stroke="currentColor" class="w-5 h-5">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    d="M4.5 12.75l7.5-7.5 7.5 7.5m-15 6l7.5-7.5 7.5 7.5" />
                                                            </svg>
                                                        @else
                                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                viewBox="0 0 24 24" stroke-width="1.5"
                                                                stroke="currentColor" class="w-5 h-5">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    d="M19.5 5.25l-7.5 7.5-7.5-7.5m15 6l-7.5 7.5-7.5-7.5" />
                                                            </svg>
                                                        @endif
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
                                                                                name="academic_subject_ids[]"
                                                                                type="checkbox"
                                                                                class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                                                                        </div>
                                                                        <div class="ml-3 text-sm leading-6">
                                                                            <label
                                                                                class="font-medium text-gray-900">{{ $academicSubject['name'] }}</label>
                                                                            <span
                                                                                class="text-gray-500"><span>{{ $academicSubject['code'] }}</span></span>
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
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

    </div>

    <div class="sm:col-span-2">
        <x-form.input value="{{ $this->amount }}" name="amount" type="number" readonly />
    </div>
</div>
