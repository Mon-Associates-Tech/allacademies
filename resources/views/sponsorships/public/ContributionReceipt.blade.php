<x-layouts.guest>
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto">
            <!-- Success Message -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-8 text-center">
                <!-- Success Icon -->
                <div class="mb-6">
                    <div
                        class="w-20 h-20 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center mx-auto">
                        <svg class="w-10 h-10 text-green-600 dark:text-green-400" fill="none" stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                </div>

                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Thank You!</h1>
                <p class="text-lg text-gray-600 dark:text-gray-400 mb-8">Your contribution has been received
                    successfully</p>

                <!-- Contribution Details -->
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6 mb-6 text-left">
                    <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4 uppercase">Contribution
                        Details</h2>

                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Program:</span>
                            <span
                                class="text-sm font-medium text-gray-900 dark:text-white">{{ $contribution->sponsorshipProgram->name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Amount Contributed:</span>
                            <span
                                class="text-sm font-medium text-gray-900 dark:text-white">GHS {{ number_format($contribution->amount, 2) }}</span>
                        </div>
                        @if($contribution->sponsor_covers_fee)
                            <div class="flex justify-between">
                                <span
                                    class="text-sm text-gray-600 dark:text-gray-400">Platform Fee (Covered by you):</span>
                                <span
                                    class="text-sm font-medium text-gray-900 dark:text-white">GHS {{ number_format($contribution->platform_fee, 2) }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between pt-3 border-t border-gray-200 dark:border-gray-600">
                            <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">Total Charged:</span>
                            <span
                                class="text-lg font-bold text-gray-900 dark:text-white">GHS {{ number_format($contribution->total_charged, 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Benefactor Receives:</span>
                            <span
                                class="text-sm font-semibold text-green-600 dark:text-green-400">GHS {{ number_format($contribution->net_amount, 2) }}</span>
                        </div>
                        <div class="flex justify-between pt-3 border-t border-gray-200 dark:border-gray-600">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Reference:</span>
                            <span
                                class="text-sm font-mono text-gray-900 dark:text-white">{{ $contribution->payment_reference }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Date:</span>
                            <span
                                class="text-sm font-medium text-gray-900 dark:text-white">{{ $contribution->paid_at->format('M d, Y h:i A') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Receipt Note -->
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                    A receipt has been sent to <strong>{{ $contribution->payer_email }}</strong>
                </p>

                <!-- Action Buttons -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <a href="{{ route('sponsorships.programs.show', $contribution->sponsorshipProgram) }}"
                       class="px-6 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition text-center">
                        View Program
                    </a>
                    <a href="{{ route('sponsorships.projects.index') }}"
                       class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-center">
                        Browse More Programs
                    </a>
                </div>
            </div>

            <!-- Share Section -->
            <div class="mt-6 bg-blue-50 dark:bg-blue-900 rounded-lg p-6 text-center">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Spread the Word!</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                    Share this program with your friends and help reach the funding goal faster
                </p>
                <div class="flex justify-center gap-3">
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('sponsorships.programs.show', $contribution->sponsorshipProgram)) }}"
                       target="_blank"
                       class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm">
                        Share on Facebook
                    </a>
                    <a href="https://twitter.com/intent/tweet?url={{ urlencode(route('sponsorships.programs.show', $contribution->sponsorshipProgram)) }}&text={{ urlencode('I just contributed to ' . $contribution->sponsorshipProgram->name) }}"
                       target="_blank"
                       class="px-4 py-2 bg-sky-500 text-white rounded-lg hover:bg-sky-600 transition text-sm">
                        Share on Twitter
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-layouts.guest>

