<x-layouts.app>
    <section>
        <div class="container mx-auto px-4 py-8">
            <div class="max-w-7xl mx-auto">
                {{-- Progress Steps --}}
                <div class="mb-8">
                    <div class="flex items-center justify-center mb-6">
                        <div class="flex items-center">
                            <div class="flex items-center text-blue-600 dark:text-blue-400">
                                <div class="flex items-center justify-center w-10 h-10 rounded-full bg-blue-600 text-white font-semibold">
                                    1
                                </div>
                                <span class="ml-2 font-medium">Choose Package</span>
                            </div>
                            <div class="w-16 h-1 mx-4 bg-gray-300 dark:bg-gray-600"></div>
                            <div class="flex items-center text-gray-400 dark:text-gray-500">
                                <div class="flex items-center justify-center w-10 h-10 rounded-full bg-gray-300 dark:bg-gray-600 text-gray-600 dark:text-gray-400 font-semibold">
                                    2
                                </div>
                                <span class="ml-2 font-medium">Payment</span>
                            </div>
                            <div class="w-16 h-1 mx-4 bg-gray-300 dark:bg-gray-600"></div>
                            <div class="flex items-center text-gray-400 dark:text-gray-500">
                                <div class="flex items-center justify-center w-10 h-10 rounded-full bg-gray-300 dark:bg-gray-600 text-gray-600 dark:text-gray-400 font-semibold">
                                    3
                                </div>
                                <span class="ml-2 font-medium">Activation</span>
                            </div>
                        </div>
                    </div>

                    <div class="text-center">
                        <h1 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-2">
                            Choose Your AI Token Package
                        </h1>
                        <p class="text-gray-600 dark:text-gray-400 text-lg">
                            Unlock powerful AI features to enhance your learning experience
                        </p>
                    </div>
                </div>

                {{-- Alerts --}}
                @if(session('error'))
                    <div class="mb-6 bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 p-4 rounded-r-lg">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-red-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-red-700 dark:text-red-300">{{ session('error') }}</span>
                        </div>
                    </div>
                @endif

                @if(session('info'))
                    <div class="mb-6 bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-500 p-4 rounded-r-lg">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-blue-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-blue-700 dark:text-blue-300">{{ session('info') }}</span>
                        </div>
                    </div>
                @endif

                <form action="{{ route('token-subscriptions.store') }}" method="POST" id="packageForm">
                    @csrf
                    <input type="hidden" name="package_id" id="selected_package_id" value="{{ $package?->id ?? '' }}">

                    {{-- Current Subscription Info (if exists) --}}
                    @if($currentSubscription)
                        <div class="mb-8 bg-gradient-to-r from-indigo-50 to-blue-50 dark:from-indigo-900/20 dark:to-blue-900/20 rounded-xl p-6 border border-indigo-200 dark:border-indigo-800">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div class="ml-4 flex-1">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                                        Your Current Package: {{ $currentSubscription->package->name }}
                                    </h3>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                                        <div>
                                            <span class="text-gray-600 dark:text-gray-400">Remaining Tokens:</span>
                                            <span class="ml-2 font-semibold text-gray-900 dark:text-white">
                                                {{ number_format($currentSubscription->remaining_tokens) }}
                                            </span>
                                        </div>
                                        <div>
                                            <span class="text-gray-600 dark:text-gray-400">Model:</span>
                                            <span class="ml-2 font-semibold text-gray-900 dark:text-white">
                                                {{ $currentSubscription->package->model }}
                                            </span>
                                        </div>
                                        <div>
                                            <span class="text-gray-600 dark:text-gray-400">Status:</span>
                                            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                Active
                                            </span>
                                        </div>
                                    </div>
                                    <p class="mt-3 text-sm text-indigo-700 dark:text-indigo-300">
                                        💡 Purchasing a new package will add tokens to your account. Your existing tokens will be preserved.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Package Cards --}}
                    <div class="mb-8">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6 flex items-center">
                            <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                            Available Packages
                        </h2>

                        <div class="grid md:grid-cols-3 gap-6">
                            @foreach($packages as $pkg)
                                <div class="relative">
                                    <input
                                        type="radio"
                                        name="package_radio"
                                        id="package_{{ $pkg->id }}"
                                        value="{{ $pkg->id }}"
                                        class="peer hidden"
                                        {{ ($package && $package->id === $pkg->id) ? 'checked' : '' }}
                                    >
                                    <label
                                        for="package_{{ $pkg->id }}"
                                        class="block cursor-pointer bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden
                                       border-2 transition-all duration-200 h-full
                                       peer-checked:border-blue-500 peer-checked:shadow-2xl peer-checked:scale-[1.02]
                                       hover:shadow-xl hover:border-gray-300 dark:hover:border-gray-600
                                       border-gray-200 dark:border-gray-700">

                                        {{-- Badge --}}
                                        @if($pkg->name === 'Basic')
                                            <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white text-center py-2 text-xs font-bold uppercase tracking-wide">
                                                ⭐ Most Popular
                                            </div>
                                        @elseif($pkg->name === 'Premium')
                                            <div class="bg-gradient-to-r from-purple-500 to-purple-600 text-white text-center py-2 text-xs font-bold uppercase tracking-wide">
                                                👑 Best Value
                                            </div>
                                        @endif

                                        <div class="p-6">
                                            {{-- Package Header --}}
                                            <div class="text-center mb-6">
                                                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                                                    {{ $pkg->name }}
                                                </h3>
                                                <div class="inline-flex items-center px-3 py-1 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-xs font-semibold rounded-full">
                                                    <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                                    </svg>
                                                    {{ $pkg->model }}
                                                </div>
                                            </div>

                                            {{-- Price --}}
                                            <div class="text-center mb-6 py-4 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
                                                <div class="text-4xl font-bold text-gray-900 dark:text-white">
                                                    GH₵ {{ number_format($pkg->price, 2) }}
                                                </div>
                                                <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                                    One-time payment
                                                </div>
                                            </div>

                                            {{-- Token Amount - Highlighted --}}
                                            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-6 text-center">
                                                <div class="text-3xl font-bold text-blue-600 dark:text-blue-400">
                                                    {{ number_format($pkg->token_limit) }}
                                                </div>
                                                <div class="text-sm text-gray-600 dark:text-gray-400">
                                                    AI Tokens
                                                </div>
                                            </div>

                                            {{-- Features --}}
                                            <ul class="space-y-3 mb-6">
                                                <li class="flex items-start text-sm">
                                                    <svg class="w-5 h-5 text-green-500 mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                    </svg>
                                                    <span class="text-gray-700 dark:text-gray-300">Tokens never expire</span>
                                                </li>
                                                <li class="flex items-start text-sm">
                                                    <svg class="w-5 h-5 text-green-500 mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                    </svg>
                                                    <span class="text-gray-700 dark:text-gray-300">All AI features included</span>
                                                </li>
                                                <li class="flex items-start text-sm">
                                                    <svg class="w-5 h-5 text-green-500 mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                    </svg>
                                                    <span class="text-gray-700 dark:text-gray-300">Top-up anytime</span>
                                                </li>
                                                <li class="flex items-start text-sm">
                                                    <svg class="w-5 h-5 text-green-500 mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                    </svg>
                                                    <span class="text-gray-700 dark:text-gray-300">Secure payment via Paystack</span>
                                                </li>
                                            </ul>

                                            {{-- Description --}}
                                            <p class="text-xs text-gray-600 dark:text-gray-400 text-center mb-4 pb-4 border-b border-gray-200 dark:border-gray-700">
                                                {{ $pkg->description }}
                                            </p>

                                            {{-- Selection Indicator --}}
                                            <div class="text-center">
                                                <div class="hidden peer-checked:flex items-center justify-center text-blue-600 dark:text-blue-400 font-semibold text-sm">
                                                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                    </svg>
                                                    ✓ Selected
                                                </div>
                                                <div class="peer-checked:hidden text-gray-500 dark:text-gray-400 text-sm">
                                                    Click to select
                                                </div>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            @endforeach
                        </div>

                        @error('package_id')
                        <p class="mt-4 text-red-600 dark:text-red-400 text-sm text-center">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Token Usage Guide --}}
                    <div class="mb-8 bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/10 dark:to-indigo-900/10 border border-blue-200 dark:border-blue-800 rounded-xl p-6">
                        <div class="flex items-center mb-4">
                            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                            </svg>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                💡 Token Usage Guide
                            </h3>
                        </div>
                        <div class="grid md:grid-cols-3 gap-6">
                            <div class="bg-white dark:bg-gray-800 rounded-lg p-4">
                                <div class="flex items-center mb-2">
                                    <svg class="w-5 h-5 text-purple-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                                    </svg>
                                    <span class="font-semibold text-gray-900 dark:text-white">Chat Messages</span>
                                </div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">~100-500 tokens per conversation</p>
                            </div>
                            <div class="bg-white dark:bg-gray-800 rounded-lg p-4">
                                <div class="flex items-center mb-2">
                                    <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <span class="font-semibold text-gray-900 dark:text-white">Quiz Generation</span>
                                </div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">~500-2000 tokens per quiz</p>
                            </div>
                            <div class="bg-white dark:bg-gray-800 rounded-lg p-4">
                                <div class="flex items-center mb-2">
                                    <svg class="w-5 h-5 text-orange-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <span class="font-semibold text-gray-900 dark:text-white">Content Analysis</span>
                                </div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">~200-1000 tokens per document</p>
                            </div>
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="sticky bottom-4 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-200 dark:border-gray-700 p-6">
                        <div class="flex flex-col sm:flex-row gap-4 justify-between items-center">
                            <a href="{{ route('token-subscriptions.index') }}"
                               class="w-full sm:w-auto px-6 py-3 border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 text-center font-medium transition-colors flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                                </svg>
                                Back
                            </a>

                            <button
                                type="submit"
                                id="submitBtn"
                                class="w-full sm:w-auto px-8 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-semibold rounded-lg transition-all disabled:opacity-50 disabled:cursor-not-allowed shadow-lg flex items-center justify-center">
                                <span id="btnText" class="flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                    </svg>
                                    Continue to Payment
                                </span>
                                <span id="btnLoader" class="hidden">
                                    <svg class="inline animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Processing...
                                </span>
                            </button>
                        </div>
                    </div>
                </form>

                {{-- FAQ Section --}}
                <div class="mt-12 pt-8 border-t border-gray-200 dark:border-gray-700">
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 text-center flex items-center justify-center">
                        <svg class="w-7 h-7 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Frequently Asked Questions
                    </h3>
                    <div class="grid md:grid-cols-2 gap-6">
                        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-md border border-gray-200 dark:border-gray-700">
                            <h4 class="font-semibold text-gray-900 dark:text-white mb-3 flex items-center">
                                <span class="flex items-center justify-center w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-400 mr-3 text-sm font-bold">Q</span>
                                Do tokens expire?
                            </h4>
                            <p class="text-gray-600 dark:text-gray-400 text-sm ml-11">
                                No! Your tokens never expire. Use them at your own pace and top up whenever you need more.
                            </p>
                        </div>
                        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-md border border-gray-200 dark:border-gray-700">
                            <h4 class="font-semibold text-gray-900 dark:text-white mb-3 flex items-center">
                                <span class="flex items-center justify-center w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-400 mr-3 text-sm font-bold">Q</span>
                                Can I upgrade my package?
                            </h4>
                            <p class="text-gray-600 dark:text-gray-400 text-sm ml-11">
                                Yes! You can purchase additional token packages at any time. Your tokens will stack together.
                            </p>
                        </div>
                        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-md border border-gray-200 dark:border-gray-700">
                            <h4 class="font-semibold text-gray-900 dark:text-white mb-3 flex items-center">
                                <span class="flex items-center justify-center w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-400 mr-3 text-sm font-bold">Q</span>
                                What's the difference between models?
                            </h4>
                            <p class="text-gray-600 dark:text-gray-400 text-sm ml-11">
                                GPT-4 Nano is great for most tasks. GPT-5 offers advanced capabilities and better understanding for complex queries.
                            </p>
                        </div>
                        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-md border border-gray-200 dark:border-gray-700">
                            <h4 class="font-semibold text-gray-900 dark:text-white mb-3 flex items-center">
                                <span class="flex items-center justify-center w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-400 mr-3 text-sm font-bold">Q</span>
                                Is payment secure?
                            </h4>
                            <p class="text-gray-600 dark:text-gray-400 text-sm ml-11">
                                Absolutely! We use Paystack, a PCI-DSS compliant payment processor. Your payment information is fully encrypted.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const form = document.getElementById('packageForm');
                const hiddenInput = document.getElementById('selected_package_id');
                const radioButtons = document.querySelectorAll('input[name="package_radio"]');
                const submitBtn = document.getElementById('submitBtn');
                const btnText = document.getElementById('btnText');
                const btnLoader = document.getElementById('btnLoader');

                // Update hidden input when radio selection changes
                radioButtons.forEach(radio => {
                    radio.addEventListener('change', function () {
                        hiddenInput.value = this.value;
                    });
                });

                // Handle form submission
                form.addEventListener('submit', function (e) {
                    if (!hiddenInput.value) {
                        e.preventDefault();
                        alert('Please select a package to continue');
                        return;
                    }

                    // Show loader
                    submitBtn.disabled = true;
                    btnText.classList.add('hidden');
                    btnLoader.classList.remove('hidden');
                });
            });
        </script>
    </section>
</x-layouts.app>
