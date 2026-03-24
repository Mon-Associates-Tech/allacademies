<x-layouts.app>
    <div
        class="min-h-screen bg-gradient-to-br from-green-50 via-emerald-50 to-teal-50 dark:from-gray-900 dark:via-green-950 dark:to-emerald-950 py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Success Message -->
            <div class="text-center mb-8">
                <div
                    class="inline-flex items-center justify-center w-20 h-20 bg-green-100 dark:bg-green-900 rounded-full mb-4">
                    <svg class="w-12 h-12 text-green-600 dark:text-green-400" fill="none" stroke="currentColor"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h1 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-2">Payment Successful!</h1>
                <p class="text-lg text-gray-600 dark:text-gray-300">Thank you for your generous contribution</p>
            </div>

            <!-- Payment Details Card -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden mb-6">
                <div
                    class="bg-gradient-to-r from-green-600 to-emerald-600 dark:from-green-700 dark:to-emerald-700 px-6 py-4">
                    <h2 class="text-xl font-semibold text-white">Payment Details</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Transaction Reference</p>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $contribution->reference }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Date & Time</p>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $contribution->created_at->format('M d, Y h:i A') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Amount Contributed</p>
                            <p class="text-2xl font-bold text-green-600 dark:text-green-400">
                                GHS {{ number_format($contribution->amount, 2) }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Payment Method</p>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ ucfirst($contribution->payment_method) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Project Details Card -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden mb-6">
                <div
                    class="bg-gradient-to-r from-blue-600 to-indigo-600 dark:from-blue-700 dark:to-indigo-700 px-6 py-4">
                    <h2 class="text-xl font-semibold text-white">Project Supported</h2>
                </div>
                <div class="p-6">
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">{{ $contribution->sponsorshipProject->name }}</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">{{ $contribution->sponsorshipProject->code }}</p>

                    @if($contribution->sponsorshipProject->description)
                        <p class="text-gray-700 dark:text-gray-300 mb-4">{{ Str::limit($contribution->sponsorshipProject->description, 200) }}</p>
                    @endif

                    <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4 mb-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Fundraising Progress</span>
                            <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $contribution->sponsorshipProject->progress_percentage }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                            <div
                                class="bg-gradient-to-r from-blue-600 to-indigo-600 h-3 rounded-full transition-all duration-300"
                                style="width: {{ $contribution->sponsorshipProject->progress_percentage }}%"></div>
                        </div>
                        <div class="flex items-center justify-between mt-2">
                            <span class="text-sm text-gray-600 dark:text-gray-400">GHS {{ number_format($contribution->sponsorshipProject->amount_raised, 2) }} raised</span>
                            <span
                                class="text-sm text-gray-600 dark:text-gray-400">Goal: GHS {{ number_format($contribution->sponsorshipProject->amount_goal, 2) }}</span>
                        </div>
                    </div>

                    <a href="{{ route('sponsorships.projects.show', $contribution->sponsorshipProject) }}"
                       class="inline-flex items-center text-blue-600 dark:text-blue-400 hover:underline">
                        View Full Project Details
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Contributor Information -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden mb-6">
                <div
                    class="bg-gradient-to-r from-purple-600 to-pink-600 dark:from-purple-700 dark:to-pink-700 px-6 py-4">
                    <h2 class="text-xl font-semibold text-white">Contributor Information</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Name</p>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $contribution->payer_name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Email</p>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $contribution->payer_email }}</p>
                        </div>
                        @if($contribution->payer_phone)
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Phone</p>
                                <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $contribution->payer_phone }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                @auth
                    <a href="{{ route('sponsorships.contributions.receipt', $contribution) }}"
                       class="inline-flex items-center justify-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition shadow-lg">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        View Receipt
                    </a>
                    <a href="{{ route('sponsorships.contributions.mine') }}"
                       class="inline-flex items-center justify-center px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition shadow-lg">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        My Contributions
                    </a>
                @endauth
                <a href="{{ route('sponsorships.projects.index') }}"
                   class="inline-flex items-center justify-center px-6 py-3 border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                    Browse More Projects
                </a>
            </div>

            <!-- Thank You Message -->
            <div class="mt-8 text-center">
                <p class="text-gray-600 dark:text-gray-400">
                    Your contribution makes a real difference. A confirmation email has been sent to
                    <strong>{{ $contribution->payer_email }}</strong>
                </p>
            </div>
        </div>
    </div>
</x-layouts.app>
