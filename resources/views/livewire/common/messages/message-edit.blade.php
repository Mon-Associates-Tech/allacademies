<div class="max-w-4xl mx-auto">
    <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Edit Message</h1>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Modify your draft message</p>
                </div>
                <div class="flex items-center space-x-3">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300">
                        Draft
                    </span>
                    <span class="text-sm text-gray-500 dark:text-gray-400">
                        Created: {{ $message->created_at->format('M j, Y H:i') }}
                    </span>
                </div>
            </div>
        </div>

        <form wire:submit.prevent="update" class="p-6">
            <!-- Subject -->
            <div class="mb-6">
                <label for="subject" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
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
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Target Audience <span class="text-red-500">*</span>
                </label>

                <!-- Target Type Display (Read-only for editing) -->
                <div class="mb-4 p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700/50 rounded-lg">
                    <div class="text-sm font-medium text-blue-900 dark:text-blue-100 mb-1">Target Type</div>
                    <div class="text-sm text-blue-800 dark:text-blue-300 capitalize">
                        {{ str_replace('_', ' ', $targetType) }}
                    </div>
                </div>

                <!-- Target Criteria Based on Selection -->
                @if($targetType === 'role')
                <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg">
                    <h3 class="font-medium text-gray-900 dark:text-gray-100 mb-3">Selected Roles</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                        @foreach($availableRoles as $roleValue => $roleLabel)
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox"
                                   wire:click="toggleRole('{{ $roleValue }}')"
                                   {{ in_array($roleValue, $selectedRoles) ? 'checked' : '' }}
                            class="rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ $roleLabel }}</span>
                        </label>
                        @endforeach
                    </div>

                    @if(!empty($selectedRoles))
                    <div class="mt-3">
                        <div class="flex flex-wrap gap-2">
                            @foreach($selectedRoles as $role)
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $availableRoles[$role] }}
                                            <button type="button" wire:click="removeRole('{{ $role }}')" class="ml-1 text-blue-600 hover:text-blue-800">×</button>
                                        </span>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
                @endif

                @if($targetType === 'academic_group')
                <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg">
                    <h3 class="font-medium text-gray-900 dark:text-gray-100 mb-3">Selected Academic Groups</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mb-4">
                        @foreach($academicGroups as $group)
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox"
                                   wire:click="toggleAcademicGroup({{ $group->id }})"
                                   {{ in_array($group->id, $selectedAcademicGroups) ? 'checked' : '' }}
                            class="rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ $group->name }}</span>
                        </label>
                        @endforeach
                    </div>

                    <div class="flex gap-4">
                        <label class="flex items-center">
                            <input type="checkbox" wire:model.live="includeStudents" class="rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Include Students</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" wire:model.live="includeTeachers" class="rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Include Teachers</span>
                        </label>
                    </div>
                </div>
                @endif

                @if($targetType === 'academic_level')
                <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg">
                    <h3 class="font-medium text-gray-900 dark:text-gray-100 mb-3">Selected Academic Levels</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mb-4">
                        @foreach($academicLevels as $level)
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox"
                                   wire:click="toggleAcademicLevel({{ $level->id }})"
                                   {{ in_array($level->id, $selectedAcademicLevels) ? 'checked' : '' }}
                            class="rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ $level->name }} @if($level->label)({{ $level->label }})@endif</span>
                        </label>
                        @endforeach
                    </div>

                    <div class="flex gap-4">
                        <label class="flex items-center">
                            <input type="checkbox" wire:model.live="includeStudents" class="rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Include Students</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" wire:model.live="includeTeachers" class="rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Include Teachers</span>
                        </label>
                    </div>
                </div>
                @endif

                @if($targetType === 'subject')
                <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg">
                    <h3 class="font-medium text-gray-900 dark:text-gray-100 mb-3">Selected Subjects</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mb-4">
                        @foreach($academicSubjects as $subject)
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox"
                                   wire:click="toggleSubject({{ $subject->id }})"
                                   {{ in_array($subject->id, $selectedSubjects) ? 'checked' : '' }}
                            class="rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ $subject->name }}</span>
                        </label>
                        @endforeach
                    </div>

                    <div class="flex gap-4">
                        <label class="flex items-center">
                            <input type="checkbox" wire:model.live="includeStudents" class="rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Include Students</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" wire:model.live="includeTeachers" class="rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Include Teachers</span>
                        </label>
                    </div>
                </div>
                @endif

                @if($targetType === 'individual')
                <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg">
                    <h3 class="font-medium text-gray-900 dark:text-gray-100 mb-3">Selected Individual Users</h3>
                    <div class="mb-3">
                        <input type="text"
                               wire:model.live.debounce.300ms="userSearch"
                               placeholder="Search users by name or email..."
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    @if(!empty($userSearch))
                    <div class="max-h-40 overflow-y-auto border border-gray-200 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800">
                        @foreach($searchedUsers as $user)
                        <div class="flex items-center justify-between p-2 hover:bg-gray-50 dark:hover:bg-gray-700">
                            <div class="flex items-center">
                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $user->name }}</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400 ml-2">({{ $user->email }})</div>
                            </div>
                            <button type="button"
                                    wire:click="toggleUser({{ $user->id }})"
                                    class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 text-sm">
                                {{ in_array($user->id, $selectedUsers) ? 'Remove' : 'Add' }}
                            </button>
                        </div>
                        @endforeach
                    </div>
                    @endif

                    @if(!empty($selectedUsers))
                    <div class="mt-3">
                        <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Selected Users:</h4>
                        <div class="flex flex-wrap gap-2">
                            @foreach($selectedUsersList as $user)
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300">
                                            {{ $user->name }}
                                            <button type="button" wire:click="removeUser({{ $user->id }})" class="ml-1 text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">×</button>
                                        </span>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
                @endif

                @if($targetType === 'custom')
                <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg space-y-4">
                    <h3 class="font-medium text-gray-900 dark:text-gray-100">Custom Targeting (Multiple Criteria)</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Roles</h4>
                            @foreach($availableRoles as $roleValue => $roleLabel)
                            <label class="flex items-center mb-1 cursor-pointer">
                                <input type="checkbox"
                                       wire:click="toggleRole('{{ $roleValue }}')"
                                       {{ in_array($roleValue, $selectedRoles) ? 'checked' : '' }}
                                class="rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ $roleLabel }}</span>
                            </label>
                            @endforeach
                        </div>

                        <div>
                            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Academic Groups</h4>
                            @foreach($academicGroups->take(5) as $group)
                            <label class="flex items-center mb-1 cursor-pointer">
                                <input type="checkbox"
                                       wire:click="toggleAcademicGroup({{ $group->id }})"
                                       {{ in_array($group->id, $selectedAcademicGroups) ? 'checked' : '' }}
                                class="rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ $group->name }}</span>
                            </label>
                            @endforeach
                            @if($academicGroups->count() > 5)
                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                                ... and {{ $academicGroups->count() - 5 }} more groups available
                            </div>
                            @endif
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <label class="flex items-center">
                            <input type="checkbox" wire:model.live="includeStudents" class="rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Include Students</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" wire:model.live="includeTeachers" class="rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Include Teachers</span>
                        </label>
                    </div>
                </div>
                @endif

                <!-- Preview Recipients Button -->
                <div class="mt-4">
                    <button type="button"
                            wire:click="previewRecipients"
                            class="px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500">
                        Preview Recipients
                    </button>
                </div>

                <!-- Recipients Preview -->
                @if($showPreview)
                <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="font-medium text-blue-900">Recipients Preview</h4>
                        <span class="text-sm text-blue-700">{{ $recipientCount }} recipients</span>
                    </div>
                    <div class="max-h-32 overflow-y-auto">
                        @forelse($previewRecipients->take(20) as $recipient)
                        <div class="text-sm text-blue-800">{{ $recipient->name }} ({{ $recipient->email }})</div>
                        @empty
                        <p class="text-sm text-blue-600">No recipients found for selected criteria</p>
                        @endforelse
                        @if($previewRecipients->count() > 20)
                        <div class="text-sm text-blue-600 mt-2">... and {{ $previewRecipients->count() - 20 }} more</div>
                        @endif
                    </div>
                </div>
                @endif
            </div>

            <!-- Message Body -->
            <div class="mb-6" wire:ignore>
                <label for="body" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Message Content <span class="text-red-500">*</span>
                </label>
                <textarea
                    id="message-editor"
                    wire:model="body"
                    class="w-full"
                    rows="8">
                </textarea>
                @error('body') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Attachments -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Attachments</label>

                <!-- Existing Attachments -->
                @if(!empty($existingAttachments))
                <div class="mb-4">
                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Current Attachments</h4>
                    <div class="space-y-2">
                        @foreach($existingAttachments as $attachment)
                        <div class="flex items-center justify-between p-2 bg-gray-50 dark:bg-gray-700/50 rounded-md">
                            <div class="flex items-center">
                                <svg class="h-5 w-5 text-gray-400 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <span class="text-sm text-gray-700 dark:text-gray-300">{{ $attachment['original_filename'] }}</span>
                                <span class="text-xs text-gray-500 dark:text-gray-400 ml-2">({{ number_format($attachment['size'] / 1024, 1) }} KB)</span>
                            </div>
                            <button type="button"
                                    wire:click="removeExistingAttachment({{ $attachment['id'] }})"
                                    class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Add New Attachments -->
                <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-4">
                    <input type="file"
                           wire:model="attachments"
                           multiple
                           class="hidden"
                           id="attachment-input">
                    <label for="attachment-input" class="cursor-pointer">
                        <div class="text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                            </svg>
                            <div class="mt-2">
                                <p class="text-sm text-gray-600 dark:text-gray-400">Click to upload additional files or drag and drop</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Max 10MB per file</p>
                            </div>
                        </div>
                    </label>
                </div>

                @if(!empty($attachments))
                <button type="button"
                        wire:click="uploadAttachment"
                        class="mt-2 px-3 py-1 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700">
                    Upload Files
                </button>
                @endif

                @error('attachments.*') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Message Options -->
            <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                <h3 class="font-medium text-gray-900 dark:text-gray-100 mb-3">Message Options</h3>

                <div class="space-y-3">
                    <label class="flex items-center">
                        <input type="checkbox" wire:model.live="isUrgent" class="rounded border-gray-300 dark:border-gray-600 text-red-600 focus:ring-red-500">
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Mark as urgent</span>
                    </label>

                    <div>
                        <label class="flex items-center mb-2">
                            <input type="radio" wire:model.live="sendNow" value="1" class="border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Send immediately</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" wire:model.live="sendNow" value="0" class="border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Schedule for later</span>
                        </label>

                        @if(!$sendNow)
                        <div class="mt-2 ml-6">
                            <input type="datetime-local"
                                   wire:model.live="scheduledAt"
                                   class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('scheduledAt') <span class="text-red-500 text-sm block mt-1">{{ $message }}</span> @enderror
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-between">
                <div class="flex space-x-3">
                    <button type="submit"
                            class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500">
                        Update Draft
                    </button>

                    <a href="{{ route('admin.messages.show', $message) }}"
                       class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-md hover:bg-gray-50 dark:hover:bg-gray-700">
                        Cancel
                    </a>
                </div>

                <button type="button"
                        wire:click="updateAndSend"
                        class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    {{ $sendNow ? 'Update & Send Message' : 'Update & Schedule Message' }}
                </button>
            </div>
        </form>
    </div>
</div>
