<div>
    <!-- Header -->
    <div class="sm:flex sm:justify-between sm:items-center mb-8">
        <div class="mb-4 sm:mb-0">
            <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">Reports & Analytics</h1>
            <p class="text-gray-600 dark:text-gray-400">Generate and view comprehensive reports for your ward's academic performance</p>
        </div>
    </div>

    <!-- Ward Selection Header -->
    @if($this->wards->count() > 0)
        <div class="bg-gradient-to-r from-violet-500 to-purple-600 rounded-lg shadow-lg p-6 text-white mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center flex-1">
                    <div class="w-16 h-16 bg-white/20 backdrop-blur rounded-full flex items-center justify-center text-2xl font-bold">
                        {{ $this->selectedWard ? substr($this->selectedWard->user->name, 0, 2) : '?' }}
                    </div>
                    <div class="ml-4 flex-1">
                        @if($this->selectedWard)
                            <h2 class="text-2xl font-bold">{{ $this->selectedWard->user->name }}</h2>
                            <p class="text-violet-100">
                                {{ $this->selectedWard->academicLevel->academicGroup->name ?? 'N/A' }} - {{ $this->selectedWard->academicLevel->name ?? 'N/A' }}
                            </p>
                        @else
                            <h2 class="text-2xl font-bold">Select a Ward</h2>
                            <p class="text-violet-100">Choose a student to generate reports</p>
                        @endif
                    </div>
                </div>

                <!-- Dropdown Selector (only if multiple wards) -->
                @if($this->wards->count() > 1)
                    <div x-data="{ open: false }" class="relative" @click.away="open = false">
                        <button @click="open = !open"
                                class="flex items-center space-x-2 px-4 py-2 bg-white/20 hover:bg-white/30 backdrop-blur rounded-lg transition-all">
                            <span class="font-medium">Switch Ward</span>
                            <svg class="w-5 h-5 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <!-- Dropdown Menu -->
                        <div x-show="open"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 transform scale-95"
                             x-transition:enter-end="opacity-100 transform scale-100"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 transform scale-100"
                             x-transition:leave-end="opacity-0 transform scale-95"
                             class="absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-gray-200 dark:border-gray-700 z-50 max-h-96 overflow-y-auto"
                             style="display: none;">
                            <div class="p-2">
                                <div class="px-3 py-2 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Your Wards ({{ $this->wards->count() }})
                                </div>
                                @foreach($this->wards as $ward)
                                    <button wire:click="selectWard({{ $ward->id }})"
                                            @click="open = false"
                                            class="w-full flex items-center p-3 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors {{ $selectedWardId == $ward->id ? 'bg-violet-50 dark:bg-violet-900/20' : '' }}">
                                        <div class="w-12 h-12 bg-gradient-to-br from-violet-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold flex-shrink-0">
                                            {{ substr($ward->user->name, 0, 2) }}
                                        </div>
                                        <div class="ml-3 flex-1 text-left">
                                            <h3 class="font-semibold text-gray-900 dark:text-gray-100">{{ $ward->user->name }}</h3>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                                {{ $ward->academicLevel->academicGroup->name ?? 'N/A' }} - {{ $ward->academicLevel->name ?? 'N/A' }}
                                            </p>
                                        </div>
                                        @if($selectedWardId == $ward->id)
                                            <svg class="w-5 h-5 text-violet-600 dark:text-violet-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                        @endif
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif

    @if($this->selectedWard)
        <!-- Tabs -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm mb-6">
            <div class="border-b border-gray-200 dark:border-gray-700">
                <nav class="flex space-x-8 px-6" aria-label="Tabs">
                    <button wire:click="changeTab('generate')"
                            class="py-4 px-1 border-b-2 font-medium text-sm transition-colors {{ $activeTab === 'generate' ? 'border-violet-500 text-violet-600 dark:text-violet-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-500 dark:text-gray-400 dark:hover:text-gray-300' }}">
                        Generate New Report
                    </button>
                    <button wire:click="changeTab('history')"
                            class="py-4 px-1 border-b-2 font-medium text-sm transition-colors {{ $activeTab === 'history' ? 'border-violet-500 text-violet-600 dark:text-violet-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-500 dark:text-gray-400 dark:hover:text-gray-300' }}">
                        Report History
                    </button>
                </nav>
            </div>
        </div>

        @if($activeTab === 'generate')
            <!-- Report Configuration -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-6">Report Configuration</h2>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Report Type -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Report Type</label>
                        <select wire:model.live="selectedReportType" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300">
                            @foreach($this->reportTypes as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Period -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Period</label>
                        <select wire:model.live="selectedPeriod" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300">
                            @foreach($this->periods as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Subject Filter (for performance reports) -->
                    @if($selectedReportType === 'performance' || $selectedReportType === 'progress')
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Subject (Optional)</label>
                            <select wire:model.live="selectedSubjectId" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300">
                                <option value="">All Subjects</option>
                                @foreach($this->availableSubjects as $subject)
                                    <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                </div>

                <div class="mt-6 flex items-center space-x-4">
                    <button wire:click="generateReport"
                            class="bg-violet-600 hover:bg-violet-700 text-white px-6 py-3 rounded-lg font-medium transition-colors flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Generate Report
                    </button>

                    @if($generatedReport)
                        <button wire:click="downloadReport('pdf')"
                                class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-medium transition-colors flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Download PDF
                        </button>
                        <button wire:click="printReport"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                            </svg>
                            Print
                        </button>
                    @endif
                </div>
            </div>

            <!-- Report Preview -->
            @if($showReportPreview && $generatedReport)
                @include('livewire.parent.partials.report-preview', ['report' => $generatedReport])
            @endif
        @endif

        @if($activeTab === 'history')
            <!-- Report History -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mb-6">Report History</h2>

                @if($this->reportHistory->isEmpty())
                    <div class="text-center py-12">
                        <svg class="w-16 h-16 mx-auto text-gray-400 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <h3 class="mt-4 text-lg font-semibold text-gray-900 dark:text-gray-100">No Reports Yet</h3>
                        <p class="mt-2 text-gray-600 dark:text-gray-400">Generate your first report to see it here</p>
                        <button wire:click="changeTab('generate')"
                                class="mt-4 bg-violet-600 hover:bg-violet-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                            Generate Report
                        </button>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach($this->reportHistory as $report)
                            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-gray-900 dark:text-gray-100">{{ $report['type'] }} Report</h4>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">
                                            Generated on {{ $report['generated_at']->format('M d, Y \a\t h:i A') }}
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">
                                            Period: {{ $report['period'] }}
                                        </p>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <button class="p-2 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </button>
                                        <button class="p-2 text-green-600 hover:bg-green-50 dark:hover:bg-green-900/20 rounded-lg transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @endif
    @else
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-12 text-center">
            <svg class="w-16 h-16 mx-auto text-gray-400 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            <h3 class="mt-4 text-xl font-semibold text-gray-900 dark:text-gray-100">No Wards Found</h3>
            <p class="mt-2 text-gray-600 dark:text-gray-400">You don't have any wards assigned to your account.</p>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-500">Please contact your school administrator to link your ward(s).</p>
        </div>
    @endif
</div>
