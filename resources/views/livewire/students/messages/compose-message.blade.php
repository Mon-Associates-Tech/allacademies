<div class="max-w-4xl mx-auto">
    <div class="bg-white shadow-lg rounded-lg">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200">
            <h1 class="text-2xl font-bold text-gray-900">Compose Message</h1>
            <p class="text-sm text-gray-600 mt-1">Send messages to your teachers or parents</p>
        </div>

        <form wire:submit.prevent="send" class="p-6">
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
                    Send To <span class="text-red-500">*</span>
                </label>

                <!-- Target Type Selection -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-4">
                    <label class="relative">
                        <input type="radio" wire:model.live="targetType" value="teacher" class="sr-only peer">
                        <div class="p-3 border-2 border-gray-200 rounded-lg cursor-pointer peer-checked:border-blue-500 peer-checked:bg-blue-50">
                            <div class="text-sm font-medium text-gray-900">Teachers</div>
                            <div class="text-xs text-gray-500">Your assigned teachers</div>
                        </div>
                    </label>

                    <label class="relative">
                        <input type="radio" wire:model.live="targetType" value="parent" class="sr-only peer">
                        <div class="p-3 border-2 border-gray-200 rounded-lg cursor-pointer peer-checked:border-blue-500 peer-checked:bg-blue-50">
                            <div class="text-sm font-medium text-gray-900">Parents</div>
                            <div class="text-xs text-gray-500">Your parents/guardians</div>
                        </div>
                    </label>

                    <label class="relative">
                        <input type="radio" wire:model.live="targetType" value="individual" class="sr-only peer">
                        <div class="p-3 border-2 border-gray-200 rounded-lg cursor-pointer peer-checked:border-blue-500 peer-checked:bg-blue-50">
                            <div class="text-sm font-medium text-gray-900">Individual</div>
                            <div class="text-xs text-gray-500">Specific person</div>
                        </div>
                    </label>
                </div>

                <!-- Target Criteria Based on Selection -->
                @if($targetType === 'teacher')
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="font-medium text-gray-900 mb-3">Select Teachers</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mb-4">
                            @if($teachers !== null &&  count($teachers))
                            @forelse($teachers as $teacher)
                                <label class="flex items-center cursor-pointer">
                                    <input type="checkbox"
                                           wire:click="toggleTeacher({{ $teacher->id }})"
                                           {{ in_array($teacher->id, $selectedTeachers) ? 'checked' : '' }}
                                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-700">{{ $teacher->name }}</span>
                                </label>
                            @empty
                                <p class="text-sm text-gray-500">You don't have any assigned teachers.</p>
                            @endforelse
                                @endif
                        </div>
                    </div>
                @endif

                @if($targetType === 'parent')
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="font-medium text-gray-900 mb-3">Select Parents/Guardians</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mb-4">
                            @if($parents !== null &&  count($parents))
                            @forelse($parents as $parent)
                                <label class="flex items-center cursor-pointer">
                                    <input type="checkbox"
                                           wire:click="toggleParent({{ $parent->id }})"
                                           {{ in_array($parent->id, $selectedParents) ? 'checked' : '' }}
                                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-700">{{ $parent->name }}</span>
                                </label>
                            @empty
                                <p class="text-sm text-gray-500">No parents/guardians found.</p>
                            @endforelse
                                @endif
                        </div>
                    </div>
                @endif

                @if($targetType === 'individual')
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="font-medium text-gray-900 mb-3">Select Recipient</h3>
                        <div class="mb-3">
                            <input type="text"
                                   wire:model.live.debounce.300ms="userSearch"
                                   placeholder="Search teachers or parents by name or email..."
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        @if(!empty($userSearch))
                            <div class="max-h-40 overflow-y-auto border border-gray-200 rounded-md">
                                @forelse($searchedUsers as $user)
                                    <div class="flex items-center justify-between p-2 hover:bg-gray-50">
                                        <div class="flex items-center">
                                            <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                            <div class="text-sm text-gray-500 ml-2">({{ $user->email }})</div>
                                        </div>
                                        <button type="button"
                                                wire:click="toggleUser({{ $user->id }})"
                                                class="text-blue-600 hover:text-blue-800 text-sm">
                                            {{ in_array($user->id, $selectedUsers) ? 'Remove' : 'Add' }}
                                        </button>
                                    </div>
                                @empty
                                    <div class="p-2 text-sm text-gray-500">No matching users found.</div>
                                @endforelse
                            </div>
                        @endif

                        @if(!empty($selectedUsers))
                            <div class="mt-3">
                                <h4 class="text-sm font-medium text-gray-700 mb-2">Selected Recipients:</h4>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($selectedUsersList as $user)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $user->name }}
                                            <button type="button" wire:click="removeUser({{ $user->id }})" class="ml-1 text-blue-600 hover:text-blue-800">×</button>
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
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
                <label for="body" class="block text-sm font-medium text-gray-700 mb-2">
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
                                <p class="text-xs text-gray-500">Max 10MB per file • Files will be added automatically</p>
                            </div>
                        </div>
                    </label>
                </div>

                <!-- Selected Attachments Preview -->
                @if(!empty($tempAttachments))
                    <div class="mt-4 space-y-2">
                        <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">Selected Files:</h4>
                        @foreach($tempAttachments as $attachment)
                            <div class="flex items-center justify-between p-3 bg-gray-50 border border-gray-200 rounded-md">
                                <div class="flex items-center">
                                    <svg class="h-5 w-5 text-gray-400 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $attachment['original_filename'] }}</div>
                                        <div class="text-xs text-gray-500">{{ $attachment['human_size'] }} • {{ $attachment['mime_type'] }}</div>
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

                <div class="space-y-3">
                    <label class="flex items-center">
                        <input type="checkbox" wire:model.live="isUrgent" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                        <span class="ml-2 text-sm text-gray-700">Mark as urgent</span>
                    </label>

                    <div>
                        <label class="flex items-center mb-2">
                            <input type="radio" wire:model.live="sendNow" value="1" class="border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700">Send immediately</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" wire:model.live="sendNow" value="0" class="border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700">Schedule for later</span>
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
                    <a href="{{ route('students.messages.index') }}"
                       class="px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-50">
                        Cancel
                    </a>
                </div>

                <x-button.primary type="submit"
                                  class="">
                    {{ $sendNow ? 'Send Message' : 'Schedule Message' }}
                </x-button.primary>
            </div>
        </form>
    </div>
</div>
