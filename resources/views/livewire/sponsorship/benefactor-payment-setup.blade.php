<div>
    <div class="max-w-2xl mx-auto">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Payment Setup</h2>
            <p class="text-gray-600 dark:text-gray-400 mb-6">Configure your bank account to receive contributions directly</p>

            @if($hasSubaccount)
                <div class="mb-6 p-4 bg-green-50 dark:bg-green-900 border border-green-200 dark:border-green-700 rounded-lg">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-green-600 dark:text-green-400 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <div>
                            <h3 class="text-sm font-medium text-green-800 dark:text-green-200">Payment Setup Complete</h3>
                            <p class="mt-1 text-sm text-green-700 dark:text-green-300">
                                Your bank account is configured. You can update your details below.
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <form wire:submit.prevent="save">
                <!-- Business Name -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Business/Account Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" wire:model="business_name" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500" placeholder="Enter your business or personal name">
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">This should match your bank account name</p>
                    @error('business_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Bank Selection -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Bank <span class="text-red-500">*</span>
                    </label>
                    <select wire:model.live="bank_code" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select your bank</option>
                        @foreach($banks as $code => $name)
                            <option value="{{ $code }}">{{ $name }}</option>
                        @endforeach
                    </select>
                    @error('bank_code') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Account Number -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Account Number <span class="text-red-500">*</span>
                    </label>
                    <input type="text" wire:model="account_number" maxlength="20" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500" placeholder="Enter your account number">
                    @error('account_number') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Description -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Description (Optional)
                    </label>
                    <textarea wire:model="description" rows="3" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500" placeholder="Additional notes about this account..."></textarea>
                    @error('description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Platform Fee Information -->
                <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900 border border-blue-200 dark:border-blue-700 rounded-lg">
                    <h4 class="text-sm font-medium text-blue-800 dark:text-blue-200 mb-2">Platform Fee</h4>
                    <p class="text-sm text-blue-700 dark:text-blue-300">
                        A {{ $platformFeePercentage }} platform fee will be deducted from all contributions.
                        This fee covers transaction costs and platform maintenance.
                    </p>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end gap-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <a href="{{ route('benefactor.dashboard') }}" class="px-6 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="save">{{ $hasSubaccount ? 'Update Setup' : 'Complete Setup' }}</span>
                        <span wire:loading wire:target="save">Processing...</span>
                    </button>
                </div>
            </form>
        </div>

        @if($hasSubaccount && $existingSubaccount)
            <div class="mt-6 bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Current Payment Information</h3>

                <dl class="space-y-3">
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-600 dark:text-gray-400">Business Name:</dt>
                        <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $existingSubaccount->business_name ?? 'N/A' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-600 dark:text-gray-400">Bank:</dt>
                        <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $existingSubaccount->settlement_bank ?? 'N/A' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-600 dark:text-gray-400">Account Number:</dt>
                        <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $existingSubaccount->account_number ?? 'N/A' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-600 dark:text-gray-400">Subaccount Code:</dt>
                        <dd class="text-sm font-mono text-gray-900 dark:text-white">{{ $existingSubaccount->subaccount_code ?? 'N/A' }}</dd>
                    </div>
                </dl>
            </div>
        @endif
    </div>
</div>
