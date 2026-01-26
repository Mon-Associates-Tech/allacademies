{{-- resources/views/livewire/subscribers/premium.blade.php --}}
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="text-center mb-12">
        <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">All Academies Premium</h1>
        <p class="text-xl text-gray-600 dark:text-gray-400">Unlock unlimited access to premium content and advanced features</p>
    </div>

    @if($currentPlan)
        <!-- Current Subscription Status -->
        <div class="bg-green-50 dark:bg-green-900 border border-green-200 dark:border-green-700 rounded-lg p-6 mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-green-800 dark:text-green-200">Active Premium Subscription</h2>
                    <p class="text-green-600 dark:text-green-300">
                        Your {{ $currentPlan->package }} plan expires on {{ $currentPlan->expires_at->format('M d, Y') }}
                    </p>
                </div>
                <button wire:click="cancelSubscription" 
                        class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700">
                    Cancel Subscription
                </button>
            </div>
        </div>

        <!-- Premium Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 text-center">
                <h3 class="text-2xl font-bold text-violet-600">{{ $premiumStats['books_accessed'] }}</h3>
                <p class="text-gray-600 dark:text-gray-400">Premium Books Accessed</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 text-center">
                <h3 class="text-2xl font-bold text-green-600">${{ number_format($premiumStats['money_saved'], 2) }}</h3>
                <p class="text-gray-600 dark:text-gray-400">Money Saved</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 text-center">
                <h3 class="text-2xl font-bold text-blue-600">{{ $premiumStats['days_remaining'] }}</h3>
                <p class="text-gray-600 dark:text-gray-400">Days Remaining</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 text-center">
                <h3 class="text-2xl font-bold text-yellow-600">{{ $premiumStats['reading_time'] }}h</h3>
                <p class="text-gray-600 dark:text-gray-400">Reading Time This Month</p>
            </div>
        </div>
    @else
        <!-- Premium Plans -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
            @foreach($availablePlans as $index => $plan)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden {{ $plan['popular'] ?? false ? 'ring-2 ring-violet-500' : '' }}">
                    @if($plan['popular'] ?? false)
                        <div class="bg-violet-500 text-white text-center py-2 text-sm font-medium">
                            Most Popular
                        </div>
                    @endif
                    
                    <div class="p-8">
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">{{ $plan['name'] }}</h3>
                        <div class="mb-6">
                            <span class="text-4xl font-bold text-gray-900 dark:text-white">${{ $plan['price'] }}</span>
                            <span class="text-gray-600 dark:text-gray-400">/ {{ $plan['billing'] }}</span>
                            @if(isset($plan['savings']))
                                <div class="bg-green-100 text-green-800 text-sm px-2 py-1 rounded mt-2 inline-block">
                                    Save {{ $plan['savings'] }}
                                </div>
                            @endif
                        </div>
                        
                        <ul class="space-y-3 mb-8">
                            @foreach($plan['features'] as $feature)
                                <li class="flex items-center">
                                    <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    {{ $feature }}
                                </li>
                            @endforeach
                        </ul>
                        
                        <button wire:click="subscribeToPlan({{ $index }})" 
                                class="w-full bg-violet-600 text-white py-3 px-6 rounded-lg hover:bg-violet-700 transition-colors">
                            Choose {{ $plan['name'] }}
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Feature Comparison -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden mb-8">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Feature Comparison</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Feature
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Free
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Premium
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($premiumFeatures as $feature)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                {{ $feature['feature'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                @if($feature['free'] === true)
                                    <svg class="w-5 h-5 text-green-500 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                @elseif($feature['free'] === false)
                                    <svg class="w-5 h-5 text-red-500 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                @else
                                    <span class="text-gray-600 dark:text-gray-400">{{ $feature['free'] }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                @if($feature['premium'] === true)
                                    <svg class="w-5 h-5 text-green-500 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                @else
                                    <span class="text-gray-600 dark:text-gray-400">{{ $feature['premium'] }}</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
