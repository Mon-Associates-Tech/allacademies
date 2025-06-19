<div>
    <h2 class="text-xl font-bold mb-6">Essay Responses</h2>

    @foreach ($essays as $essay)
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow mb-6">
            <h3 class="text-lg font-semibold mb-2">{{ $essay['question_text'] }}</h3>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Student Response</label>
                <textarea readonly rows="6" class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700">{{ $essay['user_answer'] }}</textarea>
            </div>

            <div class="mb-4">
                <label for="score_{{ $essay['question_id'] }}" class="block text-sm font-medium mb-1">Score</label>
                <input type="number"
                       id="score_{{ $essay['question_id'] }}"
                       wire:model.lazy="scores.{{ $essay['question_id'] }}"
                       min="0"
                       max="{{ $essay['max_score'] }}"
                       class="w-20 border-gray-300 dark:border-gray-600 dark:bg-gray-700">
            </div>

            <button wire:click="saveScore({{ $essay['question_id'] }})"
                    class="bg-green-600 text-white px-4 py-2 rounded">
                Save Score
            </button>
        </div>
    @endforeach
</div>

