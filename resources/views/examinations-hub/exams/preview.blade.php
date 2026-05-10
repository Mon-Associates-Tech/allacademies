<x-layouts.app>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Examination Preview</h1>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Review and edit questions before creating the examination</p>
            </div>
            @if($hardenedMode)
                <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-lg text-sm font-medium">🔒 Hardened Mode</span>
            @endif
        </div>

        <section class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6 shadow-sm">
            <h2 class="text-xl font-semibold mb-4 text-gray-900 dark:text-white">{{ $payload['title'] }}</h2>
            <div class="grid md:grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-gray-600 dark:text-gray-400"><span class="font-medium">Description:</span> {{ $payload['description'] ?? 'N/A' }}</p>
                    <p class="text-gray-600 dark:text-gray-400"><span class="font-medium">Duration:</span> {{ $payload['duration_in_minutes'] ?? 'Not set' }} minutes</p>
                    <p class="text-gray-600 dark:text-gray-400"><span class="font-medium">Status:</span> {{ ucfirst($payload['status']) }}</p>
                </div>
                <div>
                    <p class="text-gray-600 dark:text-gray-400"><span class="font-medium">Start:</span> {{ $payload['starts_at'] }}</p>
                    <p class="text-gray-600 dark:text-gray-400"><span class="font-medium">End:</span> {{ $payload['ends_at'] }}</p>
                    <p class="text-gray-600 dark:text-gray-400"><span class="font-medium">Participant Mode:</span> {{ ucfirst($payload['participant_mode']) }}</p>
                </div>
            </div>
            @if(!empty($payload['instructions']))
                <div class="mt-4 p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                    <p class="text-sm font-medium text-blue-900 dark:text-blue-100">Instructions:</p>
                    <p class="text-sm text-blue-800 dark:text-blue-200 mt-1">{{ $payload['instructions'] }}</p>
                </div>
            @endif
        </section>

        <section class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6 shadow-sm">
            <h2 class="text-xl font-semibold mb-4 text-gray-900 dark:text-white">Section Overview</h2>
            <div class="space-y-3">
                @foreach($payload['sections'] as $i => $section)
                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 bg-gray-50 dark:bg-gray-900">
                        <div class="flex items-center justify-between">
                            <h3 class="font-medium text-gray-900 dark:text-white">{{ $i + 1 }}. {{ $section['title'] }}</h3>
                            <span class="text-xs px-2 py-1 bg-indigo-100 text-indigo-800 rounded">{{ $section['source_type'] }}</span>
                        </div>
                        <div class="grid md:grid-cols-3 gap-2 mt-2 text-sm text-gray-600 dark:text-gray-400">
                            <p><span class="font-medium">Type:</span> {{ str_replace('_', ' ', $section['question_type']) }}</p>
                            <p><span class="font-medium">Questions:</span> {{ $section['question_count'] }}</p>
                            <p><span class="font-medium">Time:</span> {{ $section['time_limit_minutes'] ?: 'No limit' }} min</p>
                        </div>
                        @if(!empty($section['instructions']))
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">{{ $section['instructions'] }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        </section>

        <form method="POST" action="{{ route('examinations-hub.create.store') }}">
            @csrf
            <input type="hidden" name="payload_json" value="{{ $payloadJson }}">
            
            @livewire('examinations.question-editor', [
                'sections' => $payload['sections'],
                'questions' => $generatedQuestions,
                'hardenedMode' => $hardenedMode,
            ], key('question-editor-'.md5($payloadJson)))

            <div class="flex items-center justify-between mt-6">
                <a href="{{ route('examinations-hub.create', ['draft' => $payloadJson]) }}" class="px-6 py-2 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300">
                    ← Back to Edit
                </a>
                <button type="submit" class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium shadow-sm">
                    Create Examination
                </button>
            </div>
        </form>
    </div>
</x-layouts.app>
