<div class="p-6 bg-gray-50 dark:bg-gray-900 min-h-screen">
    <div class="max-w-6xl mx-auto bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
        <div class="p-8 border-b dark:border-gray-700">
            <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Quiz Creator</h2>
            <p class="text-gray-600 dark:text-gray-400">Design your quiz by adding sections and questions from multiple sources.</p>
        </div>

        <div class="p-8 space-y-8">
            <!-- Validation Errors -->
            @if ($errors->any())
                <div class="p-4 bg-red-50 border-l-4 border-red-400 text-red-700">
                    <p class="font-bold">Please fix the following errors:</p>
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session()->has('error'))
                <div class="p-4 bg-red-50 border-l-4 border-red-400 text-red-700">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Basic Info -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 bg-gray-50 dark:bg-gray-700/20 p-6 rounded-xl border border-gray-100 dark:border-gray-700">
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Quiz Title</label>
                        <input type="text" wire:model="title" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white transition duration-200" placeholder="e.g., Mid-term Mathematics Quiz">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Description</label>
                        <textarea wire:model="description" rows="3" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white transition duration-200" placeholder="Provide a brief overview of the quiz..."></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">General Instructions</label>
                        <textarea wire:model="instructions" rows="3" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white transition duration-200" placeholder="General instructions for all students..."></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Subject</label>
                            <livewire:common.searchable-multi-select
                                name="subject_id"
                                :items="$subjects->map(fn($s) => [
                                    'id' => $s->id,
                                    'name' => ($s->academicLevel->academicGroup->name ?? 'N/A') . ' - ' . ($s->academicLevel->name ?? 'N/A') . ' - ' . $s->name
                                ])->toArray()"
                                :selected="[$subject_id]"
                                placeholder="Select a subject"
                                :multiple="false"
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Overall Duration (mins)</label>
                            <input type="number" wire:model="quizDuration" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white transition duration-200" placeholder="Optional overall duration">
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Starts At</label>
                            <input type="datetime-local" wire:model="starts_at" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white transition duration-200">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Ends At</label>
                            <input type="datetime-local" wire:model="ends_at" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white transition duration-200">
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 p-4 rounded-lg border dark:border-gray-600 space-y-4 shadow-sm">
                        <h4 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider">Settings & Restrictions</h4>

                        <div class="flex items-center justify-between">
                            <div class="flex flex-col">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Randomize Questions</span>
                                <span class="text-xs text-gray-500">Students get questions in different order</span>
                            </div>
                            <button type="button" wire:click="$toggle('is_randomized')" class="{{ $is_randomized ? 'bg-indigo-600' : 'bg-gray-200 dark:bg-gray-600' }} relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2">
                                <span class="{{ $is_randomized ? 'translate-x-5' : 'translate-x-0' }} pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"></span>
                            </button>
                        </div>

                        <div class="flex items-center justify-between">
                            <div class="flex flex-col">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Restrict Navigation</span>
                                <span class="text-xs text-gray-500">Prevent navigating back to previous questions</span>
                            </div>
                            <button type="button" wire:click="$toggle('restrict_navigation')" class="{{ $restrict_navigation ? 'bg-indigo-600' : 'bg-gray-200 dark:bg-gray-600' }} relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2">
                                <span class="{{ $restrict_navigation ? 'translate-x-5' : 'translate-x-0' }} pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"></span>
                            </button>
                        </div>

                        <div class="flex items-center justify-between">
                            <div class="flex flex-col">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Auto-submit on Violation</span>
                                <span class="text-xs text-gray-500">Submit automatically if tab switching limit exceeded</span>
                            </div>
                            <button type="button" wire:click="$toggle('auto_submit_on_violation')" class="{{ $auto_submit_on_violation ? 'bg-indigo-600' : 'bg-gray-200 dark:bg-gray-600' }} relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2">
                                <span class="{{ $auto_submit_on_violation ? 'translate-x-5' : 'translate-x-0' }} pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"></span>
                            </button>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Max Tab Switches Allowed</label>
                            <input type="number" wire:model="max_tab_switches" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="e.g., 3 (Leave empty for no limit)">
                        </div>

                        <div class="pt-4 border-t dark:border-gray-700">
                            <label class="block text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider mb-3">Participants & Access</label>

                            <div class="space-y-4">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-2">Audience Type</label>
                                    <livewire:common.searchable-multi-select
                                        name="audienceType"
                                        :items="[
                                            ['id' => 'students', 'name' => 'Registered Students'],
                                            ['id' => 'guests', 'name' => 'Guests'],
                                            ['id' => 'public', 'name' => 'General Public']
                                        ]"
                                        :selected="[$audienceType]"
                                        placeholder="Select Audience"
                                        :multiple="false"
                                    />
                                </div>

                                @if($audienceType === 'students')
                                    <div class="space-y-3 p-3 bg-gray-50 dark:bg-gray-900/50 rounded-lg border dark:border-gray-700">
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">Target Academic Groups</label>
                                            <livewire:common.searchable-multi-select
                                                name="selectedGroups"
                                                :items="collect($groups)->map(fn($g) => ['id' => $g->id, 'name' => $g->name])->toArray()"
                                                :selected="$selectedGroups"
                                                placeholder="All Groups"
                                                :multiple="true"
                                            />
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">Target Academic Levels</label>
                                            <livewire:common.searchable-multi-select
                                                name="selectedLevels"
                                                :items="collect($levels)->map(fn($l) => ['id' => $l->id, 'name' => ($l->academicGroup->name ?? '') . ' - ' . $l->name])->toArray()"
                                                :selected="$selectedLevels"
                                                placeholder="All Levels"
                                                :multiple="true"
                                            />
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">Specific Students</label>
                                            <div class="mb-2">
                                                <input type="text" wire:model.live.debounce.300ms="studentSearch"
                                                    class="block w-full text-xs rounded-lg border-gray-300 dark:bg-gray-800 dark:border-gray-600 dark:text-white"
                                                    placeholder="Search by name or email...">
                                            </div>
                                            <livewire:common.searchable-multi-select
                                                name="selectedStudents"
                                                :items="$foundStudents"
                                                :selected="$selectedStudents"
                                                placeholder="Select students from search results"
                                                :multiple="true"
                                            />
                                            <p class="text-[10px] text-gray-500 mt-1">Leave empty to include all students in selected groups/levels.</p>
                                        </div>
                                    </div>
                                @elseif($audienceType === 'guests')
                                    <div class="p-3 bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 text-xs rounded-lg border border-blue-100 dark:border-blue-800">
                                        Guests will be able to access this quiz via their guest dashboard.
                                    </div>
                                @elseif($audienceType === 'public')
                                    <div class="p-3 bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-300 text-xs rounded-lg border border-green-100 dark:border-green-800">
                                        This quiz will be publicly accessible via a direct link.
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sections Tabs -->
            <div class="border-b border-gray-200 dark:border-gray-700">
                <nav class="-mb-px flex space-x-8 overflow-x-auto">
                    @foreach($sections as $index => $section)
                        <button
                            wire:click="$set('activeSectionIndex', {{ $index }})"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm {{ $activeSectionIndex === $index ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
                        >
                            {{ ($section['title'] ?? '') ?: 'Untitled Section' }}
                        </button>
                    @endforeach
                    <button wire:click="addSection" class="whitespace-nowrap py-4 px-1 border-b-2 border-transparent font-medium text-sm text-green-600 hover:text-green-700">
                        + Add Section
                    </button>
                </nav>
            </div>

            <!-- Active Section Content -->
            @if(isset($sections[$activeSectionIndex]))
                <div wire:key="section-{{ $sections[$activeSectionIndex]['id'] ?? $activeSectionIndex }}" class="space-y-6 bg-gray-50 dark:bg-gray-700/30 p-6 rounded-lg">
                    <div class="flex justify-between items-start">
                        <div class="flex-1 space-y-4">
                            <input type="text" wire:model.live="sections.{{ $activeSectionIndex }}.title" placeholder="Section Title" class="text-xl font-semibold bg-transparent border-0 border-b border-gray-300 focus:ring-0 w-full dark:text-white">
                            @error('sections.' . $activeSectionIndex . '.title') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            <textarea wire:model="sections.{{ $activeSectionIndex }}.instructions" placeholder="Section Instructions" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" rows="2"></textarea>
                            <div class="flex items-center space-x-4">
                                <label class="text-sm text-gray-600 dark:text-gray-400">Duration (mins):</label>
                                <input type="number" wire:model="sections.{{ $activeSectionIndex }}.duration_minutes" class="w-20 rounded-md border-gray-300 dark:bg-gray-700 dark:text-white">
                            </div>
                        </div>
                        <button wire:click="removeSection({{ $activeSectionIndex }})" class="text-red-500 hover:text-red-700 ml-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                        </button>
                    </div>

                    <!-- Questions List -->
                    <div class="space-y-4">
                        <h4 class="font-medium text-gray-900 dark:text-white">Questions ({{ count($sections[$activeSectionIndex]['questions']) }})</h4>
                        @foreach($sections[$activeSectionIndex]['questions'] as $qIndex => $question)
                            <div x-data="{ expanded: false }" class="bg-white dark:bg-gray-800 rounded-md shadow-sm border dark:border-gray-600 overflow-hidden">
                                <div class="p-4 flex justify-between items-start">
                                    <div class="flex-1">
                                        <div class="flex items-center mb-2">
                                            <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 mr-2 border border-indigo-200 dark:border-indigo-800">{{ str_replace('_', ' ', $question['type']) }}</span>
                                            <span class="text-xs text-gray-500 dark:text-gray-400 font-medium">Question {{ $qIndex + 1 }}</span>
                                        </div>

                                        <div class="text-gray-800 dark:text-gray-200 text-sm leading-relaxed mb-2">
                                            {!! nl2br(e($question['text'])) !!}
                                        </div>

                                        <!-- Options Preview -->
                                        <div class="mt-4 space-y-2 border-t dark:border-gray-700 pt-4 bg-gray-50/50 dark:bg-gray-800/50 -mx-4 px-4 pb-4">
                                            @if($question['type'] === 'multiple_choice' && isset($question['options']))
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                                    @foreach($question['options'] as $key => $option)
                                                        @if(!empty($option))
                                                            <div class="flex items-center p-2 rounded border {{ ($question['correct_answer'] ?? '') === $key ? 'bg-green-50 border-green-200 dark:bg-green-900/20 dark:border-green-800' : 'bg-white border-gray-200 dark:bg-gray-700 dark:border-gray-600' }}">
                                                                <span class="w-6 h-6 flex items-center justify-center rounded-full bg-gray-100 dark:bg-gray-600 text-[10px] font-bold mr-2 {{ ($question['correct_answer'] ?? '') === $key ? 'bg-green-100 text-green-700 dark:bg-green-800 dark:text-green-300' : 'text-gray-500 dark:text-gray-400' }}">
                                                                    {{ strtoupper($key) }}
                                                                </span>
                                                                <span class="text-xs {{ ($question['correct_answer'] ?? '') === $key ? 'text-green-700 dark:text-green-300 font-medium' : 'text-gray-600 dark:text-gray-400' }}">
                                                                    {{ $option }}
                                                                </span>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            @elseif($question['type'] === 'true_false')
                                                <div class="flex gap-4">
                                                    <div class="flex items-center p-2 rounded border {{ strtolower($question['correct_answer'] ?? '') === 'true' ? 'bg-green-50 border-green-200 dark:bg-green-900/20 dark:border-green-800' : 'bg-white border-gray-200 dark:bg-gray-700 dark:border-gray-600' }}">
                                                        <span class="text-xs {{ strtolower($question['correct_answer'] ?? '') === 'true' ? 'text-green-700 dark:text-green-300 font-bold' : 'text-gray-600 dark:text-gray-400' }}">True</span>
                                                    </div>
                                                    <div class="flex items-center p-2 rounded border {{ strtolower($question['correct_answer'] ?? '') === 'false' ? 'bg-green-50 border-green-200 dark:bg-green-900/20 dark:border-green-800' : 'bg-white border-gray-200 dark:bg-gray-700 dark:border-gray-600' }}">
                                                        <span class="text-xs {{ strtolower($question['correct_answer'] ?? '') === 'false' ? 'text-green-700 dark:text-green-300 font-bold' : 'text-gray-600 dark:text-gray-400' }}">False</span>
                                                    </div>
                                                </div>
                                            @elseif($question['type'] === 'essay')
                                                <div class="bg-blue-50 dark:bg-blue-900/10 border border-blue-100 dark:border-blue-800 p-3 rounded-md">
                                                    <span class="text-[10px] font-semibold text-blue-600 dark:text-blue-400 uppercase block mb-1">Grading Criteria / Rubric:</span>
                                                    <p class="text-xs text-blue-800 dark:text-blue-300 italic">
                                                        {{ $question['grading_scheme'] ?? 'Students will provide a free-text response for evaluation.' }}
                                                    </p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="flex flex-col space-y-2 ml-4">
                                        <button wire:click="removeQuestion({{ $activeSectionIndex }}, {{ $qIndex }})" class="p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-full transition-colors" title="Remove Question">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        <button
                            wire:click="$set('showQuestionModal', true)"
                            class="w-full py-4 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg text-gray-500 hover:border-indigo-500 hover:text-indigo-500 transition-colors flex items-center justify-center space-x-2"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                            <span>Add Question</span>
                        </button>
                    </div>
                </div>
            @endif

            <!-- Submit -->
            <div class="flex justify-end pt-6 border-t dark:border-gray-700">
                <button wire:click="saveQuiz" class="bg-indigo-600 text-white px-8 py-3 rounded-lg font-bold hover:bg-indigo-700 transition-colors shadow-lg">
                    Save and Create Quiz
                </button>
            </div>
        </div>
    </div>

    <!-- Question Modal -->
    @if($showQuestionModal)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                    <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                </div>

                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                    <div class="flex border-b dark:border-gray-700">
                        <button wire:click="setSourcingMode('manual')" class="flex-1 py-4 text-sm font-medium {{ $sourcingMode === 'manual' ? 'bg-indigo-50 text-indigo-600 border-b-2 border-indigo-600' : 'text-gray-500 hover:bg-gray-50' }} dark:{{ $sourcingMode === 'manual' ? 'bg-indigo-900/20' : '' }}">Manual Input</button>
                        <button wire:click="setSourcingMode('db')" class="flex-1 py-4 text-sm font-medium {{ $sourcingMode === 'db' ? 'bg-indigo-50 text-indigo-600 border-b-2 border-indigo-600' : 'text-gray-500 hover:bg-gray-50' }} dark:{{ $sourcingMode === 'db' ? 'bg-indigo-900/20' : '' }}">From Database</button>
                        <button wire:click="setSourcingMode('ai')" class="flex-1 py-4 text-sm font-medium {{ $sourcingMode === 'ai' ? 'bg-indigo-50 text-indigo-600 border-b-2 border-indigo-600' : 'text-gray-500 hover:bg-gray-50' }} dark:{{ $sourcingMode === 'ai' ? 'bg-indigo-900/20' : '' }}">Generate with AI</button>
                    </div>

                    <div class="p-6 max-h-[70vh] overflow-y-auto">
                        @if($sourcingMode === 'manual')
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Question Type</label>
                                    <livewire:common.searchable-multi-select
                                        name="manualQuestionType"
                                        :items="[
                                            ['id' => 'multiple_choice', 'name' => 'Multiple Choice'],
                                            ['id' => 'true_false', 'name' => 'True/False'],
                                            ['id' => 'essay', 'name' => 'Essay / Short Answer']
                                        ]"
                                        :selected="[$manualQuestion['type']]"
                                        :multiple="false"
                                        :clearable="false"
                                    />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Question Text</label>
                                    <textarea wire:model="manualQuestion.text" rows="3" class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white"></textarea>
                                </div>

                                @if($manualQuestion['type'] === 'multiple_choice')
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        @foreach(['A', 'B', 'C', 'D', 'E'] as $letter)
                                            <div>
                                                <label class="text-xs font-bold text-gray-500">Option {{ $letter }}</label>
                                                <input type="text" wire:model="manualQuestion.options.{{ $letter }}" class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white">
                                            </div>
                                        @endforeach
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Correct Answer</label>
                                        <livewire:common.searchable-multi-select
                                            name="manualQuestionCorrectAnswer"
                                            :items="collect(['A', 'B', 'C', 'D', 'E'])->map(fn($l) => ['id' => $l, 'name' => 'Option ' . $l])->toArray()"
                                            :selected="[$manualQuestion['correct_answer']]"
                                            :multiple="false"
                                            placeholder="Select Option"
                                        />
                                    </div>
                                @elseif($manualQuestion['type'] === 'true_false')
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Correct Answer</label>
                                        <livewire:common.searchable-multi-select
                                            name="manualQuestionCorrectAnswer"
                                            :items="[['id' => 'True', 'name' => 'True'], ['id' => 'False', 'name' => 'False']]"
                                            :selected="[$manualQuestion['correct_answer']]"
                                            :multiple="false"
                                            placeholder="Select"
                                        />
                                    </div>
                                @endif
                                <button wire:click="addManualQuestion" class="w-full bg-indigo-600 text-white py-2 rounded-md hover:bg-indigo-700">Add to Section</button>
                            </div>

                        @elseif($sourcingMode === 'db')
                            <div class="space-y-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 mb-1">Academic Group</label>
                                        <livewire:common.searchable-multi-select
                                            name="dbGroupId"
                                            :items="collect($groups)->map(fn($g) => ['id' => $g['id'] ?? $g->id, 'name' => $g['name'] ?? $g->name])->toArray()"
                                            :selected="[$dbGroupId]"
                                            :multiple="false"
                                            placeholder="Select Group"
                                            size="sm"
                                        />
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 mb-1">Academic Level</label>
                                        <livewire:common.searchable-multi-select
                                            name="dbLevelId"
                                            :items="collect($levels)->map(fn($l) => ['id' => $l['id'] ?? $l->id, 'name' => $l['name'] ?? $l->name])->toArray()"
                                            :selected="[$dbLevelId]"
                                            :multiple="false"
                                            placeholder="Select Level"
                                            size="sm"
                                        />
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 mb-1">Subject</label>
                                        <livewire:common.searchable-multi-select
                                            name="dbSubjectId"
                                            :items="collect($subjects_list)->map(fn($s) => ['id' => $s['id'] ?? $s->id, 'name' => $s['name'] ?? $s->name])->toArray()"
                                            :selected="[$dbSubjectId]"
                                            :multiple="false"
                                            placeholder="Select Subject"
                                            size="sm"
                                        />
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 mb-1">Topic</label>
                                        <livewire:common.searchable-multi-select
                                            name="dbTopicId"
                                            :items="collect($topics)->map(fn($t) => ['id' => $t['id'] ?? $t->id, 'name' => $t['name'] ?? $t->name])->toArray()"
                                            :selected="[$dbTopicId]"
                                            :multiple="false"
                                            placeholder="Select Topic (Optional)"
                                            size="sm"
                                        />
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 mb-1">Subtopic</label>
                                        <livewire:common.searchable-multi-select
                                            name="dbSubtopicId"
                                            :items="collect($subtopics)->map(fn($st) => ['id' => $st['id'] ?? $st->id, 'name' => $st['name'] ?? $st->name])->toArray()"
                                            :selected="[$dbSubtopicId]"
                                            :multiple="false"
                                            placeholder="Select Subtopic (Optional)"
                                            size="sm"
                                        />
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 mb-1">Question Type</label>
                                        <livewire:common.searchable-multi-select
                                            name="dbQuestionType"
                                            :items="[
                                                ['id' => 'all', 'name' => 'All Types'],
                                                ['id' => 'multiple_choice', 'name' => 'Multiple Choice'],
                                                ['id' => 'true_false', 'name' => 'True/False'],
                                                ['id' => 'essay', 'name' => 'Essay / Short Answer']
                                            ]"
                                            :selected="[$dbQuestionType]"
                                            :multiple="false"
                                            :clearable="false"
                                            size="sm"
                                        />
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 mb-1">Selection Mode</label>
                                        <livewire:common.searchable-multi-select
                                            name="dbSelectionMode"
                                            :items="[
                                                ['id' => 'manual_select', 'name' => 'Select Questions Manually'],
                                                ['id' => 'random', 'name' => 'Randomly Generate']
                                            ]"
                                            :selected="[$dbSelectionMode]"
                                            :multiple="false"
                                            :clearable="false"
                                            size="sm"
                                        />
                                    </div>
                                </div>

                                @if($dbSelectionMode === 'random')
                                    <div class="flex items-center space-x-4 bg-indigo-50 dark:bg-indigo-900/20 p-4 rounded-md border border-indigo-100 dark:border-indigo-800">
                                        <div class="flex-1">
                                            <label class="block text-xs font-medium text-indigo-700 dark:text-indigo-300 uppercase tracking-wider">Number of Questions</label>
                                            <input type="number" wire:model.live="dbQuestionCount" min="1" max="50" class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white text-sm">
                                        </div>
                                        <div class="pt-5">
                                            <button wire:click="searchQuestions" class="bg-indigo-600 text-white px-4 py-2 rounded-md text-sm hover:bg-indigo-700 transition-colors">Refresh Random Set</button>
                                        </div>
                                    </div>
                                @else
                                    <div class="flex space-x-2">
                                        <input type="text" wire:model="searchQuery" wire:keydown.enter="searchQuestions" placeholder="Search keywords..." class="flex-1 rounded-md border-gray-300 dark:bg-gray-700 dark:text-white text-sm">
                                        <button wire:click="searchQuestions" class="bg-indigo-600 text-white px-4 py-2 rounded-md text-sm">Search</button>
                                    </div>
                                @endif

                                <div class="space-y-2 max-h-60 overflow-y-auto border dark:border-gray-700 rounded-md p-2">
                                    @if($dbSubjectId)
                                        <div class="flex justify-between items-center mb-2 px-1">
                                            <span class="text-xs font-bold text-gray-500 uppercase">{{ count($dbQuestions) }} Questions found</span>
                                            @if($dbSelectionMode === 'random' && count($dbQuestions) > 0)
                                                <button wire:click="addAllQuestions" class="text-xs bg-green-600 text-white px-2 py-1 rounded hover:bg-green-700">Add All to Section</button>
                                            @endif
                                        </div>
                                        @foreach($dbQuestions as $q)
                                            @php
                                                $isAdded = collect($sections[$activeSectionIndex]['questions'])->contains(function ($existingQ) use ($q) {
                                                    if (isset($existingQ['id'], $q['id']) && $existingQ['id'] == $q['id'] && $existingQ['type'] == $q['type']) {
                                                        return true;
                                                    }
                                                    return trim($existingQ['text']) === trim($q['text']);
                                                });
                                            @endphp
                                            <div class="flex justify-between items-center p-3 border rounded-md dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 bg-white dark:bg-gray-800 {{ $isAdded ? 'opacity-50' : '' }}">
                                                <div class="flex-1 pr-4">
                                                    <span class="text-[10px] font-bold uppercase text-gray-400 mr-2">{{ str_replace('_', ' ', $q['type']) }}</span>
                                                    <span class="dark:text-white text-sm">{{ $q['text'] }}</span>
                                                </div>
                                                @if($isAdded)
                                                    <span class="text-green-600 font-bold text-sm flex items-center">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                                        Added
                                                    </span>
                                                @else
                                                    <button wire:click="addQuestionToSection({{ json_encode($q) }})" class="text-indigo-600 font-bold text-sm hover:text-indigo-800">+ Add</button>
                                                @endif
                                            </div>
                                        @endforeach
                                    @else
                                        <p class="text-center text-gray-500 py-8 text-sm italic">Select a Subject to load questions.</p>
                                    @endif

                                    @if($dbSubjectId && empty($dbQuestions))
                                        <p class="text-center text-gray-500 py-4 text-sm">No questions found matching your criteria.</p>
                                    @endif
                                </div>
                            </div>

                        @elseif($sourcingMode === 'ai')
                            <div class="space-y-6">
                                @if(session()->has('error'))
                                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                                        <span class="block sm:inline">{{ session('error') }}</span>
                                    </div>
                                @endif

                                @if(session()->has('info'))
                                    <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative" role="alert">
                                        <span class="block sm:inline">{{ session('info') }}</span>
                                    </div>
                                @endif
                                <div class="flex space-x-4">
                                    <button wire:click="$set('aiMode', 'prompt')" class="flex-1 py-3 px-4 rounded-lg border-2 {{ $aiMode === 'prompt' ? 'border-indigo-600 bg-indigo-50 dark:bg-indigo-900/20' : 'border-gray-200 dark:border-gray-700' }} text-center transition-all">
                                        <div class="font-bold text-gray-900 dark:text-white">Describe Topic</div>
                                        <div class="text-xs text-gray-500">Type what you need</div>
                                    </button>
                                    <button wire:click="$set('aiMode', 'document')" class="flex-1 py-3 px-4 rounded-lg border-2 {{ $aiMode === 'document' ? 'border-indigo-600 bg-indigo-50 dark:bg-indigo-900/20' : 'border-gray-200 dark:border-gray-700' }} text-center transition-all">
                                        <div class="font-bold text-gray-900 dark:text-white">Upload Document</div>
                                        <div class="text-xs text-gray-500">PDF, DOCX, TXT</div>
                                    </button>
                                </div>

                                @if($aiMode === 'prompt')
                                    <div class="space-y-4">
                                        <div class="space-y-2">
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                                            <textarea wire:model="aiPrompt" rows="4" placeholder="e.g., Photosynthesis in plants, including light and dark reactions..." class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white"></textarea>
                                        </div>
                                    </div>
                                @else
                                    <div class="space-y-4">
                                        <div class="space-y-2">
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Upload Document</label>
                                            <input type="file" wire:model="aiDocument" class="mt-1 block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                            <div wire:loading wire:target="aiDocument" class="text-xs text-indigo-600">Uploading...</div>
                                        </div>
                                    </div>
                                @endif

                                <div class="bg-purple-50 dark:bg-purple-900/20 p-4 rounded-md border border-purple-100 dark:border-purple-800">
                                    <label class="block text-xs font-medium text-purple-700 dark:text-purple-300 uppercase tracking-wider mb-3">Questions to Generate</label>
                                    <div class="grid grid-cols-3 gap-4">
                                        <div>
                                            <label class="block text-[10px] text-gray-500 uppercase font-bold mb-1">MCQ (A-E)</label>
                                            <input type="number" wire:model.live="aiMcqCount" min="0" max="20" class="block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white text-sm">
                                        </div>
                                        <div>
                                            <label class="block text-[10px] text-gray-500 uppercase font-bold mb-1">True/False</label>
                                            <input type="number" wire:model.live="aiTfCount" min="0" max="20" class="block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white text-sm">
                                        </div>
                                        <div>
                                            <label class="block text-[10px] text-gray-500 uppercase font-bold mb-1">Essay</label>
                                            <input type="number" wire:model.live="aiEssayCount" min="0" max="20" class="block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white text-sm">
                                        </div>
                                    </div>
                                </div>

                                <button
                                    wire:click="generateWithAi"
                                    wire:loading.attr="disabled"
                                    class="w-full bg-purple-600 text-white py-3 rounded-md hover:bg-purple-700 disabled:opacity-50 flex items-center justify-center font-bold"
                                >
                                    @php
                                        $totalAi = (int)$aiMcqCount + (int)$aiTfCount + (int)$aiEssayCount;
                                    @endphp
                                    <span wire:loading.remove wire:target="generateWithAi">Generate {{ $totalAi }} Questions</span>
                                    <span wire:loading wire:target="generateWithAi" class="flex items-center">
                                        <svg class="animate-spin h-5 w-5 mr-3 border-2 border-white border-t-transparent rounded-full" viewBox="0 0 24 24"></svg>
                                        Generating...
                                    </span>
                                </button>
                            </div>
                        @endif
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t dark:border-gray-700">
                        <button wire:click="$set('showQuestionModal', false)" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600">Close</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
