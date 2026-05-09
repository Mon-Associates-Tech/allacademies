<div class="p-6">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Question Availability Checker</h2>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Check if sufficient questions are available before generating examinations</p>
    </div>

    @if (session()->has('message'))
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Left Column: Form -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Check Availability</h3>

            <form wire:submit.prevent="checkAvailability" class="space-y-4">
                <!-- Academic Group -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Academic Group
                    </label>
                    <select wire:model.live="selectedGroup" 
                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Select Group</option>
                        @foreach($academicGroups as $group)
                            <option value="{{ $group->id }}">{{ $group->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Academic Level -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Academic Level
                    </label>
                    <select wire:model.live="selectedLevel" 
                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            {{ !$selectedGroup ? 'disabled' : '' }}>
                        <option value="">Select Level</option>
                        @foreach($academicLevels as $level)
                            <option value="{{ $level->id }}">{{ $level->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Academic Subject -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Academic Subject <span class="text-red-500">*</span>
                    </label>
                    <select wire:model.live="selectedSubject" 
                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            {{ !$selectedLevel ? 'disabled' : '' }}>
                        <option value="">Select Subject</option>
                        @foreach($academicSubjects as $subject)
                            <option value="{{ $subject->id }}">{{ $subject->name }} ({{ $subject->code }})</option>
                        @endforeach
                    </select>
                    @error('selectedSubject') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Topics (Multiple Select) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Topics (Optional - Leave empty for all topics)
                    </label>
                    <select wire:model="selectedTopics" 
                            multiple
                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            size="5"
                            {{ !$selectedSubject ? 'disabled' : '' }}>
                        @foreach($academicTopics as $topic)
                            <option value="{{ $topic->id }}">{{ $topic->name }}</option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Hold Ctrl/Cmd to select multiple topics</p>
                </div>

                <!-- Question Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Question Type <span class="text-red-500">*</span>
                    </label>
                    <select wire:model="questionType" 
                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="multiple_choice_questions">Multiple Choice Questions</option>
                        <option value="essay_questions">Essay Questions</option>
                        <option value="true_or_false_questions">True or False Questions</option>
                    </select>
                    @error('questionType') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Required Count -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Required Question Count <span class="text-red-500">*</span>
                    </label>
                    <input type="number" 
                           wire:model="requiredCount" 
                           min="1"
                           class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('requiredCount') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Submit Button -->
                <button type="submit" 
                        class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-md transition duration-150 ease-in-out disabled:opacity-50"
                        wire:loading.attr="disabled">
                    <span wire:loading.remove>Check Availability</span>
                    <span wire:loading>Checking...</span>
                </button>
            </form>
        </div>

        <!-- Right Column: Results -->
        <div class="space-y-6">
            <!-- ID Map Section - Moved to top -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Academic ID Map</h3>
                    <button wire:click="generateIdMap" 
                            class="bg-gray-600 hover:bg-gray-700 text-white text-sm font-semibold py-1 px-3 rounded transition duration-150 ease-in-out"
                            wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="generateIdMap">Generate Map</span>
                        <span wire:loading wire:target="generateIdMap">Generating...</span>
                    </button>
                </div>

                @if($idMap)
                    <div class="space-y-4">
                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            <p class="mb-2">✓ ID Map available with {{ count($idMap) }} academic groups</p>
                            <p class="text-xs mb-3">Location: storage/app/academic_id_map.json</p>
                            <a href="{{ route('admin.academic.id-map.download') }}" 
                               class="inline-block px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded transition duration-150 ease-in-out">
                                📥 Download ID Map
                            </a>
                        </div>

                        <!-- Display ID Map -->
                        <div class="mt-4 max-h-96 overflow-y-auto border border-gray-200 dark:border-gray-700 rounded-lg" x-data="{ collapsed: {} }">
                            @foreach($idMap as $groupIndex => $group)
                                <div class="border-b border-gray-200 dark:border-gray-700 last:border-b-0">
                                    <!-- Group Level -->
                                    <div class="p-3 bg-blue-50 dark:bg-blue-900/20 font-semibold text-blue-900 dark:text-blue-200 cursor-pointer hover:bg-blue-100 dark:hover:bg-blue-900/30 transition"
                                         @click="collapsed['group_{{ $groupIndex }}'] = !collapsed['group_{{ $groupIndex }}']"
                                         x-init="collapsed['group_{{ $groupIndex }}'] = false">
                                        <span class="inline-block w-4" x-text="collapsed['group_{{ $groupIndex }}'] ? '▶' : '▼'"></span>
                                        <span class="text-xs bg-blue-200 dark:bg-blue-800 px-2 py-1 rounded">ID: {{ $group['id'] }}</span>
                                        <span class="ml-2">{{ $group['name'] }}</span>
                                        @if(isset($group['tag']))
                                            <span class="ml-2 text-xs text-blue-600 dark:text-blue-400">({{ $group['tag'] }})</span>
                                        @endif
                                    </div>

                                    <div x-show="!collapsed['group_{{ $groupIndex }}']" x-collapse>
                                        @foreach($group['levels'] ?? [] as $levelIndex => $level)
                                            <!-- Level -->
                                            <div class="pl-4 p-2 bg-green-50 dark:bg-green-900/20 text-green-900 dark:text-green-200 cursor-pointer hover:bg-green-100 dark:hover:bg-green-900/30 transition"
                                                 @click="collapsed['level_{{ $groupIndex }}_{{ $levelIndex }}'] = !collapsed['level_{{ $groupIndex }}_{{ $levelIndex }}']"
                                                 x-init="collapsed['level_{{ $groupIndex }}_{{ $levelIndex }}'] = false">
                                                <span class="inline-block w-4" x-text="collapsed['level_{{ $groupIndex }}_{{ $levelIndex }}'] ? '▶' : '▼'"></span>
                                                <span class="text-xs bg-green-200 dark:bg-green-800 px-2 py-1 rounded">ID: {{ $level['id'] }}</span>
                                                <span class="ml-2 font-medium">{{ $level['name'] }}</span>
                                                @if(isset($level['label']))
                                                    <span class="ml-2 text-xs text-green-600 dark:text-green-400">({{ $level['label'] }})</span>
                                                @endif
                                            </div>

                                            <div x-show="!collapsed['level_{{ $groupIndex }}_{{ $levelIndex }}']" x-collapse>
                                                @foreach($level['subjects'] ?? [] as $subjectIndex => $subject)
                                                    <!-- Subject -->
                                                    <div class="pl-8 p-2 bg-purple-50 dark:bg-purple-900/20 text-purple-900 dark:text-purple-200 cursor-pointer hover:bg-purple-100 dark:hover:bg-purple-900/30 transition"
                                                         @click="collapsed['subject_{{ $groupIndex }}_{{ $levelIndex }}_{{ $subjectIndex }}'] = !collapsed['subject_{{ $groupIndex }}_{{ $levelIndex }}_{{ $subjectIndex }}']"
                                                         x-init="collapsed['subject_{{ $groupIndex }}_{{ $levelIndex }}_{{ $subjectIndex }}'] = false">
                                                        <span class="inline-block w-4" x-text="collapsed['subject_{{ $groupIndex }}_{{ $levelIndex }}_{{ $subjectIndex }}'] ? '▶' : '▼'"></span>
                                                        <span class="text-xs bg-purple-200 dark:bg-purple-800 px-2 py-1 rounded">ID: {{ $subject['id'] }}</span>
                                                        <span class="ml-2">{{ $subject['name'] }}</span>
                                                        @if(isset($subject['code']))
                                                            <span class="ml-2 text-xs text-purple-600 dark:text-purple-400">({{ $subject['code'] }})</span>
                                                        @endif
                                                    </div>

                                                    <div x-show="!collapsed['subject_{{ $groupIndex }}_{{ $levelIndex }}_{{ $subjectIndex }}']" x-collapse>
                                                        @foreach($subject['topics'] ?? [] as $topic)
                                                            <!-- Topic -->
                                                            <div class="pl-12 p-2 bg-orange-50 dark:bg-orange-900/20 text-orange-900 dark:text-orange-200 text-sm">
                                                                <span class="text-xs bg-orange-200 dark:bg-orange-800 px-2 py-1 rounded">ID: {{ $topic['id'] }}</span>
                                                                <span class="ml-2">{{ $topic['name'] }}</span>
                                                                @if(isset($topic['questions']))
                                                                    <span class="ml-2 text-xs text-orange-600 dark:text-orange-400">
                                                                        ({{ $topic['questions']['total'] }} questions)
                                                                    </span>
                                                                @endif
                                                            </div>

                                                            @foreach($topic['subtopics'] ?? [] as $subtopic)
                                                                <!-- Subtopic -->
                                                                <div class="pl-16 p-2 bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm">
                                                                    <span class="text-xs bg-gray-200 dark:bg-gray-600 px-2 py-1 rounded">ID: {{ $subtopic['id'] }}</span>
                                                                    <span class="ml-2">{{ $subtopic['name'] }}</span>
                                                                    @if(isset($subtopic['questions']))
                                                                        <span class="ml-2 text-xs text-gray-500 dark:text-gray-400">
                                                                            ({{ $subtopic['questions']['total'] }} questions)
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                            @endforeach
                                                        @endforeach
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <p class="text-sm text-gray-500 dark:text-gray-400">No ID map generated yet. Click "Generate Map" to create one.</p>
                @endif
            </div>

            <!-- Results -->
            @if($result)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Results</h3>

                    <div class="space-y-4">
                        <!-- Subject Info -->
                        <div class="border-b border-gray-200 dark:border-gray-700 pb-3">
                            <p class="text-sm text-gray-600 dark:text-gray-400">Subject</p>
                            <p class="text-lg font-medium text-gray-900 dark:text-white">
                                {{ $result['subject']['name'] }} ({{ $result['subject']['code'] }})
                            </p>
                        </div>

                        <!-- Availability Status -->
                        <div class="p-4 rounded-lg {{ $result['sufficient'] ? 'bg-green-50 dark:bg-green-900/20' : 'bg-red-50 dark:bg-red-900/20' }}">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium {{ $result['sufficient'] ? 'text-green-800 dark:text-green-300' : 'text-red-800 dark:text-red-300' }}">
                                        {{ $result['sufficient'] ? '✓ Sufficient Questions' : '✗ Insufficient Questions' }}
                                    </p>
                                    <p class="text-2xl font-bold {{ $result['sufficient'] ? 'text-green-900 dark:text-green-200' : 'text-red-900 dark:text-red-200' }}">
                                        {{ $result['available_count'] }} / {{ $result['required_count'] }}
                                    </p>
                                </div>
                                @if(!$result['sufficient'])
                                    <div class="text-right">
                                        <p class="text-sm text-red-600 dark:text-red-400">Need</p>
                                        <p class="text-xl font-bold text-red-700 dark:text-red-300">
                                            {{ $result['required_count'] - $result['available_count'] }} more
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Breakdown by Topic -->
                        @if(!empty($result['breakdown']['by_topic']))
                            <div>
                                <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Questions by Topic</h4>
                                <div class="space-y-2">
                                    @foreach($result['breakdown']['by_topic'] as $topic)
                                        <div class="flex justify-between items-center p-2 bg-gray-50 dark:bg-gray-700 rounded">
                                            <span class="text-sm text-gray-700 dark:text-gray-300">{{ $topic['name'] }}</span>
                                            <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $topic['available'] }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Breakdown by Subtopic -->
                        @if(!empty($result['breakdown']['by_subtopic']))
                            <div>
                                <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Questions by Subtopic</h4>
                                <div class="space-y-2">
                                    @foreach($result['breakdown']['by_subtopic'] as $subtopic)
                                        <div class="flex justify-between items-center p-2 bg-gray-50 dark:bg-gray-700 rounded">
                                            <span class="text-sm text-gray-700 dark:text-gray-300">{{ $subtopic['name'] }}</span>
                                            <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $subtopic['available'] }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
