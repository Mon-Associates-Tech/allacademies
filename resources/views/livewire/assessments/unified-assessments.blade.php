<div class="min-h-screen bg-gradient-to-br from-slate-50 to-indigo-100 dark:from-gray-900 dark:to-indigo-900">
    <!-- Timer (if active) -->
    @if($isTimerActive && $timeRemaining > 0)
        <div class="fixed top-4 right-4 z-50 bg-white dark:bg-gray-800 rounded-lg shadow-lg p-4 border border-gray-200 dark:border-gray-600">
            <div class="flex items-center space-x-2">
                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Time Remaining:</span>
                <span class="text-lg font-bold text-red-600 dark:text-red-400">
                    {{ gmdate('H:i:s', $timeRemaining) }}
                </span>
            </div>
        </div>
    @endif

    <!-- Add this in your assessment-step.blade.php -->
    @if(session()->has('debug'))
        <div class="fixed bottom-4 right-4 bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded">
            {{ session('debug') }}
        </div>
    @endif

    <!-- Main Content -->
    <div class="container mx-auto px-4 py-8">
        @if($step === 'selection')
            @include('livewire.assessments.partials.selection-step')
        @elseif($step === 'configuration')
            @include('livewire.assessments.partials.configuration-step')
        @elseif($step === 'assessment')
            @include('livewire.assessments.partials.assessment-step')
        @elseif($step === 'results')
            @include('livewire.assessments.partials.results-step')
        @endif
    </div>

    <!-- Timer Script -->
    @if($isTimerActive)
        <script>
            setInterval(function() {
                @this.call('updateTimer');
            }, 1000);
        </script>
    @endif

    @push('scripts')
        <script>
            document.addEventListener('livewire:init', function () {
                Livewire.on('responsesUpdated', () => {
                    console.log('Responses updated:', @json($this->responses));
                });
            });
        </script>
    @endpush
<button wire:click="debugResponses" type="button" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
    Debug Responses
</button>

    <div x-data="{ open: false }" x-show="open" x-effect="setTimeout(() => open = false, 3000)">
        <div x-show="open" class="fixed bottom-4 right-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded" role="alert">
            <strong class="font-bold">Error!</strong>
            <span class="block sm:inline">There was an issue saving your response.</span>
        </div>
    </div>

<!-- Temporary debug display -->
<div class="fixed top-0 right-0 m-4 p-4 bg-gray-800 text-green-400 text-sm max-h-64 overflow-auto w-96 z-50">
    <h3 class="font-bold mb-2">Debug Output</h3>
    <div>Current Question Index: {{ $currentQuestionIndex }}</div>
    <div>Responses Count: {{ is_array($responses) ? count($responses) : 'not set' }}</div>
    <div>Answered Count: {{ $this->getAnsweredCount() }}</div>
    <div>Can Submit: {{ $this->getCanSubmitProperty() ? 'Yes' : 'No' }}</div>

    <pre class="mt-2 overflow-auto max-h-48 text-xs">
        {{ print_r($responses, true) }}
    </pre>
</div>


</div>
