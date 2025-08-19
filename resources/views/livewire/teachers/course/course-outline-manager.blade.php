<div class="min-h-screen">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-xl shadow-lg p-8 mb-6">
            <h2 class="text-3xl font-extrabold text-gray-900 mb-2">Course Outline Manager</h2>
            <p class="text-gray-600 mb-6">Manage your academic course outlines with ease.</p>

            @if(session()->has('message'))
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg border border-green-200">
                    {{ session('message') }}
                </div>
            @endif

            <div class="bg-white p-6 rounded-xl shadow-md border border-gray-200">
                <h3 class="text-xl font-bold mb-4 text-gray-800">Create New Course Outline</h3>
                <form wire:submit.prevent="createCourseOutline">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div>
                            <label for="subject" class="block text-sm font-semibold text-gray-700">Subject</label>
                            <select id="subject" wire:model="selectedSubject" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition duration-150 ease-in-out">
                                <option value="">Select Subject</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject['id'] }}">{{ $subject['name'] }}</option>
                                @endforeach
                            </select>
                            @error('selectedSubject') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="level" class="block text-sm font-semibold text-gray-700">Academic Level</label>
                            <select id="level" wire:model="selectedLevel" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition duration-150 ease-in-out">
                                <option value="">Select Level</option>
                                @foreach($levels as $level)
                                    <option value="{{ $level['id'] }}">{{ $level['name'] }}</option>
                                @endforeach
                            </select>
                            @error('selectedLevel') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="period" class="block text-sm font-semibold text-gray-700">Academic Period</label>
                            <select id="period" wire:model="selectedPeriod" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition duration-150 ease-in-out">
                                <option value="">Select Period</option>
                                @foreach($periods as $period)
                                    <option value="{{ $period['id'] }}">{{ $period['name'] }} ({{ $period['academic_year'] }})</option>
                                @endforeach
                            </select>
                            @error('selectedPeriod') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="mt-6">
                        <label for="title" class="block text-sm font-semibold text-gray-700">Title</label>
                        <input id="title" type="text" wire:model="title" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition duration-150 ease-in-out" placeholder="e.g., Fall 2025 Introduction to Biology">
                        @error('title') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="mt-6">
                        <label for="description" class="block text-sm font-semibold text-gray-700">Description</label>
                        <textarea id="description" wire:model="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition duration-150 ease-in-out" placeholder="Provide a brief description of the course outline."></textarea>
                        @error('description') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="mt-6">
                        <x-button.primary type="submit" wire:loading.attr="disabled" class="w-full inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-3 px-6 text-base font-medium text-white shadow-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:bg-indigo-400 disabled:cursor-not-allowed transform hover:scale-105 transition duration-200">
                            <span wire:loading.remove>Create Outline</span>
                            <span wire:loading>
                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Creating...
                            </span>
                        </x-button.primary>
                    </div>
                </form>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-8">
            <h3 class="text-xl font-bold mb-4 text-gray-800">Your Course Outlines</h3>
            <p class="text-gray-600 mb-6">Click "Add Item" to start building your course content.</p>

            <div class="space-y-6">
                @foreach($outlines as $outline)
                    <div class="bg-gray-50 shadow rounded-xl p-6 border border-gray-200">
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4">
                            <div>
                                <h4 class="text-lg font-bold text-gray-900">{{ $outline->title }}</h4>
                                <p class="text-sm text-gray-600 mt-1">
                                    <span class="font-medium">{{ $outline->academicSubject->name }}</span> |
                                    <span class="font-medium">{{ $outline->academicLevel->name }}</span> |
                                    <span class="font-medium">{{ $outline->academicPeriod->name }}</span>
                                </p>
                            </div>
                            <x-button.primary wire:click="openAddItemModal({{$outline->id}})"
                                    class="mt-4 sm:mt-0 bg-indigo-500 text-white px-5 py-2 rounded-md hover:bg-indigo-600 transition duration-150 ease-in-out">
                                Add Item
                            </x-button.primary>
                        </div>

                        <div class="space-y-4 border-l-2 border-gray-200 pl-4 ml-2 mt-4">
                            @foreach($outline->outlineItems->sortBy('order') as $item)
                                <div class="p-4 bg-white rounded-lg shadow-sm border border-gray-100">
                                    <div class="text-sm text-gray-500 mb-1">
                                        Planned for: <span class="font-medium">{{ $item->planned_date->format('M d, Y') }}</span>
                                    </div>
                                    <h5 class="text-base font-semibold text-gray-800">{{ $item->topic->name }}</h5>
                                    @if($item->subtopic)
                                        <p class="text-sm text-gray-600 mt-1">Subtopic: {{ $item->subtopic->name }}</p>
                                    @endif

                                    @if($item->learning_objectives || $item->teaching_strategy || $item->resources_needed || $item->assessment_method)
                                        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600">
                                            @if($item->learning_objectives)
                                                <div>
                                                    <span class="font-semibold text-gray-700">Learning Objectives:</span>
                                                    <p>{{ $item->learning_objectives }}</p>
                                                </div>
                                            @endif

                                            @if($item->teaching_strategy)
                                                <div>
                                                    <span class="font-semibold text-gray-700">Teaching Strategy:</span>
                                                    <p>{{ $item->teaching_strategy }}</p>
                                                </div>
                                            @endif

                                            @if($item->resources_needed)
                                                <div>
                                                    <span class="font-semibold text-gray-700">Resources Needed:</span>
                                                    <p>{{ $item->resources_needed }}</p>
                                                </div>
                                            @endif

                                            @if($item->assessment_method)
                                                <div>
                                                    <span class="font-semibold text-gray-700">Assessment Method:</span>
                                                    <p>{{ $item->assessment_method }}</p>
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach

                {{ $outlines->links() }}
            </div>
        </div>
    </div>

    @if($showItemForm)
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 flex items-center justify-center p-4 z-50 transition-opacity duration-300"
             x-data="{ open: @entangle('showItemForm') }"
             x-on:keydown.escape.window="open = false"
             x-show="open"
             style="display: none;"
        >
            <div class="bg-white rounded-lg p-8 max-w-2xl w-full mx-auto transform transition-all duration-300" @click.away="open = false">
                <h3 class="text-2xl font-bold mb-4">Add Item to "{{ $selectedOutline->title ?? '' }}"</h3>
                <p class="text-gray-600 mb-6">Fill in the details for your new course item.</p>

                <form wire:submit.prevent="createOutlineItem">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="col-span-2">
                            <label for="plannedDate" class="block text-sm font-semibold text-gray-700">Planned Date</label>
                            <input id="plannedDate" type="date" wire:model="plannedDate" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition duration-150 ease-in-out">
                            @error('plannedDate') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-span-2">
                            <label for="topic" class="block text-sm font-semibold text-gray-700">Topic</label>
                            <select id="topic" wire:model.live="selectedTopic" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition duration-150 ease-in-out">
                                <option value="">Select Topic</option>
                                @foreach($topics as $topic)
                                    <option value="{{ $topic['id'] }}">{{ $topic['name'] }}</option>
                                @endforeach
                                <option value="new_topic_option" class="font-bold">+ Create New Topic</option>
                            </select>
                            <span wire:loading wire:target="selectedTopic" class="text-sm text-gray-500 mt-1 block">Loading topics...</span>
                        </div>

                        @if($selectedTopic && $selectedTopic !== 'new_topic_option')
                            <div class="col-span-2">
                                <label for="subtopic" class="block text-sm font-semibold text-gray-700">Subtopic (Optional)</label>
                                <select id="subtopic" wire:model="selectedSubtopic" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition duration-150 ease-in-out">
                                    <option value="">Select Subtopic</option>
                                    @foreach($subtopics as $subtopic)
                                        <option value="{{ $subtopic['id'] }}">{{ $subtopic['name'] }}</option>
                                    @endforeach
                                    <option value="new_subtopic_option" class="font-bold">+ Create New Subtopic</option>
                                </select>
                                <span wire:loading wire:target="selectedSubtopic" class="text-sm text-gray-500 mt-1 block">Loading subtopics...</span>
                            </div>
                        @endif

                        <div class="col-span-2">
                            <label for="learningObjectives" class="block text-sm font-semibold text-gray-700">Learning Objectives</label>
                            <textarea id="learningObjectives" wire:model="learningObjectives" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition duration-150 ease-in-out" placeholder="e.g., Students will be able to identify the parts of a cell."></textarea>
                        </div>

                        <div>
                            <label for="teachingStrategy" class="block text-sm font-semibold text-gray-700">Teaching Strategy</label>
                            <input id="teachingStrategy" type="text" wire:model="teachingStrategy" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition duration-150 ease-in-out" placeholder="e.g., Lecture, group discussion, lab work">
                        </div>

                        <div>
                            <label for="assessmentMethod" class="block text-sm font-semibold text-gray-700">Assessment Method</label>
                            <input id="assessmentMethod" type="text" wire:model="assessmentMethod" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition duration-150 ease-in-out" placeholder="e.g., Quiz, participation, lab report">
                        </div>

                        <div class="col-span-2">
                            <label for="resourcesNeeded" class="block text-sm font-semibold text-gray-700">Resources Needed</label>
                            <textarea id="resourcesNeeded" wire:model="resourcesNeeded" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition duration-150 ease-in-out" placeholder="e.g., Projector, whiteboard, lab equipment"></textarea>
                        </div>

                        <div class="col-span-2">
                            <label for="notes" class="block text-sm font-semibold text-gray-700">Notes</label>
                            <textarea id="notes" wire:model="notes" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition duration-150 ease-in-out" placeholder="Add any additional notes for this item."></textarea>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end space-x-4">
                        <button type="button" wire:click="closeAddItemModal" class="bg-gray-200 px-6 py-3 rounded-md text-gray-700 hover:bg-gray-300 transition duration-150 ease-in-out">
                            Cancel
                        </button>
                        <button type="submit" wire:loading.attr="disabled" class="bg-blue-600 text-white px-6 py-3 rounded-md hover:bg-blue-700 disabled:bg-blue-300 disabled:cursor-not-allowed transition duration-150 ease-in-out">
                            <span wire:loading.remove>Add Item</span>
                            <span wire:loading>
                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Adding...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    @if($showNewTopicForm)
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 flex items-center justify-center p-4 z-50 transition-opacity duration-300"
             x-data="{ open: @entangle('showNewTopicForm') }"
             x-on:keydown.escape.window="open = false"
             x-show="open"
             style="display: none;"
        >
            <div class="bg-white rounded-lg p-8 max-w-lg w-full mx-auto transform transition-all duration-300" @click.away="open = false">
                <h3 class="text-2xl font-bold mb-4">Create New Topic</h3>
                <p class="text-gray-600 mb-6">Enter the details for the new topic.</p>
                <form wire:submit.prevent="createNewTopic">
                    <div class="space-y-6">
                        <div>
                            <label for="newTopicTitle" class="block text-sm font-semibold text-gray-700">Title</label>
                            <input id="newTopicTitle" type="text" wire:model="newTopicTitle" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition duration-150 ease-in-out" placeholder="e.g., The Cell">
                            @error('newTopicTitle') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="newTopicDescription" class="block text-sm font-semibold text-gray-700">Description</label>
                            <textarea id="newTopicDescription" wire:model="newTopicDescription" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition duration-150 ease-in-out" placeholder="Briefly describe this new topic."></textarea>
                            @error('newTopicDescription') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end space-x-4">
                        <button type="button" wire:click="$set('showNewTopicForm', false)" class="bg-gray-200 px-6 py-3 rounded-md text-gray-700 hover:bg-gray-300 transition duration-150 ease-in-out">
                            Cancel
                        </button>
                        <button type="submit" wire:loading.attr="disabled" class="bg-blue-600 text-white px-6 py-3 rounded-md hover:bg-blue-700 disabled:bg-blue-300 disabled:cursor-not-allowed transition duration-150 ease-in-out">
                            <span wire:loading.remove>Create Topic</span>
                            <span wire:loading>
                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Creating...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    @if($showNewSubtopicForm)
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 flex items-center justify-center p-4 z-50 transition-opacity duration-300"
             x-data="{ open: @entangle('showNewSubtopicForm') }"
             x-on:keydown.escape.window="open = false"
             x-show="open"
             style="display: none;"
        >
            <div class="bg-white rounded-lg p-8 max-w-lg w-full mx-auto transform transition-all duration-300" @click.away="open = false">
                <h3 class="text-2xl font-bold mb-4">Create New Subtopic</h3>
                <p class="text-gray-600 mb-6">Enter the details for the new subtopic for "{{ $topics->where('id', $selectedTopic)->first()['name'] ?? 'Selected Topic' }}".</p>
                <form wire:submit.prevent="createNewSubtopic">
                    <div class="space-y-6">
                        <div>
                            <label for="newSubtopicTitle" class="block text-sm font-semibold text-gray-700">Title</label>
                            <input id="newSubtopicTitle" type="text" wire:model="newSubtopicTitle" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition duration-150 ease-in-out" placeholder="e.g., Cellular Respiration">
                            @error('newSubtopicTitle') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="newSubtopicDescription" class="block text-sm font-semibold text-gray-700">Description</label>
                            <textarea id="newSubtopicDescription" wire:model="newSubtopicDescription" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition duration-150 ease-in-out" placeholder="Briefly describe this new subtopic."></textarea>
                            @error('newSubtopicDescription') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end space-x-4">
                        <button type="button" wire:click="$set('showNewSubtopicForm', false)" class="bg-gray-200 px-6 py-3 rounded-md text-gray-700 hover:bg-gray-300 transition duration-150 ease-in-out">
                            Cancel
                        </button>
                        <button type="submit" wire:loading.attr="disabled" class="bg-blue-600 text-white px-6 py-3 rounded-md hover:bg-blue-700 disabled:bg-blue-300 disabled:cursor-not-allowed transition duration-150 ease-in-out">
                            <span wire:loading.remove>Create Subtopic</span>
                            <span wire:loading>
                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Creating...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
