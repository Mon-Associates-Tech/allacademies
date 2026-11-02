<x-layouts.app>
    <x-examination-hub.navigation active="center-exams" />
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-7"
         style="font-family: 'system-ui', -apple-system, sans-serif;">

        {{-- ── PAGE HEADER ── --}}
        <div class="overflow-hidden"
             style="border-radius: 2px; background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); box-shadow: 0 4px 24px rgba(0,0,0,0.15);">
            <div class="h-1 w-full" style="background: linear-gradient(90deg, #b45309, #d97706, #f59e0b);"></div>
            <div class="px-7 py-6">
                <nav class="flex items-center gap-1 text-sm text-slate-400 mb-3">
                    <a href="{{ route('center-sync.exams.index') }}"
                       class="hover:text-slate-200 transition-colors">Exams</a>
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                    <a href="{{ route('center-sync.exams.show', $exam) }}"
                       class="hover:text-slate-200 transition-colors">{{ $exam->title }}</a>
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                    <span class="text-slate-200">Revision {{ $package->revision }}</span>
                </nav>
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h1 class="text-2xl font-bold text-white leading-snug"
                            style="letter-spacing: -0.02em; font-family: 'Georgia', serif;">
                            Package Details — Revision {{ $package->revision }}
                        </h1>
                        <p class="text-sm text-slate-400 mt-1">
                            Exam: {{ $exam->title }} &middot; UUID: <span class="font-mono text-slate-300">{{ $exam->uuid }}</span>
                        </p>
                    </div>
                    <a href="{{ route('center-sync.exams.show', $exam) }}"
                       class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-slate-300 rounded-sm border border-slate-600 hover:bg-slate-700/50 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 19.5L8.25 12l7.5-7.5"/>
                        </svg>
                        Back to Exam
                    </a>
                </div>
            </div>
        </div>

        {{-- ── PACKAGE METADATA ── --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Package Info --}}
            <div class="bg-white dark:bg-slate-900 overflow-hidden"
                 style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                    <div class="w-1 h-5"
                         style="background: linear-gradient(180deg, #b45309, #d97706); border-radius: 1px;"></div>
                    <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider"
                        style="letter-spacing: 0.1em;">Package Metadata</h2>
                </div>
                <div class="p-5">
                    <dl class="space-y-3">
                        <div class="flex justify-between items-center py-2 border-b border-slate-50 dark:border-slate-800/50">
                            <dt class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Revision</dt>
                            <dd class="text-sm font-semibold text-slate-800 dark:text-slate-200">{{ $package->revision }}</dd>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-slate-50 dark:border-slate-800/50">
                            <dt class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">State Version</dt>
                            <dd class="text-sm font-semibold text-slate-800 dark:text-slate-200">{{ $payload['state_version'] ?? 'N/A' }}</dd>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-slate-50 dark:border-slate-800/50">
                            <dt class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Access Code</dt>
                            <dd class="font-mono text-sm font-semibold text-slate-800 dark:text-slate-200">{{ $payload['access_code'] ?? 'Not set' }}</dd>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-slate-50 dark:border-slate-800/50">
                            <dt class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Created</dt>
                            <dd class="text-sm text-slate-700 dark:text-slate-300">{{ $package->created_at->format('Y-m-d H:i:s') }}</dd>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-slate-50 dark:border-slate-800/50">
                            <dt class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Created By</dt>
                            <dd class="text-sm text-slate-700 dark:text-slate-300">{{ $package->createdBy ? $package->createdBy->name : 'System' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            {{-- Integrity & Summary --}}
            <div class="space-y-6">
                {{-- Integrity Checks --}}
                <div class="bg-white dark:bg-slate-900 overflow-hidden"
                     style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                    <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                        <div class="w-1 h-5"
                             style="background: linear-gradient(180deg, #065f46, #10b981); border-radius: 1px;"></div>
                        <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider"
                            style="letter-spacing: 0.1em;">Integrity Checks</h2>
                    </div>
                    <div class="p-5 space-y-3">
                        <div class="flex items-center justify-between py-2">
                            <span class="text-sm text-slate-600 dark:text-slate-400">Checksum</span>
                            <span class="@if($checksumValid) text-emerald-600 dark:text-emerald-400 @else text-red-600 dark:text-red-400 @endif font-semibold text-sm flex items-center gap-1">
                                @if($checksumValid)
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Valid
                                @else
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                    Invalid
                                @endif
                            </span>
                        </div>
                        <div class="flex items-center justify-between py-2">
                            <span class="text-sm text-slate-600 dark:text-slate-400">Signature</span>
                            <span class="@if($signatureValid === true) text-emerald-600 dark:text-emerald-400 @elseif($signatureValid === false) text-red-600 dark:text-red-400 @else text-slate-400 @endif font-semibold text-sm flex items-center gap-1">
                                @if($signatureValid === true)
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Verified
                                @elseif($signatureValid === false)
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                    Failed
                                @else
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Not checked
                                @endif
                            </span>
                        </div>
                        <div class="py-2">
                            <span class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Checksum</span>
                            <p class="font-mono text-xs text-slate-600 dark:text-slate-400 mt-1 break-all">{{ $package->checksum }}</p>
                        </div>
                    </div>
                </div>

                {{-- Content Summary --}}
                <div class="bg-white dark:bg-slate-900 overflow-hidden"
                     style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                    <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                        <div class="w-1 h-5"
                             style="background: linear-gradient(180deg, #1d4ed8, #3b82f6); border-radius: 1px;"></div>
                        <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider"
                            style="letter-spacing: 0.1em;">Content Summary</h2>
                    </div>
                    <div class="p-5">
                        <dl class="grid grid-cols-2 gap-3">
                            <div class="bg-slate-50 dark:bg-slate-800/50 rounded-sm px-4 py-3">
                                <dt class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Sections</dt>
                                <dd class="text-xl font-bold text-slate-800 dark:text-slate-200 mt-1">{{ $summary['sections'] }}</dd>
                            </div>
                            <div class="bg-slate-50 dark:bg-slate-800/50 rounded-sm px-4 py-3">
                                <dt class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Questions</dt>
                                <dd class="text-xl font-bold text-slate-800 dark:text-slate-200 mt-1">{{ $summary['questions'] }}</dd>
                            </div>
                            <div class="bg-slate-50 dark:bg-slate-800/50 rounded-sm px-4 py-3">
                                <dt class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Participants</dt>
                                <dd class="text-xl font-bold text-slate-800 dark:text-slate-200 mt-1">{{ $summary['participants'] }}</dd>
                            </div>
                            <div class="bg-slate-50 dark:bg-slate-800/50 rounded-sm px-4 py-3">
                                <dt class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Subjects</dt>
                                <dd class="text-xl font-bold text-slate-800 dark:text-slate-200 mt-1">{{ $summary['academic_subjects'] }}</dd>
                            </div>
                            <div class="bg-slate-50 dark:bg-slate-800/50 rounded-sm px-4 py-3">
                                <dt class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Levels</dt>
                                <dd class="text-xl font-bold text-slate-800 dark:text-slate-200 mt-1">{{ $summary['academic_levels'] }}</dd>
                            </div>
                            <div class="bg-slate-50 dark:bg-slate-800/50 rounded-sm px-4 py-3">
                                <dt class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Groups</dt>
                                <dd class="text-xl font-bold text-slate-800 dark:text-slate-200 mt-1">{{ $summary['academic_groups'] }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── EXAM SETTINGS ── --}}
        @if($payload['settings'])
        <div class="bg-white dark:bg-slate-900 overflow-hidden"
             style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                <div class="w-1 h-5"
                     style="background: linear-gradient(180deg, #7c3aed, #a78bfa); border-radius: 1px;"></div>
                <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider"
                    style="letter-spacing: 0.1em;">Exam Settings</h2>
            </div>
            <div class="p-5">
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach($payload['settings'] as $key => $value)
                        <div class="flex justify-between items-center py-2 border-b border-slate-50 dark:border-slate-800/50">
                            <dt class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">{{ ucwords(str_replace('_', ' ', $key)) }}</dt>
                            <dd class="text-sm font-medium text-slate-800 dark:text-slate-200">
                                @if(is_bool($value))
                                    <span class="{{ $value ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400' }}">
                                        {{ $value ? 'Yes' : 'No' }}
                                    </span>
                                @else
                                    {{ is_array($value) ? json_encode($value) : $value }}
                                @endif
                            </dd>
                        </div>
                    @endforeach
                </dl>
            </div>
        </div>
        @endif

        {{-- ── GRADING SCALE ── --}}
        @if($payload['grading_scale'])
        <div class="bg-white dark:bg-slate-900 overflow-hidden"
             style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                <div class="w-1 h-5"
                     style="background: linear-gradient(180deg, #065f46, #10b981); border-radius: 1px;"></div>
                <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider"
                    style="letter-spacing: 0.1em;">Grading Scale</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                    <tr class="border-b border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
                        <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Grade</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Minimum %</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Label</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 dark:divide-slate-800">
                        @foreach($payload['grading_scale'] as $grade)
                            <tr class="hover:bg-emerald-50/30 dark:hover:bg-slate-800/30 transition-colors">
                                <td class="px-6 py-3 font-semibold text-slate-800 dark:text-slate-200">{{ $grade['grade'] ?? $grade['letter'] ?? '-' }}</td>
                                <td class="px-6 py-3 text-slate-700 dark:text-slate-300">{{ $grade['min_percentage'] ?? $grade['min_percent'] ?? '-' }}%</td>
                                <td class="px-6 py-3 text-slate-700 dark:text-slate-300">{{ $grade['label'] ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        {{-- ── SECTIONS & QUESTIONS ── --}}
        @if($payload['sections'])
        <div class="bg-white dark:bg-slate-900 overflow-hidden"
             style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                <div class="w-1 h-5"
                     style="background: linear-gradient(180deg, #1d4ed8, #3b82f6); border-radius: 1px;"></div>
                <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider"
                    style="letter-spacing: 0.1em;">Sections &amp; Questions ({{ $summary['sections'] }} sections, {{ $summary['questions'] }} questions)</h2>
            </div>

            @foreach($payload['sections'] as $sectionIndex => $section)
                <div class="@if(!$loop->first) border-t border-slate-100 dark:border-slate-800 @endif">
                    <div class="px-5 py-3 bg-slate-50 dark:bg-slate-800/50 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="inline-flex items-center justify-center w-7 h-7 rounded-sm bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300 text-xs font-bold">{{ $loop->iteration }}</span>
                            <h3 class="font-semibold text-sm text-slate-800 dark:text-slate-200">{{ $section['title'] ?? "Section {$loop->iteration}" }}</h3>
                            <span class="text-xs text-slate-400 font-mono">{{ Str::limit($section['uuid'] ?? '', 12) }}</span>
                        </div>
                        <span class="text-xs text-slate-500 dark:text-slate-400">{{ count($section['questions'] ?? []) }} questions</span>
                    </div>

                    @if($section['questions'])
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
                                <th class="px-4 py-2 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Topic</th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50 dark:divide-slate-800">
                                @foreach($section['questions'] as $qIndex => $question)
                                    <tr class="hover:bg-blue-50/20 dark:hover:bg-slate-800/20 transition-colors">
                                        <td class="px-4 py-2.5 text-slate-400 text-xs">{{ $loop->iteration }}</td>
                                        <td class="px-4 py-2.5">
                                            <div class="max-w-md">
                                                @php
                                                    $qText = $question['question'] ?? $question['stem'] ?? 'No question text';
                                                    $qText = is_array($qText) ? implode(' ', array_filter($qText, 'is_string')) : $qText;
                                                @endphp
                                                <p class="text-sm text-slate-700 dark:text-slate-300 line-clamp-2">{{ strip_tags($qText) }}</p>
                                                <span class="text-xs font-mono text-slate-400">{{ Str::limit($question['uuid'] ?? '', 12) }}</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-2.5">
                                            <span class="px-2 py-0.5 text-xs rounded-sm bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300">
                                                {{ ucfirst($question['type'] ?? 'mcq') }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-2.5">
                                            <span class="font-mono font-semibold text-sm text-emerald-600 dark:text-emerald-400">{{ $question['correct_answer'] ?? '-' }}</span>
                                        </td>
                                        <td class="px-4 py-2.5 text-sm text-slate-700 dark:text-slate-300">{{ $question['marks'] ?? '-' }}</td>
                                        <td class="px-4 py-2.5">
                                            @if($question['excluded_from_grading'] ?? false)
                                                <span class="px-2 py-0.5 text-xs rounded-sm bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-300">Excluded</span>
                                                @if($question['award_marks_on_exclusion'] ?? false)
                                                    <span class="ml-1 px-1.5 py-0.5 text-xs rounded-sm bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-300">Awarded</span>
                                                @endif
                                            @else
                                                <span class="text-slate-400 text-xs">—</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-2.5 text-xs text-slate-500 dark:text-slate-400">{{ $question['topic_name'] ?? '-' }}</td>
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
        @if($payload['participants'])
        <div class="bg-white dark:bg-slate-900 overflow-hidden"
             style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                <div class="w-1 h-5"
                     style="background: linear-gradient(180deg, #065f46, #10b981); border-radius: 1px;"></div>
                <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider"
                    style="letter-spacing: 0.1em;">Participants ({{ $summary['participants'] }})</h2>
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
                        @foreach($payload['participants'] as $participant)
                            <tr class="hover:bg-emerald-50/20 dark:hover:bg-slate-800/20 transition-colors">
                                <td class="px-6 py-3 text-slate-400 text-xs">{{ $loop->iteration }}</td>
                                <td class="px-6 py-3 text-sm text-slate-700 dark:text-slate-300">{{ $participant['name'] ?? '-' }}</td>
                                <td class="px-6 py-3 font-mono text-xs text-slate-600 dark:text-slate-400">{{ $participant['index_number'] ?? '-' }}</td>
                                <td class="px-6 py-3 text-sm text-slate-600 dark:text-slate-400">{{ $participant['email'] ?? '-' }}</td>
                                <td class="px-6 py-3">
                                    <span class="{{ ($participant['is_active'] ?? true) ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }} text-xs font-medium">
                                        {{ ($participant['is_active'] ?? true) ? 'Yes' : 'No' }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        {{-- ── ACADEMIC STRUCTURE ── --}}
        @php $academicStructure = $payload['academic_structure'] ?? null; @endphp
        @if($academicStructure)
        <div class="bg-white dark:bg-slate-900 overflow-hidden"
             style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                <div class="w-1 h-5"
                     style="background: linear-gradient(180deg, #7c3aed, #a78bfa); border-radius: 1px;"></div>
                <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider"
                    style="letter-spacing: 0.1em;">Academic Structure</h2>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 divide-y lg:divide-y-0 lg:divide-x divide-slate-100 dark:divide-slate-800">
                {{-- Groups --}}
                @php $groups = $academicStructure['groups'] ?? []; @endphp
                @if($groups)
                <div class="p-5">
                    <h3 class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-3" style="letter-spacing: 0.08em;">
                        Groups ({{ count($groups) }})
                    </h3>
                    <ul class="space-y-2">
                        @foreach($groups as $group)
                            <li class="flex items-start gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-violet-400 mt-1.5 flex-shrink-0"></span>
                                <div>
                                    <p class="text-sm text-slate-700 dark:text-slate-300">{{ $group['name'] }}</p>
                                    <span class="text-xs font-mono text-slate-400">{{ Str::limit($group['uuid'] ?? '', 12) }}</span>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
                @endif

                {{-- Levels --}}
                @php $levels = $academicStructure['levels'] ?? []; @endphp
                @if($levels)
                <div class="p-5">
                    <h3 class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-3" style="letter-spacing: 0.08em;">
                        Levels ({{ count($levels) }})
                    </h3>
                    <ul class="space-y-2">
                        @foreach($levels as $level)
                            <li class="flex items-start gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-400 mt-1.5 flex-shrink-0"></span>
                                <div>
                                    <p class="text-sm text-slate-700 dark:text-slate-300">{{ $level['name'] }}</p>
                                    <span class="text-xs font-mono text-slate-400">{{ Str::limit($level['uuid'] ?? '', 12) }}</span>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
                @endif

                {{-- Subjects --}}
                @php $subjects = $academicStructure['subjects'] ?? []; @endphp
                @if($subjects)
                <div class="p-5">
                    <h3 class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-3" style="letter-spacing: 0.08em;">
                        Subjects ({{ count($subjects) }})
                    </h3>
                    <ul class="space-y-2">
                        @foreach($subjects as $subject)
                            <li class="flex items-start gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 mt-1.5 flex-shrink-0"></span>
                                <div>
                                    <p class="text-sm text-slate-700 dark:text-slate-300">{{ $subject['name'] }}</p>
                                    <span class="text-xs font-mono text-slate-400">{{ Str::limit($subject['uuid'] ?? '', 12) }}</span>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
                @endif
            </div>
        </div>
        @endif

        {{-- ── RAW PAYLOAD (collapsible) ── --}}
        <div class="bg-white dark:bg-slate-900 overflow-hidden"
             style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
            <details class="group">
                <summary class="cursor-pointer px-5 py-4 flex items-center gap-2 select-none hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                    <div class="w-1 h-5"
                         style="background: linear-gradient(180deg, #475569, #64748b); border-radius: 1px;"></div>
                    <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider"
                        style="letter-spacing: 0.1em;">Raw Package Payload</h2>
                    <svg class="w-4 h-4 text-slate-400 ml-auto group-open:rotate-90 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </summary>
                <div class="px-5 py-4 border-t border-slate-100 dark:border-slate-800">
                    <pre class="bg-slate-50 dark:bg-slate-800/50 rounded-sm p-4 overflow-x-auto text-xs font-mono text-slate-700 dark:text-slate-300 max-h-96 overflow-y-auto">{{ json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                </div>
            </details>
        </div>

    </div>{{-- /container --}}
</x-layouts.app>
