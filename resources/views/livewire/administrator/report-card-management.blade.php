<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Report Card Management</h1>
        <div class="flex flex-wrap gap-2">
            <select wire:model.live="selectedYearId" class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                <option value="">All Years</option>
                @foreach($years as $year)
                    <option value="{{ $year->id }}">{{ $year->name }}</option>
                @endforeach
            </select>
            <select wire:model.live="selectedPeriodId" class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                <option value="">All Periods</option>
                @foreach($periods as $period)
                    <option value="{{ $period->id }}">{{ $period->name }}</option>
                @endforeach
            </select>
            <select wire:model.live="selectedLevelId" class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                <option value="">All Levels</option>
                @foreach($levels as $level)
                    <option value="{{ $level->id }}">{{ $level->name }}</option>
                @endforeach
            </select>
            <select wire:model.live="selectedGroupId" class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                <option value="">All Groups</option>
                @foreach($groups as $group)
                    <option value="{{ $group->id }}">{{ $group->name }}</option>
                @endforeach
            </select>
            <select wire:model.live="selectedStudentId" class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                <option value="">All Students</option>
                @foreach($students as $student)
                    <option value="{{ $student->id }}">{{ $student->user->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Tabs -->
    <div class="border-b border-gray-200 dark:border-gray-700 mb-6">
        <nav class="flex gap-8">
            <button wire:click="$set('activeTab', 'configurations')" class="py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'configurations' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                Configurations
            </button>
            <button wire:click="$set('activeTab', 'report-cards')" class="py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'report-cards' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                All Report Cards
            </button>
            <button wire:click="$set('activeTab', 'approvals')" class="py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'approvals' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                Pending Approvals ({{ $pendingApprovals->total() }})
            </button>
            <button wire:click="$set('activeTab', 'grade-scales')" class="py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'grade-scales' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                Grade Scales
            </button>
            <button wire:click="$set('activeTab', 'weightings')" class="py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'weightings' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                Score Weightings
            </button>
        </nav>
    </div>

    <!-- Configurations Tab -->
    @if($activeTab === 'configurations')
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Report Card Configurations</h3>
                    <button wire:click="openConfigModal" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        New Configuration
                    </button>
                </div>

                <div class="space-y-4">
                    @forelse($configurations as $config)
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="font-medium text-gray-900 dark:text-white">{{ $config->academicLevel->name }}</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $config->academicPeriod->name }} - {{ $config->academicPeriod->academic_year }}</p>
                                    <div class="flex gap-2 mt-2">
                                        <span class="px-2 py-1 text-xs rounded {{ $config->is_published ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                            {{ $config->is_published ? 'Published' : 'Draft' }}
                                        </span>
                                        <span class="px-2 py-1 text-xs rounded bg-blue-100 text-blue-800">
                                            {{ ucfirst($config->preparation_mode) }}
                                        </span>
                                        @if($config->requires_approval)
                                            <span class="px-2 py-1 text-xs rounded bg-yellow-100 text-yellow-800">Requires Approval</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex gap-2">
                                    <button wire:click="generateReportCards({{ $config->id }})" class="px-3 py-1 text-sm bg-green-600 text-white rounded hover:bg-green-700" wire:loading.attr="disabled">
                                        <span wire:loading.remove wire:target="generateReportCards({{ $config->id }})">
                                            @if($selectedStudentId)
                                                Generate for Student
                                            @elseif($selectedGroupId)
                                                Generate for Group
                                            @else
                                                Generate for Level
                                            @endif
                                        </span>
                                        <span wire:loading wire:target="generateReportCards({{ $config->id }})">Generating...</span>
                                    </button>
                                    <button wire:click="togglePublishConfig({{ $config->id }})" class="px-3 py-1 text-sm {{ $config->is_published ? 'bg-yellow-600 hover:bg-yellow-700' : 'bg-indigo-600 hover:bg-indigo-700' }} text-white rounded">
                                        {{ $config->is_published ? 'Unpublish' : 'Publish' }}
                                    </button>
                                    <button wire:click="openConfigModal({{ $config->id }})" class="px-3 py-1 text-sm bg-gray-600 text-white rounded hover:bg-gray-700">
                                        Edit
                                    </button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 dark:text-gray-400 text-center py-8">No configurations found</p>
                    @endforelse
                </div>
            </div>
        </div>
    @endif

    <!-- All Report Cards Tab -->
    @if($activeTab === 'report-cards')
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">All Report Cards</h3>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead>
                            <tr>
                                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Student</th>
                                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Level/Term</th>
                                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Status</th>
                                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Accessible</th>
                                <th class="px-3 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($reportCards as $rc)
                                <tr>
                                    <td class="px-3 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $rc->student->user->name }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $rc->student->student_id }}</div>
                                    </td>
                                    <td class="px-3 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-white">{{ $rc->configuration?->academicLevel?->name ?? 'N/A' }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $rc->term }}</div>
                                    </td>
                                    <td class="px-3 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs rounded-full
                                            {{ $rc->status === 'approved' ? 'bg-green-100 text-green-800' :
                                               ($rc->status === 'submitted' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                                            {{ ucfirst($rc->status) }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ $rc->is_accessible ? 'Yes' : 'No' }}
                                    </td>
                                    <td class="px-3 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        @if($rc->status !== 'approved')
                                            <button wire:click="approveReportCard({{ $rc->id }})" class="text-green-600 hover:text-green-900 mr-2">Approve</button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-3 py-8 text-center text-gray-500 dark:text-gray-400">No report cards found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $reportCards->links() }}
                </div>
            </div>
        </div>
    @endif

    <!-- Approvals Tab -->
    @if($activeTab === 'approvals')
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Pending Approvals</h3>

                <div class="space-y-4">
                    @forelse($pendingApprovals as $reportCard)
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="font-medium text-gray-900 dark:text-white">{{ $reportCard->student->user->name }}</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ $reportCard->configuration?->academicLevel?->name ?? 'N/A' }} - {{ $reportCard->term }}
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        Submitted {{ $reportCard->submitted_at->diffForHumans() }}
                                    </p>
                                </div>
                                <div class="flex gap-2">
                                    <button wire:click="approveReportCard({{ $reportCard->id }})" class="px-3 py-1 text-sm bg-green-600 text-white rounded hover:bg-green-700">
                                        Approve
                                    </button>
                                    <button onclick="if(confirm('Enter rejection reason:')) { let reason = prompt('Reason:'); if(reason) @this.rejectReportCard({{ $reportCard->id }}, reason); }" class="px-3 py-1 text-sm bg-red-600 text-white rounded hover:bg-red-700">
                                        Reject
                                    </button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 dark:text-gray-400 text-center py-8">No pending approvals</p>
                    @endforelse
                </div>

                <div class="mt-4">
                    {{ $pendingApprovals->links() }}
                </div>
            </div>
        </div>
    @endif

    <!-- Grade Scales Tab -->
    @if($activeTab === 'grade-scales')
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Grade Scales</h3>
                    <button wire:click="openGradeScaleModal" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        Add Grade Scale
                    </button>
                </div>

                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead>
                        <tr>
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Grade</th>
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Range</th>
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Remarks</th>
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Scope</th>
                            <th class="px-3 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($gradeScales as $scale)
                            <tr>
                                <td class="px-3 py-3 text-sm font-medium text-gray-900 dark:text-white">{{ $scale->letter_grade }}</td>
                                <td class="px-3 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $scale->min_score }} - {{ $scale->max_score }}</td>
                                <td class="px-3 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $scale->remarks }}</td>
                                <td class="px-3 py-3 text-sm">
                                    <span class="px-2 py-1 text-xs rounded {{ $scale->is_default ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ $scale->is_default ? 'School-wide' : ($scale->academicLevel->name ?? 'Level') }}
                                    </span>
                                </td>
                                <td class="px-3 py-3 text-sm text-right">
                                    <button wire:click="openGradeScaleModal({{ $scale->id }})" class="text-blue-600 hover:text-blue-800">Edit</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-3 py-8 text-center text-gray-500 dark:text-gray-400">No grade scales found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Weightings Tab -->
    @if($activeTab === 'weightings')
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Score Weightings</h3>
                    <button wire:click="openWeightingModal" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        Add Weighting
                    </button>
                </div>

                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead>
                        <tr>
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Name</th>
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Weight %</th>
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Scope</th>
                            <th class="px-3 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($weightings as $weighting)
                            <tr>
                                <td class="px-3 py-3 text-sm font-medium text-gray-900 dark:text-white">{{ $weighting->name }}</td>
                                <td class="px-3 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $weighting->weight_percentage }}%</td>
                                <td class="px-3 py-3 text-sm">
                                    <span class="px-2 py-1 text-xs rounded {{ $weighting->is_default ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ $weighting->is_default ? 'School-wide' : ($weighting->academicLevel->name ?? 'Level') }}
                                    </span>
                                </td>
                                <td class="px-3 py-3 text-sm text-right">
                                    <button wire:click="openWeightingModal({{ $weighting->id }})" class="text-blue-600 hover:text-blue-800">Edit</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-3 py-8 text-center text-gray-500 dark:text-gray-400">No weightings found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Configuration Modal -->
    <x-modal-component name="configModal" title="{{ $configId ? 'Edit' : 'New' }} Configuration" size="2xl">
        <x-slot name="body">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Academic Period</label>
                    <select wire:model="selectedPeriodId" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                        @foreach($periods as $period)
                            <option value="{{ $period->id }}">{{ $period->name }} ({{ $period->academic_year }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Academic Level</label>
                    <select wire:model="selectedLevelId" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                        @foreach($levels as $level)
                            <option value="{{ $level->id }}">{{ $level->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Preparation Mode</label>
                    <select wire:model="preparationMode" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                        <option value="manual">Manual Only</option>
                        <option value="automated">Automated Only</option>
                        <option value="hybrid">Hybrid (Auto + Manual)</option>
                    </select>
                </div>

                <div class="flex items-center gap-4">
                    <label class="flex items-center">
                        <input type="checkbox" wire:model="requiresApproval" class="rounded border-gray-300">
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Requires Approval</span>
                    </label>

                    <label class="flex items-center">
                        <input type="checkbox" wire:model="isPublished" class="rounded border-gray-300">
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Published</span>
                    </label>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Available From</label>
                        <input type="datetime-local" wire:model="availableFrom" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Available Until</label>
                        <input type="datetime-local" wire:model="availableUntil" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    </div>
                </div>
            </div>
        </x-slot>

        <x-slot name="actions">
            <button @click="$dispatch('close-modal', { name: 'configModal' })" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                Cancel
            </button>
            <button wire:click="saveConfiguration" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                Save
            </button>
        </x-slot>
    </x-modal-component>

    <!-- Grade Scale Modal -->
    <x-modal-component name="gradeScaleModal" title="{{ $gradeScaleId ? 'Edit' : 'Add' }} Grade Scale" size="2xl">
        <x-slot name="body">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
                    <input type="text" wire:model="gradeName" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Min Score</label>
                        <input type="number" wire:model="minScore" step="0.01" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Max Score</label>
                        <input type="number" wire:model="maxScore" step="0.01" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Letter Grade</label>
                        <input type="text" wire:model="letterGrade" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Grade Point</label>
                        <input type="number" wire:model="gradePoint" step="0.01" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Remarks</label>
                    <input type="text" wire:model="gradeRemarks" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Academic Level (Optional)</label>
                    <select wire:model="gradeLevelId" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                        <option value="">School-wide</option>
                        @foreach($levels as $level)
                            <option value="{{ $level->id }}">{{ $level->name }}</option>
                        @endforeach
                    </select>
                </div>

                <label class="flex items-center">
                    <input type="checkbox" wire:model="isDefaultGrade" class="rounded border-gray-300">
                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Set as Default</span>
                </label>
            </div>
        </x-slot>

        <x-slot name="actions">
            <button @click="$dispatch('close-modal', { name: 'gradeScaleModal' })" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                Cancel
            </button>
            <button wire:click="saveGradeScale" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                Save
            </button>
        </x-slot>
    </x-modal-component>

    <!-- Weighting Modal -->
    <x-modal-component name="weightingModal" title="{{ $weightingId ? 'Edit' : 'Add' }} Score Weighting" size="2xl">
        <x-slot name="body">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
                    <input type="text" wire:model="weightingName" placeholder="e.g., Class Score, Tests, Exams" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Weight Percentage</label>
                    <input type="number" wire:model="weightPercentage" step="0.01" min="0" max="100" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Academic Level (Optional)</label>
                    <select wire:model="weightingLevelId" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                        <option value="">School-wide</option>
                        @foreach($levels as $level)
                            <option value="{{ $level->id }}">{{ $level->name }}</option>
                        @endforeach
                    </select>
                </div>

                <label class="flex items-center">
                    <input type="checkbox" wire:model="isDefaultWeighting" class="rounded border-gray-300">
                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Set as Default</span>
                </label>
            </div>
        </x-slot>

        <x-slot name="actions">
            <button @click="$dispatch('close-modal', { name: 'weightingModal' })" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                Cancel
            </button>
            <button wire:click="saveWeighting" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                Save
            </button>
        </x-slot>
    </x-modal-component>
</div>
