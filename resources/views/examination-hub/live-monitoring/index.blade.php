<x-layouts.app>
    <x-examination-hub.navigation active="manage" />

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6"
         x-data="liveMonitoring({{ json_encode($initialData) }})"
         x-init="init()">

        {{-- ── PAGE HEADER ── --}}
        <div class="overflow-hidden"
             style="border-radius: 2px; background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); box-shadow: 0 4px 24px rgba(0,0,0,0.15);">
            <div class="h-1 w-full" style="background: linear-gradient(90deg, #10b981, #34d399, #6ee7b7);"></div>
            <div class="px-7 py-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2 text-xs text-slate-500 mb-2">
                        <a href="{{ route('examination-hub.exams.show', $exam) }}"
                           class="hover:text-slate-300 transition-colors">{{ $exam->title }}</a>
                        <x-heroicon-o-chevron-right class="w-3 h-3" />
                        <span class="text-slate-400">Live Monitoring</span>
                    </div>
                    <h1 class="text-2xl font-bold text-white leading-snug flex items-center gap-3"
                        style="letter-spacing: -0.02em; font-family: 'Georgia', serif;">
                        <span class="relative flex h-3 w-3">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                        </span>
                        Live Monitoring Dashboard
                    </h1>
                    <p class="text-slate-400 mt-2 text-sm">
                        Real-time participant tracking and proctoring control
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-xs text-slate-500" x-text="'Last updated: ' + lastUpdated"></span>
                    <button @click="refreshData()"
                            class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white transition-all"
                            style="border-radius: 2px; background: linear-gradient(135deg, #334155, #475569);">
                        <x-heroicon-o-arrow-path class="w-4 h-4" [class]="{ 'animate-spin': loading }" />
                        Refresh
                    </button>
                </div>
            </div>
        </div>

        {{-- ── STATS GRID ── --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-3">
            <template x-for="stat in statsCards" :key="stat.label">
                <div class="bg-white dark:bg-slate-900 overflow-hidden"
                     style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                    <div class="h-0.5 w-full" :style="'background:' + stat.gradient"></div>
                    <div class="px-4 py-3 flex items-center gap-3">
                        <div class="w-8 h-8 flex-shrink-0 flex items-center justify-center"
                             :style="'border-radius: 2px; background:' + stat.gradient">
                            <template x-if="stat.icon === 'users'">
                                <x-heroicon-o-users class="w-4 h-4 text-white" />
                            </template>
                            <template x-if="stat.icon === 'check-circle'">
                                <x-heroicon-o-check-circle class="w-4 h-4 text-white" />
                            </template>
                            <template x-if="stat.icon === 'clock'">
                                <x-heroicon-o-clock class="w-4 h-4 text-white" />
                            </template>
                            <template x-if="stat.icon === 'signal'">
                                <x-heroicon-o-signal class="w-4 h-4 text-white" />
                            </template>
                            <template x-if="stat.icon === 'signal-slash'">
                                <x-heroicon-o-signal-slash class="w-4 h-4 text-white" />
                            </template>
                            <template x-if="stat.icon === 'flag'">
                                <x-heroicon-o-flag class="w-4 h-4 text-white" />
                            </template>
                            <template x-if="stat.icon === 'exclamation-triangle'">
                                <x-heroicon-o-exclamation-triangle class="w-4 h-4 text-white" />
                            </template>
                        </div>
                        <div>
                            <p class="text-lg font-bold text-slate-900 dark:text-white leading-none" x-text="stat.value"></p>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5" x-text="stat.label"></p>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        {{-- ── FILTER TABS ── --}}
        <div class="flex items-center gap-2 flex-wrap">
            <template x-for="filter in filters" :key="filter.value">
                <button @click="activeFilter = filter.value"
                        :class="activeFilter === filter.value
                            ? 'bg-indigo-600 text-white'
                            : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700'"
                        class="px-4 py-2 text-sm font-medium transition-all"
                        style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06);">
                    <span x-text="filter.label"></span>
                    <span class="ml-1 text-xs opacity-70" x-text="'(' + filter.count + ')'"></span>
                </button>
            </template>
        </div>

        {{-- ── PARTICIPANTS TABLE ── --}}
        <div class="bg-white dark:bg-slate-900 overflow-hidden"
             style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-1 h-5" style="background: linear-gradient(180deg, #10b981, #34d399); border-radius: 1px;"></div>
                    <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider"
                        style="letter-spacing: 0.1em;">Participants</h2>
                    <span class="text-xs text-slate-400" x-text="'(' + filteredParticipants.length + ')'"></span>
                </div>
                <div class="flex items-center gap-2">
                    <button @click="openMessageAllModal()"
                            class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white transition-all"
                            style="border-radius: 2px; background: linear-gradient(135deg, #3b82f6, #60a5fa);">
                        <x-heroicon-o-chat-bubble-left-right class="w-4 h-4" />
                        Message All Active
                    </button>
                    <input type="text"
                           x-model="searchQuery"
                           placeholder="Search participants..."
                           class="px-3 py-1.5 text-sm border border-slate-200 dark:border-slate-700 dark:bg-slate-800 focus:ring-2 focus:ring-indigo-500"
                           style="border-radius: 2px;">
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
                            <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Status</th>
                            <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Participant</th>
                            <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Progress</th>
                            <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Violations</th>
                            <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Time Spent</th>
                            <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Limit</th>
                            <th class="px-5 py-3 text-right text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 dark:divide-slate-800">
                        <template x-for="participant in filteredParticipants" :key="participant.id">
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition-colors"
                                :class="{ 'bg-red-50/30 dark:bg-red-900/10': participant.is_flagged }">
                                {{-- Status --}}
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-2">
                                        <span class="relative flex h-2.5 w-2.5">
                                            <template x-if="participant.status === 'active'">
                                                <span>
                                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-green-500"></span>
                                                </span>
                                            </template>
                                            <template x-if="participant.status === 'idle'">
                                                <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-yellow-500"></span>
                                            </template>
                                            <template x-if="participant.status === 'away'">
                                                <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-orange-500"></span>
                                            </template>
                                            <template x-if="participant.status === 'disconnected'">
                                                <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-slate-400"></span>
                                            </template>
                                            <template x-if="participant.status === 'completed'">
                                                <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-blue-500"></span>
                                            </template>
                                            <template x-if="participant.status === 'terminated'">
                                                <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-red-500"></span>
                                            </template>
                                        </span>
                                        <span class="text-xs font-medium capitalize"
                                              :class="{
                                                  'text-green-600': participant.status === 'active',
                                                  'text-yellow-600': participant.status === 'idle',
                                                  'text-orange-600': participant.status === 'away',
                                                  'text-slate-500': participant.status === 'disconnected',
                                                  'text-blue-600': participant.status === 'completed',
                                                  'text-red-600': participant.status === 'terminated'
                                              }"
                                              x-text="participant.status"></span>
                                        <template x-if="!participant.is_focused && participant.status === 'active'">
                                            <span class="text-xs text-orange-500" title="Window not focused">
                                                <x-heroicon-o-eye-slash class="w-3.5 h-3.5" />
                                            </span>
                                        </template>
                                    </div>
                                </td>

                                {{-- Participant Info --}}
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-2">
                                        <template x-if="participant.is_flagged">
                                            <x-heroicon-s-flag class="w-4 h-4 text-red-500 flex-shrink-0" />
                                        </template>
                                        <div>
                                            <p class="font-medium text-slate-900 dark:text-white" x-text="participant.participant_name"></p>
                                            <p class="text-xs text-slate-500 dark:text-slate-400" x-text="participant.participant_email || '—'"></p>
                                        </div>
                                    </div>
                                </td>

                                {{-- Progress --}}
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-24 bg-slate-200 dark:bg-slate-700 h-2 overflow-hidden" style="border-radius: 1px;">
                                            <div class="h-full transition-all duration-300"
                                                 :style="'width:' + participant.progress_percentage + '%; background: linear-gradient(90deg, #10b981, #34d399);'"></div>
                                        </div>
                                        <span class="text-xs text-slate-600 dark:text-slate-400 whitespace-nowrap"
                                              x-text="participant.questions_answered + '/' + participant.total_questions"></span>
                                    </div>
                                </td>

                                {{-- Violations --}}
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-1.5">
                                        <template x-if="participant.high_severity_count > 0">
                                            <span class="inline-flex items-center justify-center text-xs font-bold px-2 py-0.5"
                                                  style="border-radius: 2px; background: linear-gradient(135deg,#fef2f2,#fee2e2); color: #b91c1c; border: 1px solid rgba(185,28,28,0.15);"
                                                  x-text="participant.high_severity_count"></span>
                                        </template>
                                        <template x-if="participant.medium_severity_count > 0">
                                            <span class="inline-flex items-center justify-center text-xs font-bold px-2 py-0.5"
                                                  style="border-radius: 2px; background: linear-gradient(135deg,#fffbeb,#fef3c7); color: #b45309; border: 1px solid rgba(180,83,9,0.15);"
                                                  x-text="participant.medium_severity_count"></span>
                                        </template>
                                        <template x-if="participant.violation_count === 0">
                                            <span class="text-slate-400">—</span>
                                        </template>
                                    </div>
                                </td>

                                {{-- Time Spent --}}
                                <td class="px-5 py-4">
                                    <template x-if="participant.status === 'completed' || participant.status === 'terminated'">
                                        <div>
                                            <span class="text-xs font-mono font-semibold text-slate-700 dark:text-slate-200"
                                                  x-text="formatSeconds(participant.time_taken_seconds ?? participant.elapsed_seconds)"></span>
                                            <span class="ml-1 text-xs px-1 py-0.5 rounded font-medium"
                                                  :class="participant.status === 'completed' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400'"
                                                  x-text="participant.status === 'completed' ? 'submitted' : 'terminated'"></span>
                                        </div>
                                    </template>
                                    <template x-if="participant.status !== 'completed' && participant.status !== 'terminated'">
                                        <span class="text-xs font-mono text-slate-600 dark:text-slate-400"
                                              x-text="formatSeconds(participant.elapsed_seconds)"></span>
                                    </template>
                                </td>

                                {{-- Limit --}}
                                <td class="px-5 py-4">
                                    <template x-if="participant.has_duration">
                                        <div class="space-y-0.5">
                                            <div class="flex items-center gap-1">
                                                <span class="text-xs text-slate-400 dark:text-slate-500">Allowed:</span>
                                                <span class="text-xs font-mono font-semibold text-slate-600 dark:text-slate-300"
                                                      x-text="formatSeconds(participant.total_allowed_seconds)"></span>
                                            </div>
                                            <template x-if="participant.status !== 'completed' && participant.status !== 'terminated'">
                                                <div class="flex items-center gap-1">
                                                    <span class="text-xs text-slate-400 dark:text-slate-500">Left:</span>
                                                    <span class="text-xs font-mono font-semibold"
                                                          :class="{
                                                              'text-red-500 animate-pulse': participant.remaining_seconds !== null && participant.remaining_seconds <= 300,
                                                              'text-amber-600 dark:text-amber-400': participant.remaining_seconds !== null && participant.remaining_seconds > 300 && participant.remaining_seconds <= 600,
                                                              'text-slate-600 dark:text-slate-300': participant.remaining_seconds === null || participant.remaining_seconds > 600
                                                          }"
                                                          x-text="participant.remaining_seconds !== null ? formatSeconds(participant.remaining_seconds) : '—'"></span>
                                                </div>
                                            </template>
                                        </div>
                                    </template>
                                    <template x-if="!participant.has_duration">
                                        <span class="text-xs text-slate-400 dark:text-slate-500">No limit</span>
                                    </template>
                                </td>

                                {{-- Actions --}}
                                <td class="px-5 py-4 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        {{-- View Details --}}
                                        <a :href="'/examinations/exams/' + {{ $exam->id }} + '/live-monitoring/participant/' + participant.submission_id"
                                           class="p-1.5 text-slate-500 hover:text-indigo-600 transition-colors" title="View Details">
                                            <x-heroicon-o-eye class="w-4 h-4" />
                                        </a>

                                        {{-- Send Message --}}
                                        <button @click="openMessageModal(participant)"
                                                class="p-1.5 text-slate-500 hover:text-blue-600 transition-colors" title="Send Message">
                                            <x-heroicon-o-chat-bubble-left class="w-4 h-4" />
                                        </button>

                                        {{-- Send Warning --}}
                                        <button @click="openWarningModal(participant)"
                                                class="p-1.5 text-slate-500 hover:text-yellow-600 transition-colors" title="Send Warning">
                                            <x-heroicon-o-exclamation-triangle class="w-4 h-4" />
                                        </button>

                                        {{-- Force Submit --}}
                                        <button @click="openForceSubmitModal(participant)"
                                                :disabled="participant.status === 'completed' || participant.status === 'terminated'"
                                                class="p-1.5 text-slate-500 hover:text-orange-600 transition-colors disabled:opacity-30 disabled:cursor-not-allowed"
                                                title="Force Submit">
                                            <x-heroicon-o-paper-airplane class="w-4 h-4" />
                                        </button>

                                        {{-- Terminate --}}
                                        <button @click="openTerminateModal(participant)"
                                                :disabled="participant.status === 'completed' || participant.status === 'terminated'"
                                                class="p-1.5 text-slate-500 hover:text-red-600 transition-colors disabled:opacity-30 disabled:cursor-not-allowed"
                                                title="Terminate Session">
                                            <x-heroicon-o-x-circle class="w-4 h-4" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>

                        <template x-if="filteredParticipants.length === 0">
                            <tr>
                                <td colspan="7" class="px-5 py-12 text-center">
                                    <div class="w-12 h-12 mx-auto flex items-center justify-center mb-4"
                                         style="border-radius: 2px; background: linear-gradient(135deg, #64748b, #94a3b8);">
                                        <x-heroicon-o-users class="w-6 h-6 text-white" />
                                    </div>
                                    <p class="text-sm text-slate-500 dark:text-slate-400">No participants found.</p>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ── MESSAGE MODAL ── --}}
        <template x-teleport="body">
            <div x-show="showMessageModal"
                 x-transition:enter="ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
                 @click.self="showMessageModal = false">
                <div class="bg-white dark:bg-slate-900 w-full max-w-md overflow-hidden"
                     style="border-radius: 2px; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);">
                    <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800">
                        <h3 class="font-bold text-slate-900 dark:text-white">Send Message</h3>
                        <p class="text-sm text-slate-500 mt-1" x-text="'To: ' + (selectedParticipant?.participant_name || '')"></p>
                    </div>
                    <div class="px-6 py-4">
                        <textarea x-model="messageText"
                                  rows="3"
                                  placeholder="Type your message..."
                                  class="w-full px-3 py-2 text-sm border border-slate-200 dark:border-slate-700 dark:bg-slate-800 focus:ring-2 focus:ring-indigo-500"
                                  style="border-radius: 2px;"></textarea>
                    </div>
                    <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/50 flex justify-end gap-3">
                        <button @click="showMessageModal = false"
                                class="px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors"
                                style="border-radius: 2px;">
                            Cancel
                        </button>
                        <button @click="sendMessage()"
                                :disabled="!messageText.trim()"
                                class="px-4 py-2 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 transition-colors disabled:opacity-50"
                                style="border-radius: 2px;">
                            Send Message
                        </button>
                    </div>
                </div>
            </div>
        </template>

        {{-- ── MESSAGE ALL MODAL ── --}}
        <template x-teleport="body">
            <div x-show="showMessageAllModal"
                 x-transition:enter="ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
                 @click.self="showMessageAllModal = false">
                <div class="bg-white dark:bg-slate-900 w-full max-w-md overflow-hidden"
                     style="border-radius: 2px; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);">
                    <div class="h-1 w-full" style="background: linear-gradient(90deg, #3b82f6, #60a5fa);"></div>
                    <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800">
                        <h3 class="font-bold text-slate-900 dark:text-white flex items-center gap-2">
                            <x-heroicon-o-chat-bubble-left-right class="w-5 h-5 text-blue-500" />
                            Message All Active Participants
                        </h3>
                        <p class="text-sm text-slate-500 mt-1">This message will be sent to all currently active participants</p>
                    </div>
                    <div class="px-6 py-4">
                        <textarea x-model="messageAllText"
                                  rows="3"
                                  placeholder="Type your message to all participants..."
                                  class="w-full px-3 py-2 text-sm border border-slate-200 dark:border-slate-700 dark:bg-slate-800 focus:ring-2 focus:ring-blue-500"
                                  style="border-radius: 2px;"></textarea>
                        <p class="text-xs text-slate-500 mt-2" x-text="'Active participants: ' + stats.active"></p>
                    </div>
                    <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/50 flex justify-end gap-3">
                        <button @click="showMessageAllModal = false"
                                class="px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors"
                                style="border-radius: 2px;">
                            Cancel
                        </button>
                        <button @click="sendMessageToAll()"
                                :disabled="!messageAllText.trim() || stats.active === 0"
                                class="px-4 py-2 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 transition-colors disabled:opacity-50"
                                style="border-radius: 2px;">
                            Send to All
                        </button>
                    </div>
                </div>
            </div>
        </template>

        {{-- ── TERMINATE MODAL ── --}}
        <template x-teleport="body">
            <div x-show="showTerminateModal"
                 x-transition:enter="ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
                 @click.self="showTerminateModal = false">
                <div class="bg-white dark:bg-slate-900 w-full max-w-md overflow-hidden"
                     style="border-radius: 2px; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);">
                    <div class="h-1 w-full bg-gradient-to-r from-red-600 to-red-400"></div>
                    <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800">
                        <h3 class="font-bold text-slate-900 dark:text-white flex items-center gap-2">
                            <x-heroicon-o-x-circle class="w-5 h-5 text-red-500" />
                            Terminate Session
                        </h3>
                        <p class="text-sm text-slate-500 mt-1" x-text="'Participant: ' + (selectedParticipant?.participant_name || '')"></p>
                    </div>
                    <div class="px-6 py-4">
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Reason for termination <span class="text-red-500">*</span></label>
                        <textarea x-model="terminateReason"
                                  rows="3"
                                  placeholder="Enter a reason (required)..."
                                  class="w-full px-3 py-2 text-sm border border-slate-200 dark:border-slate-700 dark:bg-slate-800 focus:ring-2 focus:ring-red-500"
                                  style="border-radius: 2px;"></textarea>
                        <p class="text-xs text-slate-500 mt-2">This reason will be shown to the participant and logged in the audit trail.</p>
                    </div>
                    <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/50 flex justify-end gap-3">
                        <button @click="showTerminateModal = false"
                                class="px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors"
                                style="border-radius: 2px;">
                            Cancel
                        </button>
                        <button @click="executeTerminate()"
                                :disabled="!terminateReason.trim() || actionLoading"
                                class="px-4 py-2 text-sm font-semibold text-white bg-red-600 hover:bg-red-700 transition-colors disabled:opacity-50"
                                style="border-radius: 2px;">
                            <span x-show="!actionLoading">Terminate Session</span>
                            <span x-show="actionLoading">Terminating...</span>
                        </button>
                    </div>
                </div>
            </div>
        </template>

        {{-- ── FORCE SUBMIT MODAL ── --}}
        <template x-teleport="body">
            <div x-show="showForceSubmitModal"
                 x-transition:enter="ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
                 @click.self="showForceSubmitModal = false">
                <div class="bg-white dark:bg-slate-900 w-full max-w-md overflow-hidden"
                     style="border-radius: 2px; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);">
                    <div class="h-1 w-full bg-gradient-to-r from-orange-500 to-orange-400"></div>
                    <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800">
                        <h3 class="font-bold text-slate-900 dark:text-white flex items-center gap-2">
                            <x-heroicon-o-paper-airplane class="w-5 h-5 text-orange-500" />
                            Force Submit Exam
                        </h3>
                        <p class="text-sm text-slate-500 mt-1" x-text="'Participant: ' + (selectedParticipant?.participant_name || '')"></p>
                    </div>
                    <div class="px-6 py-4">
                        <div class="p-3 mb-4 bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800 rounded text-sm text-orange-800 dark:text-orange-300">
                            This will immediately submit the participant's exam with all current answers. This action cannot be undone.
                        </div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Reason (optional)</label>
                        <textarea x-model="forceSubmitReason"
                                  rows="2"
                                  placeholder="Optional reason..."
                                  class="w-full px-3 py-2 text-sm border border-slate-200 dark:border-slate-700 dark:bg-slate-800 focus:ring-2 focus:ring-orange-500"
                                  style="border-radius: 2px;"></textarea>
                    </div>
                    <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/50 flex justify-end gap-3">
                        <button @click="showForceSubmitModal = false"
                                class="px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors"
                                style="border-radius: 2px;">
                            Cancel
                        </button>
                        <button @click="executeForceSubmit()"
                                :disabled="actionLoading"
                                class="px-4 py-2 text-sm font-semibold text-white bg-orange-500 hover:bg-orange-600 transition-colors disabled:opacity-50"
                                style="border-radius: 2px;">
                            <span x-show="!actionLoading">Force Submit</span>
                            <span x-show="actionLoading">Submitting...</span>
                        </button>
                    </div>
                </div>
            </div>
        </template>

        {{-- ── WARNING MODAL ── --}}
        <template x-teleport="body">
            <div x-show="showWarningModal"
                 x-transition:enter="ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
                 @click.self="showWarningModal = false">
                <div class="bg-white dark:bg-slate-900 w-full max-w-md overflow-hidden"
                     style="border-radius: 2px; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);">
                    <div class="h-1 w-full" style="background: linear-gradient(90deg, #f59e0b, #fbbf24);"></div>
                    <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800">
                        <h3 class="font-bold text-slate-900 dark:text-white flex items-center gap-2">
                            <x-heroicon-o-exclamation-triangle class="w-5 h-5 text-yellow-500" />
                            Send Warning
                        </h3>
                        <p class="text-sm text-slate-500 mt-1" x-text="'To: ' + (selectedParticipant?.participant_name || '')"></p>
                    </div>
                    <div class="px-6 py-4">
                        <textarea x-model="warningText"
                                  rows="3"
                                  placeholder="Warning message..."
                                  class="w-full px-3 py-2 text-sm border border-slate-200 dark:border-slate-700 dark:bg-slate-800 focus:ring-2 focus:ring-yellow-500"
                                  style="border-radius: 2px;"></textarea>
                        <p class="text-xs text-slate-500 mt-2">This warning will be displayed prominently to the participant.</p>
                    </div>
                    <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/50 flex justify-end gap-3">
                        <button @click="showWarningModal = false"
                                class="px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors"
                                style="border-radius: 2px;">
                            Cancel
                        </button>
                        <button @click="sendWarning()"
                                :disabled="!warningText.trim()"
                                class="px-4 py-2 text-sm font-semibold text-white bg-yellow-500 hover:bg-yellow-600 transition-colors disabled:opacity-50"
                                style="border-radius: 2px;">
                            Send Warning
                        </button>
                    </div>
                </div>
            </div>
        </template>

    </div>

    @push('scripts')
    <script>
        function liveMonitoring(initialData) {
            return {
                exam: initialData.exam,
                stats: initialData.stats,
                participants: initialData.participants,
                loading: false,
                actionLoading: false,
                lastUpdated: new Date().toLocaleTimeString(),
                activeFilter: 'all',
                searchQuery: '',
                pollingInterval: null,

                // Modals
                showMessageModal: false,
                showMessageAllModal: false,
                showWarningModal: false,
                showTerminateModal: false,
                showForceSubmitModal: false,
                selectedParticipant: null,
                messageText: '',
                messageAllText: '',
                warningText: '',
                terminateReason: '',
                forceSubmitReason: '',

                get statsCards() {
                    return [
                        { label: 'Total', value: this.stats.total_participants, gradient: 'linear-gradient(135deg,#6366f1,#818cf8)', icon: 'users' },
                        { label: 'Active', value: this.stats.active, gradient: 'linear-gradient(135deg,#10b981,#34d399)', icon: 'signal' },
                        { label: 'Idle', value: this.stats.idle, gradient: 'linear-gradient(135deg,#f59e0b,#fbbf24)', icon: 'clock' },
                        { label: 'Away', value: this.stats.away, gradient: 'linear-gradient(135deg,#f97316,#fb923c)', icon: 'clock' },
                        { label: 'Disconnected', value: this.stats.disconnected, gradient: 'linear-gradient(135deg,#64748b,#94a3b8)', icon: 'signal-slash' },
                        { label: 'Flagged', value: this.stats.flagged, gradient: 'linear-gradient(135deg,#ef4444,#f87171)', icon: 'flag' },
                        { label: 'Completed', value: this.stats.completed, gradient: 'linear-gradient(135deg,#3b82f6,#60a5fa)', icon: 'check-circle' },
                    ];
                },

                get filters() {
                    return [
                        { label: 'All', value: 'all', count: this.stats.total_participants },
                        { label: 'Active', value: 'active', count: this.stats.active },
                        { label: 'Idle/Away', value: 'idle', count: this.stats.idle + this.stats.away },
                        { label: 'Disconnected', value: 'disconnected', count: this.stats.disconnected },
                        { label: 'Flagged', value: 'flagged', count: this.stats.flagged },
                        { label: 'Completed', value: 'completed', count: this.stats.completed },
                    ];
                },

                get filteredParticipants() {
                    let result = this.participants;

                    // Apply status filter
                    if (this.activeFilter !== 'all') {
                        if (this.activeFilter === 'idle') {
                            result = result.filter(p => p.status === 'idle' || p.status === 'away');
                        } else if (this.activeFilter === 'flagged') {
                            result = result.filter(p => p.is_flagged);
                        } else {
                            result = result.filter(p => p.status === this.activeFilter);
                        }
                    }

                    // Apply search filter
                    if (this.searchQuery.trim()) {
                        const query = this.searchQuery.toLowerCase();
                        result = result.filter(p =>
                            (p.participant_name && p.participant_name.toLowerCase().includes(query)) ||
                            (p.participant_email && p.participant_email.toLowerCase().includes(query))
                        );
                    }

                    return result;
                },

                init() {
                    // Start polling every 10 seconds
                    this.pollingInterval = setInterval(() => this.refreshData(), 10000);

                    // Setup Echo listener for real-time updates (if available)
                    if (typeof Echo !== 'undefined') {
                        Echo.channel('exam-monitoring.' + this.exam.id)
                            .listen('.participant.heartbeat', (data) => {
                                this.updateParticipant(data);
                            })
                            .listen('.participant.violation', (data) => {
                                this.updateParticipant(data.participant);
                                this.showViolationToast(data);
                            })
                            .listen('.participant.status_changed', (data) => {
                                this.updateParticipant(data.participant);
                            });
                    }
                },

                async refreshData() {
                    this.loading = true;
                    try {
                        const response = await fetch('{{ route('examination-hub.live-monitoring.api.participants', $exam) }}');
                        const data = await response.json();
                        this.stats = data.stats;
                        this.participants = data.participants;
                        this.lastUpdated = new Date().toLocaleTimeString();
                    } catch (error) {
                        console.error('Failed to refresh data:', error);
                    }
                    this.loading = false;
                },

                updateParticipant(data) {
                    const index = this.participants.findIndex(p => p.submission_id === data.submission_id);
                    if (index !== -1) {
                        this.participants[index] = data;
                    } else {
                        this.participants.push(data);
                    }
                    this.recalculateStats();
                },

                recalculateStats() {
                    this.stats = {
                        total_participants: this.participants.length,
                        active: this.participants.filter(p => p.status === 'active').length,
                        idle: this.participants.filter(p => p.status === 'idle').length,
                        away: this.participants.filter(p => p.status === 'away').length,
                        disconnected: this.participants.filter(p => p.status === 'disconnected').length,
                        completed: this.participants.filter(p => p.status === 'completed').length,
                        terminated: this.participants.filter(p => p.status === 'terminated').length,
                        flagged: this.participants.filter(p => p.is_flagged).length,
                    };
                },

                formatSeconds(s) {
                    if (s === null || s === undefined) return '—';
                    s = Math.max(0, Math.round(s));
                    const h = Math.floor(s / 3600);
                    const m = Math.floor((s % 3600) / 60);
                    const sec = s % 60;
                    if (h > 0) {
                        return String(h).padStart(2,'0') + ':' + String(m).padStart(2,'0') + ':' + String(sec).padStart(2,'0');
                    }
                    return String(m).padStart(2,'0') + ':' + String(sec).padStart(2,'0');
                },

                formatTime(participant) {
                    const isCompleted = participant.status === 'completed' || participant.status === 'terminated';

                    if (participant.has_duration && participant.remaining_seconds !== null) {
                        const s = Math.max(0, Math.round(participant.remaining_seconds));
                        const h = Math.floor(s / 3600);
                        const m = Math.floor((s % 3600) / 60);
                        const sec = s % 60;
                        const label = h > 0
                            ? `${String(h).padStart(2,'0')}:${String(m).padStart(2,'0')}:${String(sec).padStart(2,'0')}`
                            : `${String(m).padStart(2,'0')}:${String(sec).padStart(2,'0')}`;
                        return isCompleted ? label : label + ' left';
                    }

                    const elapsed = participant.elapsed_seconds;
                    if (!elapsed && elapsed !== 0) return '—';
                    const h = Math.floor(elapsed / 3600);
                    const m = Math.floor((elapsed % 3600) / 60);
                    const sec = elapsed % 60;
                    return h > 0
                        ? `${String(h).padStart(2,'0')}:${String(m).padStart(2,'0')}:${String(sec).padStart(2,'0')}`
                        : `${String(m).padStart(2,'0')}:${String(sec).padStart(2,'0')}`;
                },

                openMessageModal(participant) {
                    this.selectedParticipant = participant;
                    this.messageText = '';
                    this.showMessageModal = true;
                },

                openMessageAllModal() {
                    this.messageAllText = '';
                    this.showMessageAllModal = true;
                },

                openWarningModal(participant) {
                    this.selectedParticipant = participant;
                    this.warningText = '';
                    this.showWarningModal = true;
                },

                async sendMessage() {
                    if (!this.messageText.trim() || !this.selectedParticipant) return;

                    try {
                        await fetch(`{{ url('examinations/exams/' . $exam->id . '/live-monitoring/message') }}/${this.selectedParticipant.submission_id}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            },
                            body: JSON.stringify({ message: this.messageText }),
                        });
                        this.showMessageModal = false;
                        this.messageText = '';
                    } catch (error) {
                        console.error('Failed to send message:', error);
                    }
                },

                async sendWarning() {
                    if (!this.warningText.trim() || !this.selectedParticipant) return;

                    try {
                        await fetch(`{{ url('examinations/exams/' . $exam->id . '/live-monitoring/warn') }}/${this.selectedParticipant.submission_id}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            },
                            body: JSON.stringify({ message: this.warningText }),
                        });
                        this.showWarningModal = false;
                        this.warningText = '';
                    } catch (error) {
                        console.error('Failed to send warning:', error);
                    }
                },

                async sendMessageToAll() {
                    if (!this.messageAllText.trim()) return;

                    try {
                        const response = await fetch(`{{ url('examinations/exams/' . $exam->id . '/live-monitoring/message-all') }}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            },
                            body: JSON.stringify({ message: this.messageAllText }),
                        });

                        const data = await response.json();
                        
                        this.showMessageAllModal = false;
                        this.messageAllText = '';
                        
                        // Show success message
                        alert(`Message sent successfully!\n\nDelivered to: ${data.sent_count} participants\nFailed: ${data.failed_count} participants`);
                        
                        // Refresh data
                        this.refreshData();
                    } catch (error) {
                        console.error('Failed to send message to all:', error);
                        alert('Failed to send message to all participants');
                    }
                },

                openForceSubmitModal(participant) {
                    this.selectedParticipant = participant;
                    this.forceSubmitReason = '';
                    this.showForceSubmitModal = true;
                },

                async executeForceSubmit() {
                    if (!this.selectedParticipant) return;
                    this.actionLoading = true;
                    try {
                        await fetch(`{{ url('examinations/exams/' . $exam->id . '/live-monitoring/force-submit') }}/${this.selectedParticipant.submission_id}`, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                            body: JSON.stringify({ reason: this.forceSubmitReason || 'Admin force submission' }),
                        });
                        this.showForceSubmitModal = false;
                        this.addToast('success', `Exam force submitted for ${this.selectedParticipant.participant_name}`);
                        this.refreshData();
                    } catch (error) {
                        this.addToast('error', 'Failed to force submit');
                    }
                    this.actionLoading = false;
                },

                openTerminateModal(participant) {
                    this.selectedParticipant = participant;
                    this.terminateReason = '';
                    this.showTerminateModal = true;
                },

                async executeTerminate() {
                    if (!this.selectedParticipant || !this.terminateReason.trim()) return;
                    this.actionLoading = true;
                    try {
                        await fetch(`{{ url('examinations/exams/' . $exam->id . '/live-monitoring/terminate') }}/${this.selectedParticipant.submission_id}`, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                            body: JSON.stringify({ reason: this.terminateReason }),
                        });
                        this.showTerminateModal = false;
                        this.addToast('success', `Session terminated for ${this.selectedParticipant.participant_name}`);
                        this.refreshData();
                    } catch (error) {
                        this.addToast('error', 'Failed to terminate session');
                    }
                    this.actionLoading = false;
                },

                showViolationToast(data) {
                    // You can implement a toast notification here
                    console.log('Violation recorded:', data);
                },

                // Cleanup on destroy
                destroy() {
                    if (this.pollingInterval) {
                        clearInterval(this.pollingInterval);
                    }
                },
            };
        }
    </script>
    @endpush
</x-layouts.app>
