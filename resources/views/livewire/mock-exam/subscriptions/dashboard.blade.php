<x-exam.page>
    <x-exam.header
        eyebrow="CANDIDATE DESK · SUBSCRIPTIONS, RESULTS & PERFORMANCE"
        title="My Exams"
        sub="Manage your exam subscriptions, review candidate results, and track participant performance across cycles."
        :lines="['MY', 'DESK']"
    />

    <x-exam.flash />

<x-exam.tabs
    :tabs="[
        'subscriptions' => 'My Subscriptions',
        'results'       => 'Results & Scores',
        'performance'   => 'Participant Performance',
        'identity'      => 'My Identity',
    ]"
    :active="$activeTab"
    property="activeTab"
/>

    {{-- ==================== SUBSCRIPTIONS TAB ==================== --}}
    @if($activeTab === 'subscriptions')
        <div class="animate-rise">
            <!-- Search and Filter Controls -->
            <div class="mb-6 flex flex-col sm:flex-row gap-4">
                <div class="flex-1">
                    <x-exam.input 
                        wire:model.live.debounce.300ms="search" 
                        placeholder="Search subscriptions, mocks, subjects..." 
                        icon="search"
                    />
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

            {{-- Subscriptions list --}}
            <div class="grid gap-4">
                @forelse($mockSubscriptions as $sub)
                    @php($groupLabel = $sub->user_group instanceof \App\MockExam\Models\MockExamUserGroup ? $sub->user_group->label() : ucfirst(str_replace('_', ' ', $sub->user_group ?? '')))
                    <article wire:key="sub-{{ $sub->id }}"
                             class="animate-rise rounded-md border border-line bg-surface p-4 shadow-[0_1px_2px_rgba(20,24,34,0.04)]">
                        <div class="flex items-start justify-between gap-4">
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2.5">
                                    <h3 class="truncate text-base font-semibold text-ink">{{ $sub->plan->name }}</h3>
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-success/10 px-2 py-0.5 text-xs font-medium text-success">
                                        <span class="h-1.5 w-1.5 rounded-full bg-success"></span>
                                        Active
                                    </span>
                                </div>

                                <ul class="mt-1.5 space-y-1 font-mono text-[10.5px] text-muted">
                                    <li class="flex items-baseline gap-2">
                                        <span class="text-muted/80">·</span>
                                        <span>{{ $groupLabel }} · {{ ucfirst($sub->type) }}</span>
                                    </li>
                                    <li class="flex items-baseline gap-2">
                                        <span class="text-muted/80">·</span>
                                        <span>{{ $sub->subjects->count() }} subject{{ $sub->subjects->count() !== 1 ? 's' : '' }} · {{ $sub->granted_by ? 'Granted by ' . $sub->grantedBy->name : 'Self-purchased' }}</span>
                                    </li>
                                    <li class="flex items-baseline gap-2">
                                        <span class="text-muted/80">·</span>
                                        <span>Expires {{ $sub->expires_at ? $sub->expires_at->format('M j, Y \\a\\t g:i A') : 'Never' }}</span>
                                    </li>
                                </ul>
                            </div>

                            <div class="flex shrink-0 flex-col items-end gap-2">
                                <div class="flex items-center gap-2">
                                    @if($sub->max_exams_remaining > 0)
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-primary/10 px-2 py-0.5 text-xs font-medium text-primary" title="You have {{ $sub->max_exams_remaining }} exam cycle{{ $sub->max_exams_remaining !== 1 ? 's' : '' }} remaining out of {{ $sub->max_exams }} total">
                                                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                {{ $sub->max_exams_remaining }} remaining
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-destructive/10 px-2 py-0.5 text-xs font-medium text-destructive" title="You have reached your limit of {{ $sub->max_exams }} exam cycle{{ $sub->max_exams !== 1 ? 's' : '' }}">
                                                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                Limit reached
                                        </span>
                                    @endif

                                    @if($sub->type === 'online' && $sub->participant_slots_remaining > 0)
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-primary/10 px-2 py-0.5 text-xs font-medium text-primary" title="You have {{ $sub->participant_slots_remaining }} participant slot{{ $sub->participant_slots_remaining !== 1 ? 's' : '' }} remaining">
                                            <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                            </svg>
                                            {{ $sub->participant_slots_remaining }} slots
                                        </span>
                                    @elseif($sub->type === 'online')
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-destructive/10 px-2 py-0.5 text-xs font-medium text-destructive" title="You have 0 participant slots remaining. Use 'Top Up' to add more.">
                                            <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            No slots
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Mock exams associated with this subscription --}}
                        @if($sub->mockExams->isNotEmpty())
                            <div class="mt-4 border-t border-line pt-4">
                                <p class="mb-3 font-mono text-[10px] uppercase tracking-[0.14em] text-muted">Mocks under this subscription</p>
                                
                                @foreach($sub->mockExams as $mock)
                                    <div class="mb-3 last:mb-0">
                                        <div class="flex items-center justify-between gap-4 py-2 border-b border-line last:border-b-0">
                                            <div class="flex-1">
                                                <p class="text-sm font-semibold text-ink">{{ $mock->title }}</p>
                                                <p class="font-mono text-[10.5px] text-muted">
                                                    {{ $mock->subjectExams->count() }} subject exam(s)
                                                    · {{ ucfirst($mock->status) }}
                                                    · {{ ucfirst($mock->delivery_type) }}
                                                </p>
                                            </div>

                                        </div>
                                        
                                        {{-- List individual subject exams under this mock --}}
                                        @if($mock->subjectExams->isNotEmpty())
                                            <div class="ml-4 mt-2 space-y-1 border-l-2 border-primary/20 pl-3">
                                                @foreach($mock->subjectExams as $subjectExam)
                                                    <div class="flex items-center justify-between py-1">
                                                        <div class="flex-1">
                                                            <div class="flex items-center gap-2">
                                                                <span class="inline-block px-2 py-0.5 rounded text-xs font-semibold bg-primary/10 text-primary">
                                                                    {{ $subjectExam->academicSubject->name ?? 'Unknown Subject' }}
                                                                </span>
                                                                <span class="text-sm text-ink">{{ $subjectExam->getDisplayTitle() }}</span>
                                                            </div>
                                                            <div class="flex gap-3 mt-1">
                                                                <span class="inline-flex items-center gap-1 font-mono text-[9px] bg-slate-100 text-slate-600 px-1.5 py-0.5 rounded">
                                                                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                                                    </svg>
                                                                    {{ $subjectExam->sections->count() }} sections
                                                                </span>
                                                                <span class="inline-flex items-center gap-1 font-mono text-[9px] bg-slate-100 text-slate-600 px-1.5 py-0.5 rounded">
                                                                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                    </svg>
                                                                    {{ $subjectExam->sections->sum(fn($s) => $s->questions->count()) }} questions
                                                                </span>
                                                                <span class="inline-flex items-center gap-1 font-mono text-[9px] bg-slate-100 text-slate-600 px-1.5 py-0.5 rounded">
                                                                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                    </svg>
                                                                    {{ $subjectExam->duration_in_minutes ?? 'N/A' }} min
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <a href="{{ route('mock-exams.subject-exams.subscription-preview', [$mock, $subjectExam]) }}"
                                                           class="font-mono text-[11px] tracking-wide text-primary hover:underline whitespace-nowrap">
                                                            Preview →
                                                        </a>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </article>
                @empty
                    <x-exam.empty
                        title="No subscriptions yet"
                        description="You don't have any active subscriptions."
                    />
                @endforelse
            </div>
        </div>
    @endif

    {{-- ==================== RESULTS TAB ==================== --}}
    @if($activeTab === 'results')
        <div class="animate-rise">
            <p class="font-mono text-[10.5px] tracking-[0.14em] text-muted">YOUR EXAM RESULTS</p>
            <x-exam.empty
                title="No results yet"
                description="Complete an exam to see your results here."
            />
        </div>
    @endif

    {{-- ==================== PERFORMANCE TAB ==================== --}}
    @if($activeTab === 'performance')
        <div class="animate-rise">
            <p class="font-mono text-[10.5px] tracking-[0.14em] text-muted">PARTICIPANT PERFORMANCE ANALYSIS</p>
            <x-exam.empty
                title="No performance data"
                description="Track participant performance after completing exams."
            />
        </div>
    @endif

    {{-- ==================== IDENTITY TAB ==================== --}}
    @if($activeTab === 'identity')
        <div class="animate-rise">
            <p class="font-mono text-[10.5px] tracking-[0.14em] text-muted">YOUR EXAM IDENTIFICATION DETAILS</p>
            <livewire:mock-exam.user-identity-form />
        </div>
    @endif
</x-exam.page>