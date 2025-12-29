<x-layouts.app>
    <section class="py-12 bg-gradient-to-b from-white to-gray-50 dark:from-gray-900 dark:to-gray-800 min-h-screen">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-6xl mx-auto" x-data="{ selectedPlan: null, showComparison: false, estimatedCost: 0, showNotification: false, notificationMessage: '', notificationType: 'success' }">

                {{-- Header Section --}}
                <div class="mb-12 text-center animate-fadeInDown">
                    {{-- Toast Notification --}}
                    <div x-show="showNotification" x-transition:enter="transition ease-out duration-300" x-transition:leave="transition ease-in duration-200" class="fixed top-4 right-4 z-50 max-w-sm">
                        <div :class="{
                            'bg-gradient-to-r from-green-500 to-emerald-600': notificationType === 'success',
                            'bg-gradient-to-r from-red-500 to-rose-600': notificationType === 'error',
                            'bg-gradient-to-r from-blue-500 to-indigo-600': notificationType === 'info'
                        }" class="rounded-lg shadow-xl p-4 text-white flex items-start gap-3">
                            <div class="flex-shrink-0 mt-0.5">
                                <svg x-show="notificationType === 'success'" class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <svg x-show="notificationType === 'error'" class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                                <svg x-show="notificationType === 'info'" class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p x-text="notificationMessage" class="text-sm font-semibold"></p>
                            </div>
                            <button @click="showNotification = false" class="text-white/80 hover:text-white transition-colors">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="mb-6">
                        <div class="mb-4 inline-block px-4 py-2 bg-gradient-to-r from-blue-100 to-indigo-100 dark:from-blue-900/30 dark:to-indigo-900/30 rounded-full border border-blue-200 dark:border-blue-800">
                            <span class="text-sm font-semibold text-blue-700 dark:text-blue-300">✨ Unlock Messenger Features</span>
                        </div>
                        <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-gray-900 dark:text-white mb-4">
                            Choose Your Messenger Package
                        </h1>
                        <p class="text-lg text-gray-600 dark:text-gray-300 max-w-2xl mx-auto">
                            Unlock powerful Messenger features to enhance your learning experience. Pay once, use forever.
                        </p>
                    </div>

                    {{-- Progress Steps --}}
                    <div class="flex justify-center mt-8">
                        <div class="flex items-center gap-2 sm:gap-4">
                            <div class="flex flex-col items-center animate-fadeInUp" style="animation-delay: 0.1s;">
                                <div class="flex items-center justify-center w-10 h-10 rounded-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-bold text-sm shadow-lg transform transition-transform duration-300 hover:scale-110">
                                    1
                                </div>
                                <span class="text-xs sm:text-sm mt-2 text-gray-600 dark:text-gray-400 whitespace-nowrap font-medium">Choose</span>
                            </div>
                            <div class="w-8 sm:w-12 h-1 bg-gradient-to-r from-blue-300 to-indigo-300 dark:from-blue-700 dark:to-indigo-700 rounded-full animate-slideIn" style="animation-delay: 0.2s;"></div>
                            <div class="flex flex-col items-center animate-fadeInUp" style="animation-delay: 0.3s;">
                                <div class="flex items-center justify-center w-10 h-10 rounded-full bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-300 font-bold text-sm shadow-md transform transition-transform duration-300 hover:scale-110">
                                    2
                                </div>
                                <span class="text-xs sm:text-sm mt-2 text-gray-500 dark:text-gray-400 whitespace-nowrap font-medium">Payment</span>
                            </div>
                            <div class="w-8 sm:w-12 h-1 bg-gray-300 dark:bg-gray-600 rounded-full"></div>
                            <div class="flex flex-col items-center animate-fadeInUp" style="animation-delay: 0.4s;">
                                <div class="flex items-center justify-center w-10 h-10 rounded-full bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-300 font-bold text-sm shadow-md transform transition-transform duration-300 hover:scale-110">
                                    3
                                </div>
                                <span class="text-xs sm:text-sm mt-2 text-gray-500 dark:text-gray-400 whitespace-nowrap font-medium">Activate</span>
                            </div>
                        </div>
                    </div>

                    {{-- Comparison Toggle --}}
                    <div class="flex justify-center mt-12 mb-8 animate-fadeInUp" style="animation-delay: 0.5s;">
                        <button 
                            @click="showComparison = !showComparison"
                            class="flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-indigo-500 to-blue-500 hover:from-indigo-600 hover:to-blue-600 text-white font-semibold rounded-full shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 4H5a2 2 0 00-2 2v14a2 2 0 002 2h4m0-21v21m0-21h10a2 2 0 012 2v14a2 2 0 01-2 2h-10"/>
                            </svg>
                            <span x-text="showComparison ? 'Hide Comparison' : 'Compare Plans'"></span>
                        </button>
                    </div>
                </div>

                {{-- Main Content Container --}}
                <div id="packageContainer">

                    {{-- Current Subscription Info (if exists) --}}
                    @if($currentSubscription)
                        <div class="mb-8 bg-gradient-to-r from-indigo-50 to-blue-50 dark:from-indigo-900/20 dark:to-blue-900/20 rounded-2xl p-6 border border-indigo-200 dark:border-indigo-800 shadow-lg hover:shadow-xl transition-all duration-300 animate-slideIn">
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0 pt-1 animate-pulse">
                                    <div class="relative">
                                        <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">
                                        Your Current Package: 
                                        <span class="text-indigo-600 dark:text-indigo-400 bg-indigo-100/50 dark:bg-indigo-900/30 px-3 py-1 rounded-full text-sm">
                                            {{ $currentSubscription->package->name }}
                                        </span>
                                    </h3>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <div class="bg-white dark:bg-gray-800/50 rounded-lg p-3 transform transition-transform duration-300 hover:scale-105">
                                            <p class="text-xs text-gray-600 dark:text-gray-400 uppercase tracking-wide font-semibold">Remaining Messengers</p>
                                            <p class="text-3xl font-bold text-indigo-600 dark:text-indigo-400 mt-1">
                                                {{ number_format($currentSubscription->tokens_remaining) }}
                                            </p>
                                        </div>
                                        <div class="bg-white dark:bg-gray-800/50 rounded-lg p-3 transform transition-transform duration-300 hover:scale-105">
                                            <p class="text-xs text-gray-600 dark:text-gray-400 uppercase tracking-wide font-semibold">Model Type</p>
                                            <p class="text-lg font-semibold text-gray-900 dark:text-white mt-1">
                                                {{ $currentSubscription->package->name === 'Premium' ? '⚡ Advanced' : '✓ Basic' }}
                                            </p>
                                        </div>
                                        <div class="bg-white dark:bg-gray-800/50 rounded-lg p-3 transform transition-transform duration-300 hover:scale-105">
                                            <p class="text-xs text-gray-600 dark:text-gray-400 uppercase tracking-wide font-semibold">Status</p>
                                            <div class="mt-1">
                                                <p class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 shadow-sm">
                                                    <span class="inline-block w-2 h-2 mr-2 rounded-full bg-green-600 dark:bg-green-400 animate-pulse"></span>
                                                    Active
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-4 p-4 bg-indigo-100/50 dark:bg-indigo-900/30 rounded-lg border-l-4 border-indigo-600 dark:border-indigo-400">
                                        <p class="text-sm text-indigo-900 dark:text-indigo-100">
                                            <strong>💡 Pro Tip:</strong> Purchasing a new package will add messengers to your account. Your existing messengers will be preserved.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif



                    {{-- Package Cards Grid --}}
                    <div class="mb-8">
                        {{-- Comparison Table --}}
                        <div x-show="showComparison" x-transition:enter="transition ease-out duration-300" x-transition:leave="transition ease-in duration-300" class="mb-12 max-w-5xl mx-auto">
                            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-x-auto">
                                <table class="w-full">
                                    <thead>
                                        <tr class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-gray-700 dark:to-gray-600 border-b border-gray-200 dark:border-gray-700">
                                            <th class="px-6 py-4 text-left text-sm font-bold text-gray-900 dark:text-white">Feature</th>
                                            @foreach($pricingTiers as $tier)
                                                <th class="px-6 py-4 text-center text-sm font-bold {{ $tier->name === 'Premium' ? 'text-purple-700 dark:text-purple-300' : 'text-blue-700 dark:text-blue-300' }}">
                                                    {{ $tier->name }}
                                                </th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                            <td class="px-6 py-4 text-sm font-semibold text-gray-900 dark:text-white">Initial Price (6 months)</td>
                                            @foreach($pricingTiers as $tier)
                                                <td class="px-6 py-4 text-center text-sm">
                                                    <span class="text-2xl font-bold {{ $tier->name === 'Premium' ? 'text-purple-600 dark:text-purple-400' : 'text-blue-600 dark:text-blue-400' }}">GH₵ {{ number_format($tier->initial_price, 2) }}</span>
                                                </td>
                                            @endforeach
                                        </tr>
                                        <tr class="border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                            <td class="px-6 py-4 text-sm font-semibold text-gray-900 dark:text-white">Reduced Price (after 6 months)</td>
                                            @foreach($pricingTiers as $tier)
                                                <td class="px-6 py-4 text-center text-sm">
                                                    <span class="text-2xl font-bold text-green-600 dark:text-green-400">GH₵ {{ number_format($tier->subsequent_price, 2) }}</span>
                                                </td>
                                            @endforeach
                                        </tr>
                                        <tr class="border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors bg-blue-50/50 dark:bg-blue-900/10">
                                            <td class="px-6 py-4 text-sm font-semibold text-gray-900 dark:text-white">Monthly Tokens</td>
                                            @foreach($pricingTiers as $tier)
                                                <td class="px-6 py-4 text-center text-sm">
                                                    <span class="text-2xl font-bold {{ $tier->name === 'Premium' ? 'text-purple-600 dark:text-purple-400' : 'text-blue-600 dark:text-blue-400' }}">{{ number_format($tier->monthly_token_limit) }}</span>
                                                </td>
                                            @endforeach
                                        </tr>
                                        <tr class="border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                            <td class="px-6 py-4 text-sm font-semibold text-gray-900 dark:text-white">Billing</td>
                                            @foreach($pricingTiers as $tier)
                                                <td class="px-6 py-4 text-center text-sm">
                                                    <span class="text-sm font-medium text-gray-600 dark:text-gray-300">Monthly Recurring</span>
                                                </td>
                                            @endforeach
                                        </tr>
                                        <tr class="border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                            <td class="px-6 py-4 text-sm font-semibold text-gray-900 dark:text-white">Auto Reset</td>
                                            @foreach($pricingTiers as $tier)
                                                <td class="px-6 py-4 text-center">
                                                    <svg class="w-6 h-6 text-green-500 mx-auto" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                    </svg>
                                                </td>
                                            @endforeach
                                        </tr>
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                            <td class="px-6 py-4 text-sm font-semibold text-gray-900 dark:text-white">Cancel Anytime</td>
                                            @foreach($pricingTiers as $tier)
                                                <td class="px-6 py-4 text-center">
                                                    <svg class="w-6 h-6 text-green-500 mx-auto" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                    </svg>
                                                </td>
                                            @endforeach
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- Value Indicators --}}
                        @if($pricingTiers->count() > 1)
                            <div class="mt-8 grid md:grid-cols-{{ $pricingTiers->count() }} gap-4 max-w-4xl mx-auto">
                            </div>
                        @endif
                        
                        {{-- Step 1: Tier Selection --}}
                        <div class="mb-12" x-data="{ selectedTier: null, selectedMonths: 1 }">
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Step 1: Choose Your Tier</h2>
                            <div class="grid md:grid-cols-2 gap-6 max-w-4xl mx-auto mb-12">

                                {{-- Paid Pricing Tiers (Monthly Incremental Pricing) --}}
                                @if($pricingTiers && $pricingTiers->count() > 0)
                                    @foreach($pricingTiers as $tier)
                                        @php
                                            $isPremium = $tier->name === 'Premium';
                                            $isBasic = $tier->name === 'Basic';
                                        @endphp
                                        <button
                                            @click="selectedTier = {{ $tier->id }}"
                                            type="button"
                                            class="text-left h-full group/form transition-all duration-300 border-2 {{ $isPremium ? 'border-purple-200 dark:border-purple-700' : 'border-blue-200 dark:border-blue-700' }} rounded-2xl shadow-lg hover:shadow-2xl overflow-hidden transform hover:scale-105 hover:-translate-y-1 animate-fadeInUp {{ $isPremium ? 'ring-2 ring-purple-400 ring-offset-2 dark:ring-offset-gray-900' : '' }}" style="animation-delay: {{ $loop->index === 0 ? '0.2s' : '0.3s' }};" :class="{ 
                                                'bg-gradient-to-br {{ $isPremium ? 'from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20' : 'from-blue-50 to-cyan-50 dark:from-blue-900/20 dark:to-cyan-900/20' }}': selectedTier === {{ $tier->id }},
                                                'bg-white dark:bg-gray-800': selectedTier !== {{ $tier->id }}
                                            }">
                                            {{-- Premium Badge --}}
                                            @if($isPremium)
                                                <div class="absolute -top-3 left-1/2 transform -translate-x-1/2 z-50">
                                                    <span class="inline-flex items-center gap-1 px-3 py-1 bg-gradient-to-r from-purple-500 to-pink-600 text-white text-xs font-bold rounded-full shadow-lg animate-pulse">
                                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                        </svg>
                                                        RECOMMENDED
                                                    </span>
                                                </div>
                                            @endif

                                            {{-- Header Badge --}}
                                            <div class="bg-gradient-to-r rounded-t-2xl {{ $isPremium ? 'from-purple-500 to-pink-600' : 'from-blue-500 to-cyan-600' }} text-white text-center py-3 px-4 text-xs font-bold uppercase tracking-widest relative overflow-hidden">
                                                <div class="absolute inset-0 bg-white/10 animate-pulse"></div>
                                                <span class="relative">{{ $isPremium ? '👑 Premium Plan' : '⭐ Basic Plan' }}</span>
                                            </div>

                                            {{-- Content --}}
                                            <div class="p-6 flex flex-col flex-1 relative">
                                                {{-- Selection Indicator --}}
                                                <div class="absolute top-4 right-4">
                                                    <div :class="{ 'opacity-100': selectedTier === {{ $tier->id }}, 'opacity-0': selectedTier !== {{ $tier->id }} }" class="transition-opacity duration-300">
                                                        <svg class="w-6 h-6 {{ $isPremium ? 'text-purple-600' : 'text-blue-600' }}" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                        </svg>
                                                    </div>
                                                </div>

                                                {{-- Title & Description --}}
                                                <div class="mb-6">
                                                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                                                        {{ $tier->name }}
                                                    </h3>
                                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                                        {{ $tier->description }}
                                                    </p>
                                                </div>

                                                {{-- Pricing Structure --}}
                                                <div class="mb-6 space-y-3">
                                                    {{-- Initial Period --}}
                                                    <div class="p-4 bg-gradient-to-br {{ $isPremium ? 'from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20' : 'from-blue-50 to-cyan-50 dark:from-blue-900/20 dark:to-cyan-900/20' }} rounded-xl border {{ $isPremium ? 'border-purple-200 dark:border-purple-700' : 'border-blue-200 dark:border-blue-700' }}">
                                                        <div class="flex justify-between items-start mb-2">
                                                            <div>
                                                                <p class="text-xs font-bold text-gray-600 dark:text-gray-400 uppercase tracking-wide">First 6 Months</p>
                                                                <p class="text-2xl font-bold {{ $isPremium ? 'text-purple-600 dark:text-purple-400' : 'text-blue-600 dark:text-blue-400' }} mt-1">GH₵ {{ number_format($tier->initial_price, 2) }}/month</p>
                                                            </div>
                                                            <span class="inline-block px-2 py-1 bg-{{ $isPremium ? 'purple' : 'blue' }}-200 dark:bg-{{ $isPremium ? 'purple' : 'blue' }}-800 rounded text-xs font-semibold text-{{ $isPremium ? 'purple' : 'blue' }}-700 dark:text-{{ $isPremium ? 'purple' : 'blue' }}-200">Intro</span>
                                                        </div>
                                                    </div>

                                                    {{-- Subsequent Period --}}
                                                    <div class="p-4 bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-xl border border-green-200 dark:border-green-700">
                                                        <div class="flex justify-between items-start mb-2">
                                                            <div>
                                                                <p class="text-xs font-bold text-gray-600 dark:text-gray-400 uppercase tracking-wide">After 6 Months</p>
                                                                <p class="text-2xl font-bold text-green-600 dark:text-green-400 mt-1">GH₵ {{ number_format($tier->subsequent_price, 2) }}/month</p>
                                                            </div>
                                                            <span class="inline-block px-2 py-1 bg-green-200 dark:bg-green-800 rounded text-xs font-semibold text-green-700 dark:text-green-200">Reduced</span>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- Monthly Tokens --}}
                                                <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-xl border border-blue-200 dark:border-blue-800">
                                                    <p class="text-xs text-gray-600 dark:text-gray-400 uppercase tracking-wide font-semibold">Monthly Token Allowance</p>
                                                    <p class="text-3xl font-bold text-blue-600 dark:text-blue-400 mt-1">
                                                        {{ number_format($tier->monthly_token_limit) }}
                                                    </p>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2 font-medium">
                                                        🔄 Tokens reset on the 1st of each month
                                                    </p>
                                                </div>

                                                {{-- Key Features --}}
                                                <ul class="space-y-3 mb-6 flex-1">
                                                    <li class="flex items-start gap-3 text-sm transform transition-transform duration-200 hover:translate-x-1">
                                                        <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                        </svg>
                                                        <span class="text-gray-700 dark:text-gray-300">{{ $tier->monthly_token_limit / 1000 }}K tokens per month</span>
                                                    </li>
                                                    <li class="flex items-start gap-3 text-sm transform transition-transform duration-200 hover:translate-x-1">
                                                        <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                        </svg>
                                                        <span class="text-gray-700 dark:text-gray-300">Monthly pricing reset</span>
                                                    </li>
                                                    <li class="flex items-start gap-3 text-sm transform transition-transform duration-200 hover:translate-x-1">
                                                        <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                        </svg>
                                                        <span class="text-gray-700 dark:text-gray-300">Automatic price reduction</span>
                                                    </li>
                                                    <li class="flex items-start gap-3 text-sm transform transition-transform duration-200 hover:translate-x-1">
                                                        <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                        </svg>
                                                        <span class="text-gray-700 dark:text-gray-300">Cancel anytime</span>
                                                    </li>
                                                </ul>
                                            </div>
                                        </button>
                                    @endforeach
                                @else
                                    <div class="col-span-full text-center py-12">
                                        <p class="text-gray-600 dark:text-gray-400 text-lg">Pricing tiers are not available at the moment. Please try again later.</p>
                                    </div>
                                @endif
                            </div>

                            {{-- Step 2: Duration Selection --}}
                            <div x-show="selectedTier" x-transition class="mb-12 max-w-4xl mx-auto">
                                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Step 2: Select Duration (Months)</h2>
                                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 p-8">
                                    <p class="text-gray-600 dark:text-gray-400 mb-6">How many months would you like to subscribe for?</p>
                                    
                                    {{-- Month Options Grid --}}
                                    <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-3 mb-8">
                                        @for ($m = 1; $m <= 12; $m++)
                                            <button
                                                @click="selectedMonths = {{ $m }}"
                                                type="button"
                                                class="py-3 px-2 rounded-lg font-semibold text-sm transition-all duration-300 border-2"
                                                :class="{
                                                    'bg-blue-600 dark:bg-blue-700 text-white border-blue-600 dark:border-blue-700': selectedMonths === {{ $m }},
                                                    'bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white border-gray-200 dark:border-gray-600 hover:border-blue-400': selectedMonths !== {{ $m }}
                                                }">
                                                {{ $m }}mo
                                            </button>
                                        @endfor
                                    </div>

                                    {{-- Price Calculation --}}
                                    <template x-if="selectedTier">
                                        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-xl p-6 border border-blue-200 dark:border-blue-700">
                                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Price Breakdown</h3>
                                            <div class="space-y-3">
                                                <div class="flex justify-between items-center pb-3 border-b border-blue-200 dark:border-blue-700">
                                                    <span class="text-gray-600 dark:text-gray-400">Selected Duration</span>
                                                    <span class="font-semibold text-gray-900 dark:text-white" x-text="`${selectedMonths} month(s)`"></span>
                                                </div>
                                                <div class="flex justify-between items-center pb-3 border-b border-blue-200 dark:border-blue-700">
                                                    <span class="text-gray-600 dark:text-gray-400">Monthly Rate</span>
                                                    <span class="font-semibold text-gray-900 dark:text-white" x-text="selectedTier === 1 ? 'GH₵ {{ number_format($pricingTiers->first()->initial_price, 2) }}' : 'GH₵ {{ number_format($pricingTiers->last()->initial_price, 2) }}'"></span>
                                                </div>
                                                <div class="flex justify-between items-center pt-3">
                                                    <span class="text-lg font-bold text-gray-900 dark:text-white">Total Cost</span>
                                                    <span class="text-2xl font-bold text-blue-600 dark:text-blue-400" x-text="`GH₵ ${(selectedMonths * (selectedTier === 1 ? {{ $pricingTiers->first()->initial_price }} : {{ $pricingTiers->last()->initial_price }})).toFixed(2)}`"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </template>

                                    {{-- Continue to Payment Button --}}
                                    <div class="mt-8">
                                        <form action="{{ route('token-subscriptions.checkout') }}" method="POST" x-show="selectedTier">
                                            @csrf
                                            <input type="hidden" name="pricing_tier_id" :value="selectedTier">
                                            <input type="hidden" name="months" :value="selectedMonths">
                                            <button
                                                type="submit"
                                                class="w-full py-4 px-6 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold rounded-lg transition-all duration-300 transform hover:scale-105 shadow-lg flex items-center justify-center gap-2">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                                </svg>
                                                Proceed to Payment
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Error Message --}}
                        @error('package_id')
                        <div class="mt-6 p-4 bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-200 dark:border-red-800 max-w-2xl mx-auto animate-shake">
                            <p class="text-red-700 dark:text-red-300 text-sm font-medium flex items-center gap-2">
                                <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                                <strong>Error:</strong> {{ $message }}
                            </p>
                        </div>
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                // Trigger error notification
                                const event = new CustomEvent('showNotification', {
                                    detail: {
                                        message: '{{ $message }}',
                                        type: 'error'
                                    }
                                });
                                document.dispatchEvent(event);
                            });
                        </script>
                        @enderror
                    </div>

                    {{-- Token Usage Guide --}}
                    <div class="mb-12 bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/10 dark:to-indigo-900/10 border border-blue-200 dark:border-blue-800 rounded-2xl p-8 animate-fadeIn" style="animation-delay: 0.5s;">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-12 h-12 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-lg flex items-center justify-center shadow-lg">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-white">
                                How Messengers Work
                            </h3>
                        </div>
                        <p class="text-gray-700 dark:text-gray-300 mb-8 max-w-3xl text-base leading-relaxed">
                            Messengers are tokens consumed when using Messenger features. The amount depends on the complexity and length of your requests. More complex queries naturally consume more messengers.
                        </p>
                        <div class="grid md:grid-cols-3 gap-6">
                            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-blue-100 dark:border-blue-900 hover:shadow-lg hover:border-blue-300 dark:hover:border-blue-700 transition-all duration-300 transform hover:-translate-y-1">
                                <div class="flex items-center gap-3 mb-4">
                                    <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                                        </svg>
                                    </div>
                                    <h4 class="font-semibold text-gray-900 dark:text-white text-lg">Chat Messages</h4>
                                </div>
                                <p class="text-gray-600 dark:text-gray-400">
                                    <strong class="text-purple-600 dark:text-purple-400">100-500</strong> messengers per conversation
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-500 mt-2">Quick responses and short queries</p>
                            </div>
                            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-blue-100 dark:border-blue-900 hover:shadow-lg hover:border-blue-300 dark:hover:border-blue-700 transition-all duration-300 transform hover:-translate-y-1">
                                <div class="flex items-center gap-3 mb-4">
                                    <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </div>
                                    <h4 class="font-semibold text-gray-900 dark:text-white text-lg">Quiz Generation</h4>
                                </div>
                                <p class="text-gray-600 dark:text-gray-400">
                                    <strong class="text-green-600 dark:text-green-400">500-2,000</strong> messengers per quiz
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-500 mt-2">Depends on number of questions</p>
                            </div>
                            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-blue-100 dark:border-blue-900 hover:shadow-lg hover:border-blue-300 dark:hover:border-blue-700 transition-all duration-300 transform hover:-translate-y-1">
                                <div class="flex items-center gap-3 mb-4">
                                    <div class="w-10 h-10 bg-orange-100 dark:bg-orange-900/30 rounded-lg flex items-center justify-center">
                                        <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    <h4 class="font-semibold text-gray-900 dark:text-white text-lg">Content Analysis</h4>
                                </div>
                                <p class="text-gray-600 dark:text-gray-400">
                                    <strong class="text-orange-600 dark:text-orange-400">200-1,000</strong> messengers per document
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-500 mt-2">Based on document length</p>
                            </div>
                        </div>
                    </div>

                    {{-- FAQ Section --}}
                    <div class="mb-8 animate-fadeIn" style="animation-delay: 0.6s;">
                        <h3 class="text-3xl font-bold text-gray-900 dark:text-white mb-8 text-center">
                            Frequently Asked Questions
                        </h3>
                        <div class="max-w-4xl mx-auto" x-data="{ openFaq: null }" @keydown.escape="openFaq = null">
                            <div class="space-y-4">
                                {{-- FAQ Item 1 --}}
                                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 hover:border-blue-300 dark:hover:border-blue-700 overflow-hidden transition-all duration-300 shadow-sm hover:shadow-md">
                                    <button
                                        @click="openFaq === 1 ? openFaq = null : openFaq = 1"
                                        class="w-full flex items-center justify-between p-6 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors"
                                        :class="{ 'bg-blue-50 dark:bg-blue-900/20': openFaq === 1 }">
                                        <div class="flex items-start gap-4 text-left">
                                            <div class="flex items-center justify-center w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-400 flex-shrink-0 font-bold text-sm mt-0.5">
                                                Q
                                            </div>
                                            <h4 class="font-semibold text-gray-900 dark:text-white text-lg">
                                                Do messengers expire?
                                            </h4>
                                        </div>
                                        <svg class="w-6 h-6 text-gray-600 dark:text-gray-400 flex-shrink-0 transition-transform duration-300" :class="{ 'rotate-180': openFaq === 1 }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                                        </svg>
                                    </button>
                                    <div x-show="openFaq === 1" x-transition class="px-6 pb-6">
                                        <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
                                            No! Your purchased messengers never expire and can be used anytime. Trial messengers last for 7 days from activation.
                                        </p>
                                    </div>
                                </div>

                                {{-- FAQ Item 2 --}}
                                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 hover:border-blue-300 dark:hover:border-blue-700 overflow-hidden transition-all duration-300 shadow-sm hover:shadow-md">
                                    <button
                                        @click="openFaq === 2 ? openFaq = null : openFaq = 2"
                                        class="w-full flex items-center justify-between p-6 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors"
                                        :class="{ 'bg-blue-50 dark:bg-blue-900/20': openFaq === 2 }">
                                        <div class="flex items-start gap-4 text-left">
                                            <div class="flex items-center justify-center w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-400 flex-shrink-0 font-bold text-sm mt-0.5">
                                                Q
                                            </div>
                                            <h4 class="font-semibold text-gray-900 dark:text-white text-lg">
                                                Can I upgrade my package?
                                            </h4>
                                        </div>
                                        <svg class="w-6 h-6 text-gray-600 dark:text-gray-400 flex-shrink-0 transition-transform duration-300" :class="{ 'rotate-180': openFaq === 2 }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                                        </svg>
                                    </button>
                                    <div x-show="openFaq === 2" x-transition class="px-6 pb-6">
                                        <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
                                            Absolutely! You can purchase additional messenger packages at any time. Your new messengers will be added to your existing balance, and you'll maintain all previously purchased messengers.
                                        </p>
                                    </div>
                                </div>

                                {{-- FAQ Item 3 --}}
                                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 hover:border-blue-300 dark:hover:border-blue-700 overflow-hidden transition-all duration-300 shadow-sm hover:shadow-md">
                                    <button
                                        @click="openFaq === 3 ? openFaq = null : openFaq = 3"
                                        class="w-full flex items-center justify-between p-6 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors"
                                        :class="{ 'bg-blue-50 dark:bg-blue-900/20': openFaq === 3 }">
                                        <div class="flex items-start gap-4 text-left">
                                            <div class="flex items-center justify-center w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-400 flex-shrink-0 font-bold text-sm mt-0.5">
                                                Q
                                            </div>
                                            <h4 class="font-semibold text-gray-900 dark:text-white text-lg">
                                                What's the difference between plans?
                                            </h4>
                                        </div>
                                        <svg class="w-6 h-6 text-gray-600 dark:text-gray-400 flex-shrink-0 transition-transform duration-300" :class="{ 'rotate-180': openFaq === 3 }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                                        </svg>
                                    </button>
                                    <div x-show="openFaq === 3" x-transition class="px-6 pb-6">
                                        <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
                                            Both plans give you the same features and functionality. The difference is in the messenger count and price. The Premium plan offers more messengers for better value and longer usage.
                                        </p>
                                    </div>
                                </div>

                                {{-- FAQ Item 4 --}}
                                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 hover:border-blue-300 dark:hover:border-blue-700 overflow-hidden transition-all duration-300 shadow-sm hover:shadow-md">
                                    <button
                                        @click="openFaq === 4 ? openFaq = null : openFaq = 4"
                                        class="w-full flex items-center justify-between p-6 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors"
                                        :class="{ 'bg-blue-50 dark:bg-blue-900/20': openFaq === 4 }">
                                        <div class="flex items-start gap-4 text-left">
                                            <div class="flex items-center justify-center w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-400 flex-shrink-0 font-bold text-sm mt-0.5">
                                                Q
                                            </div>
                                            <h4 class="font-semibold text-gray-900 dark:text-white text-lg">
                                                Is my payment secure?
                                            </h4>
                                        </div>
                                        <svg class="w-6 h-6 text-gray-600 dark:text-gray-400 flex-shrink-0 transition-transform duration-300" :class="{ 'rotate-180': openFaq === 4 }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                                        </svg>
                                    </button>
                                    <div x-show="openFaq === 4" x-transition class="px-6 pb-6">
                                        <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
                                            Yes! We use Paystack, a PCI-DSS compliant payment processor. Your payment information is fully encrypted, secure, and never stored on our servers.
                                        </p>
                                    </div>
                                </div>

                                {{-- FAQ Item 5 --}}
                                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 hover:border-blue-300 dark:hover:border-blue-700 overflow-hidden transition-all duration-300 shadow-sm hover:shadow-md">
                                    <button
                                        @click="openFaq === 5 ? openFaq = null : openFaq = 5"
                                        class="w-full flex items-center justify-between p-6 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors"
                                        :class="{ 'bg-blue-50 dark:bg-blue-900/20': openFaq === 5 }">
                                        <div class="flex items-start gap-4 text-left">
                                            <div class="flex items-center justify-center w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-400 flex-shrink-0 font-bold text-sm mt-0.5">
                                                Q
                                            </div>
                                            <h4 class="font-semibold text-gray-900 dark:text-white text-lg">
                                                What happens if I run out of messengers?
                                            </h4>
                                        </div>
                                        <svg class="w-6 h-6 text-gray-600 dark:text-gray-400 flex-shrink-0 transition-transform duration-300" :class="{ 'rotate-180': openFaq === 5 }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                                        </svg>
                                    </button>
                                    <div x-show="openFaq === 5" x-transition class="px-6 pb-6">
                                        <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
                                            You can purchase more messengers anytime! Simply return to this page and buy another package. We'll notify you when your messengers are running low.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes shake {
            0%, 100% {
                transform: translateX(0);
            }
            25% {
                transform: translateX(-5px);
            }
            75% {
                transform: translateX(5px);
            }
        }

        .animate-fadeIn {
            animation: fadeIn 0.6s ease-in-out forwards;
            opacity: 0;
        }

        .animate-fadeInDown {
            animation: fadeInDown 0.7s ease-out forwards;
            opacity: 0;
        }

        .animate-fadeInUp {
            animation: fadeInUp 0.6s ease-out forwards;
            opacity: 0;
        }

        .animate-slideIn {
            animation: slideIn 0.6s ease-out forwards;
            opacity: 0;
        }

        .animate-shake {
            animation: shake 0.5s ease-in-out;
        }

        /* Smooth scrolling */
        html {
            scroll-behavior: smooth;
        }

        /* Enhanced button states */
        button[type="submit"]:active {
            transform: scale(0.98);
        }

        /* Smooth hover transitions for cards */
        .group\/form {
            transition: all 0.3s ease-out;
        }

        /* Animation for badge pulse */
        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.7;
            }
        }

        .animate-pulse {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        /* Enhanced focus states for accessibility */
        @media (prefers-reduced-motion: reduce) {
            * {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }

        /* Dark mode transitions */
        @media (prefers-color-scheme: dark) {
            .dark {
                transition: background-color 0.3s ease, border-color 0.3s ease, color 0.3s ease;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle notification display
            document.addEventListener('showNotification', function(e) {
                const message = e.detail?.message || 'An action was performed';
                const type = e.detail?.type || 'info';
                
                // Find Alpine component and update it
                const section = document.querySelector('[x-data*="showNotification"]');
                if (section && section.__x) {
                    section.__x.$data.notificationMessage = message;
                    section.__x.$data.notificationType = type;
                    section.__x.$data.showNotification = true;
                    
                    // Auto-hide after 5 seconds
                    setTimeout(() => {
                        section.__x.$data.showNotification = false;
                    }, 5000);
                }
            });

            // Track form submissions
            document.querySelectorAll('form[x-data*="submitting"]').forEach(form => {
                form.addEventListener('submit', function() {
                    // Add visual feedback
                    const button = this.querySelector('button[type="submit"]');
                    if (button) {
                        button.style.opacity = '0.7';
                    }
                });
            });
        });
    </script>
</x-layouts.app>
