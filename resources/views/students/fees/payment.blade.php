<x-layouts.app>
    <x-slot name="header">
        <div class="flex items-center space-x-4">
            <a href="{{ route('students.fees.index') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Make Payment') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Payment Summary -->
                <div class="bg-gradient-to-br from-violet-500 to-purple-600 rounded-xl shadow-lg p-8 text-white">
                    <h3 class="text-2xl font-bold mb-6">Payment Summary</h3>

                    <div class="space-y-4">
                        <div class="flex justify-between items-center pb-4 border-b border-white/20">
                            <span class="text-white/80">Term</span>
                            <span class="font-semibold">{{ $currentTerm->name ?? 'Current Term' }}</span>
                        </div>

                        <div class="flex justify-between items-center pb-4 border-b border-white/20">
                            <span class="text-white/80">Total Fees</span>
                            <span class="font-semibold">₵{{ number_format($termTotalAmount, 2) }}</span>
                        </div>

                        <div class="flex justify-between items-center pb-4 border-b border-white/20">
                            <span class="text-white/80">Amount Paid</span>
                            <span class="font-semibold">₵{{ number_format($totalPaid, 2) }}</span>
                        </div>

                        <div class="flex justify-between items-center pt-2">
                            <span class="text-lg font-medium">Balance Due</span>
                            <span class="text-2xl font-bold">₵{{ number_format($remainingAmount, 2) }}</span>
                        </div>
                    </div>

                    <div class="mt-8 p-4 bg-white/10 rounded-lg backdrop-blur-sm">
                        <p class="text-sm text-white/90">
                            <svg class="w-5 h-5 inline mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                            You can make full or partial payment
                        </p>
                    </div>
                </div>

                <!-- Payment Form -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-8">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Enter Payment Amount</h3>

                    <form action="{{ route('students.fees.initialize') }}" method="POST">
                        @csrf

                        <div class="mb-6">
                            <label for="amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Amount (GH₵)
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-3 text-gray-500 dark:text-gray-400 text-lg font-semibold">₵</span>
                                <input type="number"
                                       name="amount"
                                       id="amount"
                                       min="1"
                                       max="{{ $remainingAmount }}"
                                       step="0.01"
                                       value="{{ $remainingAmount }}"
                                       required
                                       class="block w-full pl-8 pr-12 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-lg focus:ring-violet-500 focus:border-violet-500 dark:bg-gray-700 dark:text-white text-lg font-semibold">
                            </div>
                            @error('amount')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror

                            <div class="mt-4 flex flex-wrap gap-2">
                                @if($remainingAmount > 0)
                                    <button type="button" onclick="document.getElementById('amount').value = {{ $remainingAmount }}"
                                            class="px-4 py-2 text-sm bg-violet-100 dark:bg-violet-900 text-violet-700 dark:text-violet-300 rounded-lg hover:bg-violet-200 dark:hover:bg-violet-800 transition">
                                        Full Payment
                                    </button>
                                @endif
                                @if($remainingAmount >= 100)
                                    <button type="button" onclick="document.getElementById('amount').value = 100"
                                            class="px-4 py-2 text-sm bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                                        ₵100
                                    </button>
                                @endif
                                @if($remainingAmount >= 500)
                                    <button type="button" onclick="document.getElementById('amount').value = 500"
                                            class="px-4 py-2 text-sm bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                                        ₵500
                                    </button>
                                @endif
                            </div>
                        </div>

                        <div class="mb-6">
                            <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                                <h4 class="text-sm font-semibold text-blue-900 dark:text-blue-300 mb-2">Student Information</h4>
                                <dl class="space-y-1 text-sm">
                                    <div class="flex justify-between">
                                        <dt class="text-blue-700 dark:text-blue-400">Name:</dt>
                                        <dd class="text-blue-900 dark:text-blue-200 font-medium">{{ $student->user->name }}</dd>
                                    </div>
                                    <div class="flex justify-between">
                                        <dt class="text-blue-700 dark:text-blue-400">ID:</dt>
                                        <dd class="text-blue-900 dark:text-blue-200 font-medium">{{ $student->student_id }}</dd>
                                    </div>
                                    <div class="flex justify-between">
                                        <dt class="text-blue-700 dark:text-blue-400">Class:</dt>
                                        <dd class="text-blue-900 dark:text-blue-200 font-medium">{{ $student->academicLevel->name ?? 'N/A' }}</dd>
                                    </div>
                                </dl>
                            </div>
                        </div>

                        <button type="submit"
                                class="w-full flex items-center justify-center px-6 py-4 bg-gradient-to-r from-violet-600 to-purple-600 border border-transparent rounded-lg font-semibold text-white uppercase tracking-wider hover:from-violet-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-violet-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-lg hover:shadow-xl">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            Proceed to Payment
                        </button>

                        <p class="mt-4 text-xs text-center text-gray-500 dark:text-gray-400">
                            Secured by Paystack • Your payment information is encrypted
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
