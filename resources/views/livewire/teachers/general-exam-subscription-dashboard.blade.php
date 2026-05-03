<div>
    {{-- Tab Navigation --}}
    <div class="flex gap-1 mb-6 border-b border-gray-200 dark:border-gray-700">
        @foreach(['subscriptions' => 'My Subscriptions', 'results' => 'Results & Scores', 'performance' => 'Participant Performance'] as $tab => $label)
            <button wire:click="$set('activeTab', '{{ $tab }}')"
                    class="px-4 py-2 text-sm font-medium border-b-2 transition
                        {{ $activeTab === $tab ? 'border-violet-500 text-violet-600 dark:text-violet-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300' }}">
                {{ $label }}
            </button>
        @endforeach
    </div>

    {{-- ==================== SUBSCRIPTIONS TAB ==================== --}}
    @if($activeTab === 'subscriptions')
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-base font-semibold text-gray-800 dark:text-gray-100">My Exam Subscriptions</h2>
            <button wire:click="$set('showPurchaseForm', true)"
                    class="px-4 py-2 text-sm font-medium text-white bg-violet-600 rounded-lg hover:bg-violet-700">
                + New Subscription
            </button>
        </div>

        {{-- Purchase Form --}}
        @if($showPurchaseForm)
            <div class="mb-6 p-5 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">Configure Subscription</h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Plan</label>
                        <select wire:model.live="planId" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 text-sm">
                            <option value="0">Select plan...</option>
                            @foreach($plans as $plan)
                                <option value="{{ $plan->id }}">{{ $plan->name }} ({{ $plan->type }})</option>
                            @endforeach
                        </select>
                        @error('planId') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Delivery Type</label>
                        <select wire:model.live="type" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 text-sm">
                            <option value="online">Online (students take on platform)</option>
                            <option value="print">Print (answer sheet generated)</option>
                        </select>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Subjects (select all that apply)</label>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Academic Group</label>
                            <select wire:model.live="selectedAcademicGroupId" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 text-sm">
                                <option value="">Select group...</option>
                                @foreach($this->academicGroups as $group)
                                    <option value="{{ $group->id }}">{{ $group->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Academic Level</label>
                            <select wire:model.live="selectedAcademicLevelId" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 text-sm" {{ !$selectedAcademicGroupId ? 'disabled' : '' }}>
                                <option value="">Select level...</option>
                                @foreach($this->academicLevels as $level)
                                    <option value="{{ $level->id }}">{{ $level->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    @if($this->selectedSubjects->isNotEmpty())
                        <div class="flex flex-wrap gap-1.5 mb-2">
                            @foreach($this->selectedSubjects as $subject)
                                <span wire:key="sel-{{ $subject->id }}"
                                      class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-violet-100 text-violet-700 dark:bg-violet-900/30 dark:text-violet-300">
                                    {{ $subject->name }}
                                    @if($subject->academicLevel)
                                        <span class="opacity-80">
                                            ({{ $subject->academicLevel->academicGroup->name ?? 'N/A' }} • {{ $subject->academicLevel->name }})
                                        </span>
                                    @endif
                                    <button wire:click="removeSubject({{ $subject->id }})" class="hover:text-red-500">&times;</button>
                                </span>
                            @endforeach
                        </div>
                    @endif

                    <div class="relative">
                        <input type="text" wire:model.live="subjectSearch"
                               placeholder="Type to search subjects in selected level..."
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
                                No subjects found. Ask your administrator to create subjects first.
                            </div>
                        @endif
                    </div>

                    @if($selectedAcademicLevelId)
                        <div class="mt-2 max-h-44 overflow-y-auto rounded-lg border border-gray-200 dark:border-gray-700">
                            @forelse($this->filteredSubjects as $subject)
                                <button wire:click="addSubject({{ $subject->id }})"
                                        class="w-full text-left px-3 py-2 text-sm hover:bg-violet-50 dark:hover:bg-violet-900/20 text-gray-700 dark:text-gray-300 border-b last:border-b-0 border-gray-100 dark:border-gray-700">
                                    {{ $subject->name }}
                                </button>
                            @empty
                                <div class="px-3 py-2 text-xs text-gray-500 dark:text-gray-400">No available subjects in this level.</div>
                            @endforelse
                        </div>
                    @endif
                    @error('selectedSubjectIds') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                @if($type === 'online')
                    <div class="mb-4">
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Number of Participants</label>
                        <input type="number" wire:model.live="participantCount" min="1"
                               class="w-full sm:w-48 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 text-sm" />
                        @error('participantCount') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                @endif

                <div class="mb-4">
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Exam Cycles Per Subject</label>
                    <input type="number" wire:model.live="maxExams" min="1"
                           class="w-full sm:w-48 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 text-sm" />
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Total generations = selected subjects × cycles per subject.</p>
                    @error('maxExams') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                @if($calculatedPrice > 0)
                    <div class="mb-4 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-700">
                        <p class="text-sm text-blue-700 dark:text-blue-300">
                            Total: <strong>GHS {{ number_format($calculatedPrice, 2) }}</strong>
                            @if($type === 'online')
                                <span class="text-xs text-blue-500">({{ count($selectedSubjectIds) }} subject(s) × {{ $participantCount }} participants)</span>
                            @else
                                <span class="text-xs text-blue-500">({{ count($selectedSubjectIds) }} subject(s) flat rate)</span>
                            @endif
                        </p>
                    </div>
                @endif

                <div class="flex gap-2 justify-end">
                    <button wire:click="$set('showPurchaseForm', false)"
                            class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
                        Cancel
                    </button>
                    <button wire:click="purchase"
                            class="px-4 py-2 text-sm font-medium text-white bg-violet-600 rounded-lg hover:bg-violet-700">
                        Proceed to Payment
                    </button>
                </div>
            </div>
        @endif

        {{-- Top-up Modal --}}
        @if($showTopUpForm)
            <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 w-full max-w-md shadow-xl">
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">Add Participant Slots</h3>
                    <div class="mb-4">
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Additional Participants</label>
                        <input type="number" wire:model.live="additionalParticipants" min="1"
                               class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 text-sm" />
                    </div>
                    @if($topUpPrice > 0)
                        <p class="text-sm text-blue-700 dark:text-blue-300 mb-4">
                            Top-up cost: <strong>GHS {{ number_format($topUpPrice, 2) }}</strong>
                        </p>
                    @endif
                    <div class="flex gap-2 justify-end">
                        <button wire:click="$set('showTopUpForm', false)"
                                class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400 border border-gray-300 dark:border-gray-600 rounded-lg">Cancel</button>
                        <button wire:click="processTopUp"
                                class="px-4 py-2 text-sm font-medium text-white bg-violet-600 rounded-lg hover:bg-violet-700">Pay & Add Slots</button>
                    </div>
                </div>
            </div>
        @endif

        {{-- Subscriptions List --}}
        <div class="grid gap-4">
            @forelse($subscriptions as $sub)
                <div wire:key="sub-{{ $sub->id }}"
                     class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <span class="font-medium text-gray-800 dark:text-gray-100">{{ $sub->plan->name }}</span>
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $sub->type === 'online' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' : 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400' }}">
                                    {{ ucfirst($sub->type) }}
                                </span>
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium
                                    {{ $sub->status->value === 'active' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400' }}">
                                    {{ ucfirst($sub->status->value) }}
                                </span>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                Subjects: {{ $sub->subjects->pluck('name')->join(', ') ?: 'None' }}
                            </p>
                            @if($sub->type === 'online')
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                    Participants: {{ $sub->participants_used }}/{{ $sub->participant_slots }} used
                                    &nbsp;|&nbsp; Exams: {{ $sub->exams_used }}{{ $sub->max_exams ? '/'.$sub->max_exams : '' }}
                                </p>
                            @endif
                            @if($sub->expires_at)
                                <p class="text-xs text-gray-400 mt-0.5">Expires: {{ $sub->expires_at->format('M d, Y') }}</p>
                            @endif
                        </div>
                        <div class="flex flex-col items-end gap-2">
                            <button wire:click="$set('selectedSubscriptionId', {{ $sub->id }}); $set('activeTab', 'results')"
                                    class="text-xs text-violet-600 dark:text-violet-400 hover:underline">View Results</button>
                            @if($sub->type === 'online' && $sub->isActive())
                                <button wire:click="openTopUp({{ $sub->id }})"
                                        class="text-xs text-blue-600 dark:text-blue-400 hover:underline">Add Slots</button>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-12 text-gray-400 dark:text-gray-500">
                    <p class="text-sm">No subscriptions yet. Purchase one to start creating exams.</p>
                </div>
            @endforelse
        </div>
    @endif

    {{-- ==================== RESULTS TAB ==================== --}}
    @if($activeTab === 'results')
        <div class="flex gap-4 mb-4">
            <select wire:model.live="selectedSubscriptionId"
                    class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 text-sm">
                <option value="">Select subscription...</option>
                @foreach($subscriptions as $sub)
                    <option value="{{ $sub->id }}">{{ $sub->plan->name }} — {{ $sub->subjects->pluck('name')->join(', ') }}</option>
                @endforeach
            </select>

            @if($selectedSubscription)
                <select wire:model.live="selectedExamId"
                        class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 text-sm">
                    <option value="">Select exam...</option>
                    @foreach($exams as $exam)
                        <option value="{{ $exam->id }}">{{ $exam->title }} ({{ $exam->submissions_count }} submissions)</option>
                    @endforeach
                </select>
            @endif
        </div>

        @if($selectedExamId && $submissions->count())
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Participant</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Score</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Grade</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Started</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Submitted</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Duration</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($submissions as $submission)
                            <tr wire:key="submission-{{ $submission->id }}" class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                                <td class="px-4 py-3">
                                    <p class="font-medium text-gray-800 dark:text-gray-100">{{ $submission->getParticipantName() }}</p>
                                    <p class="text-xs text-gray-400">{{ $submission->getParticipantEmail() }}</p>
                                </td>
                                <td class="px-4 py-3 text-gray-700 dark:text-gray-300">
                                    {{ $submission->score }}/{{ $submission->total_marks }}
                                    <span class="text-xs text-gray-400">({{ $submission->percentage }}%)</span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-0.5 rounded-full text-xs font-bold
                                        {{ in_array($submission->grade, ['A+','A']) ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : '' }}
                                        {{ $submission->grade === 'B' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' : '' }}
                                        {{ $submission->grade === 'C' ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400' : '' }}
                                        {{ in_array($submission->grade, ['D','F']) ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' : '' }}">
                                        {{ $submission->grade ?? '—' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-xs text-gray-500 dark:text-gray-400">{{ $submission->started_at?->format('M d, H:i') ?? '—' }}</td>
                                <td class="px-4 py-3 text-xs text-gray-500 dark:text-gray-400">{{ $submission->submitted_at?->format('M d, H:i') ?? '—' }}</td>
                                <td class="px-4 py-3 text-xs text-gray-500 dark:text-gray-400">
                                    @if($submission->time_spent_seconds)
                                        {{ gmdate('H:i:s', $submission->time_spent_seconds) }}
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <button wire:click="openScoreEditor({{ $submission->id }})"
                                            class="text-xs text-violet-600 dark:text-violet-400 hover:underline">Edit Scores</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="px-4 py-3 border-t border-gray-100 dark:border-gray-700">
                    {{ $submissions->links() }}
                </div>
            </div>
        @elseif($selectedExamId)
            <p class="text-sm text-gray-400 dark:text-gray-500 text-center py-8">No submissions for this exam yet.</p>
        @endif

        {{-- Score Editor Modal --}}
        @if($showScoreEditor && $editingSubmission)
            <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
                <div class="bg-white dark:bg-gray-800 rounded-xl w-full max-w-2xl shadow-xl max-h-[90vh] flex flex-col">
                    <div class="p-5 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="font-semibold text-gray-800 dark:text-gray-100">Edit Scores — {{ $editingSubmission->getParticipantName() }}</h3>
                    </div>
                    <div class="p-5 overflow-y-auto flex-1">
                        @foreach($editingSubmission->assignment->questions as $question)
                            <div wire:key="q-{{ $question->id }}" class="mb-4 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                <p class="text-sm text-gray-700 dark:text-gray-300 mb-2">{{ $question->question }}</p>
                                <div class="flex items-center gap-3">
                                    <label class="text-xs text-gray-500 dark:text-gray-400">Points (max {{ $question->marks }}):</label>
                                    <input type="number" step="0.5" min="0" max="{{ $question->marks }}"
                                           wire:model="editedScores.{{ $question->id }}"
                                           class="w-24 rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 text-sm" />
                                </div>
                            </div>
                        @endforeach
                        <div class="mt-4">
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Reason for change (optional)</label>
                            <textarea wire:model="scoreEditReason" rows="2"
                                      class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 text-sm"></textarea>
                        </div>

                        @if($auditLogs->isNotEmpty())
                            <div class="mt-4">
                                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase mb-2">Audit History</p>
                                @foreach($auditLogs as $log)
                                    <div wire:key="log-{{ $log->id }}" class="text-xs text-gray-500 dark:text-gray-400 mb-1">
                                        {{ $log->editor->name }} changed score from {{ $log->old_score }} to {{ $log->new_score }}
                                        ({{ $log->old_grade }} → {{ $log->new_grade }})
                                        at {{ $log->created_at->format('M d, H:i') }}
                                        @if($log->reason) — "{{ $log->reason }}" @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    <div class="p-5 border-t border-gray-200 dark:border-gray-700 flex gap-2 justify-end">
                        <button wire:click="$set('showScoreEditor', false)"
                                class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400 border border-gray-300 dark:border-gray-600 rounded-lg">Cancel</button>
                        <button wire:click="saveScores"
                                class="px-4 py-2 text-sm font-medium text-white bg-violet-600 rounded-lg hover:bg-violet-700">Save & Recalculate</button>
                    </div>
                </div>
            </div>
        @endif
    @endif

    {{-- ==================== PERFORMANCE TAB ==================== --}}
    @if($activeTab === 'performance')
        <div class="mb-6">
            <h2 class="text-base font-semibold text-gray-800 dark:text-gray-100 mb-3">Participant Performance Over Time</h2>
            <div class="flex gap-3">
                <input type="text" wire:model="performanceSearch" placeholder="Search by name or email..."
                       class="flex-1 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 text-sm" />
                <button wire:click="searchPerformance"
                        class="px-4 py-2 text-sm font-medium text-white bg-violet-600 rounded-lg hover:bg-violet-700">Search</button>
            </div>
            @error('performanceSearch') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        @if(!empty($performanceResults))
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 text-center">
                    <p class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $performanceResults['total_exams'] }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Exams Taken</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 text-center">
                    <p class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ number_format($performanceResults['average_score'], 1) }}%</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Average Score</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 text-center">
                    <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ number_format($performanceResults['highest_score'], 1) }}%</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Highest Score</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 text-center">
                    <p class="text-2xl font-bold text-red-500 dark:text-red-400">{{ number_format($performanceResults['lowest_score'], 1) }}%</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Lowest Score</p>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Exam</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Score</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Grade</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($performanceResults['submissions'] as $submission)
                            <tr wire:key="perf-{{ $submission->id }}">
                                <td class="px-4 py-3 text-gray-800 dark:text-gray-100">{{ $submission->assignment->title }}</td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $submission->score }}/{{ $submission->total_marks }} ({{ $submission->percentage }}%)</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-0.5 rounded-full text-xs font-bold
                                        {{ in_array($submission->grade, ['A+','A']) ? 'bg-green-100 text-green-700' : '' }}
                                        {{ $submission->grade === 'B' ? 'bg-blue-100 text-blue-700' : '' }}
                                        {{ in_array($submission->grade, ['C','D','F']) ? 'bg-red-100 text-red-700' : '' }}">
                                        {{ $submission->grade ?? '—' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-xs text-gray-400">{{ $submission->submitted_at?->format('M d, Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    @endif
</div>
