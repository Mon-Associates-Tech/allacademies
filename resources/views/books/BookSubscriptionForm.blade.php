<x-layouts.app>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <h1 class="text-3xl sm:text-4xl font-extrabold text-gray-900 dark:text-white mb-6 tracking-tight animate-fade-in">
            Subscribe to <span class="text-blue-600 dark:text-blue-400">{{ $book->title }}</span>
        </h1>

        <div class="bg-white dark:bg-gray-900 shadow-xl rounded-2xl overflow-hidden border border-gray-100 dark:border-gray-800">
            <div class="px-6 py-8 sm:p-10">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Subscription Details</h3>
                <div class="max-w-2xl text-sm text-gray-600 dark:text-gray-300 space-y-3">
                    <p>
                        You are subscribing to:
                        <span class="font-semibold text-gray-900 dark:text-gray-100">{{ $book->title }}</span>
                        by <span class="font-semibold text-gray-900 dark:text-gray-100">{{ $book->author->user->name ?? 'Unknown Author' }}</span>.
                    </p>
                    <p>
                        <span class="font-semibold">Subscription Amount:</span>
                        {{ number_format($book->annual_subscription_fee, 2) }} / year
                    </p>
                    <p>
                        <span class="font-semibold">Subscription Duration:</span> 1 Year
                    </p>
                    <p>
                        By proceeding, you agree to our
                        <a href="#" class="text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 underline transition-colors duration-200">Terms and Conditions</a>.
                    </p>
                    <p class="text-gray-500 dark:text-gray-400">
                        Follow the steps below to complete your subscription.
                    </p>
                </div>

                <!-- Step-by-step payment guide -->
                <div class="space-y-6 mt-8">
                    <!-- Step 1 -->
                    <div class="flex items-start space-x-4 p-5 bg-gray-50 dark:bg-gray-800 rounded-xl border border-green-200 dark:border-green-700 transition-all duration-300 hover:shadow-md">
                        <div class="flex-shrink-0 w-12 h-12 bg-green-500 text-white rounded-full flex items-center justify-center text-xl font-bold transform transition-transform duration-300 hover:scale-105">
                            1
                        </div>
                        <div class="flex-1">
                            <h5 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Dial USSD Code</h5>
                            <p class="text-sm text-gray-600 dark:text-gray-300 mb-3">
                                Initiate the payment process by dialing the USSD code on your mobile phone.
                            </p>
                            <div class="bg-white dark:bg-gray-900 p-4 rounded-lg flex items-center justify-between border border-gray-200 dark:border-gray-700">
                                <code class="text-base font-mono text-green-600 dark:text-green-400 font-semibold">*772*30#</code>
                                <button class="ml-3 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors duration-200" onclick="navigator.clipboard.writeText('*772*30#').then(() => alert('Code copied!'))">
                                    Copy Code
                                </button>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                                Ensure your phone is connected to the mobile network.
                            </p>
                        </div>
                    </div>

                    <!-- Step 2 -->
                    <div class="flex items-start space-x-4 p-5 bg-gray-50 dark:bg-gray-800 rounded-xl border border-green-200 dark:border-green-700 transition-all duration-300 hover:shadow-md">
                        <div class="flex-shrink-0 w-12 h-12 bg-green-500 text-white rounded-full flex items-center justify-center text-xl font-bold transform transition-transform duration-300 hover:scale-105">
                            2
                        </div>
                        <div class="flex-1">
                            <h5 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Enter Merchant Code</h5>
                            <p class="text-sm text-gray-600 dark:text-gray-300 mb-3">
                                After dialing the USSD code, enter the unique merchant code for All Academies.
                            </p>
                            <div class="bg-white dark:bg-gray-900 p-4 rounded-lg flex items-center justify-between border border-gray-200 dark:border-gray-700">
                                <code class="text-base font-mono text-green-600 dark:text-green-400 font-semibold">1326001</code>
                                <button class="ml-3 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors duration-200" onclick="navigator.clipboard.writeText('1326001').then(() => alert('Code copied!'))">
                                    Copy Code
                                </button>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                                This code ensures your payment is directed to the correct recipient.
                            </p>
                        </div>
                    </div>

                    <!-- Step 3 -->
                    <div class="flex items-start space-x-4 p-5 bg-gray-50 dark:bg-gray-800 rounded-xl border border-green-200 dark:border-green-700 transition-all duration-300 hover:shadow-md">
                        <div class="flex-shrink-0 w-12 h-12 bg-green-500 text-white rounded-full flex items-center justify-center text-xl font-bold transform transition-transform duration-300 hover:scale-105">
                            3
                        </div>
                        <div class="flex-1">
                            <h5 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Choose Payment Method</h5>
                            <p class="text-sm text-gray-600 dark:text-gray-300 mb-3">
                                Select your preferred payment method from the available options.
                            </p>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="bg-blue-50 dark:bg-blue-900 p-3 rounded-lg border border-blue-200 dark:border-blue-700 transition-all duration-200 hover:bg-blue-100 dark:hover:bg-blue-800">
                                    <div class="flex items-center">
                                        <span class="w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center text-sm font-bold mr-2">1</span>
                                        <span class="text-sm font-medium text-blue-800 dark:text-blue-200">Mobile Money</span>
                                    </div>
                                </div>
                                <div class="bg-orange-50 dark:bg-orange-900 p-3 rounded-lg border border-orange-200 dark:border-orange-700 transition-all duration-200 hover:bg-orange-100 dark:hover:bg-orange-800">
                                    <div class="flex items-center">
                                        <span class="w-8 h-8 bg-orange-500 text-white rounded-full flex items-center justify-center text-sm font-bold mr-2">2</span>
                                        <span class="text-sm font-medium text-orange-800 dark:text-orange-200">Prudential Bank</span>
                                    </div>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                                Ensure you have sufficient balance in your chosen payment method.
                            </p>
                        </div>
                    </div>

                    <!-- Step 4 -->
                    <div class="flex items-start space-x-4 p-5 bg-gray-50 dark:bg-gray-800 rounded-xl border border-green-200 dark:border-green-700 transition-all duration-300 hover:shadow-md">
                        <div class="flex-shrink-0 w-12 h-12 bg-green-500 text-white rounded-full flex items-center justify-center text-xl font-bold transform transition-transform duration-300 hover:scale-105">
                            4
                        </div>
                        <div class="flex-1">
                            <h5 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Enter Reference Number</h5>
                            <p class="text-sm text-gray-600 dark:text-gray-300 mb-3">
                                Input your unique subscription reference number to ensure accurate payment allocation.
                            </p>
                            <div class="bg-yellow-50 dark:bg-yellow-900 p-4 rounded-lg border border-yellow-200 dark:border-yellow-700 flex items-center justify-between">
                                <code class="text-sm font-mono text-yellow-800 dark:text-yellow-200 break-all">
                                    {{$subscription->reference ?? 'N/A' }}
                                </code>
                                <button class="ml-3 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors duration-200" onclick="navigator.clipboard.writeText('{{ $subscriptionData['reference'] ?? '' }}').then(() => alert('Reference copied!'))">
                                    Copy Ref
                                </button>
                            </div>
                            <p class="text-xs text-yellow-700 dark:text-yellow-300 mt-2 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                                <span class="font-semibold">Important:</span> Enter the reference number exactly as shown.
                            </p>
                        </div>
                    </div>

                    <!-- Step 5 -->
                    <div class="flex items-start space-x-4 p-5 bg-gray-50 dark:bg-gray-800 rounded-xl border border-green-200 dark:border-green-700 transition-all duration-300 hover:shadow-md">
                        <div class="flex-shrink-0 w-12 h-12 bg-green-500 text-white rounded-full flex items-center justify-center text-xl font-bold transform transition-transform duration-300 hover:scale-105">
                            5
                        </div>
                        <div class="flex-1">
                            <h5 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Confirm Payment</h5>
                            <p class="text-sm text-gray-600 dark:text-gray-300 mb-3">
                                Finalize the payment by reviewing the details and confirming the transaction.
                            </p>
                            <div class="bg-white dark:bg-gray-900 p-4 rounded-lg flex items-center justify-between border border-gray-200 dark:border-gray-700">
                                <code class="text-base font-mono text-green-600 dark:text-green-400 font-semibold">1</code>
                                <span class="ml-3 text-sm text-gray-600 dark:text-gray-300">Press 1 to confirm and submit</span>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                                Follow the on-screen prompts to complete the payment process.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Success Notice -->
                <div class="mt-8 bg-blue-50 dark:bg-blue-900 p-5 rounded-xl border border-blue-200 dark:border-blue-700 transition-all duration-300 hover:bg-blue-100 dark:hover:bg-blue-800">
                    <div class="flex items-start">
                        <svg class="w-6 h-6 text-blue-500 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                        <div>
                            <h5 class="text-lg font-medium text-blue-800 dark:text-blue-200 mb-2">After Payment</h5>
                            <p class="text-sm text-blue-700 dark:text-blue-300">
                                Upon successful payment, your subscription will be activated instantly, granting access to premium content. Check your subscription status on your profile page.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Custom Animations */
        @keyframes fade-in {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in {
            animation: fade-in 0.6s ease-out forwards;
        }
    </style>
</x-layouts.app>
