<x-exam.page>
    <x-exam.header eyebrow="ADMIN · ALLOCATIONS & GRANTS" title="Subscriptions"
                   sub="Allocate, grant, and manage mock exam subscriptions across your users."
                   :lines="['EXAM', 'LEDGER']"/>

    <x-exam.flash/>


    <x-exam.tabs :tabs="['subscriptions' => 'Subscriptions', 'tiers' => 'Plans and Tiers']" :active="$activeTab"
                 property="activeTab"/>


    @if ($activeTab === 'subscriptions')
        <div class="mb-5 flex items-center justify-between gap-4">
            <p class="font-mono text-[10.5px] tracking-[0.14em] text-muted">SUBSCRIPTION REGISTRY</p>
            <x-exam.button type="button" wire:click="openAllocationForm">+ Allocate Subscription</x-exam.button>
        </div>

        @if ($showAllocationForm)
            <x-exam.card class="mb-6 animate-rise" :padded="false">
                <x-slot:header>
                    <p class="font-mono text-[10.5px] tracking-[0.14em] text-muted">ALLOCATE SUBSCRIPTION TO USER</p>
                </x-slot:header>

                <div class="space-y-5 p-5">
                    <x-exam.field bare label="User">
                        @if ($selectedUserId)
                            <div class="flex items-center gap-2 rounded border border-primary/25 bg-primary/5 p-2.5">
                                <span class="text-sm font-semibold text-ink">{{ $selectedUserName }}</span>
                                <button type="button" wire:click="$set('selectedUserId', null)"
                                        aria-label="Clear selected user"
                                        class="ml-auto grid h-5 w-5 place-items-center rounded text-muted transition hover:bg-accent/10 hover:text-accent">
                                    ✕
                                </button>
                            </div>
                        @else
                            <div class="relative">
                                <x-exam.input wire:model.live="userSearch" placeholder="Search by name or email…"/>
                                @if ($this->userSearchResults->isNotEmpty())
                                    <div
                                        class="absolute z-20 mt-1 max-h-48 w-full overflow-y-auto rounded-md border border-line bg-surface shadow-lg">
                                        @foreach ($this->userSearchResults as $user)
                                            <button type="button"
                                                    wire:click="selectUser({{ $user->id }}, '{{ addslashes($user->name) }}')"
                                                    class="flex w-full items-baseline justify-between gap-2 border-b border-line px-3 py-2 text-left text-sm text-ink transition last:border-b-0 hover:bg-primary/5">
                                                <span>{{ $user->name }}</span>
                                                <span
                                                    class="font-mono text-[10.5px] text-muted">{{ $user->email }}</span>
                                            </button>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endif
                    </x-exam.field>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                        <x-exam.field bare label="Plan">
                            <x-exam.select wire:model.live="planId">
                                <option value="0">Select plan…</option>
                                @foreach ($this->plans as $plan)
                                    <option value="{{ $plan->id }}">{{ $plan->name }} ({{ $plan->type }})
                                    </option>
                                @endforeach
                            </x-exam.select>
                        </x-exam.field>

                        <x-exam.field bare label="Delivery Type">
                            <x-exam.select wire:model.live="type">
                                <option value="print">Print</option>
                                <option value="online">Online</option>
                            </x-exam.select>
                        </x-exam.field>

                        <x-exam.field bare label="User Group">
                            <x-exam.select wire:model.live="userGroup">
                                @foreach (\App\MockExam\Models\MockExamUserGroup::options() as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </x-exam.select>
                        </x-exam.field>
                    </div>

                    <x-exam.field bare label="Subjects (each subject can only be added once)">
                        <x-exam.chips :items="$this->selectedSubjects" remove-action="removeSubject"/>

                        <div class="relative">
                            <x-exam.input wire:model.live="subjectSearch" placeholder="Type to search subjects…"/>
                            <x-exam.picklist :items="$this->subjectSearchResults" action="addSubject"
                                             :empty="strlen($subjectSearch) >= 1 ? 'No subjects found.' : null"/>
                        </div>
                    </x-exam.field>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                        @if ($type === 'online')
                            <x-exam.field bare label="Participant Slots">
                                <x-exam.input type="number" min="1" wire:model.live="participantCount"/>
                            </x-exam.field>
                        @endif

                        <x-exam.field bare label="Exam Cycles Per Subject">
                            <x-exam.input type="number" min="1" wire:model="maxExams"/>
                            <p class="mt-1.5 text-xs text-muted">How many times each subject can be printed/used.</p>
                        </x-exam.field>

                        <x-exam.field bare label="Allocation Mode">
                            <x-exam.select wire:model="allocationMode">
                                <option value="grant">Grant (no payment)</option>
                                <option value="payment">Require Payment</option>
                            </x-exam.select>
                        </x-exam.field>
                    </div>

                    @if ($calculatedPrice > 0)
                        <x-exam.callout>
                            <p class="font-display text-base font-bold text-primary">
                                GHS {{ number_format($calculatedPrice, 2) }}
                                @if ($allocationMode === 'grant')
                                    <span
                                        class="ml-1 font-sans text-xs font-normal text-muted">(waived — grant mode)</span>
                                @endif
                            </p>
                            <p class="mt-1 font-mono text-[11px] tracking-wide text-muted">
                                {{ count($selectedSubjectIds) }} SUBJECT(S) ×
                                {{ \App\MockExam\Models\MockExamUserGroup::from($userGroup)->label() }} RATE
                            </p>
                        </x-exam.callout>
                    @endif
                </div>

                <x-slot:footer>
                    <div class="flex justify-end gap-2">
                        <x-exam.button type="button" variant="ghost"
                                       wire:click="$set('showAllocationForm', false)">Cancel
                        </x-exam.button>
                        <x-exam.button type="button" wire:click="allocate">
                            {{ $allocationMode === 'grant' ? 'Grant Subscription' : 'Proceed to Payment' }}
                        </x-exam.button>
                    </div>
                </x-slot:footer>
            </x-exam.card>
        @endif
        {{-- Filters --}}
        <div class="mb-4 flex flex-col gap-3 sm:flex-row">
            <div class="flex-1">
                <x-exam.input wire:model.live="search" placeholder="Search by user…"/>
            </div>
            <x-exam.select wire:model.live="statusFilter" placeholder="All Statuses" class="sm:w-52">
                <option value="pending">Pending</option>
                <option value="active">Active</option>
                <option value="expired">Expired</option>
                <option value="cancelled">Cancelled</option>
            </x-exam.select>
        </div>

        <x-exam.table
            :headers="['User', 'Plan', 'Type', 'User Group', 'Subjects', 'Cycles', 'Status', 'Granted By', '']">
            @forelse($subscriptions as $sub)
                @php
                    $groupLabel = $sub->user_group instanceof \App\MockExam\Models\MockExamUserGroup ? $sub->user_group->label() : ucfirst(str_replace('_', ' ', $sub->user_group ?? '—'));
                $statusTone = ['active' => 'ok', 'pending' => 'warn', 'expired' => 'muted', 'cancelled' => 'muted'][$sub->status->value] ?? 'muted';
                @endphp
                <tr wire:key="sub-{{ $sub->id }}" class="transition hover:bg-surface-2/60">
                    <td class="px-4 py-3">
                        <p class="font-semibold text-ink">{{ $sub->user->name }}</p>
                        <p class="font-mono text-[10.5px] text-muted">{{ $sub->user->email }}</p>
                    </td>
                    <td class="px-4 py-3 text-ink-soft">{{ $sub->plan->name }}</td>
                    <td class="px-4 py-3">
                        <x-exam.badge
                            :tone="$sub->type === 'online' ? 'primary' : 'warn'">{{ ucfirst($sub->type) }}</x-exam.badge>
                    </td>
                    <td class="px-4 py-3 text-xs text-ink-soft">{{ $groupLabel }}</td>
                    <td class="px-4 py-3 text-xs text-ink-soft">{{ $sub->subjects->pluck('name')->join(', ') ?: '—' }}
                    </td>
                    <td class="px-4 py-3 text-center font-mono text-xs text-ink-soft">{{ $sub->max_exams ?? 1 }}</td>
                    <td class="px-4 py-3">
                        <x-exam.badge
                            :tone="$statusTone">{{ ucfirst($sub->status->value) }}</x-exam.badge>
                    </td>
                    <td class="px-4 py-3 font-mono text-[10.5px] tracking-wide text-muted">
                        {{ $sub->granted_by_owner ? $sub->grantedBy?->name ?? 'Owner' : 'Self' }}</td>
                    <td class="px-4 py-3">
                        @if ($sub->status->value === 'active')
                            <x-exam.link-button tone="danger" wire:click="cancelSubscription({{ $sub->id }})"
                                                wire:confirm="Cancel this subscription?">Cancel
                            </x-exam.link-button>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="px-4 py-10 text-center font-mono text-xs tracking-wider text-muted">NO
                        SUBSCRIPTIONS FOUND
                    </td>
                </tr>
            @endforelse

            <x-slot:footer>{{ $subscriptions->links() }}</x-slot:footer>
        </x-exam.table>
    @endif

    @if($activeTab === 'tiers')
        @livewire('mock-exam.subscriptions.mock-exam-pricing-tier-manager')

    @endif


</x-exam.page>
