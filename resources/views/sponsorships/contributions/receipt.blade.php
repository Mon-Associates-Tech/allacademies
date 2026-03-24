<x-layouts.app>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-sm border border-gray-200 dark:border-gray-700">
                <!-- Header -->
                <div class="border-b border-gray-200 dark:border-gray-700 px-8 py-6">
                    <div class="flex justify-between items-start">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900 dark:text-white uppercase tracking-wide">Official Receipt</h1>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Contribution Confirmation</p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">Receipt Number</p>
                            <p class="text-sm font-semibold text-gray-900 dark:text-white mt-1">{{ $contribution->payment_reference ?? $contribution->id }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">{{ $contribution->created_at->format('F d, Y') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Receipt Body -->
                <div class="px-8 py-8">
                    <!-- Beneficiary Information -->
                    <div class="mb-8">
                        <h2 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">Program Supported</h2>
                        <div class="border-l-4 border-gray-900 dark:border-gray-100 pl-4">
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $contribution->sponsorshipProject->name }}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Program Code: {{ $contribution->sponsorshipProject->code }}</p>
                            @if($contribution->sponsorshipProject->school)
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $contribution->sponsorshipProject->school->name }}</p>
                            @endif
                        </div>
                    </div>

                    <!-- Payment Details -->
                    <div class="mb-8">
                        <h2 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-4">Payment Details</h2>
                        <table class="w-full">
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                <tr>
                                    <td class="py-3 text-sm text-gray-700 dark:text-gray-300">Contribution Amount</td>
                                    <td class="py-3 text-sm text-right font-semibold text-gray-900 dark:text-white">GHS {{ number_format($contribution->amount, 2) }}</td>
                                </tr>
                                @if($contribution->platform_fee > 0)
                                    <tr>
                                        <td class="py-3 text-sm text-gray-700 dark:text-gray-300">
                                            Platform Fee
                                            @if($contribution->sponsor_covers_fee)
                                                <span class="text-xs text-gray-500">(Covered by contributor)</span>
                                            @endif
                                        </td>
                                        <td class="py-3 text-sm text-right font-semibold text-gray-900 dark:text-white">GHS {{ number_format($contribution->platform_fee, 2) }}</td>
                                    </tr>
                                @endif
                                <tr>
                                    <td class="py-3 text-sm text-gray-700 dark:text-gray-300">Payment Method</td>
                                    <td class="py-3 text-sm text-right font-semibold text-gray-900 dark:text-white">{{ ucfirst($contribution->payment_method) }}</td>
                                </tr>
                                <tr>
                                    <td class="py-3 text-sm text-gray-700 dark:text-gray-300">Transaction Status</td>
                                    <td class="py-3 text-sm text-right font-semibold text-gray-900 dark:text-white">{{ ucfirst($contribution->status) }}</td>
                                </tr>
                                <tr class="border-t-2 border-gray-900 dark:border-gray-100">
                                    <td class="py-4 text-base font-semibold text-gray-900 dark:text-white">Total Amount Paid</td>
                                    <td class="py-4 text-lg text-right font-bold text-gray-900 dark:text-white">GHS {{ number_format($contribution->sponsor_covers_fee ? $contribution->amount + $contribution->platform_fee : $contribution->amount, 2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Contributor Information -->
                    <div class="mb-8">
                        <h2 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-4">Contributor Information</h2>
                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">Name</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white mt-1">{{ $contribution->payer_name }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">Email Address</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white mt-1">{{ $contribution->payer_email }}</p>
                            </div>
                            @if($contribution->payer_phone)
                                <div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">Phone Number</p>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white mt-1">{{ $contribution->payer_phone }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Footer Note -->
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                        <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                            This receipt serves as official confirmation of your contribution. Please retain this document for your records. 
                            For any inquiries regarding this transaction, please reference the receipt number above.
                        </p>
                    </div>
                </div>

                <!-- Actions Footer -->
                <div class="border-t border-gray-200 dark:border-gray-700 px-8 py-4 bg-gray-50 dark:bg-gray-900">
                    <div class="flex flex-col sm:flex-row gap-3 justify-between items-center">
                        <a href="{{ route('sponsorships.contributions.mine') }}"
                           class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
                            ← Return to Contributions
                        </a>
                        <div class="flex gap-3">
                            <a href="{{ route('sponsorships.projects.show', $contribution->sponsorshipProject) }}"
                               class="px-4 py-2 text-sm border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                                View Program
                            </a>
                            <button onclick="window.print()"
                                    class="px-4 py-2 text-sm bg-gray-900 dark:bg-gray-100 text-white dark:text-gray-900 hover:bg-gray-800 dark:hover:bg-gray-200 transition">
                                Print Receipt
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
