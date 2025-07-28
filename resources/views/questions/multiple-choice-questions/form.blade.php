@php
    $isEdit = isset($multipleChoiceQuestion) && $multipleChoiceQuestion->exists;
    $question = $isEdit ? $multipleChoiceQuestion : null;
    $formTitle = $isEdit ? 'Edit Multiple Choice Question' : 'Create Multiple Choice Question';
    $formDescription = $isEdit
        ? "Update the question for {$academicTopic->name}"
        : "Add a new question with multiple options for {$academicTopic->name}";
    $submitText = $isEdit ? 'Update Question' : 'Create Question';
    $formAction = $isEdit
        ? route('multiple-choice-questions.update', [
            'multiple_choice_question' => $question,
            'academic_topic' => $academicTopic,
            'academic_subject' => getRouteParameter('academic_subject'),
            'academic_level' => getRouteParameter('academic_level'),
            'academic_group' => getRouteParameter('academic_group')
        ])
        : route('multiple-choice-questions.store', [
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
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center space-x-4">
                <div class="p-3 bg-indigo-100 rounded-full">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $formTitle }}</h1>
                    <p class="text-gray-600">{{ $formDescription }}</p>
                </div>
            </div>
        </div>

        <!-- Question Form -->
        <form method="POST" action="{{ $formAction }}" class="bg-white rounded-lg shadow-sm border border-gray-200" x-data="multipleChoiceForm()">
            @csrf
            @if($isEdit)
                @method('PUT')
            @endif

            <!-- Question Content -->
            <div class="p-6 space-y-6">
                <!-- Question Text -->
                <div class="space-y-2">
                    <x-form.rich-editor
                        class="min-h-[200px]"
                        name="question"
                        required
                        :value="old('question', $question->question ?? '')"
                        placeholder="Enter your multiple choice question here..."
                    />
                    @error('question')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Answer Options Section -->
                <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Answer Options</h3>
                        <div class="text-sm text-gray-500">
                            Select the correct answer by clicking the radio button
                        </div>
                    </div>

                    <div class="space-y-4">
                        @foreach(['a', 'b', 'c', 'd', 'e'] as $option)
                            <div class="relative group">
                                <div class="flex items-start space-x-3 border rounded-lg p-4 transition-all duration-200 hover:border-gray-300"
                                     :class="selectedAnswer === '{{ $option }}' ? 'bg-green-50 border-green-200' : 'bg-white border-gray-200'">

                                    <!-- Option Content -->
                                    <div class="flex-1">
                                        <div class="flex items-center pt-3">
                                            <input
                                                type="radio"
                                                name="answer"
                                                value="{{ $option }}"
                                                id="answer_{{ $option }}"
                                                class="form-radio text-green-600 focus:ring-green-500 h-5 w-5"
                                                x-model="selectedAnswer"
                                                {{ old('answer', $question->answer ?? '') === $option ? 'checked' : '' }}
                                            >
                                            <label for="answer_{{ $option }}" class="ml-2 flex items-center cursor-pointer">
                                                <span class="text-lg font-semibold text-gray-700">
                                                    Option {{ strtoupper($option) }}
                                                </span>
                                                <!-- Correct Answer Indicator -->
                                                <div x-show="selectedAnswer === '{{ $option }}'"
                                                     x-transition:enter="transition ease-out duration-200"
                                                     x-transition:enter-start="opacity-0 scale-95"
                                                     x-transition:enter-end="opacity-100 scale-100"
                                                     x-transition:leave="transition ease-in duration-150"
                                                     x-transition:leave-start="opacity-100 scale-100"
                                                     x-transition:leave-end="opacity-0 scale-95"
                                                     class="ml-2 flex items-center text-green-600">
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                    </svg>
                                                    <span class="ml-1 text-xs font-medium">Correct</span>
                                                </div>
                                            </label>
                                        </div>

                                        <x-form.rich-editor
                                            class="min-h-[120px]"
                                            name="option_{{ $option }}"
                                            label=""
                                            :value="old('option_' . $option, $question->{'option_' . $option} ?? '')"
                                            placeholder="Enter option {{ strtoupper($option) }} content..."
                                        />
                                        @error('option_' . $option)
                                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @error('answer')
                    <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Settings Section -->
            <div class="border-t border-gray-200 p-6 bg-gray-50 space-y-4">
                <h3 class="text-sm font-medium text-gray-900">Question Settings</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <x-form.select
                            name="difficulty_level"
                            label="Difficulty Level"
                            :value="old('difficulty_level', $question->difficulty_level ?? 'unspecified')"
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
                            label="Score Points"
                            :value="old('score', $question->score ?? 1)"
                            min="1"
                            max="100"
                        />
                    </div>
                </div>
                <div>
                    <!-- Subtopic Selector -->
                        @include('questions.subtopic-form', ['new' => !$isEdit, 'question' => $isEdit ?  $question : $academicTopic ])

                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-between px-6 py-4 border-t border-gray-200">
                <x-link.secondary :to="route('multiple-choice-questions.index', [
                    'academic_topic' => $academicTopic,
                    'academic_subject' => getRouteParameter('academic_subject'),
                    'academic_level' => getRouteParameter('academic_level'),
                    'academic_group' => getRouteParameter('academic_group')
                ])" class="inline-flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Cancel
                </x-link.secondary>

                <x-button.primary type="submit" class="inline-flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    {{ $submitText }}
                </x-button.primary>
            </div>
        </form>
    </div>

    <!-- Sidebar -->
    <div class="space-y-6">
        <!-- Question Preview -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6" x-data="questionPreview()">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Question Preview</h3>
            <div class="space-y-3">
                <div class="text-sm text-gray-600" x-show="!selectedAnswer">
                    Select a correct answer to see the preview
                </div>
                <div x-show="selectedAnswer"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     class="space-y-2">
                    <div class="text-sm font-medium text-gray-700">Correct Answer:</div>
                    <div class="flex items-center space-x-2 p-3 bg-green-50 rounded-lg border border-green-200">
                        <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center">
                            <span class="text-green-700 font-bold text-sm" x-text="selectedAnswer?.toUpperCase()"></span>
                        </div>
                        <span class="text-sm text-green-700 font-medium" x-text="'Option ' + selectedAnswer?.toUpperCase()"></span>
                        <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Help Card -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Tips for Multiple Choice Questions</h3>
            <ul class="space-y-3 text-sm text-gray-600">
                <li class="flex items-start">
                    <svg class="w-4 h-4 text-green-500 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Make all options plausible but only one correct
                </li>
                <li class="flex items-start">
                    <svg class="w-4 h-4 text-green-500 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Keep options roughly the same length
                </li>
                <li class="flex items-start">
                    <svg class="w-4 h-4 text-green-500 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Avoid "All of the above" or "None of the above"
                </li>
                <li class="flex items-start">
                    <svg class="w-4 h-4 text-green-500 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Use clear, unambiguous language
                </li>
                <li class="flex items-start">
                    <svg class="w-4 h-4 text-green-500 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Test one concept per question
                </li>
            </ul>
        </div>

        <!-- Quick Stats -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6" x-data="{ selectedAnswer: '' }" x-init="$watch('$store.answer.selected', value => selectedAnswer = value)">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Question Status</h3>
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Answer Selected:</span>
                    <div class="flex items-center">
                        <div x-show="!selectedAnswer" class="w-2 h-2 bg-red-400 rounded-full mr-2"></div>
                        <div x-show="selectedAnswer" class="w-2 h-2 bg-green-400 rounded-full mr-2"></div>
                        <span class="text-sm font-medium" x-text="selectedAnswer ? 'Yes' : 'No'"></span>
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Options Available:</span>
                    <span class="text-sm font-medium">5 (A-E)</span>
                </div>
            </div>
        </div>

        <!-- Plugins Section -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Available Tools</h3>
                <x-plugins link="{{ url()->current() . '/new' }}" />
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script>
    // Initialize Alpine store for shared state
    document.addEventListener('alpine:init', () => {
        Alpine.store('answer', {
            selected: @json(old('answer', $question->answer ?? '')),

            setSelected(value) {
                this.selected = value;
            }
        });
    });

    function multipleChoiceForm() {
        return {
            selectedAnswer: @json(old('answer', $question->answer ?? '')),

            init() {
                // Watch for changes and update the store
                this.$watch('selectedAnswer', (value) => {
                    this.$store.answer.setSelected(value);
                });

                // Initialize store with current value
                this.$store.answer.setSelected(this.selectedAnswer);
            }
        }
    }

    function questionPreview() {
        return {
            selectedAnswer: @json(old('answer', $question->answer ?? '')),

            init() {
                // Watch the store for changes
                this.$watch('$store.answer.selected', (value) => {
                    this.selectedAnswer = value;
                });
            }
        }
    }
</script>

<style>
    [x-cloak] { display: none !important; }
</style>
