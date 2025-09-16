<div>
    <form wire:submit.prevent="sendMessage">
        <div class="space-y-4">
            <!-- To field (readonly) -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    To
                </label>
                <div class="px-3 py-2 bg-gray-100 border border-gray-300 rounded-md text-sm">
                    {{ $userName }} &lt;#{{ $userId }}&gt;
                </div>
            </div>

            <!-- Subject -->
            <div>
                <label for="subject" class="block text-sm font-medium text-gray-700 mb-1">
                    Subject <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    id="subject"
                    wire:model="subject"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Enter message subject"
                >
                @error('subject')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <!-- Message Body -->
            <div wire:ignore>
                <label for="body" class="block text-sm font-medium text-gray-700 mb-1">
                    Message <span class="text-red-500">*</span>
                </label>
                <textarea
                    id="message-body"
                    wire:model="body"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    rows="6"
                    placeholder="Write your message here...">
                </textarea>
                @error('body')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <!-- Urgent checkbox -->
            <div class="flex items-center">
                <input type="checkbox"
                       wire:model="isUrgent"
                       id="isUrgent"
                       class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                <label for="isUrgent" class="ml-2 text-sm text-gray-700">
                    Mark as urgent
                </label>
            </div>

            <!-- Attachments -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Attachments</label>

                <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 hover:border-gray-400 transition-colors">
                    <input type="file"
                           wire:model.live="attachments"
                           multiple
                           class="hidden"
                           id="attachment-input-single">
                    <label for="attachment-input-single" class="cursor-pointer">
                        <div class="text-center">
                            <svg class="mx-auto h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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
                    <div class="mt-3 space-y-2">
                        @foreach($tempAttachments as $attachment)
                            <div class="flex items-center justify-between p-2 bg-gray-50 border border-gray-200 rounded-md">
                                <div class="flex items-center">
                                    <svg class="h-5 w-5 text-gray-400 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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

                @error('attachments.*')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end space-x-3 pt-4">
                <button type="button"
                        wire:click="$dispatch('close-modal', { name: 'send-message-to-user' })"
                        class="px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500">
                    Cancel
                </button>

                <x-button.primary type="submit">
                    Send Message
                </x-button.primary>
            </div>
        </div>
    </form>
</div>

