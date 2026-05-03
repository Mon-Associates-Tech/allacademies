<div>
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">General Exam Subscriptions</h2>
        <button wire:click="openAllocationForm"
                class="px-4 py-2 text-sm font-medium text-white bg-violet-600 rounded-lg hover:bg-violet-700">
            Allocate Subscription
        </button>
    </div>

    {{-- Allocation Form --}}
    @if($showAllocationForm)
        <div class="mb-6 p-5 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">Allocate Subscription to User</h3>

            {{-- User Search --}}
            <div class="mb-4" x-data>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">User</label>
                @if($selectedUserId)
                    <div class="flex items-center gap-2 p-2 bg-violet-50 dark:bg-violet-900/20 rounded-lg border border-violet-200 dark:border-violet-700">
                        <span class="text-sm text-violet-700 dark:text-violet-300 font-medium">{{ $selectedUserName }}</span>
                        <button wire:click="$set('selectedUserId', null)" class="ml-auto text-xs text-gray-400 hover:text-red-500">✕</button>
                    </div>
                @else
                    <input type="text" wire:model.live="userSearch" placeholder="Search by name or email..."
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 text-sm" />
                    @if($this->userSearchResults->isNotEmpty())
                        <div class="mt-1 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 shadow-lg max-h-48 overflow-y-auto">
                            @foreach($this->userSearchResults as $user)
                                <button wire:click="selectUser({{ $user->id }}, '{{ addslashes($user->name) }}')"
                                        class="w-full text-left px-3 py-2 text-sm hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300">
                                    {{ $user->name }} <span class="text-gray-400 text-xs">{{ $user->email }}</span>
                                </button>
                            @endforeach
                        </div>
                    @endif
                @endif
                @error('selectedUserId') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                {{-- Plan --}}
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Plan</label>
                    <select wire:model.live="planId" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 text-sm">
                        <option value="0">Select plan...</option>
                        @foreach($this->plans as $plan)
                            <option value="{{ $plan->id }}">{{ $plan->name }} ({{ $plan->type }})</option>
                        @endforeach
                    </select>
                    @error('planId') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Type --}}
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Delivery Type</label>
                    <select wire:model.live="type" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 text-sm">
                        <option value="online">Online</option>
                        <option value="print">Print</option>
                    </select>
                </div>
            </div>

            {{-- Subjects --}}
            <div class="mb-4">
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Subjects</label>

                @if($this->selectedSubjects->isNotEmpty())
                    <div class="flex flex-wrap gap-1.5 mb-2">
                        @foreach($this->selectedSubjects as $subject)
                            <span wire:key="sel-{{ $subject->id }}"
                                  class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-violet-100 text-violet-700 dark:bg-violet-900/30 dark:text-violet-300">
                                {{ $subject->name }}
                                <button wire:click="removeSubject({{ $subject->id }})" class="hover:text-red-500">&times;</button>
                            </span>
                        @endforeach
                    </div>
                @endif

                <div class="relative">
                    <input type="text" wire:model.live="subjectSearch"
                           placeholder="Type to search subjects..."
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 text-sm" />

                    @if($this->subjectSearchResults->isNotEmpty())
                        <div class="absolute z-20 mt-1 w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-lg shadow-lg max-h-48 overflow-y-auto">
                            @foreach($this->subjectSearchResults as $subject)
                                <button wire:click="addSubject({{ $subject->id }})"
                                        class="w-full text-left px-3 py-2 text-sm hover:bg-violet-50 dark:hover:bg-violet-900/20 text-gray-700 dark:text-gray-300">
                                    {{ $subject->name }}
                                </button>
                            @endforeach
                        </div>
                    @elseif(strlen($subjectSearch) >= 1)
                        <div class="absolute z-20 mt-1 w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-lg shadow-lg px-3 py-2 text-sm text-gray-400">
                            No subjects found. Create subjects via Academic Management first.
                        </div>
                    @endif
                </div>
                @error('selectedSubjectIds') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
                @if($type === 'online')
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Participant Slots</label>
                        <input type="number" wire:model.live="participantCount" min="1"
                               class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 text-sm" />
                    </div>
                @endif
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Exam Cycles Per Subject</label>
                    <input type="number" wire:model="maxExams" min="1"
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 text-sm" />
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Allocation Mode</label>
                    <select wire:model="allocationMode" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 text-sm">
                        <option value="grant">Grant (no payment)</option>
                        <option value="payment">Require Payment</option>
                    </select>
                </div>
            </div>

            @if($calculatedPrice > 0)
                <div class="mb-4 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-700">
                    <p class="text-sm text-blue-700 dark:text-blue-300">
                        Calculated Price: <strong>GHS {{ number_format($calculatedPrice, 2) }}</strong>
                        @if($allocationMode === 'grant') <span class="text-xs">(waived — grant mode)</span> @endif
                    </p>
                </div>
            @endif

            <div class="flex gap-2 justify-end">
                <button wire:click="$set('showAllocationForm', false)"
                        class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
                    Cancel
                </button>
                <button wire:click="allocate"
                        class="px-4 py-2 text-sm font-medium text-white bg-violet-600 rounded-lg hover:bg-violet-700">
                    {{ $allocationMode === 'grant' ? 'Grant Subscription' : 'Proceed to Payment' }}
                </button>
            </div>
        </div>
    @endif

    {{-- Filters --}}
    <div class="flex gap-3 mb-4">
        <input type="text" wire:model.live="search" placeholder="Search by user..."
               class="flex-1 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 text-sm" />
        <select wire:model.live="statusFilter" class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 text-sm">
            <option value="">All Statuses</option>
            <option value="pending">Pending</option>
            <option value="active">Active</option>
            <option value="expired">Expired</option>
            <option value="cancelled">Cancelled</option>
        </select>
    </div>

    {{-- Subscriptions Table --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700/50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">User</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Plan</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Type</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Subjects</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Slots</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Granted By</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($subscriptions as $sub)
                    <tr wire:key="sub-{{ $sub->id }}" class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                        <td class="px-4 py-3">
                            <p class="font-medium text-gray-800 dark:text-gray-100">{{ $sub->user->name }}</p>
                            <p class="text-xs text-gray-400">{{ $sub->user->email }}</p>
                        </td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $sub->plan->name }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $sub->type === 'online' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' : 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400' }}">
                                {{ ucfirst($sub->type) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $sub->subjects->pluck('name')->join(', ') ?: '—' }}</td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-300">
                            @if($sub->type === 'online')
                                {{ $sub->participants_used }}/{{ $sub->participant_slots }}
                            @else
                                —
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium
                                {{ $sub->status->value === 'active' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : '' }}
                                {{ $sub->status->value === 'pending' ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400' : '' }}
                                {{ in_array($sub->status->value, ['expired','cancelled']) ? 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400' : '' }}">
                                {{ ucfirst($sub->status->value) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-400">
                            {{ $sub->granted_by_owner ? ($sub->grantedBy?->name ?? 'Owner') : 'Self' }}
                        </td>
                        <td class="px-4 py-3">
                            @if($sub->status->value === 'active')
                                <button wire:click="cancelSubscription({{ $sub->id }})"
                                        wire:confirm="Cancel this subscription?"
                                        class="text-xs text-red-500 hover:underline">Cancel</button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center text-sm text-gray-400 dark:text-gray-500">No subscriptions found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-4 py-3 border-t border-gray-100 dark:border-gray-700">
            {{ $subscriptions->links() }}
        </div>
    </div>
</div>
