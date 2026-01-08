<div>
    <div class="max-w-4xl mx-auto">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <!-- Header Section -->
            <div class="border-b border-gray-200 dark:border-gray-700 pb-6 mb-6">
                <!-- Back Button -->
                <div class="mb-4">
                    <a href="{{ route('sponsorships.index') }}"
                       class="inline-flex items-center text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        Back to Sponsorships
                    </a>
                </div>
                
                <!-- Title and Description -->
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                        {{ $isEditing ? 'Edit Sponsorship Offer' : 'Create New Sponsorship Offer' }}
                    </h1>
                    <p class="text-gray-600 dark:text-gray-400">
                        {{ $isEditing ? 'Update your sponsorship offer details below.' : 'Fill out the form below to create a new sponsorship opportunity.' }}
                    </p>
                </div>
            </div>

            <form wire:submit.prevent="{{ $isEditing ? 'update' : 'save' }}">
                <!-- Title -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Offer Title <span class="text-red-500">*</span>
                    </label>
                    <input type="text" wire:model="title"
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500"
                           placeholder="e.g., Scholarship for 10 Students">
                    @error('title') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Description -->
                <div class="mb-6">
                    <x-form.markdown-editor 
                        name="description" 
                        :value="$description"
                        label="Description"
                        height="300"
                        required="true"
                        info="Describe what you're offering to sponsor..."/>
                    @error('description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Amount Offered -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Amount Offered (GHS) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" step="0.01" wire:model="amount_offered"
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500"
                           placeholder="0.00">
                    @error('amount_offered') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Criteria -->
                <div class="mb-6">
                    <x-form.markdown-editor 
                        name="criteria" 
                        :value="$criteria"
                        label="Eligibility Criteria"
                        height="200"
                        info="Optional: Specify requirements or qualifications for this sponsorship"/>
                    @error('criteria') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Accepts Bids -->
                <div class="mb-6">
                    <label class="flex items-center">
                        <input type="checkbox" wire:model="accepts_bids"
                               class="rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Accept bids from benefactors</span>
                    </label>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Allow benefactors to apply for this
                        sponsorship</p>
                    @error('accepts_bids') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Expiration Date -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Expiration Date (Optional)
                    </label>
                    <input type="date" wire:model="expires_at"
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500">
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Leave blank if there's no expiration</p>
                    @error('expires_at') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end gap-4 pt-6 border-t">
                    <a href="{{ route('sponsorships.index') }}"
                       class="px-6 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                        Cancel
                    </a>
                    <button type="submit"
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition"
                            wire:loading.attr="disabled">
                        <span wire:loading.remove>{{ $isEditing ? 'Update Offer' : 'Create Offer' }}</span>
                        <span wire:loading>Processing...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
