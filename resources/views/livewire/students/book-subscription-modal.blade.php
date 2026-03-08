<div>
    <!-- Subscription Modal -->
    @if($showModal)
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

                    <!-- Step Indicator -->
                    <div class="flex items-center justify-center mb-8">
                        <div class="flex items-center space-x-4">
                            <div class="flex items-center">
                                <div class="flex items-center justify-center w-8 h-8 rounded-full {{ $step >= 1 ? 'bg-blue-600 text-white' : 'bg-gray-300 text-gray-600' }}">
                                    1
                                </div>
                                <span class="ml-2 text-sm font-medium {{ $step >= 1 ? 'text-blue-600' : 'text-gray-500' }}">Conditions</span>
                            </div>
                            <div class="w-12 h-1 {{ $step >= 2 ? 'bg-blue-600' : 'bg-gray-300' }}"></div>
                            <div class="flex items-center">
                                <div class="flex items-center justify-center w-8 h-8 rounded-full {{ $step >= 2 ? 'bg-blue-600 text-white' : 'bg-gray-300 text-gray-600' }}">
                                    2
                                </div>
                                <span class="ml-2 text-sm font-medium {{ $step >= 2 ? 'text-blue-600' : 'text-gray-500' }}">Payment</span>
                            </div>
                        </div>
                    </div>

                    @if($step == 1)
                        <!-- Step 1: Subscription Conditions -->
                        <div class="space-y-6">
                            <!-- Subscription Info -->
                            <div class="bg-blue-50 dark:bg-blue-900 p-4 rounded-lg">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-blue-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                    </svg>
                                    <h4 class="text-lg font-semibold text-blue-800 dark:text-blue-200">Subscription Details</h4>
                                </div>
                                <div class="mt-3">
                                    <p class="text-blue-700 dark:text-blue-300"><strong>Book:</strong> {{ $subscriptionData['book_title'] ?? 'N/A' }}</p>
                                    <p class="text-blue-700 dark:text-blue-300"><strong>Annual Fee:</strong> GHS {{ number_format($subscriptionData['amount'] ?? 0, 2) }}</p>
                                    @if(isset($subscriptionData['author']))
                                        <p class="text-blue-700 dark:text-blue-300"><strong>Author:</strong> {{ $subscriptionData['author'] }}</p>
                                    @endif
                                </div>
                            </div>

                            <!-- Subscription Conditions -->
                            <div class="bg-yellow-50 dark:bg-yellow-900 p-6 rounded-lg">
                                <h4 class="font-semibold text-yellow-800 dark:text-yellow-200 mb-4">⚠️ Important Subscription Conditions:</h4>
                                <ul class="text-sm text-yellow-700 dark:text-yellow-300 space-y-2">
                                    <li class="flex items-start">
                                        <span class="font-semibold mr-2">•</span>
                                        <span>Subscription is valid for <strong>one year</strong> from payment date</span>
                                    </li>
                                    <li class="flex items-start">
                                        <span class="font-semibold mr-2">•</span>
                                        <span>Book content is for <strong>reading only</strong> - no downloading, copying, or printing allowed</span>
                                    </li>
                                    <li class="flex items-start">
                                        <span class="font-semibold mr-2">•</span>
                                        <span>Access will be <strong>revoked upon subscription expiry</strong></span>
                                    </li>
                                    <li class="flex items-start">
                                        <span class="font-semibold mr-2">•</span>
                                        <span>Subscription is <strong>non-refundable</strong></span>
                                    </li>
                                    <li class="flex items-start">
                                        <span class="font-semibold mr-2">•</span>
                                        <span>Content is protected by <strong>copyright laws</strong></span>
                                    </li>
                                    <li class="flex items-start">
                                        <span class="font-semibold mr-2">•</span>
                                        <span>Violation of terms may result in <strong>immediate suspension</strong></span>
                                    </li>
                                </ul>

                                <!-- Acceptance Checkbox -->
                                <div class="mt-6 p-4 bg-white dark:bg-gray-800 rounded border">
                                    <label class="flex items-start space-x-3 cursor-pointer">
                                        <input type="checkbox" wire:model="acceptedConditions"
                                               class="mt-1 h-4 w-4 text-blue-600 dark:text-blue-500 focus:ring-blue-500 dark:focus:ring-blue-400 border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded">
                                        <span class="text-sm text-gray-700 dark:text-gray-300">
                                            I have read, understood, and agree to all the subscription conditions listed above.
                                            I understand that the book cannot be downloaded, copied, or printed, and I accept these restrictions.
                                        </span>
                                    </label>
                                </div>
                            </div>

                            <!-- Actions for Step 1 -->
                            <div class="flex justify-between pt-4">
                                <button wire:click="closeSubscriptionModal"
                                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600">
                                    Cancel
                                </button>
                                <button wire:click="proceedToPayment"
                                        :disabled="!$wire.acceptedConditions"
                                        class="px-6 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed"
                                        x-bind:class="{ 'opacity-50 cursor-not-allowed': !$wire.acceptedConditions }">
                                    Proceed to Payment
                                </button>
                            </div>
                        </div>

                    @elseif($step == 2)
                        <!-- Step 2: Payment Information -->
                        <div class="space-y-6">
                            <!-- Subscription Summary -->
                            <div class="bg-green-50 dark:bg-green-900 p-4 rounded-lg">
                                <h4 class="font-semibold text-green-800 dark:text-green-200 mb-2">✅ Subscription Created Successfully!</h4>
                                <p class="text-sm text-green-700 dark:text-green-300">
                                    Your subscription has been created. Please complete the payment to activate access to the book.
                                </p>
                            </div>

                            <!-- Amount and Reference -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Annual Fee</label>
                                    <p class="text-2xl font-bold text-gray-900 dark:text-white">
                                        GHS {{ number_format($subscriptionData['amount'] ?? 0, 2) }}
                                    </p>
                                </div>
                                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Reference Number</label>
                                    <p class="text-sm font-mono text-gray-900 dark:text-white break-all">
                                        {{ $subscriptionData['reference'] ?? 'N/A' }}
                                    </p>
                                </div>
                            </div>

                            <!-- Payment Instructions -->
                            <div class="bg-blue-50 dark:bg-blue-900 p-6 rounded-lg">
                                <h4 class="font-semibold text-blue-800 dark:text-blue-200 mb-4">💳 Payment Instructions:</h4>
                                <div class="space-y-3">
                                    <div class="flex items-center justify-between p-3 bg-white dark:bg-gray-800 rounded border">
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">1. Dial USSD Code:</span>
                                        <code class="px-3 py-1 bg-blue-100 dark:bg-blue-800 rounded text-blue-600 dark:text-blue-300 font-mono text-lg">*772*30#</code>
                                    </div>
                                    <div class="flex items-center justify-between p-3 bg-white dark:bg-gray-800 rounded border">
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">2. Press Enter, then enter:</span>
                                        <code class="px-3 py-1 bg-blue-100 dark:bg-blue-800 rounded text-blue-600 dark:text-blue-300 font-mono text-lg">1326001</code>
                                    </div>
                                    <div class="flex items-center justify-between p-3 bg-white dark:bg-gray-800 rounded border">
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">3. Press Enter and follow instructions</span>
                                        <span class="text-green-600 font-medium">Complete Payment</span>
                                    </div>
                                    <div class="p-3 bg-yellow-50 dark:bg-yellow-900 rounded border border-yellow-200 dark:border-yellow-700">
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm font-medium text-yellow-800 dark:text-yellow-200">Payment Reference:</span>
                                            <button onclick="navigator.clipboard.writeText('{{ $subscriptionData['reference'] ?? '' }}')"
                                                    class="text-yellow-600 dark:text-yellow-400 hover:text-yellow-800 dark:hover:text-yellow-200">
                                                <code class="px-2 py-1 bg-yellow-100 dark:bg-yellow-800 rounded font-mono text-xs">
                                                    {{ $subscriptionData['reference'] ?? 'N/A' }}
                                                </code>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-4 p-3 bg-orange-50 dark:bg-orange-900 rounded">
                                    <p class="text-sm text-orange-700 dark:text-orange-300">
                                        ⚠️ <strong>Important:</strong> Please check your subscription page after payment to verify the payment was processed correctly.
                                    </p>
                                </div>
                            </div>

                            <!-- Actions for Step 2 -->
                            <div class="flex justify-between pt-4">
                                <button wire:click="closeSubscriptionModal"
                                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600">
                                    Close
                                </button>
                                <a href="{{route('subscriptions.index')}}"
                                   class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700">
                                    View My Subscriptions
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
