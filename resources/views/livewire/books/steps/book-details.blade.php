<div>
    <div class="flex items-center mb-8">
        <div class="flex items-center justify-center w-10 h-10 bg-blue-600 text-white rounded-full text-sm font-bold mr-4">2</div>
        <div>
            <h3 class="text-xl font-bold text-gray-900">Book Details</h3>
            <p class="text-gray-600">Provide additional information about the book</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Edition -->
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-3">Edition</label>
            <input type="text" wire:model="edition" placeholder="e.g., 1st Edition, Revised Edition"
                   class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            @error('edition') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <!-- Publisher -->
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-3">Publisher</label>
            <input type="text" wire:model="publisher" placeholder="Publishing house name"
                   class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            @error('publisher') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <!-- Pages -->
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-3">Number of Pages</label>
            <input type="number" wire:model.live="pages" min="1" placeholder="Total pages"
                   class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            @error('pages') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <!-- Subscription Fee -->
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-3">
                Annual Subscription Fee
                <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-full ml-1">GHS</span>
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center">
                    <span class="text-gray-500">₵</span>
                </div>
                <input type="number" wire:model="annualSubscriptionFee" step="0.01" min="0" placeholder="0.00"
                       class="block w-full pl-8 pr-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <p class="mt-1 text-xs text-gray-500">Enter 0 to make this book free</p>
            @error('annualSubscriptionFee') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <!-- Book Formats -->
        <div class="lg:col-span-2">
            <label class="block text-sm font-semibold text-gray-700 mb-3">Available Formats</label>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <label class="relative flex items-start p-6 bg-white border-2 border-gray-200 rounded-xl cursor-pointer hover:border-blue-300 hover:shadow-md transition-all">
                    <input type="checkbox" wire:model="hasHardcopy"
                           class="h-5 w-5 text-blue-600 dark:text-blue-500 focus:ring-blue-500 dark:focus:ring-blue-400 border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded mt-1">
                    <div class="ml-4">
                        <div class="flex items-center mb-2">
                            <svg class="w-6 h-6 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                            <span class="font-semibold text-gray-900">Physical Hardcopy</span>
                        </div>
                        <p class="text-sm text-gray-600">Traditional printed book available for borrowing</p>
                    </div>
                </label>

                <label class="relative flex items-start p-6 bg-white border-2 border-gray-200 rounded-xl cursor-pointer hover:border-blue-300 hover:shadow-md transition-all">
                    <input type="checkbox" wire:model="hasSoftcopy"
                           class="h-5 w-5 text-blue-600 dark:text-blue-500 focus:ring-blue-500 dark:focus:ring-blue-400 border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded mt-1">
                    <div class="ml-4">
                        <div class="flex items-center mb-2">
                            <svg class="w-6 h-6 text-purple-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <span class="font-semibold text-gray-900">Digital Softcopy</span>
                        </div>
                        <p class="text-sm text-gray-600">PDF version for online reading and downloads</p>
                    </div>
                </label>
            </div>
            @error('hasHardcopy') <p class="mt-3 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <!-- Additional Info -->
        <div class="lg:col-span-2">
            <label class="block text-sm font-semibold text-gray-700 mb-3">Additional Information</label>
            <textarea wire:model="additionalInfo" rows="4" placeholder="Any additional information about the book..."
                      class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none"></textarea>
            @error('additionalInfo') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
    </div>
</div>
