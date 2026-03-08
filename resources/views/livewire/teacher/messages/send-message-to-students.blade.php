<div class="max-w-4xl mx-auto">
    <div class="bg-white shadow-lg rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h1 class="text-2xl font-bold text-gray-900">Send Message to Students</h1>
            <p class="text-sm text-gray-600 mt-1">Send messages to your students based on different criteria</p>
        </div>

        <form wire:submit.prevent="sendMessage" class="p-6">
            <!-- Subject -->
            <div class="mb-6">
                <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">
                    Subject <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    id="subject"
                    wire:model="subject"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Enter message subject"
                >
                @error('subject') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Target Selection -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Target Students <span class="text-red-500">*</span>
                </label>

                <!-- Target Type Selection -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-4">
                    <label class="relative">
                        <input type="radio" wire:model.live="targetType" value="academic_group" class="sr-only peer">
                        <div class="p-3 border-2 border-gray-200 rounded-lg cursor-pointer peer-checked:border-blue-500 peer-checked:bg-blue-50">
                            <div class="text-sm font-medium text-gray-900">Academic Groups</div>
                        </div>
                    </label>

                    <label class="relative">
                        <input type="radio" wire:model.live="targetType" value="academic_level" class="sr-only peer">
                        <div class="p-3 border-2 border-gray-200 rounded-lg cursor-pointer peer-checked:border-blue-500 peer-checked:bg-blue-50">
                            <div class="text-sm font-medium text-gray-900">Academic Levels</div>
                        </div>
                    </label>

                    <label class="relative">
                        <input type="radio" wire:model.live="targetType" value="subject" class="sr-only peer">
                        <div class="p-3 border-2 border-gray-200 rounded-lg cursor-pointer peer-checked:border-blue-500 peer-checked:bg-blue-50">
                            <div class="text-sm font-medium text-gray-900">Subjects</div>
                        </div>
                    </label>

                    <label class="relative">
                        <input type="radio" wire:model.live="targetType" value="individual" class="sr-only peer">
                        <div class="p-3 border-2 border-gray-200 rounded-lg cursor-pointer peer-checked:border-blue-500 peer-checked:bg-blue-50">
                            <div class="text-sm font-medium text-gray-900">Individual Students</div>
                        </div>
                    </label>
                </div>

                <!-- Target Criteria Based on Selection -->
                @if($targetType === 'academic_group')
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="font-medium text-gray-900 mb-3">Select Academic Groups</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                            @foreach($academicGroups as $group)
                                <label class="flex items-center cursor-pointer">
                                    <input type="checkbox"
                                           wire:click="toggleAcademicGroup({{ $group->id }})"
                                           {{ in_array($group->id, $selectedAcademicGroups) ? 'checked' : '' }}
                                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-700">{{ $group->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if($targetType === 'academic_level')
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="font-medium text-gray-900 mb-3">Select Academic Levels</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                            @foreach($academicLevels as $level)
                                <label class="flex items-center cursor-pointer">
                                    <input type="checkbox"
                                           wire:click="toggleAcademicLevel({{ $level->id }})"
                                           {{ in_array($level->id, $selectedAcademicLevels) ? 'checked' : '' }}
                                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-700">{{ $level->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if($targetType === 'subject')
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="font-medium text-gray-900 mb-3">Select Subjects</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                            @foreach($subjects as $subject)
                                <label class="flex items-center cursor-pointer">
                                    <input type="checkbox"
                                           wire:click="toggleSubject({{ $subject->id }})"
                                           {{ in_array($subject->id, $selectedSubjects) ? 'checked' : '' }}
                                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-700">{{ $subject->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if($targetType === 'individual')
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="font-medium text-gray-900 mb-3">Select Individual Students</h3>
                        <div class="mb-3">
                            <input type="text"
                                   wire:model.live.debounce.300ms="userSearch"
                                   placeholder="Search students by name..."
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        @if(!empty($userSearch))
                            <div class="max-h-40 overflow-y-auto border border-gray-200 rounded-md mb-3">
                                @foreach($searchedStudents as $student)
                                    <div class="flex items-center justify-between p-2 hover:bg-gray-50">
                                        <div class="flex items-center">
                                            <div class="text-sm font-medium text-gray-900">{{ $student->name }}</div>
                                        </div>
                                        <button type="button"
                                                wire:click="toggleStudent({{ $student->id }})"
                                                class="text-blue-600 hover:text-blue-800 text-sm">
                                            {{ in_array($student->id, $selectedStudents) ? 'Remove' : 'Add' }}
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        @if(!empty($selectedStudents))
                            <div>
                                <h4 class="text-sm font-medium text-gray-700 mb-2">Selected Students:</h4>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($selectedStudents as $studentId)
                                        @php $student = \App\Models\User::find($studentId); @endphp
                                        @if($student)
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ $student->name }}
                                                <button type="button" wire:click="toggleStudent({{ $student->id }})" class="ml-1 text-blue-600 hover:text-blue-800">×</button>
                                            </span>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            <!-- Message Body -->
            <div class="mb-6">
                <label for="body" class="block text-sm font-medium text-gray-700 mb-2">
                    Message Content <span class="text-red-500">*</span>
                </label>
                <textarea
                    id="body"
                    wire:model="body"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    rows="6"
                    placeholder="Write your message here...">
                </textarea>
                @error('body') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Attachments -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Attachments</label>

                <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-4 hover:border-gray-400 transition-colors">
                    <input type="file"
                           wire:model.live="attachments"
                           multiple
                           class="hidden"
                           id="attachment-input">
                    <label for="attachment-input" class="cursor-pointer">
                        <div class="text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                            </svg>
                            <div class="mt-2">
                                <p class="text-sm text-gray-600">Click to select files or drag and drop</p>
                                <p class="text-xs text-gray-500">Max 10MB per file</p>
                            </div>
                        </div>
                    </label>
                </div>

                <!-- Selected Attachments Preview -->
                @if(!empty($tempAttachments))
                    <div class="mt-4 space-y-2">
                        @foreach($tempAttachments as $attachment)
                            <div class="flex items-center justify-between p-3 bg-gray-50 border border-gray-200 rounded-md">
                                <div class="flex items-center">
                                    <svg class="h-5 w-5 text-gray-400 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $attachment['original_filename'] }}</div>
                                        <div class="text-xs text-gray-500">{{ $attachment['human_size'] }}</div>
                                    </div>
                                </div>
                                <button type="button"
                                        wire:click="removeAttachment('{{ $attachment['id'] }}')"
                                        class="text-red-600 hover:text-red-800 p-1">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        @endforeach
                    </div>
                @endif

                @error('attachments.*') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Message Options -->
            <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                <h3 class="font-medium text-gray-900 mb-3">Message Options</h3>
                <label class="flex items-center">
                    <input type="checkbox" wire:model.live="isUrgent" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                    <span class="ml-2 text-sm text-gray-700">Mark as urgent</span>
                </label>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end">
                <button type="submit"
                        class="px-6 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    Send Message
                </button>
            </div>
        </form>
    </div>
</div>
