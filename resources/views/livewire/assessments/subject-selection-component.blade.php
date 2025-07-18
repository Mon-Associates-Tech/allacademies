<div class="max-w-4xl mx-auto p-6">
    <div class="bg-white rounded-lg shadow-lg p-8">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Subject Selection Test</h1>

        <div class="space-y-6">
            <!-- Subject Selection -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Select Subject *
                </label>
                <select
                    wire:model.live="selectedSubject"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                >
                    <option value="">Choose a subject...</option>
                    @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}">
                            {{ $subject->name }}
                            @if($subject->academicLevel)
                                ({{ $subject->academicLevel->name }})
                            @endif
                        </option>
                    @endforeach
                </select>

                @if(count($subjects) === 0)
                    <p class="text-sm text-red-600 mt-1">No subjects available for your academic level.</p>
                @endif
            </div>

            <!-- Topic Selection -->
            @if($selectedSubject && count($topics) > 0)
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Select Topic (Optional)
                    </label>
                    <select
                        wire:model.live="selectedTopic"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    >
                        <option value="">All topics</option>
                        @foreach($topics as $topic)
                            <option value="{{ $topic->id }}">{{ $topic->name }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            <!-- Subtopic Selection -->
            @if($selectedTopic && count($subtopics) > 0)
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Select Subtopic (Optional)
                    </label>
                    <select
                        wire:model.live="selectedSubtopic"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    >
                        <option value="">All subtopics</option>
                        @foreach($subtopics as $subtopic)
                            <option value="{{ $subtopic->id }}">{{ $subtopic->name }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            <!-- Selection Summary -->
            @if($selectedSubject)
                <div class="bg-gray-50 p-4 rounded-md">
                    <h3 class="text-lg font-medium text-gray-900 mb-3">Current Selection</h3>

                    @php
                        $hierarchy = $this->getSelectionHierarchy();
                    @endphp

                    @if(!empty($hierarchy))
                        <div class="space-y-2">
                            @if(isset($hierarchy['subject']))
                                <div class="flex items-center">
                                    <span class="text-sm font-medium text-gray-600 w-20">Subject:</span>
                                    <span class="text-sm text-gray-900">{{ $hierarchy['subject']['name'] }}</span>
                                    @if(isset($hierarchy['subject']['academic_level']))
                                        <span class="text-xs text-gray-500 ml-2">({{ $hierarchy['subject']['academic_level'] }})</span>
                                    @endif
                                </div>
                            @endif

                            @if(isset($hierarchy['topic']))
                                <div class="flex items-center">
                                    <span class="text-sm font-medium text-gray-600 w-20">Topic:</span>
                                    <span class="text-sm text-gray-900">{{ $hierarchy['topic']['name'] }}</span>
                                </div>
                            @endif

                            @if(isset($hierarchy['subtopic']))
                                <div class="flex items-center">
                                    <span class="text-sm font-medium text-gray-600 w-20">Subtopic:</span>
                                    <span class="text-sm text-gray-900">{{ $hierarchy['subtopic']['name'] }}</span>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            @endif

            <!-- Question Counts -->
            @if(!empty($questionCounts))
                <div class="bg-blue-50 p-4 rounded-md">
                    <h3 class="text-lg font-medium text-gray-900 mb-3">Available Questions</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-blue-600">{{ $questionCounts['multiple_choice'] ?? 0 }}</div>
                            <div class="text-sm text-gray-600">Multiple Choice</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-green-600">{{ $questionCounts['true_false'] ?? 0 }}</div>
                            <div class="text-sm text-gray-600">True/False</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-purple-600">{{ $questionCounts['essay'] ?? 0 }}</div>
                            <div class="text-sm text-gray-600">Essay</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-gray-900">{{ $questionCounts['total'] ?? 0 }}</div>
                            <div class="text-sm text-gray-600">Total</div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Action Buttons -->
            @if($selectedSubject)
                <div class="flex space-x-4">
                    <button
                        wire:click="loadSubjects"
                        class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500"
                    >
                        Refresh
                    </button>

                    <button
                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        onclick="alert('Selection ready for use!')"
                    >
                        Use Selection
                    </button>
                </div>
            @endif

            <!-- Debug Information -->
            <div class="mt-8 p-4 bg-gray-100 rounded-md">
                <h4 class="text-sm font-medium text-gray-700 mb-2">Debug Information</h4>
                <div class="text-xs text-gray-600 space-y-1">
                    <div>Selected Subject ID: {{ $selectedSubject ?? 'None' }}</div>
                    <div>Selected Topic ID: {{ $selectedTopic ?? 'None' }}</div>
                    <div>Selected Subtopic ID: {{ $selectedSubtopic ?? 'None' }}</div>
                    <div>Available Subjects: {{ count($subjects) }}</div>
                    <div>Available Topics: {{ count($topics) }}</div>
                    <div>Available Subtopics: {{ count($subtopics) }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
