@php
    // Determine if we're creating or editing
    $isEditing = !$new;

    // Get subtopics collection
    if($new){
        $subtopics = $question->subtopics;
    } else {
        $subtopics = $question->academicTopic->subtopics;
    }

    // Get current subtopic value
    $currentSubtopicId = null;
    $currentSubtopicName = '';

    if ($isEditing && $question->subtopic) {
        $currentSubtopicId = $question->subtopic->id;
        $currentSubtopicName = $question->subtopic->name;
    }

    // Check if we have a validation error with old input
    $oldSubtopic = old('subtopic');

    // Determine the selected value and show state
    $selectedValue = '';
    $showCustomInitially = false;
    $customSubtopicName = '';

    if ($oldSubtopic) {
        // We have old input from validation error
        if (is_numeric($oldSubtopic)) {
            // Old input is a subtopic ID
            $selectedValue = $oldSubtopic;
        } else {
            // Old input is a string (new subtopic name)
            $selectedValue = 'new';
            $showCustomInitially = true;
            $customSubtopicName = $oldSubtopic;
        }
    } elseif ($isEditing && $currentSubtopicId) {
        // We're editing and have a current subtopic
        $selectedValue = (string)$currentSubtopicId;
    }
    // If none of the above, selectedValue remains empty (no subtopic selected)
@endphp

@if($subtopics->count() > 0)
    <div x-data="subtopicSelector()" x-cloak>
        <label for="subtopic_select" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
            Subtopic <span class="text-gray-400">(Optional)</span>
        </label>

        <select
            id="subtopic_select"
            x-model="selectedValue"
            @change="handleSelectionChange()"
            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
        >
            <option value="">Select a subtopic (optional)</option>
            @foreach($subtopics as $subtopic)
                <option value="{{ $subtopic->id }}">{{ $subtopic->name }}</option>
            @endforeach
            <option value="new">+ Create New Subtopic</option>
        </select>

        <!-- Single subtopic input that changes behavior based on selection -->
        <input
            type="hidden"
            name="subtopic"
            x-model="subtopicValue"
        />

        <!-- Custom input for new subtopic -->
        <div x-show="showCustomInput" x-transition class="mt-2">
            <label for="new_subtopic_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                New Subtopic Name
            </label>
            <input
                type="text"
                id="new_subtopic_name"
                x-model="customSubtopicName"
                @input="updateSubtopicValue()"
                placeholder="Enter new subtopic name"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
            />
        </div>

        @error('subtopic')
        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <script>
        function subtopicSelector() {
            return {
                selectedValue: '{{ $selectedValue }}',
                showCustomInput: {{ $showCustomInitially ? 'true' : 'false' }},
                customSubtopicName: '{{ addslashes($customSubtopicName) }}',
                subtopicValue: '',

                init() {

                    this.updateSubtopicValue();

                    // Force the select element to show the correct selection after Alpine initializes
                    this.$nextTick(() => {
                        if (this.selectedValue) {
                            const selectElement = document.getElementById('subtopic_select');
                            if (selectElement) {
                                selectElement.value = this.selectedValue;
                            }
                        }
                    });
                },

                handleSelectionChange() {
                    this.showCustomInput = (this.selectedValue === 'new');
                    if (this.selectedValue === 'new') {
                        this.customSubtopicName = '';
                        this.subtopicValue = '';
                    } else {
                        this.updateSubtopicValue();
                    }
                },

                updateSubtopicValue() {
                    if (this.showCustomInput) {
                        this.subtopicValue = this.customSubtopicName;
                    } else {
                        this.subtopicValue = this.selectedValue;
                    }
                }
            }
        }
    </script>
@else
    {{-- Fallback: Simple text input if no subtopics exist --}}
    <div>
        <label for="subtopic_input" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
            Sub Topic <span class="text-gray-400">(Optional)</span>
        </label>
        <input
            type="text"
            id="subtopic_input"
            name="subtopic"
            value="{{ old('subtopic', $currentSubtopicName) }}"
            placeholder="Enter subtopic name"
            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
        />
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">No existing subtopics found. Enter a new one above.</p>
        @error('subtopic')
        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>
@endif

<style>
    [x-cloak] { display: none !important; }
</style>
