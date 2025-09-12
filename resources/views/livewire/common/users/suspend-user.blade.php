<div>
    @if($showConfirmation)
        <form wire:submit.prevent="suspendUser">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        User to Suspend
                    </label>
                    <div class="px-3 py-2 bg-gray-100 border border-gray-300 rounded-md text-sm">
                        {{ $userName }} &lt;#{{ $userId }}&gt;
                    </div>
                </div>

                <div>
                    <label for="reason" class="block text-sm font-medium text-gray-700 mb-1">
                        Reason for Suspension (Optional)
                    </label>
                    <textarea
                        id="reason"
                        wire:model="reason"
                        rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Enter reason for suspension..."></textarea>
                    <p class="mt-1 text-xs text-gray-500">This will be shown to the user when they try to access their account.</p>
                </div>

                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button"
                            wire:click="cancelSuspension"
                            class="px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500">
                        Cancel
                    </button>

                    <x-button.danger type="submit">
                        Confirm Suspension
                    </x-button.danger>
                </div>
            </div>
        </form>
    @else
        <div class="space-y-4">
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-yellow-700">
                            <strong>Warning:</strong> You are about to suspend this user's account.
                            They will be unable to access their account until it is reinstated by an administrator.
                        </p>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    User to Suspend
                </label>
                <div class="px-3 py-2 bg-gray-100 border border-gray-300 rounded-md text-sm">
                    {{ $userName }} &lt;#{{ $userId }}&gt;
                </div>
            </div>

            <div class="flex justify-end space-x-3 pt-4">
                <button type="button"
                        wire:click="$dispatch('close-modal', { name: 'suspend-user' })"
                        class="px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500">
                    Cancel
                </button>

                <x-button.danger type="button" wire:click="confirmSuspension">
                    Suspend Account
                </x-button.danger>
            </div>
        </div>
    @endif
</div>

