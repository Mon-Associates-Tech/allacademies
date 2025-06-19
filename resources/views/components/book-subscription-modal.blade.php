<!-- Subscription Modal -->
@if($showSubscriptionModal)
<div class="fixed inset-0 z-50 overflow-y-auto" wire:ignore.self>
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75"
             wire:click="closeSubscriptionModal"></div>

        <!-- Modal content -->
        <div class="inline-block w-full max-w-2xl p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl dark:bg-gray-800">

            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                    Book Subscription - {{ $subscriptionData['book_title'] ?? 'Book' }}
                </h3>
                <button wire:click="closeSubscriptionModal"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <div class="space-y-6">
                <!-- Subscription Info -->
                <div class="bg-blue-50 dark:bg-blue-900 p-4 rounded-lg">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-blue-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                        <h4 class="text-lg font-semibold text-blue-800 dark:text-blue-200">Subscription Details</h4>
                    </div>
                </div>

                <!-- Amount and Reference -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Annual Fee</label>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">
                            GHS {{ number_format($subscriptionData['amount'] ?? 0, 2) }}
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Reference Number</label>
                        <p class="text-lg font-mono text-gray-900 dark:text-white break-all">
                            {{ $subscriptionData['reference'] ?? 'N/A' }}
                        </p>
                    </div>
                </div>

                <!-- Subscription Conditions -->
                <div class="bg-yellow-50 dark:bg-yellow-900 p-4 rounded-lg">
                    <h4 class="font-semibold text-yellow-800 dark:text-yellow-200 mb-2">Subscription Conditions:</h4>
                    <ul class="text-sm text-yellow-700 dark:text-yellow-300 space-y-1">
                        <li>• Subscription is valid for one year from payment date</li>
                        <li>• Book content is for reading only - no downloading, copying or printing allowed</li>
                        <li>• Access will be revoked upon subscription expiry</li>
                        <li>• Subscription is non-refundable</li>
                        <li>• Content is protected by copyright laws</li>
                    </ul>
                </div>

                <!-- Payment Instructions -->
                <div class="bg-green-50 dark:bg-green-900 p-6 rounded-lg">
                    <h4 class="font-semibold text-green-800 dark:text-green-200 mb-4">Payment Instructions:</h4>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between p-3 bg-white dark:bg-gray-800 rounded border">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">1. Dial USSD Code:</span>
                            <code class="px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded text-green-600 dark:text-green-400 font-mono">*772*30#</code>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-white dark:bg-gray-800 rounded border">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">2. Press Enter, then enter:</span>
                            <code class="px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded text-green-600 dark:text-green-400 font-mono">1326001</code>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-white dark:bg-gray-800 rounded border">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">3. Press Enter, then follow instructions to pay</span>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-white dark:bg-gray-800 rounded border">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Reference for payment:</span>
                            <code class="px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded text-green-600 dark:text-green-400 font-mono text-xs">
                                {{ $subscriptionData['reference'] ?? 'N/A' }}
                            </code>
                        </div>
                    </div>
                    <p class="text-sm text-green-700 dark:text-green-300 mt-4">
                        ⚠️ Please check your subscription page after payment to confirm the numbers are correct.
                    </p>
                </div>

                <!-- Modal Actions -->
                <div class="flex justify-between pt-4">
                    <button wire:click="closeSubscriptionModal"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600">
                        Close
                    </button>
                    <a href="{{ route('students.dashboard') }}"
                       class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700">
                        View My Subscriptions
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
