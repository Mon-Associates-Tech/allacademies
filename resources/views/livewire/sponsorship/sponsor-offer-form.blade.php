<div>
    <div class="max-w-4xl mx-auto">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">
                {{ $isEditing ? 'Edit Sponsor Offer' : 'Create Sponsor Offer' }}
            </h2>

            <form wire:submit.prevent="{{ $isEditing ? 'update' : 'save' }}">
                <!-- Title -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Offer Title <span class="text-red-500">*</span>
                    </label>
                    <input type="text" wire:model="title" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500" placeholder="e.g., Scholarship for 10 Students">
                    @error('title') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Description -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Description <span class="text-red-500">*</span>
                    </label>
                    <textarea wire:model="description" rows="4" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500" placeholder="Describe what you're offering to sponsor..."></textarea>
                    @error('description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Amount Offered -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Amount Offered (GHS) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" step="0.01" wire:model="amount_offered" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500" placeholder="0.00">
                    @error('amount_offered') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Criteria -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Eligibility Criteria
                    </label>
                    <textarea wire:model="criteria" rows="3" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500" placeholder="Describe who can apply for this sponsorship..."></textarea>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Optional: Specify requirements or qualifications</p>
                    @error('criteria') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Accepts Bids -->
                <div class="mb-6">
                    <label class="flex items-center">
                        <input type="checkbox" wire:model="accepts_bids" class="rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Accept bids from benefactors</span>
                    </label>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Allow benefactors to apply for this sponsorship</p>
                    @error('accepts_bids') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Expiration Date -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Expiration Date (Optional)
                    </label>
                    <input type="date" wire:model="expires_at" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500">
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Leave blank if there's no expiration</p>
                    @error('expires_at') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end gap-4 pt-6 border-t">
                    <a href="{{ route('sponsor.dashboard') }}" class="px-6 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition" wire:loading.attr="disabled">
                        <span wire:loading.remove>{{ $isEditing ? 'Update Offer' : 'Create Offer' }}</span>
                        <span wire:loading>Processing...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
