@php
    $isEditing = isset($trueOrFalseQuestion) && $trueOrFalseQuestion;
    $formTitle = $isEditing ? 'Edit True/False Question' : 'Create True/False Question';
    $formDescription = $isEditing ? 'Update the question details and configuration' : 'Add a new question for ' . $academicTopic->name;
    $buttonText = $isEditing ? 'Update Question' : 'Create Question';
    $formAction = $isEditing
        ? route('true-or-false-questions.update', [
            'true_or_false_question' => $trueOrFalseQuestion,
            'academic_topic' => $academicTopic,
            'academic_subject' => getRouteParameter('academic_subject'),
            'academic_level' => getRouteParameter('academic_level'),
            'academic_group' => getRouteParameter('academic_group')
        ])
        : route('true-or-false-questions.store', [
            'academic_topic' => $academicTopic,
            'academic_subject' => getRouteParameter('academic_subject'),
            'academic_level' => getRouteParameter('academic_level'),
            'academic_group' => getRouteParameter('academic_group')
        ]);
    $cancelRoute = route('true-or-false-questions.index', [
        'academic_topic' => $academicTopic,
        'academic_subject' => getRouteParameter('academic_subject'),
        'academic_level' => getRouteParameter('academic_level'),
        'academic_group' => getRouteParameter('academic_group')
    ]);
@endphp

<div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Form Section -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Header -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center space-x-4">
                <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-full">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $formTitle }}</h1>
                    <p class="text-gray-600 dark:text-gray-400">{{ $formDescription }}</p>
                </div>
            </div>
        </div>

        <!-- Question Form -->
        <form method="POST" action="{{ $formAction }}" class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
            @csrf
            @if($isEditing)
                @method('PATCH')
            @endif

            <!-- Question Content -->
            <div class="p-6 space-y-6">
                <!-- Question Text -->
                <div class="space-y-2">
                    <x-form.rich-editor
                        class="min-h-[200px]"
                        name="question"
                        required
                        :value="$isEditing ? $trueOrFalseQuestion->question : old('question')"
                        placeholder="Enter your true/false question here..."
                    />
                    @error('question')
                    <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Answer Selection -->
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 border border-gray-200 dark:border-gray-600">
                    <div class="space-y-3">
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                            Correct Answer <span class="text-red-500">*</span>
                        </label>
                        <div class="flex items-center space-x-6">
                            <label class="inline-flex items-center">
                                <input
                                    type="radio"
                                    name="answer"
                                    value="1"
                                    class="form-radio text-green-600 focus:ring-green-500 focus:ring-offset-0"
                                    {{ ($isEditing && $trueOrFalseQuestion->answer == 1) || old('answer') == '1' ? 'checked' : '' }}
                                >
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300 font-medium">True</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input
                                    type="radio"
                                    name="answer"
                                    value="0"
                                    class="form-radio text-red-600 focus:ring-red-500 focus:ring-offset-0"
                                    {{ ($isEditing && $trueOrFalseQuestion->answer == 0) || old('answer') == '0' ? 'checked' : '' }}
                                >
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300 font-medium">False</span>
                            </label>
                        </div>
                        @error('answer')
                        <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Settings Section -->
            <div class="border-t border-gray-200 dark:border-gray-700 p-6 bg-gray-50 dark:bg-gray-700 space-y-4">
                <h3 class="text-sm font-medium text-gray-900 dark:text-white">Question Settings</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Difficulty Level -->
                    <div>
                        <x-form.select
                            name="difficulty_level"
                            label="Difficulty"
                            :value="$isEditing ? $trueOrFalseQuestion->difficulty_level : old('difficulty_level', 'unspecified')"
                            :options="[
                                'unspecified' => 'Unspecified',
                                'easy' => 'Easy',
                                'medium' => 'Medium',
                                'difficult' => 'Difficult',
                            ]"
                        />
                    </div>

                    <!-- Score Points -->
                    <div>
                        <x-form.input
                            name="score"
                            type="number"
                            label="Score Points"
                            :value="$isEditing ? $trueOrFalseQuestion->score : old('score', 1)"
                            min="1"
                            max="100"
                            required
                        />
                    </div>

                    <!-- Subtopic -->
                    <div>
                        @include('questions.subtopic-form', [
                            'question' => $isEditing ? $trueOrFalseQuestion : $academicTopic,
                            'new' => !$isEditing
                        ])
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-between px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                <x-link.secondary :to="$cancelRoute" class="inline-flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Cancel
                </x-link.secondary>

                <x-button.primary type="submit" class="inline-flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    {{ $buttonText }}
                </x-button.primary>
            </div>
        </form>
    </div>

    <!-- Sidebar -->
    <div class="space-y-6">
        @if($isEditing)
            <!-- Question Context Card (Edit Mode) -->
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">Question Details</h3>
                        <div class="mt-2 text-sm text-blue-700 dark:text-blue-300">
                            <p>Topic: <strong>{{ $trueOrFalseQuestion->academicTopic->name }}</strong></p>
                            <p>Subject: <strong>{{ $trueOrFalseQuestion->academicTopic->academicSubject->name }}</strong></p>
                            @if($trueOrFalseQuestion->subtopic)
                                <p>Subtopic: <strong>{{ $trueOrFalseQuestion->subtopic->name }}</strong></p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @else
            <!-- Help Card (Create Mode) -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Tips for Good Questions</h3>
                <ul class="space-y-3 text-sm text-gray-600 dark:text-gray-400">
                    <li class="flex items-start">
                        <svg class="w-4 h-4 text-green-500 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Keep questions clear and unambiguous</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-4 h-4 text-green-500 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Focus on a single concept</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-4 h-4 text-green-500 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Avoid using absolute terms like "always" or "never"</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-4 h-4 text-green-500 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Ensure statements are definitively true or false</span>
                    </li>
                </ul>
            </div>
        @endif

        <!-- Plugins Section -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Available Tools</h3>
                <x-plugins link="{{ url()->current() . ($isEditing ? '' : '/new') }}" />
            </div>
        </div>
    </div>
</div>
