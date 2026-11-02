<x-layouts.app>
    <x-examination-hub.navigation active="center-sync" />
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-7"
         style="font-family: 'system-ui', -apple-system, sans-serif;">

        {{-- ── PAGE HEADER ── --}}
        <div class="overflow-hidden"
             style="border-radius: 2px; background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); box-shadow: 0 4px 24px rgba(0,0,0,0.15);">
            <div class="h-1 w-full" style="background: linear-gradient(90deg, #065f46, #059669, #10b981);"></div>
            <div class="px-7 py-6">
                <nav class="flex items-center gap-1 text-sm text-slate-400 mb-3">
                    <a href="{{ route('center-sync.sync.index') }}"
                       class="hover:text-slate-200 transition-colors">Sync Status</a>
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                    <span class="text-slate-200">{{ $exam->title }}</span>
                </nav>
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h1 class="text-2xl font-bold text-white leading-snug"
                            style="letter-spacing: -0.02em; font-family: 'Georgia', serif;">
                            Applied Exam Details
                        </h1>
                        <p class="text-sm text-slate-400 mt-1">
                            UUID: <span class="font-mono text-slate-300">{{ $exam->uuid }}</span>
                            @if($syncState)
                                &middot; Revision {{ $syncState->applied_revision }} applied {{ $syncState->applied_at->diffForHumans() }}
                            @endif
                        </p>
                    </div>
                    <a href="{{ route('center-sync.sync.index') }}"
                       class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-slate-300 rounded-sm border border-slate-600 hover:bg-slate-700/50 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 19.5L8.25 12l7.5-7.5"/>
                        </svg>
                        Back to Sync Status
                    </a>
                </div>
            </div>
        </div>

        {{-- ── EXAM INFO ── --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white dark:bg-slate-900 overflow-hidden"
                 style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                    <div class="w-1 h-5"
                         style="background: linear-gradient(180deg, #1d4ed8, #3b82f6); border-radius: 1px;"></div>
                    <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider"
                        style="letter-spacing: 0.1em;">Exam Info</h2>
                </div>
                <div class="p-5">
                    <dl class="space-y-3">
                        <div class="flex justify-between items-center py-2 border-b border-slate-50 dark:border-slate-800/50">
                            <dt class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Title</dt>
                            <dd class="text-sm font-semibold text-slate-800 dark:text-slate-200 text-right max-w-xs truncate">{{ $exam->title }}</dd>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-slate-50 dark:border-slate-800/50">
                            <dt class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Status</dt>
                            <dd>
                                @if($exam->status === 'published')
                                    <span class="px-2 py-0.5 text-xs font-medium rounded-sm bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300">Published</span>
                                @else
                                    <span class="px-2 py-0.5 text-xs font-medium rounded-sm bg-slate-100 text-slate-800 dark:bg-slate-700 dark:text-slate-300">{{ ucfirst($exam->status ?? 'Draft') }}</span>
                                @endif
                            </dd>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-slate-50 dark:border-slate-800/50">
                            <dt class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Access Code</dt>
                            <dd class="font-mono text-sm font-semibold text-slate-800 dark:text-slate-200">{{ $exam->access_code ?? 'Not set' }}</dd>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-slate-50 dark:border-slate-800/50">
                            <dt class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Subject</dt>
                            <dd class="text-sm text-slate-700 dark:text-slate-300">{{ $exam->academicSubject ? $exam->academicSubject->name : 'N/A' }}</dd>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-slate-50 dark:border-slate-800/50">
                            <dt class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Level</dt>
                            <dd class="text-sm text-slate-700 dark:text-slate-300">{{ $exam->academicSubject && $exam->academicSubject->academicLevel ? $exam->academicSubject->academicLevel->name : 'N/A' }}</dd>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-slate-50 dark:border-slate-800/50">
                            <dt class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Group</dt>
                            <dd class="text-sm text-slate-700 dark:text-slate-300">{{ $exam->academicSubject && $exam->academicSubject->academicLevel && $exam->academicSubject->academicLevel->academicGroup ? $exam->academicSubject->academicLevel->academicGroup->name : 'N/A' }}</dd>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-slate-50 dark:border-slate-800/50">
                            <dt class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Results Released</dt>
                            <dd class="text-sm {{ $exam->results_released ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-700 dark:text-slate-300' }}">
                                {{ $exam->results_released ? 'Yes' : 'No' }}
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            {{-- Sync State --}}
            <div class="bg-white dark:bg-slate-900 overflow-hidden"
                 style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                    <div class="w-1 h-5"
                         style="background: linear-gradient(180deg, #065f46, #10b981); border-radius: 1px;"></div>
                    <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider"
                        style="letter-spacing: 0.1em;">Sync State</h2>
                </div>
                <div class="p-5">
                    @if($syncState)
                        <dl class="space-y-3">
                            <div class="flex justify-between items-center py-2 border-b border-slate-50 dark:border-slate-800/50">
                                <dt class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Applied Revision</dt>
                                <dd class="text-sm font-semibold text-slate-800 dark:text-slate-200">{{ $syncState->applied_revision }}</dd>
                            </div>
                            <div class="flex justify-between items-center py-2 border-b border-slate-50 dark:border-slate-800/50">
                                <dt class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">State Version</dt>
                                <dd class="text-sm font-semibold text-slate-800 dark:text-slate-200">{{ $syncState->applied_state_version }}</dd>
                            </div>
                            <div class="flex justify-between items-center py-2 border-b border-slate-50 dark:border-slate-800/50">
                                <dt class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Applied At</dt>
                                <dd class="text-sm text-slate-700 dark:text-slate-300">{{ $syncState->applied_at ? $syncState->applied_at->format('Y-m-d H:i:s') : 'Never' }}</dd>
                            </div>
                            <div class="flex justify-between items-center py-2">
                                <dt class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Checksum</dt>
                                <dd class="font-mono text-xs text-slate-600 dark:text-slate-400 max-w-xs truncate">{{ $syncState->checksum ?? 'N/A' }}</dd>
                            </div>
                        </dl>
                    @else
                        <div class="text-center py-8">
                            <svg class="w-12 h-12 text-slate-300 dark:text-slate-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                            </svg>
                            <p class="text-sm text-slate-500 dark:text-slate-400">No sync state found for this exam.</p>
                            <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">Run a pull sync to apply the latest package.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ── CONTENT SUMMARY ── --}}
        <div class="bg-white dark:bg-slate-900 overflow-hidden"
             style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                <div class="w-1 h-5"
                     style="background: linear-gradient(180deg, #b45309, #d97706); border-radius: 1px;"></div>
                <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider"
                    style="letter-spacing: 0.1em;">Content Summary</h2>
            </div>
            <div class="p-5">
                <dl class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div class="bg-slate-50 dark:bg-slate-800/50 rounded-sm px-4 py-3">
                        <dt class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Sections</dt>
                        <dd class="text-xl font-bold text-slate-800 dark:text-slate-200 mt-1">{{ $exam->sections->count() }}</dd>
                    </div>
                    <div class="bg-slate-50 dark:bg-slate-800/50 rounded-sm px-4 py-3">
                        <dt class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Questions</dt>
                        <dd class="text-xl font-bold text-slate-800 dark:text-slate-200 mt-1">{{ $exam->sections->sum(fn($s) => $s->questions->count()) }}</dd>
                    </div>
                    <div class="bg-slate-50 dark:bg-slate-800/50 rounded-sm px-4 py-3">
                        <dt class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Participants</dt>
                        <dd class="text-xl font-bold text-slate-800 dark:text-slate-200 mt-1">{{ $exam->configuredParticipants->count() }}</dd>
                    </div>
                    <div class="bg-slate-50 dark:bg-slate-800/50 rounded-sm px-4 py-3">
                        <dt class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Submissions</dt>
                        <dd class="text-xl font-bold text-slate-800 dark:text-slate-200 mt-1">{{ $exam->submissions->count() }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        {{-- ── SECTIONS & QUESTIONS ── --}}
        @if($exam->sections->isNotEmpty())
        <div class="bg-white dark:bg-slate-900 overflow-hidden"
             style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                <div class="w-1 h-5"
                     style="background: linear-gradient(180deg, #1d4ed8, #3b82f6); border-radius: 1px;"></div>
                <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider"
                    style="letter-spacing: 0.1em;">Sections &amp; Questions</h2>
            </div>

            @foreach($exam->sections as $section)
                <div class="@if(!$loop->first) border-t border-slate-100 dark:border-slate-800 @endif">
                    <div class="px-5 py-3 bg-slate-50 dark:bg-slate-800/50 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="inline-flex items-center justify-center w-7 h-7 rounded-sm bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300 text-xs font-bold">{{ $loop->iteration }}</span>
                            <h3 class="font-semibold text-sm text-slate-800 dark:text-slate-200">{{ $section->title }}</h3>
                            <span class="text-xs text-slate-400 font-mono">{{ Str::limit($section->uuid ?? '', 12) }}</span>
                        </div>
                        <span class="text-xs text-slate-500 dark:text-slate-400">{{ $section->questions->count() }} questions</span>
                    </div>

                    @if($section->questions->isNotEmpty())
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                            <tr class="border-b border-slate-100 dark:border-slate-800">
                                <th class="px-4 py-2 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">#</th>
                                <th class="px-4 py-2 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Question</th>
                                <th class="px-4 py-2 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Type</th>
                                <th class="px-4 py-2 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Key</th>
                                <th class="px-4 py-2 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Marks</th>
                                <th class="px-4 py-2 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Excluded</th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50 dark:divide-slate-800">
                                @foreach($section->questions as $question)
                                    <tr class="hover:bg-blue-50/20 dark:hover:bg-slate-800/20 transition-colors">
                                        <td class="px-4 py-2.5 text-slate-400 text-xs">{{ $loop->iteration }}</td>
                                        <td class="px-4 py-2.5">
                                            <div class="max-w-md">
                                                @php
                                                    $qText = $question->question ?? 'No question text';
                                                    $qText = is_object($qText) ? (string) $qText : $qText;
                                                    $qText = is_array($qText) ? implode(' ', array_filter($qText, 'is_string')) : $qText;
                                                @endphp
                                                <p class="text-sm text-slate-700 dark:text-slate-300 line-clamp-2">{{ strip_tags($qText) }}</p>
                                                <span class="text-xs font-mono text-slate-400">{{ Str::limit($question->uuid ?? '', 12) }}</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-2.5">
                                            <span class="px-2 py-0.5 text-xs rounded-sm bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300">
                                                {{ ucfirst($question->type ?? 'mcq') }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-2.5">
                                            <span class="font-mono font-semibold text-sm text-emerald-600 dark:text-emerald-400">{{ $question->correct_answer ?? '-' }}</span>
                                        </td>
                                        <td class="px-4 py-2.5 text-sm text-slate-700 dark:text-slate-300">{{ $question->marks ?? '-' }}</td>
                                        <td class="px-4 py-2.5">
                                            @if($question->excluded_from_grading ?? false)
                                                <span class="px-2 py-0.5 text-xs rounded-sm bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-300">Excluded</span>
                                                @if($question->award_marks_on_exclusion ?? false)
                                                    <span class="ml-1 px-1.5 py-0.5 text-xs rounded-sm bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-300">Awarded</span>
                                                @endif
                                            @else
                                                <span class="text-slate-400 text-xs">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
            @endforeach
        </div>
        @endif

        {{-- ── PARTICIPANTS ── --}}
        @if($exam->configuredParticipants->isNotEmpty())
        <div class="bg-white dark:bg-slate-900 overflow-hidden"
             style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                <div class="w-1 h-5"
                     style="background: linear-gradient(180deg, #065f46, #10b981); border-radius: 1px;"></div>
                <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider"
                    style="letter-spacing: 0.1em;">Participants ({{ $exam->configuredParticipants->count() }})</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                    <tr class="border-b border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
                        <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">#</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Index Number</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Active</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 dark:divide-slate-800">
                        @foreach($exam->configuredParticipants as $participant)
                            <tr class="hover:bg-emerald-50/20 dark:hover:bg-slate-800/20 transition-colors">
                                <td class="px-6 py-3 text-slate-400 text-xs">{{ $loop->iteration }}</td>
                                <td class="px-6 py-3 text-sm text-slate-700 dark:text-slate-300">{{ $participant->name ?? '-' }}</td>
                                <td class="px-6 py-3 font-mono text-xs text-slate-600 dark:text-slate-400">{{ $participant->unique_code ?? '-' }}</td>
                                <td class="px-6 py-3 text-sm text-slate-600 dark:text-slate-400">{{ $participant->email ?? '-' }}</td>
                                <td class="px-6 py-3">
                                    <span class="{{ $participant->is_active ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }} text-xs font-medium">
                                        {{ $participant->is_active ? 'Yes' : 'No' }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

    </div>{{-- /container --}}
</x-layouts.app>
