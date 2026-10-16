<x-exam.page>
    <x-exam.header eyebrow="CANDIDATE DESK · SUBSCRIPTIONS, RESULTS & PERFORMANCE" title="Mock Exam Desk" />

    <x-exam.flash />

    <x-exam.tabs :tabs="[
        'subscriptions' => 'My Subscriptions',
        'results' => 'Results & Scores',
        'performance' => 'Participant Performance',
        'identity' => 'My Identity',
    ]" :active="$activeTab" property="activeTab" />

    {{-- ==================== SUBSCRIPTIONS TAB ==================== --}}
    @if ($activeTab === 'subscriptions')
        <div class="animate-rise">
            <div class="mb-5 flex items-center justify-between gap-4">
                <p class="font-mono text-[10.5px] tracking-[0.14em] text-muted">MY SUBSCRIPTIONS ·
                    {{ count($mockSubscriptions) }}</p>
                <x-exam.button type="button" wire:click="openPurchaseForm">+ New Subscription</x-exam.button>
            </div>

            <!-- Search and Filter Controls -->
            <div class="mb-6 flex flex-col sm:flex-row gap-4">
                <div class="flex-1">
                    <x-exam.input wire:model.live.debounce.300ms="search"
                        placeholder="Search subscriptions, mocks, subjects..." icon="search" />
                </div>

                <div class="flex gap-2">
                    <x-exam.select wire:model.live="sortBy">
                        <option value="created_at">Date Created</option>
                        <option value="plan.name">Plan Name</option>
                        <option value="expires_at">Expiration Date</option>
                    </x-exam.select>

                    <x-exam.select wire:model.live="sortDirection">
                        <option value="desc">Descending</option>
                        <option value="asc">Ascending</option>
                    </x-exam.select>
                </div>
            </div>

            {{-- Purchase form --}}
            @if ($showPurchaseForm)
                <x-exam.card class="mb-6 animate-rise" :padded="false">
                    <x-slot:header>
                        <p class="font-mono text-[10.5px] tracking-[0.14em] text-muted">CONFIGURE SUBSCRIPTION</p>
                    </x-slot:header>

                    <div class="space-y-5 p-5">
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <x-exam.field bare label="I am subscribing as">
                                <x-exam.select wire:model.live="userGroup">
                                    @foreach ($userGroupOptions as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </x-exam.select>
                            </x-exam.field>

                            <x-exam.field bare label="Delivery Type">
                                <x-exam.select wire:model.live="type">
                                    <option selected value="print">Print</option>
                                    <option disabled value="online">Online</option>
                                </x-exam.select>
                            </x-exam.field>
                        </div>

                        <x-exam.field bare label="Subjects (select all that apply)">
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div>
                                    <label
                                        class="mb-1.5 block font-mono text-[10px] uppercase tracking-[0.14em] text-muted">Academic
                                        Group</label>
                                    <x-exam.select wire:model.live="selectedAcademicGroupId"
                                        placeholder="Select group…">
                                        @foreach ($this->academicGroups as $group)
                                            <option value="{{ $group->id }}">{{ $group->name }}</option>
                                        @endforeach
                                    </x-exam.select>
                                </div>
                                <div>
                                    <label
                                        class="mb-1.5 block font-mono text-[10px] uppercase tracking-[0.14em] text-muted">Academic
                                        Level</label>
                                    <x-exam.select wire:model.live="selectedAcademicLevelId" placeholder="Select level…"
                                        :disabled="!$selectedAcademicGroupId">
                                        @foreach ($this->academicLevels as $level)
                                            <option value="{{ $level->id }}">{{ $level->name }}</option>
                                        @endforeach
                                    </x-exam.select>
                                </div>
                            </div>

                            <div class="mt-3">
                                <x-exam.chips :items="$this->selectedSubjects" remove-action="removeSubject" with-meta />

                                <div class="relative">
                                    <x-exam.input wire:model.live="subjectSearch"
                                        placeholder="Type to search subjects in selected level…" />
                                    <x-exam.picklist :items="$this->subjectSearchResults" action="addSubject" :empty="strlen($subjectSearch) >= 1
                                        ? 'No subjects found. Ask your administrator to create subjects first.'
                                        : null" />
                                </div>

                                @if ($selectedAcademicLevelId)
                                    <x-exam.picklist :items="$this->filteredSubjects" action="addSubject" :absolute="false"
                                        empty="No available subjects in this level." />
                                @endif
                            </div>
                            @error('selectedSubjectIds')
                                <p class="mt-2 font-mono text-[11.5px] text-accent">▲ {{ $message }}</p>
                            @enderror
                        </x-exam.field>

                        @if ($type === 'online')
                            <x-exam.field bare label="Number of Participants" class="sm:max-w-xs">
                                <x-exam.input type="number" min="1" wire:model.live="participantCount" />
                            </x-exam.field>
                        @endif

                        <x-exam.field bare label="Exam Cycles Per Subject" class="sm:max-w-xs">
                            <x-exam.input type="number" min="1" wire:model.live="maxExams" />
                            <p class="mt-1.5 text-xs text-muted">Total generations = selected subjects × cycles per
                                subject.</p>
                        </x-exam.field>

                        @error('planId')
                            <p class="font-mono text-[11.5px] text-accent">▲ {{ $message }}</p>
                        @enderror

                        @if ($calculatedPrice > 0)
                            <x-exam.callout>
                                <p class="font-display text-lg font-bold text-primary">Total: GHS
                                    {{ number_format($calculatedPrice, 2) }}</p>
                                @if ($selectedPricingTier)
                                    <p class="mt-1 text-sm text-primary/80">Tier: {{ $selectedPricingTier->name }}</p>
                                @endif
                                <p class="mt-1 font-mono text-[11px] tracking-wide text-muted">
                                    {{ count($selectedSubjectIds) }} SUBJECT(S) ·
                                    {{ $userGroupOptions[$userGroup] ?? $userGroup }} · {{ $maxExams ?? 1 }} CYCLE(S)
                                    @if ($type === 'online')
                                        · {{ $participantCount }} PARTICIPANT(S)
                                    @endif
                                </p>
                            </x-exam.callout>
                        @endif
                    </div>

                    <x-slot:footer>
                        <div class="flex justify-end gap-2">
                            <x-exam.button type="button" variant="ghost"
                                wire:click="$set('showPurchaseForm', false)">Cancel</x-exam.button>
                            <x-exam.button type="button" wire:click="purchase">Proceed to Payment</x-exam.button>
                        </div>
                    </x-slot:footer>
                </x-exam.card>
            @endif

            {{-- Top-up modal --}}
            @if ($showTopUpForm)
                <div @close-modal.window="$wire.set('showTopUpForm', false)">
                    <x-exam.modal title="Add Participant Slots">
                        <x-exam.field bare label="Additional Participants">
                            <x-exam.input type="number" min="1" wire:model.live="additionalParticipants" />
                        </x-exam.field>

                        @if ($topUpPrice > 0)
                            <p class="mt-4 text-sm text-ink-soft">
                                Top-up cost: <strong class="font-display text-base font-bold text-primary">GHS
                                    {{ number_format($topUpPrice, 2) }}</strong>
                            </p>
                        @endif

                        <x-slot:footer>
                            <x-exam.button type="button" variant="ghost"
                                wire:click="$set('showTopUpForm', false)">Cancel</x-exam.button>
                            <x-exam.button type="button" wire:click="processTopUp">Pay & Add Slots</x-exam.button>
                        </x-slot:footer>
                    </x-exam.modal>
                </div>
            @endif

            {{-- Subscriptions list --}}
            <div class="grid gap-4">
                @forelse($mockSubscriptions as $sub)
                    @php
                        $statusValue =
                            $sub->status instanceof \App\MockExam\Models\MockExamSubscriptionStatus
                                ? $sub->status->value
                                : strtolower((string) ($sub->status ?? 'unknown'));

                        $groupLabel =
                            $sub->user_group instanceof \App\MockExam\Models\MockExamUserGroup
                                ? $sub->user_group->label()
                                : ucfirst(str_replace('_', ' ', $sub->user_group ?? 'individual'));

                        $subjectCount = $sub->subjects->count();
                        $cyclesPerSubject = max(1, (int) ($sub->max_exams ?? 1));
                        $allowedExamGenerations = $subjectCount * $cyclesPerSubject;

                        $examUsed = (int) ($sub->exams_used ?? 0);
                        $examRemaining = max(0, $allowedExamGenerations - $examUsed);
                        $examPercent =
                            $allowedExamGenerations > 0
                                ? min(100, round(($examUsed / $allowedExamGenerations) * 100))
                                : 0;

                        $participantSlots = (int) ($sub->participant_slots ?? 0);
                        $participantsUsed = (int) ($sub->participants_used ?? 0);
                        $slotRemaining = max(0, $participantSlots - $participantsUsed);
                        $slotPercent =
                            $participantSlots > 0 ? min(100, round(($participantsUsed / $participantSlots) * 100)) : 0;

                        $subjectPreview = $sub->subjects->take(2)->pluck('name')->join(', ');
                        if ($subjectCount > 2) {
                            $subjectPreview .= ' +' . ($subjectCount - 2) . ' more';
                        }

                        $title =
                            $sub->descriptive_title ??
                            trim(
                                ($sub->plan->name ?? 'Subscription') . ($subjectPreview ? ' — ' . $subjectPreview : ''),
                            );

                        $statusClasses = match ($statusValue) {
                            'active' => 'bg-success/10 text-success ring-success/20',
                            'pending' => 'bg-warn/10 text-warn ring-warn/20',
                            'expired', 'cancelled' => 'bg-destructive/10 text-destructive ring-destructive/20',
                            default => 'bg-muted/10 text-muted ring-muted/20',
                        };

                        $accentClasses = match ($statusValue) {
                            'active' => 'from-success/80 via-success/35 to-transparent',
                            'pending' => 'from-warn/80 via-warn/35 to-transparent',
                            default => 'from-muted/70 via-muted/30 to-transparent',
                        };
                    @endphp

                    <article wire:key="sub-{{ $sub->id }}"
                        class="animate-rise relative overflow-hidden rounded-xl border border-line bg-surface shadow-[0_1px_2px_rgba(20,24,34,0.04)] transition hover:border-primary/30 hover:shadow-md">
                        {{-- Top status accent --}}
                        <div
                            class="pointer-events-none absolute inset-x-0 top-0 h-1 bg-gradient-to-r {{ $accentClasses }}">
                        </div>

                        <div class="p-5">
                            <div class="flex flex-col gap-5 xl:flex-row xl:items-start xl:justify-between">
                                {{-- Main subscription info --}}
                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h3 class="max-w-full truncate text-base font-semibold text-ink"
                                            title="{{ $title }}">
                                            {{ $title }}
                                        </h3>

                                        <span
                                            class="inline-flex items-center gap-1.5 rounded-full px-2 py-0.5 text-xs font-medium ring-1 ring-inset {{ $statusClasses }}">
                                            <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                                            {{ ucfirst($statusValue) }}
                                        </span>

                                        <span
                                            class="inline-flex items-center rounded-full bg-primary/10 px-2 py-0.5 text-xs font-medium text-primary ring-1 ring-inset ring-primary/15">
                                            {{ ucfirst($sub->type ?? 'print') }}
                                        </span>

                                        <span
                                            class="inline-flex items-center rounded-full bg-surface-2 px-2 py-0.5 text-xs font-medium text-muted ring-1 ring-inset ring-line">
                                            {{ $groupLabel }}
                                        </span>
                                    </div>

                                    <p class="mt-2 font-mono text-[10.5px] tracking-[0.14em] text-muted">
                                        SUBSCRIPTION #{{ $sub->id }}
                                        · {{ strtoupper($sub->plan->name ?? 'PLAN') }}
                                        ·
                                        {{ $sub->granted_by ? 'GRANTED BY ' . strtoupper($sub->grantedBy->name ?? 'OWNER') : 'SELF-PURCHASED' }}
                                    </p>

                                    {{-- Summary cards --}}
                                    <div class="mt-4 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                                        <div class="rounded-lg border border-line bg-surface-2/70 p-3">
                                            <p class="font-mono text-[10px] uppercase tracking-[0.14em] text-muted">
                                                Subjects</p>
                                            <p class="mt-1 text-sm font-semibold text-ink">{{ $subjectCount }}</p>
                                            <p class="mt-0.5 truncate text-xs text-muted"
                                                title="{{ $sub->subjects->pluck('name')->join(', ') }}">
                                                {{ $subjectPreview ?: 'None attached' }}
                                            </p>
                                        </div>

                                        <div class="rounded-lg border border-line bg-surface-2/70 p-3">
                                            <p class="font-mono text-[10px] uppercase tracking-[0.14em] text-muted">
                                                Exam Cycles</p>
                                            <p class="mt-1 text-sm font-semibold text-ink">
                                                {{ $cyclesPerSubject }} / subject
                                            </p>
                                            <p class="mt-0.5 text-xs text-muted">
                                                {{ $allowedExamGenerations }} total allowed
                                            </p>
                                        </div>

                                        <div class="rounded-lg border border-line bg-surface-2/70 p-3">
                                            <p class="font-mono text-[10px] uppercase tracking-[0.14em] text-muted">
                                                Participants</p>

                                            @if ($sub->type === 'online')
                                                <p class="mt-1 text-sm font-semibold text-ink">
                                                    {{ $participantsUsed }}/{{ $participantSlots }}
                                                </p>
                                                <p class="mt-0.5 text-xs text-muted">
                                                    {{ $slotRemaining }} slot{{ $slotRemaining !== 1 ? 's' : '' }}
                                                    remaining
                                                </p>
                                            @else
                                                <p class="mt-1 text-sm font-semibold text-ink">Print-based</p>
                                                <p class="mt-0.5 text-xs text-muted">No participant slots required</p>
                                            @endif
                                        </div>

                                        <div class="rounded-lg border border-line bg-surface-2/70 p-3">
                                            <p class="font-mono text-[10px] uppercase tracking-[0.14em] text-muted">
                                                Expires</p>
                                            <p class="mt-1 text-sm font-semibold text-ink">
                                                {{ $sub->expires_at ? $sub->expires_at->format('M j, Y') : 'Never' }}
                                            </p>
                                            <p class="mt-0.5 text-xs text-muted">
                                                @if ($sub->expires_at)
                                                    {{ $sub->expires_at->isPast() ? 'Expired' : 'Until expiry' }}
                                                @else
                                                    No expiry date
                                                @endif
                                            </p>
                                        </div>
                                    </div>

                                    {{-- Usage progress --}}
                                    <div class="mt-4 grid gap-3 lg:grid-cols-2">
                                        <div class="rounded-lg border border-line bg-surface-2/70 p-3">
                                            <div class="flex items-center justify-between gap-4">
                                                <p
                                                    class="font-mono text-[10px] uppercase tracking-[0.14em] text-muted">
                                                    Exam Generations
                                                </p>
                                                <p class="font-mono text-[10.5px] text-muted">
                                                    {{ $examUsed }}/{{ $allowedExamGenerations }}
                                                </p>
                                            </div>

                                            <div class="mt-2 h-1.5 w-full overflow-hidden rounded-full bg-line">
                                                <div class="h-full rounded-full bg-primary transition-all"
                                                    style="width: {{ $examPercent }}%"></div>
                                            </div>

                                            <p class="mt-2 text-xs text-muted">
                                                @if ($examRemaining > 0)
                                                    {{ $examRemaining }} exam
                                                    generation{{ $examRemaining !== 1 ? 's' : '' }} remaining
                                                @else
                                                    Exam generation limit reached
                                                @endif
                                            </p>
                                        </div>

                                        @if ($sub->type === 'online')
                                            <div class="rounded-lg border border-line bg-surface-2/70 p-3">
                                                <div class="flex items-center justify-between gap-4">
                                                    <p
                                                        class="font-mono text-[10px] uppercase tracking-[0.14em] text-muted">
                                                        Participant Slots
                                                    </p>
                                                    <p class="font-mono text-[10.5px] text-muted">
                                                        {{ $participantsUsed }}/{{ $participantSlots }}
                                                    </p>
                                                </div>

                                                <div class="mt-2 h-1.5 w-full overflow-hidden rounded-full bg-line">
                                                    <div class="h-full rounded-full bg-primary transition-all"
                                                        style="width: {{ $slotPercent }}%"></div>
                                                </div>

                                                <p class="mt-2 text-xs text-muted">
                                                    @if ($slotRemaining > 0)
                                                        {{ $slotRemaining }} participant
                                                        slot{{ $slotRemaining !== 1 ? 's' : '' }} remaining
                                                    @else
                                                        No participant slots remaining
                                                    @endif
                                                </p>
                                            </div>
                                        @else
                                            <div class="rounded-lg border border-line bg-surface-2/70 p-3">
                                                <p
                                                    class="font-mono text-[10px] uppercase tracking-[0.14em] text-muted">
                                                    Delivery Mode
                                                </p>
                                                <p class="mt-2 text-sm font-semibold text-ink">Print Subscription</p>
                                                <p class="mt-1 text-xs text-muted">
                                                    Physical exam materials are generated under this subscription.
                                                </p>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                {{-- Actions panel --}}
                                <aside class="w-full shrink-0 xl:w-[220px]">
                                    <div class="rounded-xl border border-line bg-surface-2/70 p-3">
                                        <p class="font-mono text-[10px] uppercase tracking-[0.14em] text-muted">
                                            Quick Actions
                                        </p>

                                        <div class="mt-3 grid gap-2">
                                            <x-exam.button type="button" size="xs"
                                                wire:click="openSubscriptionDetails({{ $sub->id }})">
                                                Open
                                            </x-exam.button>

                                            @if ($statusValue === 'pending')
                                                <x-exam.button type="button" size="xs" variant="outline"
                                                    wire:click="retryPayment({{ $sub->id }})">
                                                    Pay Now
                                                </x-exam.button>
                                            @endif

                                            @if ($sub->type === 'online' && $statusValue === 'active')
                                                <x-exam.button type="button" size="xs" variant="outline"
                                                    wire:click="openTopUpForm({{ $sub->id }})">
                                                    Top Up Slots
                                                </x-exam.button>
                                            @endif

                                            @if (in_array($statusValue, ['active', 'pending']))
                                                <x-exam.button type="button" size="xs" variant="danger"
                                                    wire:click="confirmCancel({{ $sub->id }})">
                                                    Cancel
                                                </x-exam.button>
                                            @endif
                                        </div>

                                        <div class="mt-3 rounded-md border border-line bg-surface p-3">
                                            <p class="font-mono text-[10px] uppercase tracking-[0.14em] text-muted">
                                                Status
                                            </p>

                                            <p class="mt-1 text-xs leading-relaxed text-muted">
                                                @if ($statusValue === 'active')
                                                    This subscription is active and available for exam generation.
                                                @elseif($statusValue === 'pending')
                                                    Payment is pending. Complete payment to activate this subscription.
                                                @elseif($statusValue === 'expired')
                                                    This subscription has expired.
                                                @elseif($statusValue === 'cancelled')
                                                    This subscription has been cancelled.
                                                @else
                                                    This subscription is currently unavailable.
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </aside>
                            </div>

                            {{-- Mock exams --}}
                            @if ($sub->mockExams->isNotEmpty())
                                <details
                                    class="group mt-5 scroll-mt-24 overflow-hidden rounded-xl border border-line bg-surface-2/50 transition hover:border-primary/25">
                                    <summary
                                        class="flex cursor-pointer list-none items-center justify-between gap-4 px-4 py-3 [&::-webkit-details-marker]:hidden">
                                        <div>
                                            <p class="font-mono text-[10px] uppercase tracking-[0.14em] text-muted">
                                                Mocks under this subscription
                                            </p>
                                            <p class="mt-0.5 text-sm font-semibold text-ink">
                                                {{ $sub->mockExams->count() }} mock
                                                exam{{ $sub->mockExams->count() !== 1 ? 's' : '' }}
                                            </p>
                                        </div>

                                        <span
                                            class="inline-flex h-7 w-7 items-center justify-center rounded-full border border-line bg-surface text-muted transition group-open:border-primary/30 group-open:text-primary">
                                            <svg class="h-3.5 w-3.5 transition-transform duration-200 group-open:rotate-180"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </span>
                                    </summary>

                                    <div class="space-y-4 border-t border-line p-4">
                                        @foreach ($sub->mockExams as $mock)
                                            <div wire:key="mock-{{ $mock->id }}"
                                                class="rounded-lg border border-line bg-surface p-4 transition hover:border-primary/25">
                                                <div class="min-w-0">
                                                    <div class="flex flex-wrap items-center gap-2">
                                                        <p class="text-sm font-semibold text-ink">
                                                            {{ $mock->title }}
                                                        </p>

                                                        <span
                                                            class="inline-flex items-center rounded-full bg-primary/10 px-2 py-0.5 text-xs font-medium text-primary">
                                                            {{ ucfirst($mock->status) }}
                                                        </span>

                                                        <span
                                                            class="inline-flex items-center rounded-full bg-surface-2 px-2 py-0.5 text-xs font-medium text-muted ring-1 ring-inset ring-line">
                                                            {{ ucfirst($mock->delivery_type) }}
                                                        </span>
                                                    </div>

                                                    <p class="mt-1 font-mono text-[10.5px] text-muted">
                                                        {{ $mock->subjectExams->count() }} subject
                                                        exam{{ $mock->subjectExams->count() !== 1 ? 's' : '' }}
                                                        ·
                                                        {{ $mock->subjectExams->sum(fn($se) => $se->sections->count()) }}
                                                        sections
                                                        ·
                                                        {{ $mock->subjectExams->sum(fn($se) => $se->sections->sum(fn($s) => $s->questions->count())) }}
                                                        questions
                                                    </p>
                                                </div>

                                                @if ($mock->subjectExams->isNotEmpty())
                                                    <div class="mt-4 space-y-2 border-t border-line pt-4">
                                                      @foreach ($mock->subjectExams as $subjectExam)
    <div
        wire:key="subject-exam-{{ $subjectExam->id }}"
        class="rounded-md border border-line bg-surface-2/60 p-3 transition hover:border-primary/25"
    >
        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
            <div class="min-w-0 flex-1">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="inline-block rounded bg-primary/10 px-2 py-0.5 text-xs font-semibold text-primary">
                        {{ $subjectExam->academicSubject->name ?? 'Unknown Subject' }}
                    </span>

                    <span class="text-sm text-ink">
                        {{ $subjectExam->getDisplayTitle() }}
                    </span>
                </div>

                <div class="mt-2 flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center gap-1 rounded bg-surface px-2 py-1 font-mono text-[10px] text-muted ring-1 ring-inset ring-line">
                        {{ $subjectExam->sections->count() }}
                        sections
                    </span>

                    <span class="inline-flex items-center gap-1 rounded bg-surface px-2 py-1 font-mono text-[10px] text-muted ring-1 ring-inset ring-line">
                        {{ $subjectExam->sections->sum(fn($s) => $s->questions->count()) }}
                        questions
                    </span>

                    <span class="inline-flex items-center gap-1 rounded bg-surface px-2 py-1 font-mono text-[10px] text-muted ring-1 ring-inset ring-line">
                        {{ $subjectExam->duration_in_minutes ?? 'N/A' }}
                        min
                    </span>
                </div>
            </div>

            <div class="shrink-0">
                <a
                    href="{{ route('mock-exams.subject-exams.subscription-preview', [$mock, $subjectExam]) }}"
                    target="_blank"
                    class="inline-flex items-center gap-1.5 rounded-md border border-primary/25 bg-primary/10 px-3 py-2 font-mono text-[11px] font-semibold uppercase tracking-[0.12em] text-primary transition hover:bg-primary/15"
                >
                    Open Preview

                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M14 5l7 7m0 0l-7 7m7-7H3"
                        />
                    </svg>
                </a>
            </div>
        </div>
    </div>
@endforeach
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </details>
                            @endif
                        </div>

                    </article>
                @empty
                    <x-exam.empty title="No subscriptions yet"
                        description="Purchase a subscription to start creating mock exams." />
                @endforelse
            </div>
        </div>
    @endif

    {{-- ==================== RESULTS TAB ==================== --}}
    @if ($activeTab === 'results')
        <div class="animate-rise">
            <div class="mb-5 grid grid-cols-1 gap-3 sm:max-w-2xl sm:grid-cols-2">
                <x-exam.select wire:model.live="selectedSubscriptionId" placeholder="Select subscription…">
                    @foreach ($mockSubscriptions as $sub)
                        <option value="{{ $sub->id }}">{{ $sub->plan->name }} —
                            {{ $sub->subjects->pluck('name')->join(', ') }}</option>
                    @endforeach
                </x-exam.select>

                @if ($selectedMockSubscription)
                    <x-exam.select wire:model.live="selectedExamId" placeholder="Select exam…">
                        @foreach ($mockExams as $exam)
                            <option value="{{ $exam->id }}">{{ $exam->title }} ({{ $exam->submissions_count }}
                                submissions)</option>
                        @endforeach
                    </x-exam.select>
                @endif
            </div>

            @if ($selectedExamId && $mockSubmissions->count())
                <x-exam.table :headers="['Participant', 'Score', 'Grade', 'Started', 'Submitted', 'Duration', '']">
                    @foreach ($mockSubmissions as $submission)
                        @php
                            $gradeTone = 'muted';

                            if ($submission->grade) {
                                if (in_array($submission->grade, ['A+', 'A'])) {
                                    $gradeTone = 'ok';
                                } elseif ($submission->grade === 'B') {
                                    $gradeTone = 'primary';
                                } elseif ($submission->grade === 'C') {
                                    $gradeTone = 'warn';
                                } else {
                                    $gradeTone = 'danger';
                                }
                            }
                        @endphp <tr wire:key="submission-{{ $submission->id }}"
                            class="transition hover:bg-surface-2/60">
                            <td class="px-4 py-3">
                                <p class="font-semibold text-ink">{{ $submission->getParticipantName() }}</p>
                                <p class="font-mono text-[10.5px] text-muted">{{ $submission->getParticipantEmail() }}
                                </p>
                            </td>
                            <td class="px-4 py-3 text-ink-soft">
                                <span
                                    class="font-semibold text-ink">{{ $submission->score }}/{{ $submission->total_marks }}</span>
                                <span
                                    class="font-mono text-[10.5px] text-muted">({{ $submission->percentage }}%)</span>
                            </td>
                            <td class="px-4 py-3"><x-exam.badge
                                    :tone="$gradeTone">{{ $submission->grade ?? '—' }}</x-exam.badge></td>
                            <td class="px-4 py-3 font-mono text-[11px] text-muted">
                                {{ $submission->started_at?->format('M d, H:i') ?? '—' }}</td>
                            <td class="px-4 py-3 font-mono text-[11px] text-muted">
                                {{ $submission->submitted_at?->format('M d, H:i') ?? '—' }}</td>
                            <td class="px-4 py-3 font-mono text-[11px] text-muted">
                                {{ $submission->time_spent_seconds ? gmdate('H:i:s', $submission->time_spent_seconds) : '—' }}
                            </td>
                            <td class="px-4 py-3">
                                <x-exam.link-button wire:click="openScoreEditor({{ $submission->id }})">Edit
                                    Scores</x-exam.link-button>
                            </td>
                        </tr>
                    @endforeach

                    <x-slot:footer>{{ $mockSubmissions->links() }}</x-slot:footer>
                </x-exam.table>
            @elseif($selectedExamId)
                <x-exam.empty compact title="No submissions yet" description="No submissions for this exam yet." />
            @else
                <x-exam.empty compact title="Select an exam"
                    description="Choose a subscription and exam to view results." />
            @endif

            {{-- Score editor modal --}}
            @if ($showScoreEditor && $editingMockSubmission)
                <x-exam.modal max-width="max-w-2xl"
                    title="Edit Scores — {{ $editingMockSubmission->getParticipantName() }}">
                    <div class="space-y-3">
                        @foreach ($editingMockSubmission->assignment->questions as $question)
                            <div wire:key="q-{{ $question->id }}"
                                class="rounded-md border border-line bg-surface-2 p-3">
                                <p class="mb-2 text-sm text-ink">{{ $question->question }}</p>
                                <div class="flex items-center gap-3">
                                    <label class="font-mono text-[10.5px] uppercase tracking-wider text-muted">Points
                                        (max {{ $question->marks }})
                                    </label>
                                    <input type="number" step="0.5" min="0"
                                        max="{{ $question->marks }}" wire:model="editedScores.{{ $question->id }}"
                                        class="w-24 rounded border border-line-strong bg-surface px-3 py-2 text-sm text-ink transition hover:border-ink-soft focus:border-primary focus:outline-none focus:ring-[3px] focus:ring-primary/20">
                                </div>
                            </div>
                        @endforeach

                        <x-exam.field bare label="Reason for change (optional)">
                            <textarea wire:model="scoreEditReason" rows="2"
                                class="w-full rounded border border-line-strong bg-surface px-3 py-2.5 text-sm text-ink placeholder:text-muted transition hover:border-ink-soft focus:border-primary focus:outline-none focus:ring-[3px] focus:ring-primary/20"></textarea>
                        </x-exam.field>

                        @if ($mockAuditLogs->isNotEmpty())
                            <div class="rounded-md border border-line bg-surface-2 p-3">
                                <p
                                    class="mb-2 font-mono text-[10px] font-semibold uppercase tracking-[0.14em] text-muted">
                                    Audit History</p>
                                <ol class="space-y-1.5">
                                    @foreach ($mockAuditLogs as $log)
                                        <li wire:key="log-{{ $log->id }}"
                                            class="text-xs leading-relaxed text-muted">
                                            <span class="font-semibold text-ink-soft">{{ $log->editor->name }}</span>
                                            changed score from {{ $log->old_score }} to {{ $log->new_score }}
                                            ({{ $log->old_grade }} → {{ $log->new_grade }})
                                            at {{ $log->created_at->format('M d, H:i') }}
                                            @if ($log->reason)
                                                — “{{ $log->reason }}”
                                            @endif
                                        </li>
                                    @endforeach
                                </ol>
                            </div>
                        @endif
                    </div>

                    <x-slot:footer>
                        <x-exam.button type="button" variant="ghost"
                            wire:click="$set('showScoreEditor', false)">Cancel</x-exam.button>
                        <x-exam.button type="button" wire:click="saveScores" loading="Recalculating…">Save &
                            Recalculate</x-exam.button>
                    </x-slot:footer>
                </x-exam.modal>
            @endif
        </div>
    @endif

    {{-- ==================== PERFORMANCE TAB ==================== --}}
    @if ($activeTab === 'performance')
        <div class="animate-rise">
            <div class="mb-6">
                <p class="mb-3 font-mono text-[10.5px] tracking-[0.14em] text-muted">PARTICIPANT PERFORMANCE OVER TIME
                </p>
                <div class="flex max-w-xl gap-2">
                    <x-exam.input wire:model="performanceSearch" placeholder="Search by name or email…" />
                    <x-exam.button type="button" wire:click="searchPerformance">Search</x-exam.button>
                </div>
                @error('performanceSearch')
                    <p class="mt-2 font-mono text-[11.5px] text-accent">▲ {{ $message }}</p>
                @enderror
            </div>

            @if (!empty($performanceResults))
                <div class="mb-6 grid grid-cols-2 gap-4 sm:grid-cols-4">
                    <x-exam.stat label="Exams Taken" :value="$performanceResults['total_exams']" />
                    <x-exam.stat label="Average Score" :value="number_format($performanceResults['average_score'], 1) . '%'" />
                    <x-exam.stat label="Highest Score" :value="number_format($performanceResults['highest_score'], 1) . '%'" tone="ok" />
                    <x-exam.stat label="Lowest Score" :value="number_format($performanceResults['lowest_score'], 1) . '%'" tone="danger" />
                </div>

                <x-exam.table :headers="['Exam', 'Score', 'Grade', 'Date']">
                    @foreach ($performanceResults['submissions'] as $submission)
                        @php
                            $perfTone = 'muted';

                            if ($submission->grade) {
                                if (in_array($submission->grade, ['A+', 'A'])) {
                                    $perfTone = 'ok';
                                } elseif ($submission->grade === 'B') {
                                    $perfTone = 'primary';
                                } else {
                                    $perfTone = 'danger';
                                }
                            }
                        @endphp <tr wire:key="perf-{{ $submission->id }}"
                            class="transition hover:bg-surface-2/60">
                            <td class="px-4 py-3 font-semibold text-ink">{{ $submission->assignment->title }}</td>
                            <td class="px-4 py-3 text-ink-soft">
                                {{ $submission->score }}/{{ $submission->total_marks }}
                                <span
                                    class="font-mono text-[10.5px] text-muted">({{ $submission->percentage }}%)</span>
                            </td>
                            <td class="px-4 py-3"><x-exam.badge
                                    :tone="$perfTone">{{ $submission->grade ?? '—' }}</x-exam.badge></td>
                            <td class="px-4 py-3 font-mono text-[11px] text-muted">
                                {{ $submission->submitted_at?->format('M d, Y') }}</td>
                        </tr>
                    @endforeach
                </x-exam.table>
            @else
                <x-exam.empty compact title="No performance data"
                    description="Search for a participant to view their performance analytics." />
            @endif
        </div>
    @endif

    {{-- ==================== IDENTITY TAB ==================== --}}
    @if ($activeTab === 'identity')
        <div class="animate-rise">
            <p class="font-mono text-[10.5px] tracking-[0.14em] text-muted">YOUR EXAM IDENTIFICATION DETAILS</p>
            <livewire:mock-exam.user-identity-form />
        </div>
    @endif

    {{-- ==================== GLOBAL SUBSCRIPTION DETAILS MODAL ==================== --}}
    @if ($showSubscriptionDetails && $this->detailsSubscription)
        @php
            $details = $this->detailsSubscription;

            $detailsStatus =
                $details->status instanceof \App\MockExam\Models\MockExamSubscriptionStatus
                    ? $details->status->value
                    : strtolower((string) ($details->status ?? 'unknown'));

            $detailsGroupLabel =
                $details->user_group instanceof \App\MockExam\Models\MockExamUserGroup
                    ? $details->user_group->label()
                    : ucfirst(str_replace('_', ' ', $details->user_group ?? 'individual'));

            $detailsSubjectCount = $details->subjects->count();
            $detailsCycles = max(1, (int) ($details->max_exams ?? 1));
            $detailsAllowed = $detailsSubjectCount * $detailsCycles;
            $detailsUsed = (int) ($details->exams_used ?? 0);
            $detailsRemaining = max(0, $detailsAllowed - $detailsUsed);

            $detailsSubjectPreview = $details->subjects->take(2)->pluck('name')->join(', ');
            if ($detailsSubjectCount > 2) {
                $detailsSubjectPreview .= ' +' . ($detailsSubjectCount - 2) . ' more';
            }

            $detailsTitle =
                $details->descriptive_title ??
                trim(
                    ($details->plan->name ?? 'Subscription') .
                        ($detailsSubjectPreview ? ' — ' . $detailsSubjectPreview : ''),
                );

            $detailsStatusClasses = match ($detailsStatus) {
                'active' => 'bg-success/10 text-success ring-success/20',
                'pending' => 'bg-warn/10 text-warn ring-warn/20',
                'expired', 'cancelled' => 'bg-destructive/10 text-destructive ring-destructive/20',
                default => 'bg-muted/10 text-muted ring-muted/20',
            };
        @endphp

        <div @close-subscription-details.window="$wire.closeSubscriptionDetails()">
            <x-exam.modal title="Subscription Details" max-width="max-w-2xl"
                close-event="close-subscription-details">
                <div class="space-y-4">
                    {{-- Overview --}}
                    <div class="rounded-md border border-line bg-surface-2/70 p-4">
                        <p class="font-mono text-[10px] uppercase tracking-[0.14em] text-muted">
                            Overview
                        </p>

                        <p class="mt-1 text-base font-semibold text-ink">
                            {{ $detailsTitle }}
                        </p>

                        <p class="mt-1 text-xs text-muted">
                            Subscription #{{ $details->id }}
                            · {{ $detailsGroupLabel }}
                            · {{ ucfirst($details->type ?? 'print') }}
                        </p>

                        @if ($details->plan?->description)
                            <p class="mt-2 text-xs leading-relaxed text-muted">
                                {{ $details->plan->description }}
                            </p>
                        @endif
                    </div>

                    {{-- Billing & Dates --}}
                    <div class="rounded-md border border-line p-4">
                        <p class="font-mono text-[10px] uppercase tracking-[0.14em] text-muted">
                            Billing & Dates
                        </p>

                        <div class="mt-3 space-y-3">
                            <div
                                class="flex items-start justify-between gap-4 border-b border-line pb-3 last:border-b-0 last:pb-0">
                                <p class="font-mono text-[10px] uppercase tracking-[0.14em] text-muted">
                                    Amount Paid
                                </p>
                                <p class="text-right text-sm font-semibold text-ink">
                                    GHS {{ number_format((float) ($details->amount_paid ?? 0), 2) }}
                                </p>
                            </div>

                            <div
                                class="flex items-start justify-between gap-4 border-b border-line pb-3 last:border-b-0 last:pb-0">
                                <p class="font-mono text-[10px] uppercase tracking-[0.14em] text-muted">
                                    Purchased
                                </p>
                                <p class="text-right text-sm text-ink">
                                    {{ $details->created_at?->format('M j, Y g:i A') ?? '—' }}
                                </p>
                            </div>

                            <div
                                class="flex items-start justify-between gap-4 border-b border-line pb-3 last:border-b-0 last:pb-0">
                                <p class="font-mono text-[10px] uppercase tracking-[0.14em] text-muted">
                                    Activated
                                </p>
                                <p class="text-right text-sm text-ink">
                                    {{ $details->activated_at?->format('M j, Y g:i A') ?? '—' }}
                                </p>
                            </div>

                            <div
                                class="flex items-start justify-between gap-4 border-b border-line pb-3 last:border-b-0 last:pb-0">
                                <p class="font-mono text-[10px] uppercase tracking-[0.14em] text-muted">
                                    Expires
                                </p>
                                <p class="text-right text-sm text-ink">
                                    {{ $details->expires_at?->format('M j, Y g:i A') ?? 'Never' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Plan & Configuration --}}
                    <div class="rounded-md border border-line p-4">
                        <p class="font-mono text-[10px] uppercase tracking-[0.14em] text-muted">
                            Plan & Configuration
                        </p>

                        <div class="mt-3 space-y-3">
                            <div
                                class="flex items-start justify-between gap-4 border-b border-line pb-3 last:border-b-0 last:pb-0">
                                <p class="font-mono text-[10px] uppercase tracking-[0.14em] text-muted">
                                    Plan
                                </p>
                                <p class="text-right text-sm font-semibold text-ink">
                                    {{ $details->plan->name ?? '—' }}
                                </p>
                            </div>

                            <div
                                class="flex items-start justify-between gap-4 border-b border-line pb-3 last:border-b-0 last:pb-0">
                                <p class="font-mono text-[10px] uppercase tracking-[0.14em] text-muted">
                                    Delivery Type
                                </p>
                                <p class="text-right text-sm text-ink">
                                    {{ ucfirst($details->type ?? 'print') }}
                                </p>
                            </div>

                            <div
                                class="flex items-start justify-between gap-4 border-b border-line pb-3 last:border-b-0 last:pb-0">
                                <p class="font-mono text-[10px] uppercase tracking-[0.14em] text-muted">
                                    User Group
                                </p>
                                <p class="text-right text-sm text-ink">
                                    {{ $detailsGroupLabel }}
                                </p>
                            </div>

                            <div
                                class="flex items-start justify-between gap-4 border-b border-line pb-3 last:border-b-0 last:pb-0">
                                <p class="font-mono text-[10px] uppercase tracking-[0.14em] text-muted">
                                    Status
                                </p>
                                <span
                                    class="inline-flex items-center gap-1.5 rounded-full px-2 py-0.5 text-xs font-medium ring-1 ring-inset {{ $detailsStatusClasses }}">
                                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                                    {{ ucfirst($detailsStatus) }}
                                </span>
                            </div>

                            <div
                                class="flex items-start justify-between gap-4 border-b border-line pb-3 last:border-b-0 last:pb-0">
                                <p class="font-mono text-[10px] uppercase tracking-[0.14em] text-muted">
                                    Granted By
                                </p>
                                <p class="text-right text-sm text-ink">
                                    {{ $details->grantedBy->name ?? 'Self-purchased' }}
                                </p>
                            </div>

                            <div
                                class="flex items-start justify-between gap-4 border-b border-line pb-3 last:border-b-0 last:pb-0">
                                <p class="font-mono text-[10px] uppercase tracking-[0.14em] text-muted">
                                    Exam Cycles
                                </p>
                                <p class="text-right text-sm text-ink">
                                    {{ $detailsCycles }} per subject
                                </p>
                            </div>

                            <div
                                class="flex items-start justify-between gap-4 border-b border-line pb-3 last:border-b-0 last:pb-0">
                                <p class="font-mono text-[10px] uppercase tracking-[0.14em] text-muted">
                                    Total Allowed Generations
                                </p>
                                <p class="text-right text-sm text-ink">
                                    {{ $detailsAllowed }}
                                </p>
                            </div>

                            <div
                                class="flex items-start justify-between gap-4 border-b border-line pb-3 last:border-b-0 last:pb-0">
                                <p class="font-mono text-[10px] uppercase tracking-[0.14em] text-muted">
                                    Exams Used
                                </p>
                                <p class="text-right text-sm text-ink">
                                    {{ $detailsUsed }}
                                </p>
                            </div>

                            <div
                                class="flex items-start justify-between gap-4 border-b border-line pb-3 last:border-b-0 last:pb-0">
                                <p class="font-mono text-[10px] uppercase tracking-[0.14em] text-muted">
                                    Remaining Generations
                                </p>
                                <p class="text-right text-sm text-ink">
                                    {{ $detailsRemaining }}
                                </p>
                            </div>

                            @if ($details->type === 'online')
                                <div
                                    class="flex items-start justify-between gap-4 border-b border-line pb-3 last:border-b-0 last:pb-0">
                                    <p class="font-mono text-[10px] uppercase tracking-[0.14em] text-muted">
                                        Participant Slots
                                    </p>
                                    <p class="text-right text-sm text-ink">
                                        {{ (int) ($details->participants_used ?? 0) }}/{{ (int) ($details->participant_slots ?? 0) }}
                                    </p>
                                </div>

                                <div
                                    class="flex items-start justify-between gap-4 border-b border-line pb-3 last:border-b-0 last:pb-0">
                                    <p class="font-mono text-[10px] uppercase tracking-[0.14em] text-muted">
                                        Remaining Slots
                                    </p>
                                    <p class="text-right text-sm text-ink">
                                        {{ max(0, (int) ($details->participant_slots ?? 0) - (int) ($details->participants_used ?? 0)) }}
                                    </p>
                                </div>
                            @endif

                            <div
                                class="flex items-start justify-between gap-4 border-b border-line pb-3 last:border-b-0 last:pb-0">
                                <p class="font-mono text-[10px] uppercase tracking-[0.14em] text-muted">
                                    Mocks Created
                                </p>
                                <p class="text-right text-sm text-ink">
                                    {{ $details->mockExams->count() }}
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Subjects --}}
                    <div class="rounded-md border border-line p-4">
                        <p class="font-mono text-[10px] uppercase tracking-[0.14em] text-muted">
                            Subjects
                        </p>

                        @if ($details->subjects->isNotEmpty())
                            <div class="mt-3 space-y-2">
                                @foreach ($details->subjects as $subject)
                                    <div
                                        class="flex items-center justify-between gap-3 rounded-md border border-line bg-surface-2/60 px-3 py-2">
                                        <p class="text-sm text-ink">
                                            {{ $subject->name }}
                                        </p>

                                        <span class="font-mono text-[10px] text-muted">
                                            SUBJECT #{{ $subject->id }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="mt-2 text-sm text-muted">
                                No subjects attached to this subscription.
                            </p>
                        @endif
                    </div>
                </div>

                <x-slot:footer>
                    @if ($detailsStatus === 'pending')
                        <x-exam.button type="button" variant="outline"
                            wire:click="retryPayment({{ $details->id }})">
                            Pay Now
                        </x-exam.button>
                    @endif

                    @if (in_array($detailsStatus, ['active', 'pending']))
                        <x-exam.button type="button" variant="danger"
                            wire:click="confirmCancel({{ $details->id }})">
                            Cancel Subscription
                        </x-exam.button>
                    @endif

                    <x-exam.button type="button" variant="ghost" wire:click="closeSubscriptionDetails">
                        Close
                    </x-exam.button>
                </x-slot:footer>
            </x-exam.modal>
        </div>
    @endif

    {{-- ==================== GLOBAL CANCEL CONFIRMATION MODAL ==================== --}}
    @if ($showCancelConfirm && $this->cancellingSubscription)
        @php
            $cancelTarget = $this->cancellingSubscription;
        @endphp

        <div @close-cancel-confirm.window="$wire.closeCancelConfirm()">
            <x-exam.modal title="Cancel Subscription" max-width="max-w-md" close-event="close-cancel-confirm">
                <p class="text-sm leading-relaxed text-ink-soft">
                    Are you sure you want to cancel
                    <span class="font-semibold text-ink">
                        {{ $cancelTarget->plan->name ?? 'this subscription' }}
                    </span>?
                    This action cannot be undone.
                </p>

                <x-slot:footer>
                    <x-exam.button type="button" variant="ghost" wire:click="closeCancelConfirm">
                        Keep It
                    </x-exam.button>

                    <x-exam.button type="button" variant="danger" wire:click="cancelSubscription">
                        Yes, Cancel
                    </x-exam.button>
                </x-slot:footer>
            </x-exam.modal>
        </div>
    @endif

</x-exam.page>
