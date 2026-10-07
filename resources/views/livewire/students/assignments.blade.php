<x-exam.page>
    <x-exam.header
        eyebrow="STUDENT RECORDS · ASSIGNMENT LEDGER"
        title="My Assignments"
        sub="Complete your assignments and track your progress."
        :lines="['TASK', 'SHEET']"
    />

    <x-exam.flash />

    @php
        $availableCount = $assignments->where(fn ($a) => $this->getAssignmentStatus($a) === 'not_started')->count();
        $completedCount = $assignments->where(fn ($a) => $this->getAssignmentStatus($a) === 'completed')->count();
    @endphp

    {{-- Registry counters --}}
    <div class="mb-5 flex flex-wrap items-center gap-2.5">
        <p class="mr-1 font-mono text-[10.5px] tracking-[0.14em] text-muted">REGISTRY · {{ $assignments->total() }} ASSIGNMENTS</p>
        <x-exam.badge tone="warn">{{ $availableCount }} AVAILABLE</x-exam.badge>
        <x-exam.badge tone="ok">{{ $completedCount }} COMPLETED</x-exam.badge>
    </div>

    {{-- Filters --}}
    <x-exam.card class="mb-6" :padded="false">
        <x-slot:header>
            <p class="font-mono text-[10.5px] tracking-[0.14em] text-muted">FILTERS · REFINE THE LIST</p>
        </x-slot:header>

        <div class="grid grid-cols-1 gap-4 p-5 sm:grid-cols-2 lg:grid-cols-5">
            {{-- Search (drives the existing $search property) --}}
            <x-exam.field bare label="Search" class="lg:col-span-2">
                <x-exam.input wire:model.live.debounce.300ms="search" placeholder="Search by title…" />
            </x-exam.field>

            <x-exam.field bare label="Status">
                <x-exam.select wire:model.live="statusFilter">
                    <option value="all">All Statuses</option>
                    <option value="pending">Pending</option>
                    <option value="completed">Completed</option>
                </x-exam.select>
            </x-exam.field>

            <x-exam.field bare label="Subject">
                <x-exam.select wire:model.live="subjectFilter">
                    <option value="all">All Subjects</option>
                    @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                    @endforeach
                </x-exam.select>
            </x-exam.field>

            {{-- VERIFY: option values must match your $sortBy expectations --}}
            <x-exam.field bare label="Sort By">
                <x-exam.select wire:model.live="sortBy">
                    <option value="date">Sort by Date</option>
                    <option value="title">Sort by Title</option>
                    <option value="ends_at">Sort by Deadline</option>
                </x-exam.select>
            </x-exam.field>
        </div>

        <x-slot:footer>
            <div class="flex justify-end">
                <x-exam.link-button tone="muted" wire:click="resetFilters">RESET FILTERS</x-exam.link-button>
            </div>
        </x-slot:footer>
    </x-exam.card>

    {{-- ============ Assignment table ============ --}}
    {{-- VERIFY: sort + row-action handler names (toggleSort, startAssignment, continueAssignment, viewResults, viewDetails) --}}
    <x-exam.table>
        <x-slot:head>
            <tr class="border-b border-line bg-surface-2">
                <x-exam.th-sort field="title" label="Assignment" :sort-by="$sortBy" :direction="$sortDirection" />
                <th class="whitespace-nowrap px-4 py-3 text-left font-mono text-[10px] font-semibold uppercase tracking-[0.14em] text-muted">Subject</th>
                <th class="whitespace-nowrap px-4 py-3 text-left font-mono text-[10px] font-semibold uppercase tracking-[0.14em] text-muted">Status</th>
                <th class="whitespace-nowrap px-4 py-3 text-left font-mono text-[10px] font-semibold uppercase tracking-[0.14em] text-muted">Progress</th>
                <x-exam.th-sort field="ends_at" label="Deadline" :sort-by="$sortBy" :direction="$sortDirection" />
                <th class="whitespace-nowrap px-4 py-3 text-right font-mono text-[10px] font-semibold uppercase tracking-[0.14em] text-muted">Actions</th>
            </tr>
        </x-slot:head>

        @forelse($assignments as $assignment)
            @php
                $status    = $this->getAssignmentStatus($assignment);
                $progress  = $this->getAssignmentProgress($assignment);
                $isOverdue = $assignment->ends_at < now();
                $timeLeft  = now()->diffInHours($assignment->ends_at, true);
            @endphp

            <tr wire:key="assignment-{{ $assignment->id }}" class="transition hover:bg-surface-2/60">
                {{-- Assignment --}}
                <td class="px-4 py-3.5">
                    <p class="font-semibold text-ink">{{ $assignment->title }}</p>
                    <p class="mt-0.5 font-mono text-[10.5px] tracking-wide text-muted">
                        {{ $assignment->duration_in_minutes }} MIN
                        @if($assignment->total_marks) · {{ $assignment->total_marks }} MARKS @endif
                    </p>
                    @if($assignment->description)
                        <p class="mt-1 max-w-md truncate text-xs text-muted">{{ Str::limit($assignment->description, 60) }}</p>
                    @endif
                </td>

                {{-- Subject --}}
                <td class="px-4 py-3.5">
                    <x-exam.badge tone="muted">{{ $assignment->academicSubject->name ?? 'Unknown' }}</x-exam.badge>
                </td>

                {{-- Status --}}
                <td class="px-4 py-3.5">
                    @if($status === 'completed')
                        <x-exam.badge tone="ok">Done</x-exam.badge>
                    @elseif($status === 'in_progress')
                        <x-exam.badge tone="primary">Active</x-exam.badge>
                    @elseif($isOverdue)
                        <x-exam.badge tone="danger">Late</x-exam.badge>
                    @else
                        <x-exam.badge tone="warn">Open</x-exam.badge>
                    @endif
                </td>

                {{-- Progress --}}
                <td class="px-4 py-3.5">
                    @if($progress > 0)
                        <div class="w-24"><x-exam.progress :value="$progress" /></div>
                        <span class="mt-1 block font-mono text-[10px] tracking-wide text-muted">{{ $progress }}%</span>
                    @else
                        <span class="text-muted">—</span>
                    @endif
                </td>

                {{-- Deadline --}}
                <td class="px-4 py-3.5">
                    <p class="font-mono text-[11px] text-ink-soft">{{ $assignment->ends_at->format('M j, Y') }}</p>
                    <p class="mt-0.5 font-mono text-[10px] tracking-wide {{ $isOverdue ? 'text-accent' : ($timeLeft < 24 ? 'text-warn' : 'text-muted') }}">
                        @if($isOverdue)
                            OVERDUE
                        @elseif($timeLeft < 1)
                            &lt; 1 HOUR LEFT
                        @elseif($timeLeft < 24)
                            {{ getTimeRemaining($assignment->ends_at) }}
                        @else
                            {{ ceil($timeLeft / 24) }} DAYS LEFT
                        @endif
                    </p>
                </td>

                {{-- Actions --}}
                <td class="px-4 py-3.5">
                    <div class="flex items-center justify-end gap-3">
                        @if($status === 'completed')
                            <x-exam.link-button wire:click="viewResults({{ $assignment->id }})">RESULTS</x-exam.link-button>
                        @elseif($status === 'in_progress')
                            <x-exam.link-button wire:click="continueAssignment({{ $assignment->id }})">CONTINUE</x-exam.link-button>
                        @elseif(!$isOverdue)
                            <x-exam.link-button wire:click="startAssignment({{ $assignment->id }})">START</x-exam.link-button>
                        @else
                            <span class="font-mono text-[11px] uppercase tracking-wider text-muted">EXPIRED</span>
                        @endif
                        <x-exam.link-button tone="muted" wire:click="viewAssignment({{ $assignment->id }})">VIEW</x-exam.link-button>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="px-4 py-6">
                    <x-exam.empty compact
                                  title="No assignments found"
                                  :description="($search || $statusFilter !== 'all' || $subjectFilter !== 'all')
                            ? 'Try adjusting your search or filters.'
                            : 'No assignments have been assigned to you yet.'"
                    />
                </td>
            </tr>
        @endforelse

        @if($assignments->hasPages())
            <x-slot:footer>{{ $assignments->links() }}</x-slot:footer>
        @endif
    </x-exam.table>

    {{-- ============ Detail modal ============ --}}
    @if($showDetails && $selectedAssignment)
        @php
            $status         = $this->getAssignmentStatus($selectedAssignment);
            $progress       = $this->getAssignmentProgress($selectedAssignment);
            $isOverdue      = $selectedAssignment->ends_at < now();
            $isUpcoming     = $selectedAssignment->starts_at > now();
            $timeLeft       = now()->diffInHours($selectedAssignment->ends_at, false);
            $timeUntilStart = $isUpcoming ? now()->diffInHours($selectedAssignment->starts_at, false) : 0;
            $questionStats  = $selectedAssignment->getQuestionStatistics();
        @endphp

        <x-exam.modal max-width="max-w-3xl">
            <div x-data="{ tab: 'overview' }">

                {{-- Modal header --}}
                <div class="mb-5 flex items-start gap-3.5">
                    @if($status === 'completed')
                        <div class="grid h-11 w-11 shrink-0 place-items-center rounded-full bg-ok/10 ring-1 ring-inset ring-ok/30">
                            <svg class="h-5 w-5 text-ok" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    @elseif($status === 'in_progress')
                        <div class="grid h-11 w-11 shrink-0 place-items-center rounded-full bg-primary/10 ring-1 ring-inset ring-primary/30">
                            <svg class="h-5 w-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </div>
                    @elseif($isOverdue)
                        <div class="grid h-11 w-11 shrink-0 place-items-center rounded-full bg-accent/10 ring-1 ring-inset ring-accent/30">
                            <svg class="h-5 w-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    @else
                        <div class="grid h-11 w-11 shrink-0 place-items-center rounded-full bg-warn/10 ring-1 ring-inset ring-warn/30">
                            <svg class="h-5 w-5 text-warn" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    @endif

                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <h3 class="font-display text-lg font-bold leading-tight text-ink">{{ $selectedAssignment->title }}</h3>
                            <x-exam.badge tone="muted">{{ ucfirst($selectedAssignment->type) }}</x-exam.badge>
                        </div>
                        <p class="mt-1 font-mono text-[10.5px] tracking-wide text-muted">
                            {{ strtoupper($selectedAssignment->academicSubject->name ?? 'Unknown Subject') }} · {{ strtoupper($selectedAssignment->teacher->user->name ?? 'Unknown Teacher') }}
                            @if($questionStats['total_questions'] > 0) · {{ $questionStats['total_questions'] }} QUESTIONS @endif
                        </p>
                        <div class="mt-2">
                            @if($status === 'completed')
                                <x-exam.badge tone="ok">Completed</x-exam.badge>
                            @elseif($status === 'in_progress')
                                <x-exam.badge tone="primary">In Progress</x-exam.badge>
                            @elseif($isOverdue)
                                <x-exam.badge tone="danger">Overdue</x-exam.badge>
                            @elseif($isUpcoming)
                                <x-exam.badge tone="warn">Starts {{ $timeUntilStart < 1 ? 'soon' : 'in ' . ceil($timeUntilStart / 24) . ' day(s)' }}</x-exam.badge>
                            @else
                                <x-exam.badge tone="warn">Available</x-exam.badge>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Progress --}}
                @if($progress > 0)
                    <div class="mb-5">
                        <div class="mb-1.5 flex items-center justify-between font-mono text-[10px] tracking-wider text-muted">
                            <span>PROGRESS</span>
                            <span class="text-ink">{{ $progress }}%</span>
                        </div>
                        <x-exam.progress :value="$progress" size="lg" />
                    </div>
                @endif

                {{-- Inner tabs --}}
                <div class="mb-4 flex flex-wrap gap-1 border-b border-line">
                    @foreach([['overview', 'Overview'], ['questions', 'Questions'], ['timeline', 'Timeline']] as [$key, $label])
                        @if($key !== 'questions' || $questionStats['total_questions'] > 0)
                            <button type="button" @click="tab = '{{ $key }}'"
                                    class="relative -mb-px rounded-t border px-3.5 py-2 font-mono text-[10.5px] font-semibold uppercase tracking-[0.12em] transition
                                           {{ '' }}"
                                    :class="tab === '{{ $key }}'
                                        ? 'border-line border-b-surface bg-surface text-ink'
                                        : 'border-transparent text-muted hover:text-ink'">
                                @if($key === 'questions') {{ $label }} ({{ $questionStats['total_questions'] }}) @else {{ $label }} @endif
                                <span x-show="tab === '{{ $key }}'" class="absolute inset-x-0 top-0 h-0.5 bg-accent"></span>
                            </button>
                        @endif
                    @endforeach
                </div>

                {{-- Overview --}}
                <div x-show="tab === 'overview'" class="space-y-4">
                    @if($selectedAssignment->description)
                        <div class="rounded-md border border-line bg-surface-2 p-4">
                            <p class="mb-1.5 font-mono text-[10px] uppercase tracking-[0.14em] text-muted">Description</p>
                            <p class="text-sm leading-relaxed text-ink-soft">{{ $selectedAssignment->description }}</p>
                        </div>
                    @endif

                    @if($selectedAssignment->instructions)
                        <div class="rounded-md border border-line bg-surface-2 p-4">
                            <p class="mb-1.5 font-mono text-[10px] uppercase tracking-[0.14em] text-muted">Instructions</p>
                            <div class="text-sm leading-relaxed text-ink-soft">{!! $selectedAssignment->instructions !!}</div>
                        </div>
                    @endif

                    {{-- Details grid --}}
                    <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                        <div class="rounded-md border border-line bg-surface-2 p-3 text-center">
                            <p class="font-display text-lg font-bold text-ink">{{ $selectedAssignment->duration_in_minutes }}</p>
                            <p class="mt-0.5 font-mono text-[9.5px] tracking-wider text-muted">MINUTES</p>
                        </div>
                        @if($selectedAssignment->total_marks)
                            <div class="rounded-md border border-line bg-surface-2 p-3 text-center">
                                <p class="font-display text-lg font-bold text-ink">{{ $selectedAssignment->total_marks }}</p>
                                <p class="mt-0.5 font-mono text-[9.5px] tracking-wider text-muted">TOTAL MARKS</p>
                            </div>
                        @endif
                        <div class="rounded-md border border-line bg-surface-2 p-3 text-center">
                            <p class="font-display text-lg font-bold capitalize text-ink">{{ $selectedAssignment->type }}</p>
                            <p class="mt-0.5 font-mono text-[9.5px] tracking-wider text-muted">TYPE</p>
                        </div>
                        <div class="rounded-md border border-line bg-surface-2 p-3 text-center">
                            <p class="font-display text-lg font-bold text-ink">{{ $selectedAssignment->is_randomized ? 'Yes' : 'No' }}</p>
                            <p class="mt-0.5 font-mono text-[9.5px] tracking-wider text-muted">RANDOMIZED</p>
                        </div>
                    </div>

                    @if($selectedAssignment->is_randomized)
                        <p class="font-mono text-[10.5px] tracking-wide text-muted">◉ EACH STUDENT WILL RECEIVE DIFFERENT QUESTIONS</p>
                    @endif
                </div>

                {{-- Questions --}}
                @if($questionStats['total_questions'] > 0)
                    <div x-show="tab === 'questions'" class="space-y-4">
                        <div class="flex flex-wrap gap-2">
                            @foreach($questionStats['by_type'] as $type => $count)
                                <span class="inline-flex items-center gap-2 rounded-md border border-line bg-surface-2 px-3 py-2">
                                    <span class="font-display text-lg font-bold text-primary">{{ $count }}</span>
                                    <span class="font-mono text-[10px] uppercase tracking-[0.12em] text-muted">{{ str_replace('_', ' ', $type) }}</span>
                                </span>
                            @endforeach
                        </div>

                        @if($questionStats['estimated_duration'] > 0)
                            <x-exam.callout>
                                <p class="text-sm text-ink-soft">
                                    <b class="font-semibold text-ink">Estimated time:</b>
                                    approximately {{ $questionStats['estimated_duration'] }} minutes for the questions.
                                    Total assignment time is {{ $selectedAssignment->duration_in_minutes }} minutes.
                                </p>
                            </x-exam.callout>
                        @endif
                    </div>
                @endif

                {{-- Timeline --}}
                <div x-show="tab === 'timeline'" class="space-y-3">
                    <div class="flex items-start gap-3 rounded-md border border-line bg-surface-2 p-3.5">
                        <svg class="mt-0.5 h-4 w-4 shrink-0 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <div>
                            <p class="text-sm font-semibold text-ink">Assignment Opens</p>
                            <p class="mt-0.5 font-mono text-[11px] text-muted">{{ $selectedAssignment->starts_at->format('M j, Y \a\t g:i A') }}</p>
                            @if($isUpcoming)
                                <p class="mt-1 font-mono text-[10px] tracking-wide text-warn">
                                    {{ $timeUntilStart < 1 ? 'OPENS IN LESS THAN AN HOUR' : 'OPENS IN ' . ceil($timeUntilStart) . ' HOUR(S)' }}
                                </p>
                            @endif
                        </div>
                    </div>

                    @if(!$isUpcoming && !$isOverdue)
                        <div class="flex items-start gap-3 rounded-md border border-ok/30 bg-ok/10 p-3.5">
                            <svg class="mt-0.5 h-4 w-4 shrink-0 text-ok" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <div>
                                <p class="text-sm font-semibold text-ok">Currently Active</p>
                                <p class="mt-0.5 font-mono text-[11px] text-muted">{{ now()->format('M j, Y \a\t g:i A') }}</p>
                            </div>
                        </div>
                    @endif

                    <div class="flex items-start gap-3 rounded-md border border-line bg-surface-2 p-3.5">
                        <svg class="mt-0.5 h-4 w-4 shrink-0 {{ $isOverdue ? 'text-accent' : 'text-warn' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <div>
                            <p class="text-sm font-semibold text-ink">Assignment Closes</p>
                            <p class="mt-0.5 font-mono text-[11px] text-muted">{{ $selectedAssignment->ends_at->format('M j, Y \a\t g:i A') }}</p>
                            <p class="mt-1 font-mono text-[10px] tracking-wide {{ $isOverdue ? 'text-accent' : 'text-warn' }}">
                                @if($isOverdue)
                                    ASSIGNMENT ENDED
                                @elseif($timeLeft < 1)
                                    LESS THAN 1 HOUR REMAINING
                                @elseif($timeLeft < 24)
                                    {{ ceil($timeLeft) }} HOUR(S) REMAINING
                                @else
                                    {{ ceil($timeLeft / 24) }} DAY(S) REMAINING
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <x-slot:footer>
                <div class="flex w-full flex-wrap items-center justify-between gap-3">
                    <p class="font-mono text-[10px] tracking-wider text-muted">
                        @if($status === 'completed')
                            ASSIGNMENT COMPLETED
                        @elseif($selectedAssignment->is_randomized)
                            RANDOMIZED QUESTIONS
                        @endif
                    </p>
                    <div class="flex gap-2">
                        <x-exam.button type="button" variant="ghost" wire:click="$set('showDetails', false)">Close</x-exam.button>

                        @if($status === 'completed')
                            <x-exam.button type="button" wire:click="viewResults({{ $selectedAssignment->id }})">View Results</x-exam.button>
                        @elseif(!$isOverdue)
                            @if($status === 'in_progress')
                                <x-exam.button type="button" wire:click="continueAssignment({{ $selectedAssignment->id }})">Continue Assignment</x-exam.button>
                            @elseif($isUpcoming)
                                <x-exam.button type="button" disabled>Not Available Yet</x-exam.button>
                            @else
                                <x-exam.button type="button" wire:click="startAssignment({{ $selectedAssignment->id }})">Start Assignment</x-exam.button>
                            @endif
                        @endif
                    </div>
                </div>
            </x-slot:footer>
        </x-exam.modal>
    @endif

    <style>[x-cloak] { display: none !important; }</style>
</x-exam.page>
