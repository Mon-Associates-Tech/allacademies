<x-layouts.app :title="$mockExam->title">

<div x-data="{ tab: '{{ session('tab', 'overview') }}' }">

    {{-- Header --}}
    <div class="flex items-start justify-between gap-4 mb-6">
        <div class="flex items-center gap-3">
            <x-ui.button href="{{ route('mock-exams.index') }}" variant="ghost" size="sm" icon="arrow-left"></x-ui.button>
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <h1 class="text-xl font-bold text-slate-900 dark:text-white">{{ $mockExam->title }}</h1>
                    @php
                        $badgeClass = match($mockExam->status) {
                            'published' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400',
                            'closed'    => 'bg-slate-100 text-slate-500',
                            default     => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
                        };
                    @endphp
                    <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $badgeClass }}">{{ ucfirst($mockExam->status) }}</span>
                    <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">{{ ucfirst($mockExam->delivery_type) }}</span>
                </div>
                @if($mockExam->description)
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">{{ $mockExam->description }}</p>
                @endif
            </div>
        </div>

        <div class="flex items-center gap-2 shrink-0">
            @if($mockExam->isPrint())
                <x-ui.button href="{{ route('mock-exams.pdf.exam', $mockExam) }}" variant="info" size="sm" icon="document-arrow-down">
                    Exam PDF
                </x-ui.button>
                <x-ui.button href="{{ route('mock-exams.pdf.answer-key', $mockExam) }}" variant="ghost" size="sm" icon="document-text">
                    Answer Key
                </x-ui.button>
            @endif
            @if(!$mockExam->submissions()->exists())
                <x-ui.button href="{{ route('mock-exams.edit', $mockExam) }}" variant="ghost" size="sm" icon="pencil">
                    Edit
                </x-ui.button>
            @endif
        </div>
    </div>

    {{-- Flash --}}
    @if(session('success'))
        <div class="mb-4 px-4 py-3 rounded-[2px] bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm dark:bg-emerald-900/20 dark:border-emerald-800 dark:text-emerald-400">
            {!! session('success') !!}
        </div>
    @endif
    @if(session('info'))
        <div class="mb-4 px-4 py-3 rounded-[2px] bg-blue-50 border border-blue-200 text-blue-700 text-sm">{{ session('info') }}</div>
    @endif

    {{-- Tab nav --}}
    <div class="flex border-b border-slate-200 dark:border-slate-700 mb-6 overflow-x-auto">
        @foreach([
            'overview'      => 'Overview',
            'subjects'      => 'Subject Exams (' . $mockExam->subjectExams->count() . ')',
            'participants'  => 'Participants (' . $mockExam->configuredParticipants->count() . ')',
            'results'       => 'Results (' . $submissions->total() . ')',
        ] as $key => $label)
            <button @click="tab = '{{ $key }}'"
                    :class="tab === '{{ $key }}'
                        ? 'border-violet-500 text-violet-600 dark:text-violet-400'
                        : 'border-transparent text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-300'"
                    class="px-4 py-2.5 text-sm font-medium border-b-2 -mb-px transition-colors whitespace-nowrap">
                {{ $label }}
            </button>
        @endforeach
    </div>

    {{-- ──────────────────────────────────────────────────────────────────────
         TAB: Overview
    ────────────────────────────────────────────────────────────────────────── --}}
    <div x-show="tab === 'overview'" x-transition>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            @foreach([
                ['label' => 'Subject Exams',  'value' => $mockExam->subjectExams->count(),  'color' => 'violet'],
                ['label' => 'Total Questions','value' => $mockExam->getTotalQuestions(),     'color' => 'blue'],
                ['label' => 'Total Marks',    'value' => number_format($mockExam->getTotalMarks(), 1), 'color' => 'emerald'],
                ['label' => 'Submissions',    'value' => $submissions->total(),              'color' => 'amber'],
            ] as $stat)
            <x-ui.card>
                <div class="p-4 text-center">
                    <p class="text-2xl font-bold text-{{ $stat['color'] }}-600 dark:text-{{ $stat['color'] }}-400">{{ $stat['value'] }}</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">{{ $stat['label'] }}</p>
                </div>
            </x-ui.card>
            @endforeach
        </div>

        <div class="grid md:grid-cols-2 gap-4">
            <x-ui.card>
                <x-ui.card-header title="Exam Details" accent="primary" />
                <div class="p-5 space-y-3 text-sm">
                    @if($mockExam->access_code && $mockExam->isOnline())
                        <div class="flex justify-between">
                            <span class="text-slate-500 dark:text-slate-400">Access Code</span>
                            <span class="font-mono font-bold text-violet-600 dark:text-violet-400 text-base tracking-widest">
                                {{ $mockExam->access_code }}
                            </span>
                        </div>
                    @endif
                    <div class="flex justify-between">
                        <span class="text-slate-500">Participant Mode</span>
                        <span class="font-medium text-slate-700 dark:text-slate-300">{{ ucfirst($mockExam->participant_mode) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Result Visibility</span>
                        <span class="font-medium text-slate-700 dark:text-slate-300">{{ str_replace('_', ' ', ucfirst($mockExam->result_visibility)) }}</span>
                    </div>
                    @if($mockExam->starts_at)
                        <div class="flex justify-between">
                            <span class="text-slate-500">Opens</span>
                            <span class="font-medium text-slate-700 dark:text-slate-300">{{ $mockExam->starts_at->format('M d, Y H:i') }}</span>
                        </div>
                    @endif
                    @if($mockExam->ends_at)
                        <div class="flex justify-between">
                            <span class="text-slate-500">Closes</span>
                            <span class="font-medium text-slate-700 dark:text-slate-300">{{ $mockExam->ends_at->format('M d, Y H:i') }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between">
                        <span class="text-slate-500">Max Attempts</span>
                        <span class="font-medium text-slate-700 dark:text-slate-300">{{ $mockExam->max_attempts }}</span>
                    </div>
                </div>
            </x-ui.card>

            @if($mockExam->isOnline() && $mockExam->result_visibility === 'manual_release')
                <x-ui.card>
                    <x-ui.card-header title="Result Release" accent="warning" />
                    <div class="p-5">
                        @if($mockExam->results_released)
                            <p class="text-sm text-emerald-600 dark:text-emerald-400 font-medium mb-3">
                                ✓ Results released on {{ $mockExam->results_released_at?->format('M d, Y H:i') }}
                            </p>
                        @else
                            <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">
                                Results are currently hidden from participants. Release when ready.
                            </p>
                            <form method="POST" action="{{ route('mock-exams.results.release', $mockExam) }}">
                                @csrf
                                <x-ui.button type="submit" variant="success" icon="eye">
                                    Release Results Now
                                </x-ui.button>
                            </form>
                        @endif
                    </div>
                </x-ui.card>
            @endif
        </div>
    </div>

    {{-- ──────────────────────────────────────────────────────────────────────
         TAB: Subject Exams
    ────────────────────────────────────────────────────────────────────────── --}}
    <div x-show="tab === 'subjects'" x-transition>
        <div class="flex justify-end mb-4">
            <x-ui.button href="{{ route('mock-exams.subject-exams.create', $mockExam) }}" variant="primary" icon="plus">
                Add Subject Exam
            </x-ui.button>
        </div>

        @forelse($mockExam->subjectExams as $se)
            <x-ui.card class="mb-4" x-data="{ expanded: false }">
                <div class="p-4 flex items-start justify-between gap-4">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <button @click="expanded = !expanded" class="flex items-center gap-1 text-sm font-semibold text-slate-900 dark:text-white hover:text-violet-600 transition-colors">
                                <x-heroicon-o-chevron-right class="w-4 h-4 transition-transform" ::class="{ 'rotate-90': expanded }" />
                                {{ $se->getDisplayTitle() }}
                            </button>
                            <span class="text-xs text-slate-400">{{ $se->academicSubject?->name }}</span>
                        </div>
                        <div class="flex items-center gap-3 mt-1 ml-5">
                            <span class="text-xs text-slate-500">{{ $se->sections->count() }} section(s)</span>
                            <span class="text-xs text-slate-500">
                                {{ $se->sections->sum(fn($s) => $s->questions->count()) }} question(s)
                            </span>
                            <span class="text-xs text-slate-500">
                                {{ number_format($se->sections->sum(fn($s) => $s->questions->sum('marks')), 1) }} marks
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center gap-1">
                        <x-ui.button href="{{ route('mock-exams.subject-exams.edit', [$mockExam, $se]) }}" variant="ghost" size="sm" icon="pencil"></x-ui.button>
                        <form method="POST" action="{{ route('mock-exams.subject-exams.destroy', [$mockExam, $se]) }}"
                              onsubmit="return confirm('Remove this subject exam and all its questions?')">
                            @csrf @method('DELETE')
                            <x-ui.button type="submit" variant="ghost" size="sm" icon="trash"></x-ui.button>
                        </form>
                    </div>
                </div>

                {{-- Expanded: sections & questions --}}
                <div x-show="expanded" x-transition class="border-t border-slate-100 dark:border-slate-800">
                    @foreach($se->sections as $section)
                        <div class="px-5 py-3 border-b border-slate-50 dark:border-slate-800/50 last:border-0">
                            <div class="flex items-center justify-between">
                                <p class="text-xs font-semibold text-slate-700 dark:text-slate-300">
                                    Section {{ $loop->iteration }}: {{ $section->title }}
                                </p>
                                <span class="text-xs text-slate-400">
                                    {{ ucfirst(str_replace('_', ' ', $section->question_type)) }} ·
                                    {{ $section->questions->count() }} Qs ·
                                    {{ number_format($section->getTotalMarks(), 1) }} marks
                                    @if($section->is_randomized) · Randomised @endif
                                </span>
                            </div>
                            @if($section->instructions)
                                <p class="text-xs text-slate-400 mt-0.5">{{ $section->instructions }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </x-ui.card>
        @empty
            <x-ui.card>
                <div class="py-12 text-center">
                    <p class="text-sm text-slate-400">No subject exams yet. Add one to get started.</p>
                </div>
            </x-ui.card>
        @endforelse
    </div>

    {{-- ──────────────────────────────────────────────────────────────────────
         TAB: Participants
    ────────────────────────────────────────────────────────────────────────── --}}
    <div x-show="tab === 'participants'" x-transition>

        @if($mockExam->participant_mode !== 'configured')
            <x-ui.card>
                <div class="p-6 text-center text-sm text-slate-500">
                    This exam uses <strong>general</strong> participant mode — no pre-registration needed.
                </div>
            </x-ui.card>
        @else
            <div class="grid md:grid-cols-2 gap-4 mb-4">
                {{-- Add single --}}
                <x-ui.card>
                    <x-ui.card-header title="Add Participant" accent="success" />
                    <form method="POST" action="{{ route('mock-exams.participants.store', $mockExam) }}" class="p-5 space-y-3">
                        @csrf
                        <x-ui.input name="name" label="Name" required placeholder="Student name" />
                        <x-ui.input name="email" type="email" label="Email" required placeholder="student@example.com" />
                        <x-ui.input name="unique_code" label="Unique Code (optional)" placeholder="e.g. STU-001" />
                        <x-ui.button type="submit" variant="success" icon="user-plus">Add Participant</x-ui.button>
                    </form>
                </x-ui.card>

                {{-- CSV import --}}
                <x-ui.card>
                    <x-ui.card-header title="Import from CSV" accent="info" />
                    <form method="POST" action="{{ route('mock-exams.participants.import', $mockExam) }}"
                          enctype="multipart/form-data" class="p-5 space-y-3">
                        @csrf
                        <p class="text-xs text-slate-500 dark:text-slate-400">
                            CSV format: <code class="bg-slate-100 dark:bg-slate-800 px-1 rounded">name, email, unique_code</code> (header row optional).
                        </p>
                        <input type="file" name="csv_file" accept=".csv,.txt"
                               class="w-full text-sm text-slate-600 dark:text-slate-300 file:mr-3 file:py-2 file:px-4 file:rounded-[2px] file:border-0 file:text-sm file:font-medium file:bg-violet-50 file:text-violet-700 dark:file:bg-violet-900/30 dark:file:text-violet-400 hover:file:bg-violet-100">
                        <x-ui.button type="submit" variant="info" icon="arrow-up-tray">Import CSV</x-ui.button>
                    </form>
                </x-ui.card>
            </div>

            {{-- Participant list --}}
            <x-ui.card>
                <x-ui.card-header title="Registered Participants ({{ $mockExam->configuredParticipants->count() }})" accent="primary" />
                @if($mockExam->configuredParticipants->isEmpty())
                    <div class="py-8 text-center text-sm text-slate-400">No participants added yet.</div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-slate-100 dark:border-slate-800">
                                    <th class="text-left px-5 py-3 text-xs font-medium text-slate-500 uppercase tracking-wider">Name</th>
                                    <th class="text-left px-5 py-3 text-xs font-medium text-slate-500 uppercase tracking-wider">Email</th>
                                    <th class="text-left px-5 py-3 text-xs font-medium text-slate-500 uppercase tracking-wider">Code</th>
                                    <th class="px-5 py-3"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50 dark:divide-slate-800/50">
                                @foreach($mockExam->configuredParticipants as $p)
                                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30">
                                        <td class="px-5 py-3 font-medium text-slate-800 dark:text-slate-200">{{ $p->name }}</td>
                                        <td class="px-5 py-3 text-slate-500">{{ $p->email }}</td>
                                        <td class="px-5 py-3 font-mono text-slate-500">{{ $p->unique_code ?? '—' }}</td>
                                        <td class="px-5 py-3 text-right">
                                            <form method="POST" action="{{ route('mock-exams.participants.destroy', [$mockExam, $p]) }}">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-xs text-red-500 hover:text-red-700">Remove</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </x-ui.card>
        @endif
    </div>

    {{-- ──────────────────────────────────────────────────────────────────────
         TAB: Results
    ────────────────────────────────────────────────────────────────────────── --}}
    <div x-show="tab === 'results'" x-transition>
        <x-ui.card>
            <x-ui.card-header title="Submissions" accent="primary" />
            @if($submissions->isEmpty())
                <div class="py-10 text-center text-sm text-slate-400">No submissions yet.</div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-slate-100 dark:border-slate-800">
                                <th class="text-left px-5 py-3 text-xs font-medium text-slate-500 uppercase">Participant</th>
                                <th class="text-left px-5 py-3 text-xs font-medium text-slate-500 uppercase">Submitted</th>
                                <th class="text-left px-5 py-3 text-xs font-medium text-slate-500 uppercase">Score</th>
                                <th class="text-left px-5 py-3 text-xs font-medium text-slate-500 uppercase">Grade</th>
                                <th class="text-left px-5 py-3 text-xs font-medium text-slate-500 uppercase">Status</th>
                                <th class="px-5 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50 dark:divide-slate-800/50">
                            @foreach($submissions as $sub)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30">
                                    <td class="px-5 py-3">
                                        <p class="font-medium text-slate-800 dark:text-slate-200">{{ $sub->participant_name }}</p>
                                        <p class="text-xs text-slate-400">{{ $sub->participant_email }}</p>
                                    </td>
                                    <td class="px-5 py-3 text-slate-500">
                                        {{ $sub->submitted_at ? $sub->submitted_at->format('M d, H:i') : '—' }}
                                    </td>
                                    <td class="px-5 py-3">
                                        @if($sub->submitted_at)
                                            <span class="font-medium text-slate-700 dark:text-slate-300">
                                                {{ number_format($sub->score ?? 0, 1) }} / {{ number_format($sub->total_marks ?? 0, 1) }}
                                            </span>
                                            <span class="text-xs text-slate-400 ml-1">({{ number_format($sub->percentage ?? 0, 1) }}%)</span>
                                        @else
                                            <span class="text-slate-400">In progress</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3">
                                        @if($sub->grade)
                                            <span class="px-2 py-0.5 rounded text-xs font-bold bg-violet-100 text-violet-700 dark:bg-violet-900/30 dark:text-violet-400">
                                                {{ $sub->grade }}
                                            </span>
                                        @else
                                            <span class="text-slate-400">—</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3">
                                        <span class="text-xs text-slate-500">{{ str_replace('_', ' ', ucfirst($sub->status)) }}</span>
                                        @if($sub->requires_manual_review)
                                            <span class="ml-1 text-xs text-amber-600 font-medium">⚠ Review</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3 text-right">
                                        <a href="{{ route('mock-exams.results.show', [$mockExam, $sub]) }}"
                                           class="text-xs text-violet-600 hover:text-violet-800 font-medium">Detail</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="px-5 py-3 border-t border-slate-100 dark:border-slate-800">
                    {{ $submissions->links() }}
                </div>
            @endif
        </x-ui.card>
    </div>

</div>{{-- x-data --}}

</x-layouts.app>
