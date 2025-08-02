{{-- resources/views/livewire/subscribers/assessments.blade.php --}}
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    @if($currentView === 'dashboard')
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Self Assessments</h1>
            <button wire:click="startSelfAssessment" 
                    class="bg-violet-600 text-white px-4 py-2 rounded-lg hover:bg-violet-700">
                Take New Assessment
            </button>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Total Assessments</h3>
                <p class="text-3xl font-bold text-violet-600">{{ $assessmentHistory->count() }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Average Score</h3>
                <p class="text-3xl font-bold text-green-600">{{ round($assessmentHistory->avg('score') ?? 0) }}%</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Best Score</h3>
                <p class="text-3xl font-bold text-blue-600">{{ $assessmentHistory->max('score') ?? 0 }}%</p>
            </div>
        </div>

        <!-- Recent Assessments -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Assessments</h2>
            </div>
            <div class="p-6">
                @forelse($assessmentHistory as $assessment)
                    <div class="flex justify-between items-center py-3 border-b last:border-b-0">
                        <div>
                            <h3 class="font-medium text-gray-900 dark:text-white">{{ $assessment->title }}</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                {{ $assessment->subject->title ?? 'General' }} • {{ $assessment->created_at->format('M d, Y') }}
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold text-gray-900 dark:text-white">{{ $assessment->score ?? 'N/A' }}%</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $assessment->status }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-600 dark:text-gray-400 text-center py-8">
                        No assessments taken yet. Start your first assessment!
                    </p>
                @endforelse
            </div>
        </div>
    @elseif($currentView === 'take-assessment')
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Take Assessment</h1>
            <button wire:click="backToDashboard" 
                    class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
                Back to Dashboard
            </button>
        </div>
        
        <!-- Include the existing self-assessment component -->
        @livewire('students.self-assessments')
    @endif
</div>
