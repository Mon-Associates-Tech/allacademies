<div wire:poll.{{ $pollInterval }}ms="refreshData" class="space-y-6">

    {{-- STATS CARDS --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <x-ui.metric-card
            label="Total Joined"
            :value="$stats['total_joined'] ?? 0"
            icon="users"
            color="blue"
        />
        <x-ui.metric-card
            label="In Progress"
            :value="$stats['in_progress'] ?? 0"
            icon="clock"
            color="yellow"
        />
        <x-ui.metric-card
            label="Submitted"
            :value="$stats['submitted'] ?? 0"
            icon="check-circle"
            color="green"
        />
    </div>

    {{-- POLL INTERVAL SELECTOR --}}
    <div class="bg-white dark:bg-slate-900 px-5 py-4 flex items-center justify-between"
         style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
        <div class="flex items-center gap-2">
            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
            <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Refresh Interval:</span>
        </div>
        <div class="flex gap-2">
            @foreach([5000 => '5s', 15000 => '15s', 30000 => '30s'] as $ms => $label)
                <button
                    wire:click="adjustPollInterval({{ $ms }})"
                    class="px-3 py-1 text-xs font-semibold transition-all"
                    style="border-radius: 2px; {{ $pollInterval === $ms ? 'background: linear-gradient(135deg, #7c3aed, #a78bfa); color: white;' : 'background: #f1f5f9; color: #64748b; border: 1px solid #e2e8f0;' }}">
                    {{ $label }}
                </button>
            @endforeach
        </div>
    </div>

    {{-- PARTICIPANTS TABLE --}}
    <x-ui.card variant="default" :shadow="true">
        <x-ui.card-header title="Active Participants" accent="primary">
            <x-slot:actions>
                <span class="text-xs text-slate-500">
                    Last updated: {{ now()->format('H:i:s') }}
                </span>
            </x-slot:actions>
        </x-ui.card-header>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 dark:bg-slate-800">
                    <tr>
                        <x-table.th>Participant</x-table.th>
                        <x-table.th>Section</x-table.th>
                        <x-table.th>Progress</x-table.th>
                        <x-table.th>Time Elapsed</x-table.th>
                        <x-table.th>Last Activity</x-table.th>
                        <x-table.th>Status</x-table.th>
                        <x-table.th>Actions</x-table.th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($participants as $participant)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                            <x-table.td>
                                <div>
                                    <div class="font-medium text-slate-900 dark:text-white">
                                        {{ $participant['name'] }}
                                    </div>
                                    <div class="text-xs text-slate-500">
                                        {{ $participant['email'] }}
                                    </div>
                                </div>
                            </x-table.td>
                            <x-table.td>
                                <span class="inline-flex items-center px-2 py-0.5 text-xs font-semibold"
                                      style="border-radius: 2px; background: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe;">
                                    Section {{ $participant['section_index'] + 1 }}
                                </span>
                            </x-table.td>
                            <x-table.td>
                                <div class="flex items-center gap-2">
                                    <div class="flex-1 h-2 bg-slate-200 dark:bg-slate-700 rounded-full overflow-hidden">
                                        <div class="h-full transition-all duration-300"
                                             style="width: {{ $participant['progress_percent'] }}%; background: linear-gradient(90deg, #7c3aed, #a78bfa);"></div>
                                    </div>
                                    <span class="text-xs font-medium text-slate-600 dark:text-slate-400 w-10 text-right">
                                        {{ $participant['progress_percent'] }}%
                                    </span>
                                </div>
                                <div class="text-xs text-slate-500 mt-1">
                                    {{ $participant['answered_count'] }}/{{ $participant['total_questions'] }} answered
                                </div>
                            </x-table.td>
                            <x-table.td>
                                <span class="text-sm text-slate-600 dark:text-slate-400">
                                    {{ $participant['time_elapsed'] }}
                                </span>
                            </x-table.td>
                            <x-table.td>
                                <span class="text-sm text-slate-600 dark:text-slate-400">
                                    {{ $participant['last_activity'] }}
                                </span>
                            </x-table.td>
                            <x-table.td>
                                @if($participant['is_idle'])
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-semibold"
                                          style="border-radius: 2px; background: #fef3c7; color: #92400e; border: 1px solid #fde68a;">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                        </svg>
                                        Idle
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-semibold"
                                          style="border-radius: 2px; background: #d1fae5; color: #065f46; border: 1px solid #6ee7b7;">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        Active
                                    </span>
                                @endif
                            </x-table.td>
                            <x-table.td>
                                <button
                                    wire:click="kickParticipant({{ $participant['id'] }})"
                                    wire:confirm="Force-submit this participant's exam?"
                                    class="inline-flex items-center gap-1 px-2 py-1 text-xs font-semibold text-red-700 hover:text-red-900 transition-colors"
                                    style="border-radius: 2px; background: #fee2e2; border: 1px solid #fecaca;">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                    Kick
                                </button>
                            </x-table.td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                    </svg>
                                    <p class="text-sm font-medium text-slate-600 dark:text-slate-400">No active participants</p>
                                    <p class="text-xs text-slate-500 mt-1">Participants will appear here when they start the exam</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-ui.card>

</div>
