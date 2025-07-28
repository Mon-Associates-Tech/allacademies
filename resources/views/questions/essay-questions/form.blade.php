@props([
    'academicTopic',
    'essayQuestion' => null,
    'action',
    'method' => 'POST',
    'title' => 'Essay Question',
    'description' => 'Fill in the details for your essay question.',
    'submitText' => 'Save Question'
])

@php
    $isEditing = !is_null($essayQuestion);
    $currentDifficulty = $isEditing ? $essayQuestion->difficulty_level : old('difficulty_level', 'unspecified');
    $currentScore = $isEditing ? $essayQuestion->score : old('score', 15);
    $currentSubtopic = $isEditing ? ($essayQuestion->subtopic?->name ?? '') : old('subtopic', '');
    $currentQuestion = $isEditing ? $essayQuestion->question->down : old('question', '');
    $currentAnswer = $isEditing ? $essayQuestion->answer->down : old('answer', '');
@endphp

<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $title }}</h2>
        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $description }}</p>
    </div>

    <form method="POST" action="{{ $action }}" class="p-6 space-y-6">
        @csrf
        @if($method !== 'POST')
            @method($method)
        @endif

        <!-- Basic Question Properties -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <x-form.select
                    name="difficulty_level"
                    label="Difficulty Level"
                    :value="$currentDifficulty"
                    :options="[
                        'unspecified' => 'Unspecified',
                        'easy' => 'Easy',
                        'medium' => 'Medium',
                        'difficult' => 'Difficult',
                    ]"
                />
            </div>
            <div>
                <x-form.input
                    name="score"
                    type="number"
                    :value="$currentScore"
                    label="Maximum Score"
                    min="1"
                    max="100"
                />
            </div>
        </div>
        <!-- Subtopic Selection -->
        @include('questions.subtopic-form', [
    'new' => !$isEditing,
    'question' => $isEditing ? $essayQuestion : $academicTopic
    ])
        <!-- Question Content -->
        <div class="space-y-6">
            <div>
                <x-form.rich-editor
                    class="rich-editor"
                    full
                    name="question"
                    required
                    :value="$currentQuestion"
                />
                @error('question')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <x-form.rich-editor
                    class="rich-editor"
                    full
                    info="Provide a sample answer, grading rubric, or guidelines for evaluating student responses."
                    name="answer"
                    :value="$currentAnswer"
                />
                @error('answer')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex items-center justify-between pt-6 border-t border-gray-200 dark:border-gray-700">
            <div class="text-sm text-gray-500 dark:text-gray-400">
                <span class="text-red-500">*</span> Required fields
            </div>
            <div class="flex items-center space-x-3">
                <x-button.secondary type="button" onclick="history.back()">
                    Cancel
                </x-button.secondary>
                <x-button.primary type="submit">
                    {{ $submitText }}
                </x-button.primary>
            </div>
        </div>
    </form>
</div>


