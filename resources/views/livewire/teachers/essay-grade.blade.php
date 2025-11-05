<x-layouts.app>
    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">

                <h2 class="text-xl font-bold mb-6">Grade Essay Responses</h2>

                @foreach ($essays as $essay)
                    <div class="mb-8 border-b pb-6 border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold mb-2">{{ $essay['question_text'] }}</h3>

                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-1">Student Answer</label>
                            <textarea readonly rows="6" class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded-md">{{ $essay['response'] }}</textarea>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-1">Correct Answer</label>
                            <textarea rows="6" wire:model.lazy="correctAnswers.{{ $essay['question_id'] }}" class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded-md"></textarea>
                        </div>

                        <div class="flex items-center space-x-4">
                            <div>
                                <label for="score_{{ $essay['question_id'] }}" class="block text-sm font-medium mb-1">Score (out of {{ $essay['points'] }})</label>
                                <input type="number"
                                       id="score_{{ $essay['question_id'] }}"
                                       wire:model.lazy="scores.{{ $essay['question_id'] }}"
                                       min="0"
                                       max="{{ $essay['points'] }}"
                                       class="w-20 border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded-md">
                            </div>
                            <button wire:click="saveGrades({{ $essay['question_id'] }})"
                                    class="mt-6 inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md">
                                Save Score
                            </button>
                        </div>
                    </div>
                @endforeach

                <div class="mt-6 flex justify-end">
                    <button wire:click="submitAllScores"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-md">
                        Submit All Scores
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
