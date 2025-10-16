<div>
    <form wire:submit.prevent="resetPassword">
        <div class="space-y-4">
            <!-- Current User Info -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    User
                </label>
                <div class="px-3 py-2 bg-gray-100 border border-gray-300 rounded-md text-sm">
                    {{ $userName }} &lt;#{{ $userId }}&gt;
                </div>
            </div>

            <!-- New Password -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                    New Password <span class="text-red-500">*</span>
                </label>
                <input
                    type="password"
                    id="password"
                    wire:model="password"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Enter new password"
                >
                @error('password')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <!-- Password Confirmation -->
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
                    Confirm Password <span class="text-red-500">*</span>
                </label>
                <input
                    type="password"
                    id="password_confirmation"
                    wire:model="password_confirmation"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Confirm new password"
                >
                @error('password')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <!-- Password Requirements Note -->
            <div class="text-xs text-gray-500">
                Password must be at least 8 characters long and should contain a mix of letters, numbers, and symbols.
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end space-x-3 pt-4">
                <button type="button"
                        wire:click="$dispatch('close-modal', { name: 'reset-user-password' })"
                        class="px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500">
                    Cancel
                </button>

                <x-button.primary type="submit">
                    Reset Password
                </x-button.primary>
            </div>
        </div>
    </form>
</div>

