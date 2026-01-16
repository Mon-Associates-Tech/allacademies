<x-layouts.app>
    <section class="py-12 bg-gradient-to-b from-white to-gray-50 dark:from-gray-900 dark:to-gray-800 min-h-screen">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-4xl mx-auto">
                {{-- Header --}}
                <div class="mb-8 text-center">
                    <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">
                        Confirm Your Subscription
                    </h1>
                    <p class="text-lg text-gray-600 dark:text-gray-300">
                        Review your subscription details before proceeding to payment
                    </p>
                </div>

                {{-- Progress Steps --}}
                <div class="flex justify-center mb-12">
                    <div class="flex items-center gap-2 sm:gap-4">
                        <div class="flex flex-col items-center">
                            <div
                                class="flex items-center justify-center w-10 h-10 rounded-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-bold text-sm shadow-lg">
                                1
                            </div>
                            <span
                                class="text-xs sm:text-sm mt-2 text-gray-600 dark:text-gray-400 whitespace-nowrap font-medium">Choose</span>
                        </div>
                        <div
                            class="w-8 sm:w-12 h-1 bg-gradient-to-r from-blue-300 to-indigo-300 dark:from-blue-700 dark:to-indigo-700 rounded-full"></div>
                        <div class="flex flex-col items-center">
                            <div
                                class="flex items-center justify-center w-10 h-10 rounded-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-bold text-sm shadow-lg">
                                2
                            </div>
                            <span
                                class="text-xs sm:text-sm mt-2 text-gray-600 dark:text-gray-400 whitespace-nowrap font-medium">Review</span>
                        </div>
                        <div class="w-8 sm:w-12 h-1 bg-gray-300 dark:bg-gray-600 rounded-full"></div>
                        <div class="flex flex-col items-center">
                            <div
                                class="flex items-center justify-center w-10 h-10 rounded-full bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-300 font-bold text-sm shadow-md">
                                3
                            </div>
                            <span
                                class="text-xs sm:text-sm mt-2 text-gray-500 dark:text-gray-400 whitespace-nowrap font-medium">Payment</span>
                        </div>
                    </div>
                </div>

                {{-- Main Content Card --}}
                <div
                    class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="grid lg:grid-cols-2 gap-8 p-8">
                        {{-- Left Column - Details --}}
                        <div class="lg:col-span-1 space-y-8">
                            {{-- Subscription Details --}}
                            <div>
                                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 flex items-center">
                                    <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor"
                                         viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Subscription Details
                                </h2>
                                <div class="space-y-4">
                                    {{-- Plan Name --}}
                                    <div
                                        class="flex justify-between items-center p-4 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                                        <span class="text-gray-600 dark:text-gray-400 font-medium">Selected Plan</span>
                                        <span
                                            class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $pricingTier->name }}</span>
                                    </div>

                                    {{-- Duration --}}
                                    <div
                                        class="flex justify-between items-center p-4 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-lg border border-green-200 dark:border-green-800">
                                        <span
                                            class="text-gray-600 dark:text-gray-400 font-medium">Subscription Duration</span>
                                        <span class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $months }} Month{{ $months > 1 ? 's' : '' }}</span>
                                    </div>

                                    {{-- Monthly Tokens --}}
                                    <div
                                        class="flex justify-between items-center p-4 bg-gradient-to-r from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20 rounded-lg border border-purple-200 dark:border-purple-800">
                                        <span
                                            class="text-gray-600 dark:text-gray-400 font-medium">Messengers Per Month</span>
                                        <span
                                            class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ number_format($pricingTier->monthly_token_limit) }}</span>
                                    </div>

                                    {{-- Total Tokens --}}
                                    <div
                                        class="flex justify-between items-center p-4 bg-gradient-to-r from-orange-50 to-red-50 dark:from-orange-900/20 dark:to-red-900/20 rounded-lg border border-orange-200 dark:border-orange-800">
                                        <span class="text-gray-600 dark:text-gray-400 font-medium">Total Messengers (All Months)</span>
                                        <span
                                            class="text-2xl font-bold text-orange-600 dark:text-orange-400">{{ number_format($pricingTier->monthly_token_limit * $months) }}</span>
                                    </div>
                                </div>
                            </div>

                            {{-- Plan Features --}}
                            <div>
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4 flex items-center">
                                    <svg class="w-6 h-6 mr-2 text-green-600" fill="none" stroke="currentColor"
                                         viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    What You Get
                                </h3>
                                <ul class="space-y-3">
                                    <li class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
                                        <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor"
                                             viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                  d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                  clip-rule="evenodd"/>
                                        </svg>
                                        <span class="text-gray-700 dark:text-gray-300">{{ number_format($pricingTier->monthly_token_limit) }} tokens every month</span>
                                    </li>
                                    <li class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
                                        <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor"
                                             viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                  d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                  clip-rule="evenodd"/>
                                        </svg>
                                        <span class="text-gray-700 dark:text-gray-300">Full access to all messenger features</span>
                                    </li>
                                    <li class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
                                        <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor"
                                             viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                  d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                  clip-rule="evenodd"/>
                                        </svg>
                                        <span class="text-gray-700 dark:text-gray-300">30-day anniversary cycles (not calendar months)</span>
                                    </li>
                                    <li class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
                                        <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor"
                                             viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                  d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                  clip-rule="evenodd"/>
                                        </svg>
                                        <span class="text-gray-700 dark:text-gray-300">Automatic topup carryover to next cycle</span>
                                    </li>
                                    <li class="hidden items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
                                        <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor"
                                             viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                  d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                  clip-rule="evenodd"/>
                                        </svg>
                                        <span
                                            class="text-gray-700 dark:text-gray-300">Cancel anytime, no hidden fees</span>
                                    </li>
                                </ul>
                            </div>

                            {{-- User Info --}}
                            <div>
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4 flex items-center">
                                    <svg class="w-6 h-6 mr-2 text-indigo-600" fill="none" stroke="currentColor"
                                         viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Billing Information
                                </h3>
                                <div class="space-y-3">
                                    <div
                                        class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
                                        <span class="text-gray-600 dark:text-gray-400">Email</span>
                                        <span
                                            class="font-semibold text-gray-900 dark:text-white">{{ $user->email }}</span>
                                    </div>
                                    <div
                                        class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
                                        <span class="text-gray-600 dark:text-gray-400">Name</span>
                                        <span
                                            class="font-semibold text-gray-900 dark:text-white">{{ $user->name }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Right Column - Summary & Payment --}}
                        <div class="lg:col-span-1">
                            <div
                                class="bg-gradient-to-br from-gray-50 to-blue-50 dark:from-gray-700/30 dark:to-blue-900/10 rounded-xl p-6 border border-gray-200 dark:border-gray-700 sticky top-20">
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6">Order Summary</h3>

                                {{-- Price Breakdown Table --}}
                                @if(isset($priceBreakdown) && count($priceBreakdown) > 0)
                                    <div class="space-y-2 mb-6 pb-6 border-b border-gray-200 dark:border-gray-700">
                                        <div
                                            class="grid grid-cols-3 gap-2 mb-2 text-xs font-semibold text-gray-600 dark:text-gray-400">
                                            <span>Month</span>
                                            <span class="text-right">Monthly Cost</span>
                                            <span class="text-right">Cumulative</span>
                                        </div>
                                        @foreach($priceBreakdown as $cycleNum => $pricing)
                                            <div
                                                class="grid grid-cols-3 gap-2 text-sm p-2 bg-white dark:bg-gray-800/50 rounded">
                                                <span
                                                    class="text-gray-700 dark:text-gray-300">Month {{ $cycleNum }}</span>
                                                <span
                                                    class="text-right font-semibold text-gray-900 dark:text-white">GH₵ {{ number_format($pricing['monthly_increment'], 2) }}</span>
                                                <span class="text-right font-semibold text-blue-600 dark:text-blue-400">GH₵ {{ number_format($pricing['cumulative'], 2) }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="space-y-4 mb-6 pb-6 border-b border-gray-200 dark:border-gray-700">
                                        <div class="flex justify-between items-center">
                                            <span
                                                class="text-gray-600 dark:text-gray-400">× {{ $months }} Month{{ $months > 1 ? 's' : '' }}</span>
                                            <span
                                                class="font-semibold text-gray-900 dark:text-white">GH₵ {{ number_format($totalPrice, 2) }}</span>
                                        </div>
                                    </div>
                                @endif

                                {{-- Total --}}
                                <div
                                    class="flex justify-between items-center mb-6 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-lg p-4">
                                    <span class="text-white font-bold">Total Amount</span>
                                    <span
                                        class="text-2xl font-bold text-white">GH₵ {{ number_format($totalPrice, 2) }}</span>
                                </div>

                                {{-- Payment Method --}}
                                <div class="mb-6">
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-3 font-medium">Payment
                                        Method</p>
                                    <div
                                        class="flex items-center gap-3 p-4 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg">
                                        <div
                                            class="w-10 h-10 bg-gradient-to-br from-orange-400 to-red-600 rounded-lg flex items-center justify-center">
                                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path
                                                    d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 6H6.28l-.31-1.243A1 1 0 005 3H3z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-900 dark:text-white">Paystack</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">Secure payment
                                                processing</p>
                                        </div>
                                    </div>
                                </div>

                                {{-- Security Badge --}}
                                <div
                                    class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4 mb-6">
                                    <div class="flex items-start gap-3">
                                        <svg class="w-5 h-5 text-green-600 dark:text-green-400 flex-shrink-0 mt-0.5"
                                             fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                  d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                                                  clip-rule="evenodd"/>
                                        </svg>
                                        <div>
                                            <p class="text-sm font-semibold text-green-900 dark:text-green-100">Secure &
                                                Encrypted</p>
                                            <p class="text-xs text-green-700 dark:text-green-200 mt-1">Your payment
                                                information is protected with industry-standard encryption</p>
                                        </div>
                                    </div>
                                </div>

                                {{-- Action Buttons --}}
                                <div class="space-y-3">
                                    <form action="{{ route('token-subscriptions.process-payment') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="pricing_tier_id" value="{{ $pricingTier->id }}">
                                        <input type="hidden" name="months" value="{{ $months }}">
                                        <button type="submit"
                                                class="w-full py-3 px-4 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-semibold rounded-lg transition-all duration-300 transform hover:scale-105 shadow-lg flex items-center justify-center gap-2">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M3 10h18M7 15h10m4 0a1 1 0 11-2 0m2 0a1 1 0 10-2 0m-4-5a4 4 0 11-8 0 4 4 0 018 0z"/>
                                            </svg>
                                            Proceed to Payment
                                        </button>
                                    </form>

                                    {{-- Terms --}}
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-6 text-center">
                                        By clicking "Proceed to Payment", you agree to our <a
                                            href="{{ route('branding.terms') }}"
                                            class="text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">Terms
                                            of Service</a>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- How the Cycle Works --}}
                    <div class="mt-12 px-6 max-w-4xl mx-auto">
                        <div
                            class="bg-gradient-to-r from-indigo-50 to-blue-50 dark:from-indigo-900/20 dark:to-blue-900/20 rounded-2xl p-8 border border-indigo-200 dark:border-indigo-800">
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 flex items-center">
                                <svg class="w-6 h-6 mr-3 text-indigo-600" fill="none" stroke="currentColor"
                                     viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                About 30-Day Anniversary Cycles
                            </h2>
                            <div class="grid md:grid-cols-2 gap-6">
                                <div class="bg-white dark:bg-gray-800 rounded-lg p-4">
                                    <h3 class="font-semibold text-gray-900 dark:text-white mb-2 flex items-center">
                                        <svg class="w-5 h-5 text-indigo-600 mr-2" fill="currentColor"
                                             viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                  d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                  clip-rule="evenodd"/>
                                        </svg>
                                        Your Anniversary Date
                                    </h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Your subscription cycle resets
                                        30 days from today, not on the 1st of each month. This gives you consistent
                                        service for exactly 30 days.</p>
                                </div>
                                <div class="bg-white dark:bg-gray-800 rounded-lg p-4">
                                    <h3 class="font-semibold text-gray-900 dark:text-white mb-2 flex items-center">
                                        <svg class="w-5 h-5 text-green-600 mr-2" fill="currentColor"
                                             viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                  d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"
                                                  clip-rule="evenodd"/>
                                        </svg>
                                        Smart Topup Carryover
                                    </h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">When your cycle resets, unused
                                        topup tokens carry over to the next cycle. Your base allocation always resets
                                        for fair usage.</p>
                                </div>
                            </div>
                            <div
                                class="mt-6 p-4 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg border border-indigo-300 dark:border-indigo-700">
                                <p class="text-sm text-indigo-900 dark:text-indigo-100">
                                    <span class="font-semibold">Example:</span> If you start today
                                    ({{ now()->format('M d, Y') }}), your cycle resets
                                    on {{ now()->addDays(30)->format('M d, Y') }} and gives you a fresh set of tokens
                                    for another 30 days.
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- FAQ Section --}}
                    <div class="mt-12 px-6 mb-12">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 text-center">Common
                            Questions</h2>
                        <div class="grid md:grid-cols-2 gap-6 max-w-4xl mx-auto">
                            <div
                                class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
                                <h3 class="font-bold text-gray-900 dark:text-white mb-2 flex items-center">
                                    <svg class="w-5 h-5 text-blue-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                              d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2z"
                                              clip-rule="evenodd"/>
                                    </svg>
                                    When does my cycle reset?
                                </h3>
                                <p class="text-gray-600 dark:text-gray-400 text-sm">Your cycle resets 30 days from when
                                    you activate your subscription, not on calendar months. You'll receive a
                                    notification when it's about to reset.</p>
                            </div>
                            <div
                                class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
                                <h3 class="font-bold text-gray-900 dark:text-white mb-2 flex items-center">
                                    <svg class="w-5 h-5 text-blue-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                              d="M5 2a1 1 0 011 1v1h1a1 1 0 010 2H6v1a1 1 0 01-2 0V6H3a1 1 0 010-2h1V3a1 1 0 011-1zm0 10a1 1 0 011 1v1h1a1 1 0 110 2H6v1a1 1 0 11-2 0v-1H3a1 1 0 110-2h1v-1a1 1 0 011-1zM16 2a1 1 0 011 1v1h1a1 1 0 110 2h-1v1a1 1 0 11-2 0V6h-1a1 1 0 110-2h1V3a1 1 0 011-1z"
                                              clip-rule="evenodd"/>
                                    </svg>
                                    What happens to unused tokens?
                                </h3>
                                <p class="text-gray-600 dark:text-gray-400 text-sm">If you buy extra tokens (topups),
                                    unused topups automatically carry over to your next cycle. Your base allocation
                                    always resets for fair usage.</p>
                            </div>
                            <div
                                class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
                                <h3 class="font-bold text-gray-900 dark:text-white mb-2 flex items-center">
                                    <svg class="w-5 h-5 text-blue-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                              d="M10 18a8 8 0 100-16 8 8 0 000 16zM7 9a1 1 0 100-2 1 1 0 000 2zm6 0a1 1 0 100-2 1 1 0 000 2zm-6 4a3 3 0 016 0H7z"
                                              clip-rule="evenodd"/>
                                    </svg>
                                    Can I upgrade my plan?
                                </h3>
                                <p class="text-gray-600 dark:text-gray-400 text-sm">Yes! You can upgrade or downgrade
                                    anytime. Changes take effect at the start of your next cycle.</p>
                            </div>
                            <div
                                class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
                                <h3 class="font-bold text-gray-900 dark:text-white mb-2 flex items-center">
                                    <svg class="w-5 h-5 text-blue-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                              d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 17v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.381z"
                                              clip-rule="evenodd"/>
                                    </svg>
                                    When will my subscription start?
                                </h3>
                                <p class="text-gray-600 dark:text-gray-400 text-sm">Your subscription begins immediately
                                    after successful payment. Tokens are added to your account right away, and your
                                    30-day cycle starts now.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </section>
</x-layouts.app>
