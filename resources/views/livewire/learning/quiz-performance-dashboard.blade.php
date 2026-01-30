<div class="quiz-performance-dashboard">
    {{-- Header --}}
    <div class="dashboard-header bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">
                    Performance Dashboard
                </h1>
                <p class="mt-1 text-sm text-gray-600">
                    @if(isset($userId) && $userId !== auth()->id())
                        Performance analytics for {{ $this->targetUser->name }}
                    @else
                        Track your performance and progress
                    @endif
                </p>
            </div>

            <div class="flex gap-3">
                <button wire:click="resetFilters"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition">
                    <svg class="inline-block w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Reset Filters
                </button>

                <button wire:click="exportPerformanceData"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition">
                    <svg class="inline-block w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Export Report
                </button>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="filters bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Period Filter --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Time Period</label>
                <select wire:model.live="selectedPeriod"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @foreach($periods as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Book Filter --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Book</label>
                <select wire:model.live="selectedBookId"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">All Books</option>
                    @foreach($availableBooks as $book)
                        <option value="{{ $book->id }}">{{ $book->title }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Difficulty Filter --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Difficulty</label>
                <select wire:model.live="selectedDifficulty"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @foreach($difficulties as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Question Type Filter --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Question Type</label>
                <select wire:model.live="selectedQuestionType"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @foreach($questionTypes as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Score Range Filter --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Min Score (%)</label>
                <input type="number" wire:model.live="minScore" min="0" max="100"
                       placeholder="0"
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Max Score (%)</label>
                <input type="number" wire:model.live="maxScore" min="0" max="100"
                       placeholder="100"
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
        </div>

        {{-- Custom Date Range --}}
        @if($selectedPeriod === 'custom')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                    <input type="date" wire:model.live="startDate"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                    <input type="date" wire:model.live="endDate"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
            </div>
        @endif
    </div>

    {{-- Overview Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        {{-- Total Quizzes --}}
        <div class="stat-card bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Quizzes</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">
                        {{ $this->performanceData['total_quizzes'] }}
                    </p>
                </div>
                <div class="p-3 bg-blue-100 rounded-full">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Average Score --}}
        <div class="stat-card bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Average Score</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">
                        {{ number_format($this->performanceData['average_score'], 1) }}%
                    </p>
                    <p class="text-xs text-gray-500 mt-1">
                        Grade: {{ $this->calculateLetterGrade($this->performanceData['average_score']) }}
                    </p>
                </div>
                <div class="p-3 bg-green-100 rounded-full">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Highest Score --}}
        <div class="stat-card bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Best Score</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">
                        {{ number_format($this->performanceData['highest_score'], 1) }}%
                    </p>
                    <p class="text-xs text-gray-500 mt-1">
                        Personal best
                    </p>
                </div>
                <div class="p-3 bg-yellow-100 rounded-full">
                    <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Improvement Trend --}}
        <div class="stat-card bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Trend</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2 capitalize">
                        {{ $this->performanceData['improvement_trend']['trend'] }}
                    </p>
                    @if($this->performanceData['improvement_trend']['change'] != 0)
                        <p class="text-xs mt-1 {{ $this->performanceData['improvement_trend']['change'] > 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ $this->performanceData['improvement_trend']['change'] > 0 ? '+' : '' }}{{ number_format($this->performanceData['improvement_trend']['change'], 1) }}%
                        </p>
                    @endif
                </div>
                <div class="p-3 {{ $this->performanceData['improvement_trend']['trend'] === 'improving' ? 'bg-green-100' : ($this->performanceData['improvement_trend']['trend'] === 'declining' ? 'bg-red-100' : 'bg-gray-100') }} rounded-full">
                    @if($this->performanceData['improvement_trend']['trend'] === 'improving')
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                    @elseif($this->performanceData['improvement_trend']['trend'] === 'declining')
                        <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/>
                        </svg>
                    @else
                        <svg class="w-8 h-8 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14"/>
                        </svg>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Additional Stats Row --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-sm font-medium text-gray-600 mb-3">Questions Answered</h3>
            <p class="text-2xl font-bold text-gray-900">
                {{ number_format($this->performanceData['total_questions_answered']) }}
            </p>
            <p class="text-sm text-gray-500 mt-1">
                {{ number_format($this->performanceData['total_correct_answers']) }} correct
            </p>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-sm font-medium text-gray-600 mb-3">Average Time</h3>
            <p class="text-2xl font-bold text-gray-900">
                {{ gmdate('i:s', $this->performanceData['average_time_taken'] ?? 0) }}
            </p>
            <p class="text-sm text-gray-500 mt-1">per quiz</p>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-sm font-medium text-gray-600 mb-3">Completion Rate</h3>
            <p class="text-2xl font-bold text-gray-900">
                {{ number_format($this->performanceData['completion_rate'], 1) }}%
            </p>
            <p class="text-sm text-gray-500 mt-1">of started quizzes</p>
        </div>
    </div>

    {{-- Main Content Tabs --}}
    <div class="bg-white rounded-lg shadow-sm mb-6">
        <div class="border-b border-gray-200">
            <nav class="flex -mb-px">
                <button wire:click="$set('activeView', 'overview')"
                        class="px-6 py-4 text-sm font-medium border-b-2 transition {{ $activeView === 'overview' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-500' }}">
                    Overview
                </button>
                <button wire:click="$set('activeView', 'detailed')"
                        class="px-6 py-4 text-sm font-medium border-b-2 transition {{ $activeView === 'detailed' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-500' }}">
                    Detailed Analysis
                </button>
                <button wire:click="$set('activeView', 'trends')"
                        class="px-6 py-4 text-sm font-medium border-b-2 transition {{ $activeView === 'trends' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-500' }}">
                    Trends
                </button>
                <button wire:click="$set('activeView', 'comparisons')"
                        class="px-6 py-4 text-sm font-medium border-b-2 transition {{ $activeView === 'comparisons' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-500' }}">
                    Comparisons
                </button>
            </nav>
        </div>

        <div class="p-6">
            @if($activeView === 'overview')
                @include('livewire.learning.partials.performance-overview')
            @elseif($activeView === 'detailed')
                @include('livewire.learning.partials.performance-detailed')
            @elseif($activeView === 'trends')
                @include('livewire.learning.partials.performance-trends')
            @elseif($activeView === 'comparisons')
                @include('livewire.learning.partials.performance-comparisons')
            @endif
        </div>
    </div>
</div>
