<div>
    <label class="block text-gray-800 text-md mt-4 mb-4 font-medium">Current Change to <span class="font-bold">
            {{ $team->name }} </span></label>
    @foreach ($details as $detail)
        @php
            $details = $institutionDetails[$numChanges - $loop->index];
            $logo_path = is_null($details) ? null : $details['logo'] ?? '';
        @endphp
        <x-detail>
            @if ($logo_path)
                <x-detail.data label="Logo">
                    <div class="w-14 h-14">
                        <img src="{{ asset('storage/' . $logo_path) }}" class="w-fit h-fit" alt=""
                            onerror="this.style.display='none'" />
                    </div>

                </x-detail.data>
            @endif
            {{-- <x-detail.data label="Team Name">{{ $team->name }}</x-detail.data> --}}

            <x-detail.data label="Type">
                {{ $details['type'] == 'institution_only'
                    ? 'Institution Only'
                    : ($details['type'] == 'department_based'
                        ? 'Department Based'
                        : ($details['type'] == 'faculty_based'
                            ? 'Faculty Based'
                            : 'College Based')) }}
            </x-detail.data>

            <x-detail.data label="Name">
                {{ $details['name'] }}
            </x-detail.data>

            @if ($details['type'] == 'college_based')
                <x-detail.data label="College">
                    {{ $details['college'] }}
                </x-detail.data>
                <x-detail.data label="College">
                    {{ $details['school'] }}
                </x-detail.data>
            @endif

            @if ($details['type'] == 'faculty_based')
                <x-detail.data label="College">
                    {{ $details['faculty'] }}
                </x-detail.data>
            @endif

            @if ($details['type'] != 'institution_only')
                <x-detail.data label="Department">
                    {{ $details['department'] }}
                </x-detail.data>
            @endif
        </x-detail>
        <div class="mb-10"></div>
    @endforeach

    <div class="relative mt-5">
        <div class="absolute inset-0 flex items-center">
            <div class="w-full border-t border-gray-200"></div>
        </div>
        <div class="relative flex justify-center">
            <span class="isolate inline-flex -space-x-px">
                @if ($canMinus)
                    <button wire:click="minus()" type="button"
                        class="relative inline-flex items-center border border-gray-300 px-3 py-2 bg-white hover:bg-gray-50 rounded-l-lg">
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                            class="feather feather-minus">
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                    </button>
                @endif
                @if ($canAdd)
                    <button wire:click="plus()" type="button"
                        class="relative inline-flex items-center border border-gray-300 px-3 py-2 bg-white hover:bg-gray-50 rounded-r-lg">
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                            class="feather feather-plus">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                    </button>
                @endif
            </span>
        </div>
    </div>
</div>
