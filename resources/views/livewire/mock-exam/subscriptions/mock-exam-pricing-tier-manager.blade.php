<x-exam.page>
    <x-exam.header
        eyebrow="CANDIDATE DESK · SUBSCRIPTIONS, RESULTS & PERFORMANCE"
        title="My Exams"
        sub="Manage your exam subscriptions, review candidate results, and track participant performance across cycles."
        :lines="['MY', 'DESK']"
    />

    <x-exam.flash />

    <x-exam.tabs
        :tabs="['subscriptions' => 'My Subscriptions', 'results' => 'Results & Scores', 'performance' => 'Participant Performance']"
        :active="$activeTab"
        property="activeTab"
    />

    {{-- ==================== SUBSCRIPTIONS TAB ==================== --}}
    @if($activeTab === 'subscriptions')
        <div class="animate-rise">
            <div class="mb-5 flex items-center justify-between gap-4">
                <p class="font-mono text-[10.5px] tracking-[0.14em] text-muted">MY SUBSCRIPTIONS · {{ count($mockSubscriptions) }}</p>
                <x-exam.button type="button" wire:click="openPurchaseForm">+ New Subscription</x-exam.button>
            </div>

            {{-- Purchase form --}}
            @if($showPurchaseForm)
                <x-exam.card class="mb-6 animate-rise" :padded="false">
                    <x-slot:header>
                        <p class="font-mono text-[10.5px] tracking-[0.14em] text-muted">CONFIGURE SUBSCRIPTION</p>
                    </x-slot:header>

                    <div class="space-y-5 p-5">
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <x-exam.field bare label="I am subscribing as">
                                <x-exam.select wire:model.live="userGroup">
                                    @foreach($userGroupOptions as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </x-exam.select>
                            </x-exam.field>

                            <x-exam.field bare label="Delivery Type">
                                <x-exam.select wire:model.live="type">
                                    <option value="print">Print</option>
                                    <option value="online">Online</option>
                                </x-exam.select>
                            </x-exam.field>
                        </div>

                        <x-exam.field bare label="Subjects (select all that apply)">
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div>
                                    <label class="mb-1.5 block font-mono text-[10px] uppercase tracking-[0.14em] text-muted">Academic Group</label>
                                    <x-exam.select wire:model.live="selectedAcademicGroupId" placeholder="Select group…">
                                        @foreach($this->academicGroups as $group)
                                            <option value="{{ $group->id }}">{{ $group->name }}</option>
                                        @endforeach
                                    </x-exam.select>
                                </div>
                                <div>
                                    <label class="mb-1.5 block font-mono text-[10px] uppercase tracking-[0.14em] text-muted">Academic Level</label>
                                    <x-exam.select wire:model.live="selectedAcademicLevelId" placeholder="Select level…" :disabled="!$selectedAcademicGroupId">
                                        @foreach($this->academicLevels as $level)
                                            <option value="{{ $level->id }}">{{ $level->name }}</option>
                                        @endforeach
                                    </x-exam.select>
                                </div>
                            </div>

                            <div class="mt-3">
                                <x-exam.chips :items="$this->selectedSubjects" remove-action="removeSubject" with-meta />

                                <div class="relative">
                                    <x-exam.input wire:model.live="subjectSearch" placeholder="Type to search subjects in selected level…" />
                                    <x-exam.picklist
                                        :items="$this->subjectSearchResults"
                                        action="addSubject"
                                        :empty="strlen($subjectSearch) >= 1 ? 'No subjects found. Ask your administrator to create subjects first.' : null"
                                    />
                                </div>

                                @if($selectedAcademicLevelId)
                                    <x-exam.picklist :items="$this->filteredSubjects" action="addSubject" :absolute="false" empty="No available subjects in this level." />
                                @endif
                            </div>
                            @error('selectedSubjectIds') <p class="mt-2 font-mono text-[11.5px] text-accent">▲ {{ $message }}</p> @enderror
                        </x-exam.field>

                        @if($type === 'online')
                            <x-exam.field bare label="Number of Participants" class="sm:max-w-xs">
                                <x-exam.input type="number" min="1" wire:model.live="participantCount" />
                            </x-exam.field>
                        @endif

                        <x-exam.field bare label="Exam Cycles Per Subject" class="sm:max-w-xs">
                            <x-exam.input type="number" min="1" wire:model.live="maxExams" />
                            <p class="mt-1.5 text-xs text-muted">Total generations = selected subjects × cycles per subject.</p>
                        </x-exam.field>

                        @if($calculatedPrice > 0)
                            <x-exam.callout>
                                <p class="font-display text-lg font-bold text-primary">Total: GHS {{ number_format($calculatedPrice, 2) }}</p>
                                @if($selectedPricingTier)
                                    <p class="mt-1 text-sm text-primary/80">Tier: {{ $selectedPricingTier->name }}</p>
                                @endif
                                <p class="mt-1 font-mono text-[11px] tracking-wide text-muted">
                                    {{ count($selectedSubjectIds) }} SUBJECT(S) · {{ $userGroupOptions[$userGroup] ?? $userGroup }} · {{ $maxExams ?? 1 }} CYCLE(S)@if($type === 'online') · {{ $participantCount }} PARTICIPANT(S) @endif
                                </p>
                            </x-exam.callout>
                        @endif
                    </div>

                    <x-slot:footer>
                        <div class="flex justify-end gap-2">
                            <x-exam.button type="button" variant="ghost" wire:click="$set('showPurchaseForm', false)">Cancel</x-exam.button>
                            <x-exam.button type="button" wire:click="purchase">Proceed to Payment</x-exam.button>
                        </div>
                    </x-slot:footer>
                </x-exam.card>
            @endif

            {{-- Top-up modal --}}
            @if($showTopUpForm)
                <x-exam.modal title="Add Participant Slots">
                    <x-exam.field bare label="Additional Participants">
                        <x-exam.input type="number" min="1" wire:model.live="additionalParticipants" />
                    </x-exam.field>

                    @if($topUpPrice > 0)
                        <p class="mt-4 text-sm text-ink-soft">
                            Top-up cost: <strong class="font-display text-base font-bold text-primary">GHS {{ number_format($topUpPrice, 2) }}</strong>
                        </p>
                    @endif

                    <x-slot:footer>
                        <x-exam.button type="button" variant="ghost" wire:click="$set('showTopUpForm', false)">Cancel</x-exam.button>
                        <x-exam.button type="button" wire:click="processTopUp">Pay & Add Slots</x-exam.button>
                    </x-slot:footer>
                </x-exam.modal>
            @endif

            {{-- Subscriptions list --}}
            <div class="grid gap-4">
                @forelse($mockSubscriptions as $sub)
                    @php($groupLabel = $sub->user_group instanceof \App\MockExam\Models\MockExamUserGroup ? $sub->user_group->label() : ucfirst(str_replace('_', ' ', $sub->user_group ?? '')))
                    <article wire:key="sub-{{ $sub->id }}"
                             class="animate-rise rounded-md border border-line bg-surface p-4 shadow-[0_1px_2px_rgba(20,24,34,0.04)]">
                        <div class="flex items-start justify-between gap-4">
                            <div class="min-w-0">
                                <div class="mb-2 flex flex-wrap items-center gap-2">
                                    <h3 class="font-display text-base font-bold">{{ $sub->plan->name }}</h3>
                                    <x-exam.badge :tone="$sub->type === 'online' ? 'primary' : 'warn'">{{ ucfirst($sub->type) }}</x-exam.badge>
                                    <x-exam.badge :tone="$sub->status->value === 'active' ? 'ok' : 'muted'">{{ ucfirst($sub->status->value) }}</x-exam.badge>
                                </div>

                                <div class="space-y-1 font-mono text-[11px] tracking-wide text-muted">
                                    <p><span class="text-ink-soft">SUBJECTS:</span> {{ $sub->subjects->pluck('name')->join(', ') ?: 'NONE' }}</p>
                                    <p><span class="text-ink-soft">GROUP:</span> {{ $groupLabel }} · {{ $sub->max_exams ?? 1 }} CYCLE(S) PER SUBJECT</p>
                                    @if($sub->expires_at)
                                        <p><span class="text-ink-soft">EXPIRES:</span> {{ $sub->expires_at->format('M d, Y') }}</p>
                                    @endif
                                </div>

                                @if($sub->type === 'online')
                                    @php($slotsPct = $sub->participant_slots > 0 ? min(100, round($sub->participants_used / $sub->participant_slots * 100)) : 0)
                                    <div class="mt-3">
                                        <div class="flex items-baseline justify-between font-mono text-[10.5px] tracking-wider text-muted">
                                            <span>PARTICIPANTS {{ $sub->participants_used }}/{{ $sub->participant_slots }}</span>
                                            <span>EXAMS {{ $sub->exams_used }}{{ $sub->max_exams ? '/' . $sub->max_exams : '' }}</span>
                                        </div>
                                        <div class="mt-1 h-1 w-full overflow-hidden rounded-full bg-line">
                                            <div class="h-full rounded-full bg-primary transition-all" style="width: {{ $slotsPct }}%"></div>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div class="flex shrink-0 flex-col items-end gap-2">
                                <x-exam.link-button wire:click="$set('selectedSubscriptionId', {{ $sub->id }}); $set('activeTab', 'results')">View Results</x-exam.link-button>
                                @if($sub->type === 'online' && $sub->isActive())
                                    <x-exam.link-button tone="muted" wire:click="openTopUp({{ $sub->id }})">Add Slots</x-exam.link-button>
                                @endif
                            </div>
                        </div>
                    </article>
                @empty
                    <x-exam.empty compact title="No subscriptions yet" description="Purchase one to start creating exams." />
                @endforelse
            </div>
        </div>
    @endif

    {{-- ==================== RESULTS TAB ==================== --}}
    @if($activeTab === 'results')
        <div class="animate-rise">
            <div class="mb-5 grid grid-cols-1 gap-3 sm:max-w-2xl sm:grid-cols-2">
                <x-exam.select wire:model.live="selectedSubscriptionId" placeholder="Select subscription…">
                    @foreach($mockSubscriptions as $sub)
                        <option value="{{ $sub->id }}">{{ $sub->plan->name }} — {{ $sub->subjects->pluck('name')->join(', ') }}</option>
                    @endforeach
                </x-exam.select>

                @if($selectedMockSubscription)
                    <x-exam.select wire:model.live="selectedExamId" placeholder="Select exam…">
                        @foreach($mockExams as $exam)
                            <option value="{{ $exam->id }}">{{ $exam->title }} ({{ $exam->submissions_count }} submissions)</option>
                        @endforeach
                    </x-exam.select>
                @endif
            </div>

            @if($selectedExamId && $mockSubmissions->count())
                <x-exam.table :headers="['Participant', 'Score', 'Grade', 'Started', 'Submitted', 'Duration', '']">
                    @foreach($mockSubmissions as $submission)
                        @php($gradeTone = !$submission->grade ? 'muted'
                            : (in_array($submission->grade, ['A+','A']) ? 'ok'
                            : ($submission->grade === 'B' ? 'primary'
                            : ($submission->grade === 'C' ? 'warn' : 'danger'))))
                        <tr wire:key="submission-{{ $submission->id }}" class="transition hover:bg-surface-2/60">
                            <td class="px-4 py-3">
                                <p class="font-semibold text-ink">{{ $submission->getParticipantName() }}</p>
                                <p class="font-mono text-[10.5px] text-muted">{{ $submission->getParticipantEmail() }}</p>
                            </td>
                            <td class="px-4 py-3 text-ink-soft">
                                <span class="font-semibold text-ink">{{ $submission->score }}/{{ $submission->total_marks }}</span>
                                <span class="font-mono text-[10.5px] text-muted">({{ $submission->percentage }}%)</span>
                            </td>
                            <td class="px-4 py-3"><x-exam.badge :tone="$gradeTone">{{ $submission->grade ?? '—' }}</x-exam.badge></td>
                            <td class="px-4 py-3 font-mono text-[11px] text-muted">{{ $submission->started_at?->format('M d, H:i') ?? '—' }}</td>
                            <td class="px-4 py-3 font-mono text-[11px] text-muted">{{ $submission->submitted_at?->format('M d, H:i') ?? '—' }}</td>
                            <td class="px-4 py-3 font-mono text-[11px] text-muted">{{ $submission->time_spent_seconds ? gmdate('H:i:s', $submission->time_spent_seconds) : '—' }}</td>
                            <td class="px-4 py-3">
                                <x-exam.link-button wire:click="openScoreEditor({{ $submission->id }})">Edit Scores</x-exam.link-button>
                            </td>
                        </tr>
                    @endforeach

                    <x-slot:footer>{{ $mockSubmissions->links() }}</x-slot:footer>
                </x-exam.table>
            @elseif($selectedExamId)
                <x-exam.empty compact title="No submissions yet" description="No submissions for this exam yet." />
            @endif

            {{-- Score editor modal --}}
            @if($showScoreEditor && $editingMockSubmission)
                <x-exam.modal max-width="max-w-2xl" title="Edit Scores — {{ $editingMockSubmission->getParticipantName() }}">
                    <div class="space-y-3">
                        @foreach($editingMockSubmission->assignment->questions as $question)
                            <div wire:key="q-{{ $question->id }}" class="rounded-md border border-line bg-surface-2 p-3">
                                <p class="mb-2 text-sm text-ink">{{ $question->question }}</p>
                                <div class="flex items-center gap-3">
                                    <label class="font-mono text-[10.5px] uppercase tracking-wider text-muted">Points (max {{ $question->marks }})</label>
                                    <input type="number" step="0.5" min="0" max="{{ $question->marks }}"
                                           wire:model="editedScores.{{ $question->id }}"
                                           class="w-24 rounded border border-line-strong bg-surface px-3 py-2 text-sm text-ink transition hover:border-ink-soft focus:border-primary focus:outline-none focus:ring-[3px] focus:ring-primary/20">
                                </div>
                            </div>
                        @endforeach

                        <x-exam.field bare label="Reason for change (optional)">
                            <textarea wire:model="scoreEditReason" rows="2"
                                      class="w-full rounded border border-line-strong bg-surface px-3 py-2.5 text-sm text-ink placeholder:text-muted transition hover:border-ink-soft focus:border-primary focus:outline-none focus:ring-[3px] focus:ring-primary/20"></textarea>
                        </x-exam.field>

                        @if($mockAuditLogs->isNotEmpty())
                            <div class="rounded-md border border-line bg-surface-2 p-3">
                                <p class="mb-2 font-mono text-[10px] font-semibold uppercase tracking-[0.14em] text-muted">Audit History</p>
                                <ol class="space-y-1.5">
                                    @foreach($mockAuditLogs as $log)
                                        <li wire:key="log-{{ $log->id }}" class="text-xs leading-relaxed text-muted">
                                            <span class="font-semibold text-ink-soft">{{ $log->editor->name }}</span>
                                            changed score from {{ $log->old_score }} to {{ $log->new_score }}
                                            ({{ $log->old_grade }} → {{ $log->new_grade }})
                                            at {{ $log->created_at->format('M d, H:i') }}
                                            @if($log->reason) — “{{ $log->reason }}” @endif
                                        </li>
                                    @endforeach
                                </ol>
                            </div>
                        @endif
                    </div>

                    <x-slot:footer>
                        <x-exam.button type="button" variant="ghost" wire:click="$set('showScoreEditor', false)">Cancel</x-exam.button>
                        <x-exam.button type="button" wire:click="saveScores" loading="Recalculating…">Save & Recalculate</x-exam.button>
                    </x-slot:footer>
                </x-exam.modal>
            @endif
        </div>
    @endif

    {{-- ==================== PERFORMANCE TAB ==================== --}}
    @if($activeTab === 'performance')
        <div class="animate-rise">
            <div class="mb-6">
                <p class="mb-3 font-mono text-[10.5px] tracking-[0.14em] text-muted">PARTICIPANT PERFORMANCE OVER TIME</p>
                <div class="flex max-w-xl gap-2">
                    <x-exam.input wire:model="performanceSearch" placeholder="Search by name or email…" />
                    <x-exam.button type="button" wire:click="searchPerformance">Search</x-exam.button>
                </div>
                @error('performanceSearch') <p class="mt-2 font-mono text-[11.5px] text-accent">▲ {{ $message }}</p> @enderror
            </div>

            @if(!empty($performanceResults))
                <div class="mb-6 grid grid-cols-2 gap-4 sm:grid-cols-4">
                    <x-exam.stat label="Exams Taken" :value="$performanceResults['total_exams']" />
                    <x-exam.stat label="Average Score" :value="number_format($performanceResults['average_score'], 1) . '%'" />
                    <x-exam.stat label="Highest Score" :value="number_format($performanceResults['highest_score'], 1) . '%'" tone="ok" />
                    <x-exam.stat label="Lowest Score" :value="number_format($performanceResults['lowest_score'], 1) . '%'" tone="danger" />
                </div>

                <x-exam.table :headers="['Exam', 'Score', 'Grade', 'Date']">
                    @foreach($performanceResults['submissions'] as $submission)
                        @php($perfTone = !$submission->grade ? 'muted'
                            : (in_array($submission->grade, ['A+','A']) ? 'ok'
                            : ($submission->grade === 'B' ? 'primary' : 'danger')))
                        <tr wire:key="perf-{{ $submission->id }}" class="transition hover:bg-surface-2/60">
                            <td class="px-4 py-3 font-semibold text-ink">{{ $submission->assignment->title }}</td>
                            <td class="px-4 py-3 text-ink-soft">
                                {{ $submission->score }}/{{ $submission->total_marks }}
                                <span class="font-mono text-[10.5px] text-muted">({{ $submission->percentage }}%)</span>
                            </td>
                            <td class="px-4 py-3"><x-exam.badge :tone="$perfTone">{{ $submission->grade ?? '—' }}</x-exam.badge></td>
                            <td class="px-4 py-3 font-mono text-[11px] text-muted">{{ $submission->submitted_at?->format('M d, Y') }}</td>
                        </tr>
                    @endforeach
                </x-exam.table>
            @endif
        </div>
    @endif
</x-exam.page>