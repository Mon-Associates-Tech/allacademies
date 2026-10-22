<x-exam.page :maxWidth="'max-w-7xl'">
    <!-- Flash Notifications -->
    @if(session('success'))
        <x-exam.flash type="success" :message="session('success')" />
    @endif

    @if(session('warning'))
        <x-exam.flash type="error" :message="session('warning')" />
    @endif

    @if(session('error'))
        <x-exam.flash type="error" :message="session('error')" />
    @endif

    <!-- Header -->
    <x-exam.header
        :title="'Report Card Management'"
        :sub="'Configure report cards, manage grades, and approve student reports'"
        :eyebrow="'ADMINISTRATION'"
        variant="compact"
    />

    <!-- Filters -->
    <x-exam.card class="mb-8">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-0">
            <x-exam.field :label="'Year'" :bare="true">
                <x-exam.select wire:model.live="selectedYearId" :placeholder="'All Years'">
                    @foreach($years as $year)
                        <option value="{{ $year->id }}">{{ $year->name }}</option>
                    @endforeach
                </x-exam.select>
            </x-exam.field>

            <x-exam.field :label="'Period'" :bare="true">
                <x-exam.select wire:model.live="selectedPeriodId" :placeholder="'All Periods'">
                    @foreach($periods as $period)
                        <option value="{{ $period->id }}">{{ $period->name }}</option>
                    @endforeach
                </x-exam.select>
            </x-exam.field>

            <x-exam.field :label="'Level'" :bare="true">
                <x-exam.select wire:model.live="selectedLevelId" :placeholder="'All Levels'">
                    @foreach($levels as $level)
                        <option value="{{ $level->id }}">{{ $level->name }}</option>
                    @endforeach
                </x-exam.select>
            </x-exam.field>

            <x-exam.field :label="'Group'" :bare="true">
                <x-exam.select wire:model.live="selectedGroupId" :placeholder="'All Groups'">
                    @foreach($groups as $group)
                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                    @endforeach
                </x-exam.select>
            </x-exam.field>

            <x-exam.field :label="'Student'" :bare="true">
                <x-exam.select wire:model.live="selectedStudentId" :placeholder="'All Students'">
                    @foreach($students as $student)
                        <option value="{{ $student->id }}">{{ $student->user->name }}</option>
                    @endforeach
                </x-exam.select>
            </x-exam.field>
        </div>
    </x-exam.card>

    <!-- Tabs -->
    <x-exam.tabs
        :tabs="[
            'configurations' => 'Configurations',
            'report-cards' => 'All Report Cards',
            'approvals' => 'Pending Approvals (' . $pendingApprovals->total() . ')',
            'grade-scales' => 'Grade Scales',
            'weightings' => 'Score Weightings',
            'templates' => 'Templates',
        ]"
        :active="$activeTab"
    />

    <!-- Configurations Tab -->
    @if($activeTab === 'configurations')
        <x-exam.card>
            <x-slot:header>
                <div>
                    <h2 class="text-lg font-semibold text-ink">Report Card Configurations</h2>
                    <p class="text-sm text-muted mt-0.5">Manage configurations for each academic level and period</p>
                </div>
                <x-exam.button wire:click="openConfigModal">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    New Configuration
                </x-exam.button>
            </x-slot:header>

            <div class="space-y-4">
                @forelse($configurations as $config)
                    <div class="border-b border-line py-5 last:border-b-0">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="flex items-center gap-2 mb-1">
                                    <h4 class="font-semibold text-ink">{{ $config->academicLevel->name }}</h4>
                                    <x-exam.badge :tone="$config->is_published ? 'ok' : 'muted'">
                                        {{ $config->is_published ? 'Published' : 'Draft' }}
                                    </x-exam.badge>
                                    <x-exam.badge tone="primary">
                                        {{ ucfirst($config->preparation_mode) }}
                                    </x-exam.badge>
                                    @if($config->requires_approval)
                                        <x-exam.badge tone="warn">Requires Approval</x-exam.badge>
                                    @endif
                                </div>
                                <p class="text-sm text-muted">{{ $config->academicPeriod->name }} - {{ $config->academicPeriod->academic_year }}</p>
                                <p class="text-xs text-muted mt-1">
                                    Subject limits: Min {{ $config->min_subjects ?? 'N/A' }}, Max {{ $config->max_subjects ?? 'N/A' }}
                                </p>
                                <p class="text-xs text-muted mt-0.5">
                                    Principal: {{ $config->principal_name ?: 'Not set' }} | Class Teacher Label: {{ $config->class_teacher_name ?: 'Class Teacher' }}
                                </p>
                            </div>
                            <div class="flex items-center gap-2">
                                <x-exam.button variant="ok" target="generateReportCards({{ $config->id }})" wire:click="generateReportCards({{ $config->id }})">
                                    @if($selectedStudentId)
                                        Generate for Student
                                    @elseif($selectedGroupId)
                                        Generate for Group
                                    @else
                                        Generate for Level
                                    @endif
                                </x-exam.button>
                                <x-exam.button :variant="$config->is_published ? 'warn' : 'primary'" wire:click="togglePublishConfig({{ $config->id }})">
                                    {{ $config->is_published ? 'Unpublish' : 'Publish' }}
                                </x-exam.button>
                                <x-exam.button variant="ghost" wire:click="openConfigModal({{ $config->id }})">
                                    Edit
                                </x-exam.button>
                            </div>
                        </div>
                    </div>
                @empty
                    <x-exam.empty :title="'No configurations found'" :description="'Create your first report card configuration to get started.'">
                        <x-exam.button class="mt-4" wire:click="openConfigModal">
                            New Configuration
                        </x-exam.button>
                    </x-exam.empty>
                @endforelse
            </div>
        </x-exam.card>
    @endif

    <!-- All Report Cards Tab -->
    @if($activeTab === 'report-cards')
        <x-exam.card :padded="false">
            <x-slot:header>
                <div>
                    <h2 class="text-lg font-semibold text-ink">All Report Cards</h2>
                    <p class="text-sm text-muted mt-0.5">View and manage all generated report cards</p>
                </div>
            </x-slot:header>

            <x-exam.table>
                <x-slot:head>
                    <tr class="border-b border-line bg-surface-2">
                        <th class="px-4 py-3 text-left font-mono text-[10px] font-semibold uppercase tracking-[0.14em] text-muted">Student</th>
                        <th class="px-4 py-3 text-left font-mono text-[10px] font-semibold uppercase tracking-[0.14em] text-muted">Level / Term</th>
                        <th class="px-4 py-3 text-left font-mono text-[10px] font-semibold uppercase tracking-[0.14em] text-muted">Status</th>
                        <th class="px-4 py-3 text-left font-mono text-[10px] font-semibold uppercase tracking-[0.14em] text-muted">Accessible</th>
                        <th class="px-4 py-3 text-right font-mono text-[10px] font-semibold uppercase tracking-[0.14em] text-muted">Actions</th>
                    </tr>
                </x-slot:head>

                @forelse($reportCards as $rc)
                    <tr class="hover:bg-surface-2 transition-colors">
                        <td class="px-4 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-ink">{{ $rc->student->user->name }}</div>
                            <div class="text-xs text-muted">{{ $rc->student->student_id }}</div>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <div class="text-sm text-ink">{{ $rc->configuration?->academicLevel?->name ?? 'N/A' }}</div>
                            <div class="text-xs text-muted">{{ $rc->term }}</div>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            @php
                                $statusTone = $rc->status === 'approved' ? 'ok' : ($rc->status === 'submitted' ? 'warn' : 'muted');
                            @endphp
                            <x-exam.badge :tone="$statusTone">{{ ucfirst($rc->status) }}</x-exam.badge>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap text-sm text-muted">
                            {{ $rc->is_accessible ? 'Yes' : 'No' }}
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap text-right">
                            <div class="flex items-center justify-end gap-2">
                                @if($rc->status !== 'approved')
                                    <x-exam.link-button tone="ok" wire:click="approveReportCard({{ $rc->id }})">Approve</x-exam.link-button>
                                @endif
                                <a href="{{ route('admin.report-cards.student-preview', $rc->id) }}" target="_blank"
                                   class="whitespace-nowrap border-b border-dashed border-transparent font-mono text-[11px] font-semibold uppercase tracking-wider text-primary hover:border-current hover:text-primary-hi transition">
                                    View as Student
                                </a>
                                @if($rc->canBeEditedBy(auth()->user()))
                                    <x-exam.link-button wire:click="openReportCardModal({{ $rc->id }})">Edit</x-exam.link-button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">
                            <x-exam.empty :title="'No report cards found'" :compact="true" />
                        </td>
                    </tr>
                @endforelse
            </x-exam.table>

            <x-slot:footer>
                {{ $reportCards->links() }}
            </x-slot:footer>
        </x-exam.card>
    @endif

    <!-- Approvals Tab -->
    @if($activeTab === 'approvals')
        <x-exam.card>
            <x-slot:header>
                <div>
                    <h2 class="text-lg font-semibold text-ink">Pending Approvals</h2>
                    <p class="text-sm text-muted mt-0.5">Review and approve submitted report cards</p>
                </div>
            </x-slot:header>

            <div class="space-y-4">
                @forelse($pendingApprovals as $reportCard)
                    <div class="border-b border-line py-5 last:border-b-0">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="flex items-center gap-2 mb-1">
                                    <h4 class="font-semibold text-ink">{{ $reportCard->student->user->name }}</h4>
                                    <x-exam.badge tone="warn">Submitted</x-exam.badge>
                                </div>
                                <p class="text-sm text-muted">
                                    {{ $reportCard->configuration?->academicLevel?->name ?? 'N/A' }} - {{ $reportCard->term }}
                                </p>
                                <p class="text-xs text-muted mt-1">
                                    Submitted {{ $reportCard->submitted_at->diffForHumans() }}
                                </p>
                            </div>
                            <div class="flex items-center gap-2">
                                <x-exam.button variant="ok" wire:click="approveReportCard({{ $reportCard->id }})">
                                    Approve
                                </x-exam.button>
                                <button
                                    onclick="if(confirm('Enter rejection reason:')) { let reason = prompt('Reason:'); if(reason) @this.rejectReportCard({{ $reportCard->id }}, reason); }"
                                    class="inline-flex items-center justify-center whitespace-nowrap rounded px-5 py-2.5 text-[13.5px] font-semibold tracking-wide transition hover:-translate-y-px focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-accent active:translate-y-0 bg-accent text-white shadow-[0_1px_0_rgba(20,24,34,0.2),0_8px_18px_-10px_rgba(178,58,42,0.5)] hover:opacity-90">
                                    Reject
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <x-exam.empty :title="'No pending approvals'" :description="'All report cards have been reviewed.'" />
                @endforelse
            </div>

            <x-slot:footer>
                {{ $pendingApprovals->links() }}
            </x-slot:footer>
        </x-exam.card>
    @endif

    <!-- Grade Scales Tab -->
    @if($activeTab === 'grade-scales')
        <x-exam.card :padded="false">
            <x-slot:header>
                <div>
                    <h2 class="text-lg font-semibold text-ink">Grade Scales</h2>
                    <p class="text-sm text-muted mt-0.5">Define letter grades, score ranges, and remarks</p>
                </div>
                <x-exam.button wire:click="openGradeScaleModal">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add Grade Scale
                </x-exam.button>
            </x-slot:header>

            <x-exam.table>
                <x-slot:head>
                    <tr class="border-b border-line bg-surface-2">
                        <th class="px-4 py-3 text-left font-mono text-[10px] font-semibold uppercase tracking-[0.14em] text-muted">Grade</th>
                        <th class="px-4 py-3 text-left font-mono text-[10px] font-semibold uppercase tracking-[0.14em] text-muted">Range</th>
                        <th class="px-4 py-3 text-left font-mono text-[10px] font-semibold uppercase tracking-[0.14em] text-muted">Remarks</th>
                        <th class="px-4 py-3 text-left font-mono text-[10px] font-semibold uppercase tracking-[0.14em] text-muted">Scope</th>
                        <th class="px-4 py-3 text-right font-mono text-[10px] font-semibold uppercase tracking-[0.14em] text-muted">Actions</th>
                    </tr>
                </x-slot:head>

                @forelse($gradeScales as $scale)
                    <tr class="hover:bg-surface-2 transition-colors">
                        <td class="px-4 py-3 text-sm font-medium text-ink">{{ $scale->letter_grade }}</td>
                        <td class="px-4 py-3 text-sm text-muted">{{ $scale->min_score }} - {{ $scale->max_score }}</td>
                        <td class="px-4 py-3 text-sm text-muted">{{ $scale->remarks }}</td>
                        <td class="px-4 py-3 text-sm">
                            <x-exam.badge :tone="$scale->is_default ? 'primary' : 'muted'">
                                {{ $scale->is_default ? 'School-wide' : ($scale->academicLevel->name ?? 'Level') }}
                            </x-exam.badge>
                        </td>
                        <td class="px-4 py-3 text-sm text-right">
                            <x-exam.link-button wire:click="openGradeScaleModal({{ $scale->id }})">Edit</x-exam.link-button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">
                            <x-exam.empty :title="'No grade scales found'" :compact="true" />
                        </td>
                    </tr>
                @endforelse
            </x-exam.table>
        </x-exam.card>
    @endif

    <!-- Weightings Tab -->
    @if($activeTab === 'weightings')
        <x-exam.card :padded="false">
            <x-slot:header>
                <div>
                    <h2 class="text-lg font-semibold text-ink">Score Weightings</h2>
                    <p class="text-sm text-muted mt-0.5">Define how scores are weighted across assessment types</p>
                </div>
                <x-exam.button wire:click="openWeightingModal">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add Weighting
                </x-exam.button>
            </x-slot:header>

            <x-exam.table>
                <x-slot:head>
                    <tr class="border-b border-line bg-surface-2">
                        <th class="px-4 py-3 text-left font-mono text-[10px] font-semibold uppercase tracking-[0.14em] text-muted">Name</th>
                        <th class="px-4 py-3 text-left font-mono text-[10px] font-semibold uppercase tracking-[0.14em] text-muted">Score Key</th>
                        <th class="px-4 py-3 text-left font-mono text-[10px] font-semibold uppercase tracking-[0.14em] text-muted">Source</th>
                        <th class="px-4 py-3 text-left font-mono text-[10px] font-semibold uppercase tracking-[0.14em] text-muted">Max Score</th>
                        <th class="px-4 py-3 text-left font-mono text-[10px] font-semibold uppercase tracking-[0.14em] text-muted">Weight %</th>
                        <th class="px-4 py-3 text-left font-mono text-[10px] font-semibold uppercase tracking-[0.14em] text-muted">Scope</th>
                        <th class="px-4 py-3 text-right font-mono text-[10px] font-semibold uppercase tracking-[0.14em] text-muted">Actions</th>
                    </tr>
                </x-slot:head>

                @forelse($weightings as $weighting)
                    <tr class="hover:bg-surface-2 transition-colors">
                        <td class="px-4 py-3 text-sm font-medium text-ink">{{ $weighting->name }}</td>
                        <td class="px-4 py-3 text-sm text-muted">{{ $weighting->score_key ?: '-' }}</td>
                        <td class="px-4 py-3 text-sm text-muted">{{ $weighting->source_type ? ucfirst($weighting->source_type) : '-' }}</td>
                        <td class="px-4 py-3 text-sm text-muted">{{ $weighting->max_score !== null ? number_format((float) $weighting->max_score, 2) : '-' }}</td>
                        <td class="px-4 py-3 text-sm text-muted">{{ $weighting->weight_percentage }}%</td>
                        <td class="px-4 py-3 text-sm">
                            <x-exam.badge :tone="$weighting->is_default ? 'primary' : 'muted'">
                                {{ $weighting->is_default ? 'School-wide' : ($weighting->academicLevel->name ?? 'Level') }}
                            </x-exam.badge>
                        </td>
                        <td class="px-4 py-3 text-sm text-right">
                            <x-exam.link-button wire:click="openWeightingModal({{ $weighting->id }})">Edit</x-exam.link-button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">
                            <x-exam.empty :title="'No weightings found'" :compact="true" />
                        </td>
                    </tr>
                @endforelse
            </x-exam.table>
        </x-exam.card>
    @endif

    {{-- Templates Tab --}}
    @if($activeTab === 'templates')
        <x-exam.card>
            <x-slot:header>
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-ink">Report Card Templates</h2>
                        <p class="text-sm text-muted mt-0.5">Customize the layout and sections of report cards</p>
                    </div>
                    <x-exam.button wire:click="openTemplateModal">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        New Template
                    </x-exam.button>
                </div>
            </x-slot:header>

            <div class="space-y-4">
                @forelse($templates as $template)
                    <div class="border-b border-line py-5 last:border-b-0">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="flex items-center gap-2 mb-1">
                                    <h4 class="font-semibold text-ink">{{ $template->name }}</h4>
                                    @if($template->is_default)
                                        <x-exam.badge tone="primary">Default</x-exam.badge>
                                    @endif
                                </div>
                                <p class="text-sm text-muted">{{ $template->academicLevel->name ?? 'All Levels' }}</p>
                                <div class="flex gap-2 mt-2">
                                    @php $slotCount = count($template->resolvedSections()['signatures']['slots'] ?? []); @endphp
                                    <x-exam.badge tone="muted">
                                        {{ $slotCount }} signature{{ $slotCount === 1 ? '' : 's' }}
                                    </x-exam.badge>
                                </div>
                            </div>
                            <x-exam.button variant="ghost" wire:click="openTemplateModal({{ $template->id }})">
                                Edit
                            </x-exam.button>
                        </div>
                    </div>
                @empty
                    <x-exam.empty :title="'No templates found'" :description="'Configurations will use built-in defaults until one is created.'" />
                @endforelse
            </div>
        </x-exam.card>
    @endif

    <!-- Configuration Modal -->
    <x-modal-component name="configModal" title="{{ $configId ? 'Edit' : 'New' }} Configuration" size="2xl">
        <x-slot name="body">
            <div class="space-y-0">
                <x-exam.field :label="'Academic Period'" :required="true">
                    <x-exam.select wire:model="configPeriodId" :placeholder="'Select Period'">
                        @foreach($periods as $period)
                            <option value="{{ $period->id }}">{{ $period->name }} ({{ $period->academic_year }})</option>
                        @endforeach
                    </x-exam.select>
                </x-exam.field>

                <x-exam.field :label="'Academic Level'" :required="true">
                    <x-exam.select wire:model="configLevelId" :placeholder="'Select Level'">
                        @foreach($levels as $level)
                            <option value="{{ $level->id }}">{{ $level->name }}</option>
                        @endforeach
                    </x-exam.select>
                </x-exam.field>

                <x-exam.field :label="'Preparation Mode'">
                    <x-exam.select wire:model="preparationMode">
                        <option value="manual">Manual Only</option>
                        <option value="automated">Automated Only</option>
                        <option value="hybrid">Hybrid (Auto + Manual)</option>
                    </x-exam.select>
                </x-exam.field>

                <x-exam.field :label="'Template'">
                    <x-exam.select wire:model="templateId" :placeholder="'Use default layout'">
                        @foreach($templates as $template)
                            <option value="{{ $template->id }}">{{ $template->name }}{{ $template->academicLevel ? " ({$template->academicLevel->name})" : '' }}</option>
                        @endforeach
                    </x-exam.select>
                </x-exam.field>

                <x-exam.field :bare="true">
                    <div class="flex items-center gap-6">
                        <label class="flex items-center">
                            <input type="checkbox" wire:model="requiresApproval" class="rounded border-line-strong">
                            <span class="ml-2 text-sm text-ink">Requires Approval</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" wire:model="isPublished" class="rounded border-line-strong">
                            <span class="ml-2 text-sm text-ink">Published</span>
                        </label>
                    </div>
                </x-exam.field>

                <x-exam.field :label="'Available From'">
                    <x-exam.input type="datetime-local" wire:model="availableFrom" />
                </x-exam.field>

                <x-exam.field :label="'Available Until'">
                    <x-exam.input type="datetime-local" wire:model="availableUntil" />
                </x-exam.field>

                <x-exam.field :label="'Min Subjects per Student'">
                    <x-exam.input type="number" min="1" max="30" wire:model="minSubjects" />
                </x-exam.field>

                <x-exam.field :label="'Max Subjects per Student'">
                    <x-exam.input type="number" min="1" max="30" wire:model="maxSubjects" />
                </x-exam.field>

                <x-exam.field :label="'Principal Name'">
                    <x-exam.input type="text" wire:model="principalName" placeholder="e.g. Mrs. Akosua Mensah" />
                </x-exam.field>

                <x-exam.field :label="'Class Teacher Label'">
                    <x-exam.input type="text" wire:model="classTeacherName" placeholder="Default: Class Teacher" />
                </x-exam.field>

                <x-exam.field :label="'Principal Signature'">
                    <div class="mb-1">
                        <x-exam.file-upload
                            id="principalSignatureUpload"
                            model="principalSignatureUpload"
                            accept="image/*"
                            :current="$principalSignaturePath ? asset('storage/' . $principalSignaturePath) : null"
                            :pending="(bool)$principalSignatureUpload"
                            :removeAction="'removePrincipalSignature'"
                            tag="SIGNATURE"
                        />
                    </div>
                    @if($principalSignatureUpload)
                        <img src="{{ $principalSignatureUpload->temporaryUrl() }}" alt="Preview"
                             class="mt-2 h-16 object-contain border border-line rounded bg-white p-1">
                    @endif
                </x-exam.field>

                <x-exam.field :label="'Class Teacher Signature'">
                    <div class="mb-1">
                        <x-exam.file-upload
                            id="classTeacherSignatureUpload"
                            model="classTeacherSignatureUpload"
                            accept="image/*"
                            :current="$classTeacherSignaturePath ? asset('storage/' . $classTeacherSignaturePath) : null"
                            :pending="(bool)$classTeacherSignatureUpload"
                            :removeAction="'removeClassTeacherSignature'"
                            tag="SIGNATURE"
                        />
                    </div>
                    @if($classTeacherSignatureUpload)
                        <img src="{{ $classTeacherSignatureUpload->temporaryUrl() }}" alt="Preview"
                             class="mt-2 h-16 object-contain border border-line rounded bg-white p-1">
                    @endif
                </x-exam.field>
            </div>
        </x-slot>

        <x-slot name="actions">
            <x-exam.button variant="ghost" @click="$dispatch('close-modal', { name: 'configModal' })">
                Cancel
            </x-exam.button>
            <x-exam.button type="button" wire:click="saveConfiguration" variant="ok">
                Save
            </x-exam.button>
        </x-slot>
    </x-modal-component>

    <!-- Grade Scale Modal -->
    <x-modal-component name="gradeScaleModal" title="{{ $gradeScaleId ? 'Edit' : 'Add' }} Grade Scale" size="2xl">
        <x-slot name="body">
            <div class="space-y-0">
                <x-exam.field :label="'Name'" :required="true">
                    <x-exam.input type="text" wire:model="gradeName" />
                </x-exam.field>

                <x-exam.field :label="'Min Score'" :required="true">
                    <x-exam.input type="number" wire:model="minScore" step="0.01" />
                </x-exam.field>

                <x-exam.field :label="'Max Score'" :required="true">
                    <x-exam.input type="number" wire:model="maxScore" step="0.01" />
                </x-exam.field>

                <x-exam.field :label="'Letter Grade'">
                    <x-exam.input type="text" wire:model="letterGrade" />
                </x-exam.field>

                <x-exam.field :label="'Grade Point'">
                    <x-exam.input type="number" wire:model="gradePoint" step="0.01" />
                </x-exam.field>

                <x-exam.field :label="'Remarks'">
                    <x-exam.input type="text" wire:model="gradeRemarks" />
                </x-exam.field>

                <x-exam.field :label="'Academic Level'">
                    <x-exam.select wire:model="gradeLevelId" :placeholder="'School-wide'">
                        @foreach($levels as $level)
                            <option value="{{ $level->id }}">{{ $level->name }}</option>
                        @endforeach
                    </x-exam.select>
                </x-exam.field>

                <x-exam.field :bare="true">
                    <label class="flex items-center">
                        <input type="checkbox" wire:model="isDefaultGrade" class="rounded border-line-strong">
                        <span class="ml-2 text-sm text-ink">Set as Default</span>
                    </label>
                </x-exam.field>
            </div>
        </x-slot>

        <x-slot name="actions">
            <x-exam.button variant="ghost" @click="$dispatch('close-modal', { name: 'gradeScaleModal' })">
                Cancel
            </x-exam.button>
            <x-exam.button wire:click="saveGradeScale" variant="ok">
                Save
            </x-exam.button>
        </x-slot>
    </x-modal-component>

    <!-- Report Card Edit Modal -->
    <x-modal-component name="reportCardModal" title="Edit Report Card" size="2xl">
        <x-slot name="body">
            <div class="space-y-0">
                <x-exam.field :label="'Student'" :bare="true">
                    <p class="text-sm text-ink">{{ $reportCard?->student?->user?->name ?? 'N/A' }}</p>
                </x-exam.field>

                <x-exam.field :label="'Academic Level'" :bare="true">
                    <p class="text-sm text-ink">{{ $reportCard?->configuration?->academicLevel?->name ?? 'N/A' }}</p>
                </x-exam.field>

                <x-exam.field :label="'Configuration'" :bare="true">
                    <p class="text-sm text-ink">{{ $reportCard?->configuration?->academicLevel?->name ?? 'N/A' }}</p>
                </x-exam.field>

                <x-exam.field :label="'Status'">
                    <x-exam.select wire:model="status">
                        <option value="draft">Draft</option>
                        <option value="published">Published</option>
                        <option value="approved">Approved</option>
                    </x-exam.select>
                </x-exam.field>

                <x-exam.field :label="'Available From'">
                    <x-exam.input type="datetime-local" wire:model="availableFrom" />
                </x-exam.field>

                <x-exam.field :label="'Available Until'">
                    <x-exam.input type="datetime-local" wire:model="availableUntil" />
                </x-exam.field>

                <x-exam.field :label="'Teacher Remarks'">
                    <textarea wire:model="teacherRemarks" rows="3"
                              placeholder="General comments for this report card"
                              class="w-full rounded border border-line-strong bg-surface px-3 py-2.5 text-sm text-ink placeholder:text-muted transition hover:border-ink-soft focus:border-primary focus:outline-none focus:ring-[3px] focus:ring-primary/20 resize-none"></textarea>
                </x-exam.field>

                <x-exam.field :label="'Total School Days'">
                    <x-exam.input type="number" min="0" wire:model="attendanceTotalDays" />
                </x-exam.field>

                <x-exam.field :label="'Days Present'">
                    <x-exam.input type="number" min="0" wire:model="attendanceDaysPresent" />
                </x-exam.field>

                {{-- Subject Grades --}}
                <x-exam.field :label="'Subject Grades'" :bare="true">
                    @if($reportCard)
                        <div class="space-y-4">
                            @forelse($reportCard->grades as $grade)
                                <x-exam.card>
                                    <div class="flex items-center justify-between mb-3">
                                        <div>
                                            <h5 class="font-semibold text-ink">{{ $grade->subject?->name ?? 'N/A' }}</h5>
                                            <p class="text-sm text-muted">{{ $grade->subject?->code ?? '' }}</p>
                                        </div>
                                        @if($grade->is_locked)
                                            <x-exam.badge tone="muted">Locked</x-exam.badge>
                                        @endif
                                    </div>

                                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-0">
                                        @foreach($this->weightingsForGrade($grade) as $weighting)
                                            @php $key = $weighting->score_key ?: \Illuminate\Support\Str::slug($weighting->name, '_'); @endphp
                                            <x-exam.field :label="$weighting->name" :bare="true">
                                                <p class="text-xs text-muted mb-1">({{ $weighting->weight_percentage }}% · max {{ $weighting->max_score ?: 100 }})</p>
                                                <x-exam.input type="number" step="0.01" min="0"
                                                       :max="$weighting->max_score ?: 100"
                                                       wire:model="subjectGradeInputs.{{ $grade->id }}.{{ $key }}" />
                                            </x-exam.field>
                                        @endforeach

                                        <div class="flex items-center gap-2">
                                            <span class="text-sm text-muted">
                                                Total: <strong class="text-ink">{{ number_format($grade->total_score, 1) }}</strong>
                                                @if($grade->grade_label)
                                                    <x-exam.badge tone="primary">{{ $grade->grade_label }}</x-exam.badge>
                                                @endif
                                            </span>
                                            <x-exam.button variant="ghost" wire:click="saveSubjectGrade({{ $grade->id }})">
                                                Save
                                            </x-exam.button>
                                        </div>
                                    </div>
                                </x-exam.card>
                            @empty
                                <x-exam.empty :title="'No subjects found'" :compact="true" />
                            @endforelse
                        </div>
                    @endif
                </x-exam.field>
            </div>
        </x-slot>

        <x-slot name="actions">
            <x-exam.button variant="ghost" @click="$dispatch('close-modal', { name: 'reportCardModal' })">
                Cancel
            </x-exam.button>
            <x-exam.button wire:click="saveReportCard" variant="ok">
                Save All Changes
            </x-exam.button>
        </x-slot>
    </x-modal-component>

    <!-- Weighting Modal -->
    <x-modal-component name="weightingModal" title="{{ $weightingId ? 'Edit' : 'Add' }} Score Weighting" size="2xl">
        <x-slot name="body">
            <div class="space-y-0">
                <x-exam.field :label="'Name'" :required="true">
                    <x-exam.input type="text" wire:model="weightingName" placeholder="e.g., Class Score, Tests, Exams" />
                </x-exam.field>

                <x-exam.field :label="'Weight Percentage'" :required="true">
                    <x-exam.input type="number" wire:model="weightPercentage" step="0.01" min="0" max="100" />
                </x-exam.field>

                <x-exam.field :label="'Score Key'">
                    <x-exam.input type="text" wire:model="weightingScoreKey" placeholder="e.g. class_score" />
                    <p class="mt-1 font-mono text-[11px] text-muted">Used to map this heading to stored scores.</p>
                </x-exam.field>

                <x-exam.field :label="'Max Raw Score'">
                    <x-exam.input type="number" wire:model="weightingMaxScore" step="0.01" min="0" />
                </x-exam.field>

                <x-exam.field :label="'Score Source'">
                    <x-exam.select wire:model="weightingSourceType" :placeholder="'None / Manual Entry'">
                        <option value="quiz">Quiz</option>
                        <option value="examination">Examination</option>
                        <option value="practice">Practice</option>
                        <option value="assignment">All Assignment Types</option>
                        <option value="mixed">Mixed</option>
                        <option value="manual">Manual</option>
                    </x-exam.select>
                </x-exam.field>

                <x-exam.field :label="'Academic Level'">
                    <x-exam.select wire:model="weightingLevelId" :placeholder="'School-wide'">
                        @foreach($levels as $level)
                            <option value="{{ $level->id }}">{{ $level->name }}</option>
                        @endforeach
                    </x-exam.select>
                </x-exam.field>

                <x-exam.field :bare="true">
                    <label class="flex items-center">
                        <input type="checkbox" wire:model="isDefaultWeighting" class="rounded border-line-strong">
                        <span class="ml-2 text-sm text-ink">Set as Default</span>
                    </label>
                </x-exam.field>
            </div>
        </x-slot>

        <x-slot name="actions">
            <x-exam.button variant="ghost" @click="$dispatch('close-modal', { name: 'weightingModal' })">
                Cancel
            </x-exam.button>
            <x-exam.button wire:click="saveWeighting" variant="ok">
                Save
            </x-exam.button>
        </x-slot>
    </x-modal-component>

    {{-- Template Builder Modal --}}
    <x-modal-component name="templateModal" title="{{ $templateId ? 'Edit' : 'New' }} Report Card Template" size="3xl">
        <x-slot name="body">
            <div class="space-y-0">
                <x-exam.field :label="'Template Name'" :required="true">
                    <x-exam.input type="text" wire:model="templateName" placeholder="e.g. Primary Report Card" />
                </x-exam.field>

                <x-exam.field :label="'Academic Level'">
                    <x-exam.select wire:model="templateLevelId" :placeholder="'All Levels'">
                        @foreach($levels as $level)
                            <option value="{{ $level->id }}">{{ $level->name }}</option>
                        @endforeach
                    </x-exam.select>
                </x-exam.field>

                {{-- HEADER SECTION --}}
                <x-exam.callout tone="primary" class="!mb-4">
                    <div class="flex items-center gap-3">
                        <x-exam.badge tone="primary">Header</x-exam.badge>
                        <h4 class="font-semibold text-ink">Header Section</h4>
                    </div>
                </x-exam.callout>

                <div class="grid grid-cols-2 gap-0 mb-5">
                    <x-exam.field :bare="true">
                        <label class="flex items-center">
                            <input type="checkbox" wire:model="templateSections.header.show_logo" class="rounded border-line-strong">
                            <span class="ml-2 text-sm text-ink">Show Logo</span>
                        </label>
                    </x-exam.field>
                    <x-exam.field :bare="true">
                        <label class="flex items-center">
                            <input type="checkbox" wire:model="templateSections.header.show_school_name" class="rounded border-line-strong">
                            <span class="ml-2 text-sm text-ink">Show School Name</span>
                        </label>
                    </x-exam.field>
                    <x-exam.field :bare="true">
                        <label class="flex items-center">
                            <input type="checkbox" wire:model="templateSections.header.show_tagline" class="rounded border-line-strong">
                            <span class="ml-2 text-sm text-ink">Show Tagline</span>
                        </label>
                    </x-exam.field>
                    <x-exam.field :bare="true">
                        <label class="flex items-center">
                            <input type="checkbox" wire:model="templateSections.header.show_contact" class="rounded border-line-strong">
                            <span class="ml-2 text-sm text-ink">Show Contact / Location</span>
                        </label>
                    </x-exam.field>
                </div>

                <x-exam.field :label="'Tagline'">
                    <x-exam.input type="text" wire:model="templateSections.header.tagline" placeholder="School tagline, e.g. 'Excellence in Education'" />
                </x-exam.field>

                {{-- STUDENT INFO SECTION --}}
                <x-exam.callout tone="primary" class="!mb-4">
                    <div class="flex items-center gap-3">
                        <x-exam.badge tone="primary">Info</x-exam.badge>
                        <h4 class="font-semibold text-ink">Class / Academic Information</h4>
                    </div>
                </x-exam.callout>

                <div class="grid grid-cols-3 gap-0 mb-5">
                    @foreach(['name' => 'Student Name', 'student_id' => 'Student ID', 'class' => 'Class / Level', 'academic_year' => 'Academic Year', 'term' => 'Term', 'report_date' => 'Report Date'] as $key => $label)
                        <x-exam.field :bare="true">
                            <label class="flex items-center">
                                <input type="checkbox" value="{{ $key }}" wire:model="templateSections.student_info.fields" class="rounded border-line-strong">
                                <span class="ml-2 text-sm text-ink">{{ $label }}</span>
                            </label>
                        </x-exam.field>
                    @endforeach
                </div>

                {{-- GRADES TABLE SECTION --}}
                <x-exam.callout tone="primary" class="!mb-4">
                    <div class="flex items-center gap-3">
                        <x-exam.badge tone="primary">Grades</x-exam.badge>
                        <h4 class="font-semibold text-ink">Exams / Tests Section</h4>
                    </div>
                </x-exam.callout>

                <x-exam.field :bare="true" class="mb-5">
                    <label class="flex items-center mb-2">
                        <input type="checkbox" wire:model="templateSections.grades_table.enabled" class="rounded border-line-strong">
                        <span class="ml-2 text-sm text-ink">Show grades table</span>
                    </label>
                    <p class="text-xs text-muted">
                        Columns and percentages come from the <strong>Score Weightings</strong> tab for this academic level — configure them there, not here.
                    </p>
                </x-exam.field>

                {{-- ATTENDANCE SECTION --}}
                <x-exam.callout tone="primary" class="!mb-4">
                    <div class="flex items-center gap-3">
                        <x-exam.badge tone="primary">Attendance</x-exam.badge>
                        <h4 class="font-semibold text-ink">Attendance</h4>
                    </div>
                </x-exam.callout>

                <x-exam.field :bare="true" class="mb-2">
                    <label class="flex items-center">
                        <input type="checkbox" wire:model="templateSections.attendance.enabled" class="rounded border-line-strong">
                        <span class="ml-2 text-sm text-ink">Show attendance section</span>
                    </label>
                </x-exam.field>

                <x-exam.field :label="'Section Label'">
                    <x-exam.input type="text" wire:model="templateSections.attendance.label" placeholder="Section label, e.g. 'Attendance'" />
                </x-exam.field>

                <x-exam.callout tone="warn" class="mb-5">
                    <p class="text-xs text-muted">Days present/total are entered manually per report card, not pulled automatically.</p>
                </x-exam.callout>

                {{-- REMARKS SECTION --}}
                <x-exam.callout tone="primary" class="!mb-4">
                    <div class="flex items-center gap-3">
                        <x-exam.badge tone="primary">Remarks</x-exam.badge>
                        <h4 class="font-semibold text-ink">Remarks</h4>
                    </div>
                </x-exam.callout>

                <x-exam.field :bare="true" class="mb-2">
                    <label class="flex items-center">
                        <input type="checkbox" wire:model="templateSections.remarks.enabled" class="rounded border-line-strong">
                        <span class="ml-2 text-sm text-ink">Show remarks section</span>
                    </label>
                </x-exam.field>

                <x-exam.field :label="'Section Label'">
                    <x-exam.input type="text" wire:model="templateSections.remarks.label" placeholder="Section label, e.g. 'Class Teacher's Remarks'" />
                </x-exam.field>

                {{-- SIGNATURES SECTION --}}
                <x-exam.callout tone="primary" class="!mb-4">
                    <div class="flex items-center gap-3">
                        <x-exam.badge tone="primary">Signatures</x-exam.badge>
                        <h4 class="font-semibold text-ink">Signatures</h4>
                    </div>
                </x-exam.callout>

                <div class="space-y-2 mb-2">
                    @foreach($templateSections['signatures']['slots'] ?? [] as $index => $slot)
                        <div class="flex items-center gap-2">
                            <x-exam.input type="text" wire:model="templateSections.signatures.slots.{{ $index }}.label" placeholder="e.g. Principal" />
                            <x-exam.link-button tone="danger" wire:click="removeSignatureSlot({{ $index }})">Remove</x-exam.link-button>
                        </div>
                    @endforeach
                </div>

                <x-exam.link-button wire:click="addSignatureSlot">+ Add Signature</x-exam.link-button>

                <x-exam.callout tone="primary" class="mt-3 mb-5">
                    <p class="text-xs text-muted">Most schools only need Principal and Class Teacher — the name and uploaded signature image for each are set per-configuration on the Configurations tab.</p>
                </x-exam.callout>

                {{-- FOOTER SECTION --}}
                <x-exam.callout tone="primary" class="!mb-4">
                    <div class="flex items-center gap-3">
                        <x-exam.badge tone="primary">Footer</x-exam.badge>
                        <h4 class="font-semibold text-ink">Footer</h4>
                    </div>
                </x-exam.callout>

                <x-exam.field :bare="true" class="mb-2">
                    <label class="flex items-center">
                        <input type="checkbox" wire:model="templateSections.footer.enabled" class="rounded border-line-strong">
                        <span class="ml-2 text-sm text-ink">Show footer</span>
                    </label>
                </x-exam.field>

                <x-exam.field :label="'Footer Text'">
                    <x-exam.input type="text" wire:model="templateSections.footer.text" placeholder="Leave blank for default disclaimer text" />
                </x-exam.field>
            </div>
        </x-slot>

        <x-slot name="actions">
            <x-exam.button variant="ghost" @click="$dispatch('close-modal', { name: 'templateModal' })">
                Cancel
            </x-exam.button>
            <x-exam.button type="button" wire:click="saveTemplate" variant="ok">
                Save Template
            </x-exam.button>
        </x-slot>
    </x-modal-component>
</x-exam.page>
