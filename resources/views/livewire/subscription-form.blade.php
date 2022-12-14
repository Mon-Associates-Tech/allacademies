<div>
    <x-form.select wire:model="package" full name="package" :options="[
            ['value' => 'individual:full', 'label' => 'Individual (Full Option)'],
            ['value' => 'institution:full', 'label' => 'Institution (Full Option)'],
        ]" />
        <x-form.select wire:model="duration" full name="duration" :options="[
            ['value' => '3', 'label' => '3 Months'],
            ['value' => '6', 'label' => '6 Months'],
            ['value' => '12', 'label' => '12 Months'],
        ]" />
        @if('institution:full' == $package)
        <x-form.input wire:model="beneficiaries" full name="beneficiaries" type="number" />
        @endif

        <label class="block text-gray-800 font-medium text-sm mt-2">Subjects</label>

        <div class="text-sm my-2">
            @foreach ($groups as $group)
            <details class="border-t border-x last:border-b border-gray-300 open:bg-gray-50">
                <summary class="select-none p-2">{{ $group[0][0]['academic_level']['academic_group']['name'] }}</summary>
                <div class="mt-2">
                    @foreach ($group as $level)
                        <details class="border-t">
                            <summary class="select-none p-2">{{ $level[0]['academic_level']['name'] }}</summary>
                            <div class="mt-2 p-2 space-x-2">
                                <fieldset>
                                    @foreach ($level as $subject)
                                    <div class="flex items-start">
                                        <div class="flex h-5 items-center">
                                            <input wire:key="subject-{{ $loop->parent->parent->index }}-{{ $loop->parent->index }}-{{ $loop->index }}" wire:model="subjects" name="subjects[]" value="{{ $subject['id'] }}" type="checkbox" class="h-4 w-4">
                                        </div>
                                        <div class="ml-3 text-sm">
                                            <label class="font-medium text-gray-700">{{ $subject['name'] }}</label>
                                            <span class="text-gray-500">({{ $subject['code'] }})</span>
                                        </div>
                                    </div>
                                    @endforeach
                                </fieldset>
                            </div>
                        </details>
                    @endforeach
                </div>
            </details>
            {{-- <label class="block text-gray-800 font-medium text-sm mt-2">{{ $group[0]['academic_level']['name'] }} <span class="text-gray-500">({{ $group[0]['academic_level']['label'] }})</span></label>
            <fieldset>
                @foreach ($group as $subject)
                <div class="flex items-start">
                    <div class="flex h-5 items-center">
                        <input wire:key="subject-{{ $loop->parent->index }}-{{ $loop->index }}" wire:model="subjects" name="subjects[]" value="{{ $subject['id'] }}" type="checkbox" class="h-4 w-4">
                    </div>
                    <div class="ml-3 text-sm">
                        <label class="font-medium text-gray-700">{{ $subject['name'] }}</label>
                        <span class="text-gray-500">({{ $subject['code'] }})</span>
                    </div>
                </div>
                @endforeach
            </fieldset> --}}
            @endforeach
        </div>

        <x-form.input value="{{ $this->amount }}" full name="amount" type="number" readonly />
</div>
