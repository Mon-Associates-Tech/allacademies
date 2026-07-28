<div class="space-y-6">
    @if($hardenedMode)
        <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4 mb-6">
            <p class="text-yellow-800 dark:text-yellow-200 font-medium">🔒 Hardened Mode Active</p>
            <p class="text-sm text-yellow-700 dark:text-yellow-300 mt-1">Questions are hidden for security. They will only be visible after the exam start date and time.</p>
        </div>

        @foreach($sections as $sectionIndex => $section)
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        {{ $sectionIndex + 1 }}. {{ $section['title'] }}
                    </h3>
                    <span class="text-xs px-2 py-1 bg-indigo-100 text-indigo-800 rounded">{{ $section['source_type'] }}</span>
                </div>

                <div class="grid md:grid-cols-3 gap-4 text-sm">
                    <div>
                        <p class="text-gray-600 dark:text-gray-400"><span class="font-medium">Question Type:</span> {{ str_replace('_', ' ', ucfirst($section['question_type'])) }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600 dark:text-gray-400"><span class="font-medium">Total Questions:</span> {{ $section['question_count'] }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600 dark:text-gray-400"><span class="font-medium">Time Limit:</span> {{ $section['time_limit_minutes'] ?: 'No limit' }} min</p>
                    </div>
                </div>

                @if(!empty($section['description']))
                    <div class="mt-3">
                        <p class="text-sm text-gray-600 dark:text-gray-400"><span class="font-medium">Description:</span> {{ $section['description'] }}</p>
                    </div>
                @endif

                @if(!empty($section['instructions']))
                    <div class="mt-3 p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                        <p class="text-xs font-medium text-blue-900 dark:text-blue-100 mb-1">Instructions:</p>
                        <p class="text-sm text-blue-800 dark:text-blue-200">{{ $section['instructions'] }}</p>
                    </div>
                @endif

                <div class="mt-4 p-4 bg-gray-100 dark:bg-gray-700 rounded-lg text-center">
                    <p class="text-sm text-gray-600 dark:text-gray-400">🔒 {{ $section['question_count'] }} questions hidden (Hardened Mode)</p>
                </div>
            </div>
        @endforeach
    @else
        @foreach($sections as $sectionIndex => $section)
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg" x-data="{ open: true }">
                <div class="p-4 flex items-center justify-between cursor-pointer" @click="open = !open">
                    <div class="flex items-center gap-3">
                        <svg x-show="!open" class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                        <svg x-show="open" class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ $sectionIndex + 1 }}. {{ $section['title'] }}
                        </h3>
                        <span class="text-sm text-gray-500 dark:text-gray-400">
                            ({{ count($questions[$sectionIndex] ?? []) }} questions)
                        </span>
                    </div>
                    <button type="button" wire:click.stop="addManualQuestion({{ $sectionIndex }})" class="px-3 py-1 text-sm bg-green-600 text-white rounded hover:bg-green-700">
                        + Add Question
                    </button>
                </div>

                <div x-show="open" x-collapse class="border-t border-gray-200 dark:border-gray-700">
                    <div class="p-6">
                        @if(!empty($questions[$sectionIndex]))
                            <div class="space-y-4">
                                @foreach($questions[$sectionIndex] as $qIndex => $question)
                                    @if(isset($question['hardened']) && $question['hardened'])
                                        <div class="p-4 bg-gray-100 dark:bg-gray-700 rounded-lg">
                                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $question['count'] }} questions will be generated</p>
                                        </div>
                                    @elseif(isset($question['placeholder']) && $question['placeholder'])
                                        <div class="p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                                            <p class="text-sm text-blue-700 dark:text-blue-300">Manual question slot - add during creation</p>
                                        </div>
                                    @else
                                        <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-900" wire:key="q-{{ $sectionIndex }}-{{ $qIndex }}">
                                            <div class="flex items-start justify-between mb-3">
                                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Question {{ $qIndex + 1 }}</span>
                                                <div class="flex items-center gap-2">
                                                    @if($question['is_edited'] ?? false)
                                                        <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">Edited</span>
                                                    @endif
                                                    @if($question['ai_generated'] ?? false)
                                                        <span class="text-xs bg-purple-100 text-purple-800 px-2 py-1 rounded">AI</span>
                                                    @endif
                                                    <button type="button" wire:click="removeQuestion({{ $sectionIndex }}, {{ $qIndex }})" class="text-red-600 hover:text-red-800 text-sm">Remove</button>
                                                </div>
                                            </div>

                                            <div class="space-y-3">
                                                {{-- Question Text --}}
                                                <div>
                                                    <label class="text-xs text-gray-600 dark:text-gray-400 mb-1 block">Question Text</label>
                                                    @php
                                                        $qContent = $question['question'] ?? ['up' => '', 'down' => ''];
                                                        if (!is_array($qContent)) {
                                                            $qContent = ['up' => $qContent, 'down' => $qContent];
                                                        }
                                                        $displayContent = $qContent['down'] ?: $qContent['up'];
                                                        $textareaContent = $qContent['up'] ?: $qContent['down'];
                                                    @endphp

                                                    {{-- Display HTML with images --}}
                                                    <div class="mb-2 py-1 prose prose-sm dark:prose-invert max-w-none">
                                                        {!! $displayContent !!}
                                                    </div>

                                                    {{-- Editable textarea - binds ONLY to .up --}}
                                                    <textarea
                                                        wire:model.blur="questions.{{ $sectionIndex }}.{{ $qIndex }}.question.up"
                                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-800 dark:text-white text-sm"
                                                        rows="2"
                                                        placeholder="Edit question text..."
                                                    >{{ $textareaContent }}</textarea>
                                                </div>

                                                {{-- Multiple Choice Options --}}
                                                @if(($question['type'] ?? '') === 'multiple_choice' && !empty($question['options']))
                                                    <div>
                                                        <label class="text-xs text-gray-600 dark:text-gray-400 mb-1 block">Options</label>
                                                        <div class="space-y-2">
                                                            @foreach($question['options'] as $optIndex => $option)
                                                                @php
                                                                    // Handle Mark array structure
                                                                    $optArray = is_array($option) ? $option : ['up' => $option, 'down' => $option];
                                                                    $optionDisplay = $optArray['down'] ?: $optArray['up'];
                                                                    $optionText = $optArray['up'] ?: $optArray['down'];
                                                                    $optionKey = chr(65 + $optIndex);
                                                                @endphp

                                                                <div class="space-y-2">
                                                                    {{-- Readonly display with HTML/images --}}
                                                                    <div class="flex gap-2 py-1">
                                                                        <span class="text-sm font-medium w-6 flex-shrink-0 pt-0.5">{{ $optionKey }}.</span>
                                                                        <div class="flex-1 pt-0.5 prose prose-sm dark:prose-invert max-w-none">
                                                                            {!! $optionDisplay !!}
                                                                        </div>
                                                                    </div>

                                                                    {{-- Editable input - binds ONLY to .up --}}
                                                                    <div class="flex gap-2">
                                                                        <span class="text-xs font-medium w-6 text-gray-500 flex-shrink-0 pt-0.5">{{ $optionKey }}.</span>
                                                                        <input
                                                                            type="text"
                                                                            wire:model.blur="questions.{{ $sectionIndex }}.{{ $qIndex }}.options.{{ $optIndex }}.up"
                                                                            value="{{ $optionText }}"
                                                                            class="flex-1 px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-800 dark:text-white"
                                                                            placeholder="Edit option..."
                                                                        >
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>

                                                    {{-- Correct Answer & Marks --}}
                                                    <div class="grid grid-cols-2 gap-3">
                                                        <div>
                                                            <label class="text-xs text-gray-600 dark:text-gray-400 mb-1 block">Correct Answer</label>
                                                            <select
                                                                wire:model.blur="questions.{{ $sectionIndex }}.{{ $qIndex }}.correct_answer"
                                                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-800 dark:text-white"
                                                            >
                                                                <option value="">Select</option>
                                                                @foreach($question['options'] as $optIndex => $option)
                                                                    <option value="{{ chr(65 + $optIndex) }}" @selected(($question['correct_answer'] ?? '') === chr(65 + $optIndex))>{{ chr(65 + $optIndex) }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div>
                                                            <label class="text-xs text-gray-600 dark:text-gray-400 mb-1 block">Marks</label>
                                                            <input
                                                                type="number"
                                                                min="1"
                                                                wire:model.blur="questions.{{ $sectionIndex }}.{{ $qIndex }}.marks"
                                                                value="{{ $question['marks'] ?? 1 }}"
                                                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-800 dark:text-white"
                                                            >
                                                        </div>
                                                    </div>

                                                {{-- True/False Options --}}
                                                @elseif(($question['type'] ?? '') === 'true_false')
                                                    <div class="grid grid-cols-2 gap-3">
                                                        <div>
                                                            <label class="text-xs text-gray-600 dark:text-gray-400 mb-1 block">Correct Answer</label>
                                                            <select
                                                                wire:model.blur="questions.{{ $sectionIndex }}.{{ $qIndex }}.correct_answer"
                                                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-800 dark:text-white"
                                                            >
                                                                <option value="">Select</option>
                                                                <option value="True" @selected(($question['correct_answer'] ?? '') === 'True')>True</option>
                                                                <option value="False" @selected(($question['correct_answer'] ?? '') === 'False')>False</option>
                                                            </select>
                                                        </div>
                                                        <div>
                                                            <label class="text-xs text-gray-600 dark:text-gray-400 mb-1 block">Marks</label>
                                                            <input
                                                                type="number"
                                                                min="1"
                                                                wire:model.blur="questions.{{ $sectionIndex }}.{{ $qIndex }}.marks"
                                                                value="{{ $question['marks'] ?? 1 }}"
                                                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-800 dark:text-white"
                                                            >
                                                        </div>
                                                    </div>

                                                {{-- Other question types --}}
                                                @else
                                                    <div>
                                                        <label class="text-xs text-gray-600 dark:text-gray-400 mb-1 block">Marks</label>
                                                        <input
                                                            type="number"
                                                            min="1"
                                                            wire:model.blur="questions.{{ $sectionIndex }}.{{ $qIndex }}.marks"
                                                            value="{{ $question['marks'] ?? 1 }}"
                                                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-800 dark:text-white"
                                                        >
                                                    </div>
                                                @endif

                                                {{-- Explanation --}}
                                                @if(!empty($question['explanation']))
                                                    <div>
                                                        <label class="text-xs text-gray-600 dark:text-gray-400 mb-1 block">Explanation (Optional)</label>
                                                        <textarea
                                                            wire:model.blur="questions.{{ $sectionIndex }}.{{ $qIndex }}.explanation"
                                                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-800 dark:text-white"
                                                            rows="2"
                                                        >{{ $question['explanation'] ?? '' }}</textarea>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-gray-500 dark:text-gray-400">No questions generated for this section</p>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    @endif

    <input type="hidden" name="questions_json" value="{{ $this->getQuestionsJson() }}">
</div>
