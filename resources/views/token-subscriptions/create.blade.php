<x-layouts.app>
    <section class="py-12 bg-gradient-to-b from-white to-gray-50 dark:from-gray-900 dark:to-gray-800">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-6xl mx-auto">

                {{-- Header Section --}}
                <div class="mb-12 text-center">
                    <div class="mb-6">
                        <h1 class="text-4xl md:text-5xl font-bold text-gray-900 dark:text-white mb-4">
                            Choose Your Messenger Package
                        </h1>
                        <p class="text-lg text-gray-600 dark:text-gray-300 max-w-2xl mx-auto">
                            Unlock powerful Messenger features to enhance your learning experience. Pay once, use forever.
                        </p>
                    </div>

                    {{-- Progress Steps --}}
                    <div class="flex justify-center">
                        <div class="flex items-center gap-2 sm:gap-4">
                            <div class="flex flex-col items-center">
                                <div class="flex items-center justify-center w-10 h-10 rounded-full bg-blue-600 text-white font-bold text-sm">
                                    1
                                </div>
                                <span class="text-xs sm:text-sm mt-2 text-gray-600 dark:text-gray-400 whitespace-nowrap">Choose</span>
                            </div>
                            <div class="w-8 sm:w-12 h-1 bg-blue-300 dark:bg-blue-700"></div>
                            <div class="flex flex-col items-center">
                                <div class="flex items-center justify-center w-10 h-10 rounded-full bg-gray-400 dark:bg-gray-600 text-white font-bold text-sm">
                                    2
                                </div>
                                <span class="text-xs sm:text-sm mt-2 text-gray-500 dark:text-gray-400 whitespace-nowrap">Payment</span>
                            </div>
                            <div class="w-8 sm:w-12 h-1 bg-gray-300 dark:bg-gray-600"></div>
                            <div class="flex flex-col items-center">
                                <div class="flex items-center justify-center w-10 h-10 rounded-full bg-gray-400 dark:bg-gray-600 text-white font-bold text-sm">
                                    3
                                </div>
                                <span class="text-xs sm:text-sm mt-2 text-gray-500 dark:text-gray-400 whitespace-nowrap">Activate</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Main Content Container --}}
                <div id="packageContainer">

                    {{-- Current Subscription Info (if exists) --}}
                    @if($currentSubscription)
                        <div class="mb-8 bg-gradient-to-r from-indigo-50 to-blue-50 dark:from-indigo-900/20 dark:to-blue-900/20 rounded-2xl p-6 border border-indigo-200 dark:border-indigo-800 shadow-sm">
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0 pt-1">
                                    <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">
                                        Your Current Package: <span class="text-indigo-600 dark:text-indigo-400">{{ $currentSubscription->package->name }}</span>
                                    </h3>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <div class="flex items-center">
                                            <div>
                                                <p class="text-sm text-gray-600 dark:text-gray-400">Remaining Messengers</p>
                                                <p class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">
                                                    {{ number_format($currentSubscription->tokens_remaining) }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="flex items-center">
                                            <div>
                                                <p class="text-sm text-gray-600 dark:text-gray-400">Model Type</p>
                                                <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                                    {{ $currentSubscription->package->name === 'Premium' ? '⚡ Advanced' : '✓ Basic' }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="flex items-center">
                                            <div>
                                                <p class="text-sm text-gray-600 dark:text-gray-400">Status</p>
                                                <p class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                    <span class="inline-block w-2 h-2 mr-2 rounded-full bg-green-600 dark:bg-green-400"></span>
                                                    Active
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="mt-4 text-sm text-indigo-700 dark:text-indigo-300 bg-indigo-50 dark:bg-indigo-900/30 p-3 rounded-lg">
                                        💡 Purchasing a new package will add messengers to your account. Your existing messengers will be preserved.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Trial Eligibility Banner --}}
                    @if($isEligibleForTrial && $trialPackage)
                        <div class="mb-8 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-2xl p-6 border-2 border-green-300 dark:border-green-700 shadow-sm">
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0 text-3xl">🎉</div>
                                <div class="flex-1">
                                    <h3 class="text-xl font-bold text-green-900 dark:text-green-100 mb-2">
                                        You're Eligible for a FREE Trial!
                                    </h3>
                                    <p class="text-green-800 dark:text-green-200 text-base mb-3">
                                        Get <strong class="text-lg">{{ number_format($trialPackage->token_limit) }} free messengers</strong> for <strong>7 days</strong>!
                                    </p>
                                    <div class="flex gap-2 flex-wrap">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-200 dark:bg-green-800 text-green-900 dark:text-green-100">
                                            ✓ No credit card required
                                        </span>
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-200 dark:bg-green-800 text-green-900 dark:text-green-100">
                                            ✓ Start immediately
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @elseif(!$isEligibleForTrial)
                        <div class="mb-8 bg-blue-50 dark:bg-blue-900/20 rounded-2xl p-4 border border-blue-200 dark:border-blue-800 max-w-2xl mx-auto">
                            <p class="text-blue-800 dark:text-blue-200 text-sm flex items-start gap-3">
                                <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                                <span><strong>Note:</strong> You've already used your free trial. Choose a paid package below to continue using Messenger features.</span>
                            </p>
                        </div>
                    @endif

                    {{-- Package Cards Grid --}}
                    <div class="mb-8">
                        <div class="grid md:grid-cols-2 {{ $isEligibleForTrial && $trialPackage ? 'lg:grid-cols-3' : '' }} gap-6 max-w-4xl mx-auto">
                            {{-- Free Trial Package --}}
                            @if($isEligibleForTrial && $trialPackage)
                                <form action="{{ route('token-subscriptions.store') }}" method="POST" class="h-full">
                                    @csrf
                                    <input type="hidden" name="package_id" value="{{ $trialPackage->id }}">
                                    <div class="h-full flex flex-col bg-white dark:bg-gray-800 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden border-2 border-green-200 dark:border-green-700">
                                        {{-- Badge --}}
                                        <div class="bg-gradient-to-r from-green-500 to-emerald-600 text-white text-center py-3 px-4 text-xs font-bold uppercase tracking-widest">
                                            🎁 FREE TRIAL - 7 DAYS
                                        </div>

                                        {{-- Content --}}
                                        <div class="p-6 flex flex-col flex-1">
                                            {{-- Title --}}
                                            <div class="mb-6">
                                                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                                                    {{ $trialPackage->name }}
                                                </h3>
                                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                                    Perfect for trying our features risk-free
                                                </p>
                                            </div>

                                            {{-- Price --}}
                                            <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/30 rounded-xl border border-green-200 dark:border-green-700">
                                                <div class="text-center">
                                                    <div class="text-4xl font-bold text-green-600 dark:text-green-400 mb-1">FREE</div>
                                                    <p class="text-xs text-gray-600 dark:text-gray-400">No payment required</p>
                                                </div>
                                            </div>

                                            {{-- Messengers Count --}}
                                            <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-xl border border-blue-200 dark:border-blue-800">
                                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Free Messengers</p>
                                                <p class="text-3xl font-bold text-blue-600 dark:text-blue-400">
                                                    {{ number_format($trialPackage->token_limit) }}
                                                </p>
                                            </div>

                                            {{-- Features --}}
                                            <ul class="space-y-3 mb-6 flex-1">
                                                <li class="flex items-start gap-3 text-sm">
                                                    <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                    </svg>
                                                    <span class="text-gray-700 dark:text-gray-300">Instant activation</span>
                                                </li>
                                                <li class="flex items-start gap-3 text-sm">
                                                    <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                    </svg>
                                                    <span class="text-gray-700 dark:text-gray-300">All messenger features</span>
                                                </li>
                                                <li class="flex items-start gap-3 text-sm">
                                                    <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                    </svg>
                                                    <span class="text-gray-700 dark:text-gray-300">7 days full access</span>
                                                </li>
                                            </ul>

                                            {{-- CTA Button --}}
                                            <button
                                                type="submit"
                                                class="w-full py-3 px-4 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white font-semibold rounded-lg transition-all duration-200 transform hover:scale-105 shadow-md">
                                                <span class="flex items-center justify-center gap-2">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                                    </svg>
                                                    Activate Free Trial
                                                </span>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            @endif

                            {{-- Paid Packages --}}
                            @foreach($packages as $pkg)
                                <form action="{{ route('token-subscriptions.store') }}" method="POST" class="h-full">
                                    @csrf
                                    <input type="hidden" name="package_id" value="{{ $pkg->id }}">
                                    <div class="h-full flex flex-col bg-white dark:bg-gray-800 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden border-2 {{ $pkg->name === 'Premium' ? 'border-purple-200 dark:border-purple-700' : 'border-gray-200 dark:border-gray-700' }}">
                                        {{-- Badge --}}
                                        <div class="bg-gradient-to-r {{ $pkg->name === 'Premium' ? 'from-purple-500 to-purple-600' : 'from-blue-500 to-blue-600' }} text-white text-center py-3 px-4 text-xs font-bold uppercase tracking-widest">
                                            {{ $pkg->name === 'Premium' ? '👑 BEST VALUE' : '⭐ MOST POPULAR' }}
                                        </div>

                                        {{-- Content --}}
                                        <div class="p-6 flex flex-col flex-1">
                                            {{-- Title --}}
                                            <div class="mb-6">
                                                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                                                    {{ $pkg->name }} Plan
                                                </h3>
                                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                                    {{ $pkg->description }}
                                                </p>
                                            </div>

                                            {{-- Price --}}
                                            <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-700/30 rounded-xl border border-gray-200 dark:border-gray-700">
                                                <div class="text-center">
                                                    <div class="text-4xl font-bold text-gray-900 dark:text-white">GH₵ {{ number_format($pkg->price, 2) }}</div>
                                                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">One-time payment</p>
                                                </div>
                                            </div>

                                            {{-- Messengers Count --}}
                                            <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-xl border border-blue-200 dark:border-blue-800">
                                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Total Messengers</p>
                                                <p class="text-3xl font-bold text-blue-600 dark:text-blue-400">
                                                    {{ number_format($pkg->token_limit) }}
                                                </p>
                                            </div>

                                            {{-- Features --}}
                                            <ul class="space-y-3 mb-6 flex-1">
                                                <li class="flex items-start gap-3 text-sm">
                                                    <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                    </svg>
                                                    <span class="text-gray-700 dark:text-gray-300">Messengers never expire</span>
                                                </li>
                                                <li class="flex items-start gap-3 text-sm">
                                                    <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                    </svg>
                                                    <span class="text-gray-700 dark:text-gray-300">All messenger features</span>
                                                </li>
                                                <li class="flex items-start gap-3 text-sm">
                                                    <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                    </svg>
                                                    <span class="text-gray-700 dark:text-gray-300">Priority support</span>
                                                </li>
                                                <li class="flex items-start gap-3 text-sm">
                                                    <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                    </svg>
                                                    <span class="text-gray-700 dark:text-gray-300">Secure payment via Paystack</span>
                                                </li>
                                            </ul>

                                            {{-- CTA Button --}}
                                            <button
                                                type="submit"
                                                class="w-full py-3 px-4 {{ $pkg->name === 'Premium' ? 'bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-700 hover:to-purple-800' : 'bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700' }} text-white font-semibold rounded-lg transition-all duration-200 transform hover:scale-105 shadow-md">
                                                <span class="flex items-center justify-center gap-2">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                                    </svg>
                                                    Continue to Payment
                                                </span>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            @endforeach
                        </div>

                        {{-- Error Message --}}
                        @error('package_id')
                        <p class="mt-4 text-red-600 dark:text-red-400 text-sm text-center font-medium">
                            <svg class="w-5 h-5 inline mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                            {{ $message }}
                        </p>
                        @enderror
                    </div>

                    {{-- Token Usage Guide --}}
                    <div class="mb-12 bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/10 dark:to-indigo-900/10 border border-blue-200 dark:border-blue-800 rounded-2xl p-8">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-white">
                                How Messengers Work
                            </h3>
                        </div>
                        <p class="text-gray-700 dark:text-gray-300 mb-6 max-w-3xl">
                            Messengers are tokens consumed when using Messenger features. The amount depends on the complexity and length of your requests.
                        </p>
                        <div class="grid md:grid-cols-3 gap-6">
                            <div class="bg-white dark:bg-gray-800 rounded-xl p-5 border border-blue-100 dark:border-blue-900">
                                <div class="flex items-center gap-3 mb-3">
                                    <svg class="w-6 h-6 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                                    </svg>
                                    <h4 class="font-semibold text-gray-900 dark:text-white">Chat Messages</h4>
                                </div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    <strong>100-500</strong> messengers per conversation
                                </p>
                            </div>
                            <div class="bg-white dark:bg-gray-800 rounded-xl p-5 border border-blue-100 dark:border-blue-900">
                                <div class="flex items-center gap-3 mb-3">
                                    <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <h4 class="font-semibold text-gray-900 dark:text-white">Quiz Generation</h4>
                                </div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    <strong>500-2,000</strong> messengers per quiz
                                </p>
                            </div>
                            <div class="bg-white dark:bg-gray-800 rounded-xl p-5 border border-blue-100 dark:border-blue-900">
                                <div class="flex items-center gap-3 mb-3">
                                    <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                    </svg>
                                    <h4 class="font-semibold text-gray-900 dark:text-white">Content Analysis</h4>
                                </div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    <strong>200-1,000</strong> messengers per document
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- FAQ Section --}}
                    <div class="mb-8">
                        <h3 class="text-3xl font-bold text-gray-900 dark:text-white mb-8 text-center">
                            Frequently Asked Questions
                        </h3>
                        <div class="grid md:grid-cols-2 gap-6 max-w-4xl mx-auto">
                            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-md border border-gray-200 dark:border-gray-700 hover:shadow-lg transition-all duration-200">
                                <div class="flex items-start gap-3 mb-3">
                                    <div class="flex items-center justify-center w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-400 flex-shrink-0 font-bold text-sm mt-0.5">
                                        Q
                                    </div>
                                    <h4 class="font-semibold text-gray-900 dark:text-white text-lg">
                                        Do messengers expire?
                                    </h4>
                                </div>
                                <p class="text-gray-600 dark:text-gray-400">
                                    No! Your purchased messengers never expire and can be used anytime. Trial messengers last for 7 days from activation.
                                </p>
                            </div>

                            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-md border border-gray-200 dark:border-gray-700 hover:shadow-lg transition-all duration-200">
                                <div class="flex items-start gap-3 mb-3">
                                    <div class="flex items-center justify-center w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-400 flex-shrink-0 font-bold text-sm mt-0.5">
                                        Q
                                    </div>
                                    <h4 class="font-semibold text-gray-900 dark:text-white text-lg">
                                        Can I upgrade my package?
                                    </h4>
                                </div>
                                <p class="text-gray-600 dark:text-gray-400">
                                    Absolutely! You can purchase additional messenger packages at any time. Your new messengers will be added to your existing balance.
                                </p>
                            </div>

                            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-md border border-gray-200 dark:border-gray-700 hover:shadow-lg transition-all duration-200">
                                <div class="flex items-start gap-3 mb-3">
                                    <div class="flex items-center justify-center w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-400 flex-shrink-0 font-bold text-sm mt-0.5">
                                        Q
                                    </div>
                                    <h4 class="font-semibold text-gray-900 dark:text-white text-lg">
                                        What's the difference between plans?
                                    </h4>
                                </div>
                                <p class="text-gray-600 dark:text-gray-400">
                                    Both plans give you the same features. The difference is in the messenger count and price. Premium offers more messengers for better value.
                                </p>
                            </div>

                            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-md border border-gray-200 dark:border-gray-700 hover:shadow-lg transition-all duration-200">
                                <div class="flex items-start gap-3 mb-3">
                                    <div class="flex items-center justify-center w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-400 flex-shrink-0 font-bold text-sm mt-0.5">
                                        Q
                                    </div>
                                    <h4 class="font-semibold text-gray-900 dark:text-white text-lg">
                                        Is my payment secure?
                                    </h4>
                                </div>
                                <p class="text-gray-600 dark:text-gray-400">
                                    Yes! We use Paystack, a PCI-DSS compliant payment processor. Your payment information is fully encrypted and secure.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layouts.app>
