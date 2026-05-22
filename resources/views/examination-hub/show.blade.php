@extends('layouts.exam')

@section('content')
<x-examinations-hub.navigation active="manage" />

<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 mb-6">
        <a href="{{ route('examinations-hub.exams.show', $exam) }}" class="hover:text-indigo-600">{{ $exam->title }}</a>
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <a href="{{ route('examinations-hub.proctoring.index', $exam) }}" class="hover:text-indigo-600">Proctoring</a>
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span>{{ $submission->participant_name ?? 'Submission #'.$submission->id }}</span>
    </div>

    {{-- Summary cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
        @php
            $severityCard = [
                ['label' => 'Total Events', 'value' => $summary['total'],  'color' => 'gray'],
                ['label' => 'High',         'value' => $summary['high'],   'color' => 'red'],
                ['label' => 'Medium',       'value' => $summary['medium'], 'color' => 'yellow'],
                ['label' => 'Low',          'value' => $summary['low'],    'color' => 'blue'],
            ];
        @endphp
        @foreach($severityCard as $card)
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-4 text-center">
                <p class="text-2xl font-bold
                    @if($card['color'] === 'red')    text-red-600 dark:text-red-400
                    @elseif($card['color'] === 'yellow') text-yellow-600 dark:text-yellow-400
                    @elseif($card['color'] === 'blue')   text-blue-600 dark:text-blue-400
                    @else text-gray-800 dark:text-gray-200 @endif">
                    {{ $card['value'] }}
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $card['label'] }}</p>
            </div>
        @endforeach
    </div>

    @if($summary['flagged'])
        <div class="mb-6 flex items-center gap-3 rounded-lg bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-700 px-4 py-3 text-sm text-red-800 dark:text-red-300">
            <svg class="w-5 h-5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3 6a3 3 0 013-3h10a1 1 0 01.8 1.6L14.25 7l2.55 2.4A1 1 0 0116 11H6a1 1 0 00-1 1v3a1 1 0 11-2 0V6z" clip-rule="evenodd"/></svg>
            This submission has been <strong>automatically flagged</strong> for review due to violation thresholds being exceeded.
        </div>
    @endif

    {{-- Event type breakdown --}}
    @if(!empty($summary['by_type']))
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-5 mb-6">
            <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Event Breakdown</h2>
            <div class="flex flex-wrap gap-2">
                @foreach($summary['by_type'] as $type => $info)
                    <span class="inline-flex items-center gap-1.5 text-xs font-medium px-3 py-1.5 rounded-full
                        @if($info['severity'] === 'high')   bg-red-100    text-red-700    dark:bg-red-900/40    dark:text-red-400
                        @elseif($info['severity'] === 'medium') bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-400
                        @else bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-400 @endif">
                        {{ str_replace('_', ' ', ucfirst($type)) }} × {{ $info['count'] }}
                    </span>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Full log table --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
            <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Full Event Log</h2>
        </div>

        @if($logs->isEmpty())
            <p class="px-5 py-8 text-center text-sm text-gray-400 dark:text-gray-500">No events logged.</p>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700 text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-900/40">
                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Time</th>
                            <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Event</th>
                            <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Severity</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                        @foreach($logs as $log)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                                <td class="px-5 py-3 text-gray-600 dark:text-gray-400 whitespace-nowrap">
                                    {{ $log->occurred_at->format('H:i:s') }}
                                    <span class="block text-xs text-gray-400">{{ $log->occurred_at->format('Y-m-d') }}</span>
                                </td>
                                <td class="px-5 py-3 font-medium text-gray-800 dark:text-gray-200">
                                    {{ str_replace('_', ' ', ucfirst($log->event_type)) }}
                                </td>
                                <td class="px-5 py-3">
                                    <span class="inline-flex items-center text-xs font-semibold px-2 py-0.5 rounded-full
                                        @if($log->severity === 'high')   bg-red-100    text-red-700    dark:bg-red-900/40    dark:text-red-400
                                        @elseif($log->severity === 'medium') bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-400
                                        @else bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-400 @endif">
                                        {{ ucfirst($log->severity) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
