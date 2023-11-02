<x-auth title="Institutional Information Changes">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Teams (Auditing)' => route('audit-teams.index'),
        ]" />
    </x-slot>

    @php
        $name = function ($type) {
            if ('institution' === $type) {
                return 'Institution Only';
            } elseif ('college' === $type) {
                return 'College Based';
            } elseif ('faculty' === $type) {
                return 'Faculty Based';
            } elseif ('department' === $type) {
                return 'Department Based';
            }
        };
    @endphp

    <div  class="grid grid-cols-1 gap-4 sm:grid-cols-2 mb-6">
    @foreach ($audits as [$heading, $current, $incoming, $changes])
        @if ($incoming)
        <div class="flex flex-col divide-y divide-gray-200 overflow-hidden rounded-lg bg-white shadow">
            <div class="px-4 py-5 sm:px-6 flex-initial">
                <h3 class="text-base font-semibold leading-6 text-gray-900">{{ $heading }}</h3>
            </div>
            <div class="px-4 py-5 sm:p-6 flex-1">
                <p>
                    @if ($current && $current['institution'] !== $incoming['institution'])
                    <s class="bg-red-50">{{ $current['institution'] }}</s>
                    @endif
                    <span @class(['bg-green-50' => !$current || $current['institution'] !== $incoming['institution']])>{{ $incoming['institution'] }}</span>
                </p>
                @if ($current && 'college' === $current['type'] && 'college' !== $incoming['type'])
                    <p><s class="bg-red-50">{{ $current['college'] }}</s></p>
                @endif
                @if ('college' === $incoming['type'])
                <p>
                    @if ($current && 'college' === $current['type'] && $current['college'] !== $incoming['college'])
                    <s class="bg-red-50">{{ $current['college'] }}</s>
                    @endif
                    <span @class(['bg-green-50' => !$current || 'college' !== $current['type'] || $current['college'] !== $incoming['college']])>{{ $incoming['college'] }}</span>
                </p>
                @endif
                @if ($current && 'faculty' === $current['type'] && 'faculty' !== $incoming['type'])
                    <p><s class="bg-red-50">{{ $current['faculty'] }}</s></p>
                @endif
                @if ('faculty' === $incoming['type'])
                <p>
                    @if ($current && 'faculty' === $current['type'] && $current['faculty'] !== $incoming['faculty'])
                    <s class="bg-red-50">{{ $current['faculty'] }}</s>
                    @endif
                    <span @class(['bg-green-50' => !$current || 'faculty' !== $current['type'] || $current['faculty'] !== $incoming['faculty']])>{{ $incoming['faculty'] }}</span>
                </p>
                @endif
                @if ($current && 'college' === $current['type'] && 'college' !== $incoming['type'])
                    <p><s class="bg-red-50">{{ $current['school'] }}</s></p>
                @endif
                @if ('college' === $incoming['type'])
                <p>
                    @if ($current && 'college' === $current['type'] && $current['school'] !== $incoming['school'])
                    <s class="bg-red-50">{{ $current['school'] }}</s>
                    @endif
                    <span @class(['bg-green-50' => !$current || 'college' !== $current['type'] || $current['college'] !== $incoming['college']])>{{ $incoming['school'] }}</span>
                </p>
                @endif
                @if ($current && 'institution' !== $current['type'] && 'institution' === $incoming['type'])
                    <p><s class="bg-red-50">{{ $current['department'] }}</s></p>
                @endif
                @unless ('institution' === $incoming['type'])
                <p>
                    @if ($current && 'institution' !== $current['type'] && $current['department'] !== $incoming['department'])
                    <s class="bg-red-50">{{ $current['department'] }}</s>
                    @endif
                    <span @class(['bg-green-50' => !$current || 'institution' === $current['type'] || $current['department'] !== $incoming['department']])>{{ $incoming['department'] }}</span>
                </p>
                @endunless
            </div>
            <div class="px-4 py-4 sm:px-6 flex justify-between flex-initial">
                <div class="flex items-center divide-x divide-gray-200 text-xs font-medium">
                    <div class="pr-2 text-gray-500">Words Changed</div>
                    <div class="pl-2 flex items-center space-x-0.5">
                        @if ($changes->plus)
                        <div class="text-green-600 pr-2">+{{ $changes->plus }}</div>
                        @endif
                        @if ($changes->minus)
                        <div class="text-red-600 pr-2">-{{ $changes->minus }}</div>
                        @endif

                        @foreach ($changes->percentage() as $percentage)
                            @for ($i = 0; $i < $percentage; $i++)
                                <div @class(['w-2 h-2 rounded-sm', 'bg-green-600' => 0 === $loop->index, 'bg-red-600' => 1 === $loop->index, 'bg-zinc-600' => 2 === $loop->index]) class="w-2 h-2 bg-red-600 rounded-sm"></div>
                            @endfor
                        @endforeach
                    </div>
                </div>
                <div>

                    <span class="inline-flex items-center gap-x-1.5 rounded-md px-2 py-1 text-xs font-medium text-gray-900 ring-1 ring-inset ring-gray-200">
                        @if ($current && $incoming['type'] !== $current['type'])
                        {{ $name($current['type']) }}
                        <svg class="h-4 w-4 fill-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M2 10a.75.75 0 01.75-.75h12.59l-2.1-1.95a.75.75 0 111.02-1.1l3.5 3.25a.75.75 0 010 1.1l-3.5 3.25a.75.75 0 11-1.02-1.1l2.1-1.95H2.75A.75.75 0 012 10z" clip-rule="evenodd" />
                        </svg>
                        {{ $name($incoming['type']) }}
                        @else
                        <svg class="h-1.5 w-1.5 fill-green-500" viewBox="0 0 6 6" aria-hidden="true">
                            <circle cx="3" cy="3" r="3" />
                        </svg>
                        {{ $name($incoming['type']) }}
                        @endif
                    </span>
                </div>
            </div>
        </div>
        @endif
    @endforeach
    </div>


    <div class="flex justify-end space-x-2">
        <x-link.secondary :to="route('audit-teams.reason', ['audit_team' => $auditTeam])">Decline</x-link.secondary>
        <form class="inline" method="POST" action="{{ route('audit-teams.approve', ['audit_team' => $auditTeam]) }}">
            @csrf
            <x-button.primary>Approve</x-button.primary>
        </form>
    </div>
</x-auth>
