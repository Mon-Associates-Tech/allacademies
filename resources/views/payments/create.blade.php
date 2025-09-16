<x-layouts.app title="New Payment">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Payments' => route('payments.index'),
        ]"/>
    </x-slot>

    <div class="max-w-3xl mx-auto">
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Create New Payment</h1>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Record a new payment for subscription or book
                subscription services.</p>
        </div>

        <form method="POST" action="{{ route('payments.store') }}" class="space-y-6">
            @csrf

            <!-- Payment Type Selection -->
            <div
                class="bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-900/5 dark:ring-gray-700/10 rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Payment Type</h3>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div class="relative">
                        <input type="radio" id="subscription" name="payment_type" value="subscription"
                               class="sr-only peer" checked>
                        <label for="subscription"
                               class="flex p-4 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg cursor-pointer peer-checked:border-blue-600 peer-checked:ring-2 peer-checked:ring-blue-600 hover:bg-gray-50 dark:hover:bg-gray-600">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                             viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">Regular
                                        Subscription
                                    </div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">Standard platform subscription
                                        payment
                                    </div>
                                </div>
                            </div>
                        </label>
                    </div>

                    <div class="relative">
                        <input type="radio" id="book_subscription" name="payment_type" value="book_subscription"
                               class="sr-only peer">
                        <label for="book_subscription"
                               class="flex p-4 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg cursor-pointer peer-checked:border-purple-600 peer-checked:ring-2 peer-checked:ring-purple-600 hover:bg-gray-50 dark:hover:bg-gray-600">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-purple-500 rounded-md flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                             viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">Book Subscription
                                    </div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">Individual book access
                                        payment
                                    </div>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Payment Details -->
            <div
                class="bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-900/5 dark:ring-gray-700/10 rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Payment Details</h3>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <!-- Reference -->
                    <div class="sm:col-span-2">
                        <x-form.input
                            name="reference"
                            type="text"
                            label="Payment Reference"
                            placeholder="e.g., SUB123456 or BOOK789012"
                            help="Enter the subscription or book subscription reference number"
                        />
                    </div>

                    <!-- Amount -->
                    <div>
                        <x-form.input
                            name="amount"
                            label="Amount"
                            type="number"
                            placeholder="0.00"
                        />
                    </div>

                    <!-- Currency -->
                    <div>
                        <label for="currency" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Currency</label>
                        <select name="currency" id="currency"
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="GHS" selected>GHS - Ghana Cedi</option>
                            <option value="USD">USD - US Dollar</option>
                            <option value="EUR">EUR - Euro</option>
                        </select>
                        @error('currency')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Payment
                            Status</label>
                        <select name="status" id="status"
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="succeeded">Succeeded</option>
                            <option value="pending">Pending</option>
                            <option value="failed">Failed</option>
                        </select>
                        @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Gateway Reference (Optional) -->
                    <div>
                        <x-form.input
                            name="gateway_reference"
                            type="text"
                            label="Gateway Reference (Optional)"
                            placeholder="Payment gateway transaction ID"
                            help="External payment processor reference"
                        />
                    </div>
                </div>
            </div>

            <!-- Payment Notes -->
            <div
                class="bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-900/5 dark:ring-gray-700/10 rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Additional Information</h3>

                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Notes
                        (Optional)</label>
                    <textarea
                        name="notes"
                        id="notes"
                        rows="3"
                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:border-blue-500 focus:ring-blue-500"
                        placeholder="Add any additional notes about this payment..."
                    ></textarea>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Any additional information about this
                        payment</p>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex  items-end place-content-end pt-6 border-t border-gray-200 dark:border-gray-700">
                <x-button.white href :to="route('payments.index')">
                    Cancel
                </x-button.white>

                <div class="flex space-x-3 ml-4">
                    <x-button.primary type="submit" id="btn-toggle-paystack">
                        <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Create Payment ++
                    </x-button.primary>
                </div>
            </div>
        </form>

        <!-- Help Section -->
        <div class="mt-8 bg-blue-50 dark:bg-blue-900/50 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">Payment Creation Help</h3>
                    <div class="mt-2 text-sm text-blue-700 dark:text-blue-300">
                        <ul class="list-disc list-inside space-y-1">
                            <li><strong>Reference:</strong> Use subscription reference for regular subscriptions or book
                                subscription reference for book payments
                            </li>
                            <li><strong>Amount:</strong> Ensure the amount matches the subscription fee exactly</li>
                            <li><strong>Status:</strong> Set to "Succeeded" for completed payments, "Pending" for
                                processing payments
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript for form validation and UX -->
    <script>
        function validateForm() {
            const reference = document.querySelector('input[name="reference"]').value;
            const amount = document.querySelector('input[name="amount"]').value;

            if (!reference) {
                alert('Please enter a payment reference.');
                return;
            }

            if (!amount || amount <= 0) {
                alert('Please enter a valid amount.');
                return;
            }

            alert('Form validation passed! You can now submit the payment.');
        }

        // Auto-format amount input (improved version)
        document.querySelector('input[name="amount"]').addEventListener('blur', function (e) {
            let value = e.target.value;
            if (value && !isNaN(value) && value > 0) {
                e.target.value = parseFloat(value).toFixed(2);
            }
        });

        // Allow only numeric input with decimal point
        document.querySelector('input[name="amount"]').addEventListener('keypress', function (e) {
            // Allow: backspace, delete, tab, escape, enter
            if ([46, 8, 9, 27, 13].indexOf(e.keyCode) !== -1 ||
                // Allow: Ctrl+A, Ctrl+C, Ctrl+V, Ctrl+X
                (e.keyCode === 65 && e.ctrlKey === true) ||
                (e.keyCode === 67 && e.ctrlKey === true) ||
                (e.keyCode === 86 && e.ctrlKey === true) ||
                (e.keyCode === 88 && e.ctrlKey === true) ||
                // Allow: home, end, left, right
                (e.keyCode >= 35 && e.keyCode <= 39)) {
                return; // let it happen, don't do anything
            }

            // Ensure that it is a number and stop the keypress if not
            if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                e.preventDefault();
            }

            // Allow only one decimal point
            if (e.keyCode === 46 && e.target.value.indexOf('.') !== -1) {
                e.preventDefault();
            }
        });


        // Reference format helper
        document.querySelector('input[name="reference"]').addEventListener('blur', function (e) {
            const paymentType = document.querySelector('input[name="payment_type"]:checked').value;
            const reference = e.target.value.trim();

            if (reference) {
                // Auto-remove _1326001 suffix if present for validation
                const cleanReference = reference.replace(/_1326001$/, '');
                if (cleanReference !== reference) {
                    e.target.value = cleanReference;
                }
            }
        });

        // toggling paystack sdk
        document.querySelector("#btn-toggle-paystack").addEventListener("click", function(e) {
            window.location.href = "https://google.com"
        })
    </script>
</x-layouts.app>
