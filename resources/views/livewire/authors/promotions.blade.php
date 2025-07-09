<div class="min-h-screen bg-gradient-to-br from-purple-50 via-pink-50 to-indigo-50 dark:from-gray-900 dark:via-purple-900/10 dark:to-indigo-900/10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <!-- Enhanced Header Section -->
        <div class="relative mb-8 overflow-hidden rounded-3xl">
            <div class="absolute inset-0 bg-gradient-to-r from-purple-600 via-pink-600 to-indigo-600 opacity-90"></div>
            <div class="absolute inset-0 bg-black opacity-20"></div>
            <div class="absolute inset-0" style="background-image: url('data:image/svg+xml;charset=utf-8,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'100\' height=\'100\' viewBox=\'0 0 100 100\'%3E%3Cg fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'0.08\'%3E%3Cpath d=\'M50 50c13.807 0 25-11.193 25-25S63.807 0 50 0 25 11.193 25 25s11.193 25 25 25zm0 25c13.807 0 25-11.193 25-25S63.807 50 50 50s-25 11.193-25 25 11.193 25 25 25z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')"></div>

            <div class="relative px-8 py-12">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                    <div class="mb-6 lg:mb-0">
                        <div class="flex items-center mb-4">
                            <div class="p-3 bg-white/20 rounded-full backdrop-blur-sm mr-4">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                                </svg>
                            </div>
                            <div>
                                <h1 class="text-4xl lg:text-5xl font-bold text-white mb-2">Promotions & Marketing</h1>
                                <p class="text-purple-100 text-lg">Boost your book sales with powerful promotional campaigns</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center space-x-4">
                        <button wire:click="openCreateModal"
                                class="inline-flex items-center px-6 py-3 border border-white/30 text-white bg-white/20 hover:bg-white/30 rounded-xl font-semibold transition-all duration-300 backdrop-blur-sm">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Create Campaign
                        </button>
                    </div>
                </div>

                <!-- Enhanced Stats Cards -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-8">
                    <div class="bg-white/20 backdrop-blur-sm rounded-xl p-4 border border-white/30">
                        <div class="text-center">
                            <p class="text-2xl font-bold text-white">${{ number_format($promotionStats['total_revenue'], 2) }}</p>
                            <p class="text-purple-100 text-sm">Total Revenue</p>
                        </div>
                    </div>
                    <div class="bg-white/20 backdrop-blur-sm rounded-xl p-4 border border-white/30">
                        <div class="text-center">
                            <p class="text-2xl font-bold text-white">{{ $promotionStats['active_campaigns'] }}</p>
                            <p class="text-purple-100 text-sm">Active Campaigns</p>
                        </div>
                    </div>
                    <div class="bg-white/20 backdrop-blur-sm rounded-xl p-4 border border-white/30">
                        <div class="text-center">
                            <p class="text-2xl font-bold text-white">{{ number_format($promotionStats['average_conversion_rate'], 1) }}%</p>
                            <p class="text-purple-100 text-sm">Conversion Rate</p>
                        </div>
                    </div>
                    <div class="bg-white/20 backdrop-blur-sm rounded-xl p-4 border border-white/30">
                        <div class="text-center">
                            <p class="text-2xl font-bold text-white">{{ number_format($promotionStats['roi'], 1) }}%</p>
                            <p class="text-purple-100 text-sm">ROI</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alert Messages -->
        @if (session()->has('message'))
            <div class="mb-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 text-green-800 dark:text-green-200 px-4 py-3 rounded-xl shadow-sm">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    {{ session('message') }}
                </div>
            </div>
        @endif

        <!-- Tab Navigation -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 mb-8 overflow-hidden">
            <div class="flex flex-wrap border-b border-gray-200 dark:border-gray-700">
                <button wire:click="setActiveTab('campaigns')"
                        class="flex-1 px-6 py-4 text-sm font-medium transition-all duration-200 {{ $activeTab === 'campaigns' ? 'bg-purple-50 dark:bg-purple-900/20 text-purple-700 dark:text-purple-300 border-b-2 border-purple-500' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                    <div class="flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                        </svg>
                        Campaigns
                    </div>
                </button>

                <button wire:click="setActiveTab('analytics')"
                        class="flex-1 px-6 py-4 text-sm font-medium transition-all duration-200 {{ $activeTab === 'analytics' ? 'bg-purple-50 dark:bg-purple-900/20 text-purple-700 dark:text-purple-300 border-b-2 border-purple-500' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                    <div class="flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        Analytics
                    </div>
                </button>

                <button wire:click="setActiveTab('tools')"
                        class="flex-1 px-6 py-4 text-sm font-medium transition-all duration-200 {{ $activeTab === 'tools' ? 'bg-purple-50 dark:bg-purple-900/20 text-purple-700 dark:text-purple-300 border-b-2 border-purple-500' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                    <div class="flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Marketing Tools
                    </div>
                </button>

                <button wire:click="setActiveTab('social')"
                        class="flex-1 px-6 py-4 text-sm font-medium transition-all duration-200 {{ $activeTab === 'social' ? 'bg-purple-50 dark:bg-purple-900/20 text-purple-700 dark:text-purple-300 border-b-2 border-purple-500' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                    <div class="flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h4a1 1 0 011 1v2h4a1 1 0 011 1v4a1 1 0 01-1 1h-1v6a2 2 0 01-2 2H6a2 2 0 01-2-2V8H3a1 1 0 01-1-1V3a1 1 0 011-1h4z"/>
                        </svg>
                        Social Media
                    </div>
                </button>
            </div>
        </div>

        <!-- Campaigns Tab -->
        @if($activeTab === 'campaigns')
            <div class="space-y-8">
                <!-- Filters -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Search Campaigns</label>
                            <input wire:model.live.debounce.300ms="searchTerm" type="text"
                                   placeholder="Search campaigns..."
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 dark:bg-gray-700 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                            <select wire:model.live="statusFilter"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 dark:bg-gray-700 dark:text-white">
                                <option value="all">All Status</option>
                                <option value="active">Active</option>
                                <option value="scheduled">Scheduled</option>
                                <option value="paused">Paused</option>
                                <option value="expired">Expired</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Type</label>
                            <select wire:model.live="typeFilter"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 dark:bg-gray-700 dark:text-white">
                                <option value="all">All Types</option>
                                <option value="discount">Discount</option>
                                <option value="free_shipping">Free Shipping</option>
                                <option value="bundle">Bundle</option>
                                <option value="buy_x_get_y">Buy X Get Y</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Campaigns Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    @foreach($campaigns as $campaign)
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-2xl transition-all duration-300">
                            <!-- Campaign Header -->
                            <div class="bg-gradient-to-r from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="p-2 bg-purple-100 dark:bg-purple-900/50 rounded-lg">
                                            @if($campaign['type'] === 'discount')
                                                <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2zM10 8.5a.5.5 0 11-1 0 .5.5 0 011 0zm5 5a.5.5 0 11-1 0 .5.5 0 011 0z"/>
                                                </svg>
                                            @elseif($campaign['type'] === 'free_shipping')
                                                <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                                </svg>
                                            @elseif($campaign['type'] === 'bundle')
                                                <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                                </svg>
                                            @else
                                                <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12z"/>
                                                </svg>
                                            @endif
                                        </div>
                                        <div>
                                            <h3 class="font-semibold text-gray-900 dark:text-white">{{ $campaign['title'] }}</h3>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $campaign['promo_code'] }}</p>
                                        </div>
                                    </div>

                                    @php
                                        $statusConfig = [
                                            'active' => ['bg' => 'bg-green-100 text-green-800', 'icon' => '●'],
                                            'scheduled' => ['bg' => 'bg-blue-100 text-blue-800', 'icon' => '⏰'],
                                            'paused' => ['bg' => 'bg-yellow-100 text-yellow-800', 'icon' => '⏸'],
                                            'expired' => ['bg' => 'bg-red-100 text-red-800', 'icon' => '●']
                                        ];
                                        $config = $statusConfig[$campaign['status']] ?? ['bg' => 'bg-gray-100 text-gray-800', 'icon' => '●'];
                                    @endphp

                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $config['bg'] }}">
                                        {{ $config['icon'] }} {{ ucfirst($campaign['status']) }}
                                    </span>
                                </div>
                            </div>

                            <!-- Campaign Content -->
                            <div class="p-6">
                                <p class="text-gray-600 dark:text-gray-400 mb-4">{{ $campaign['description'] }}</p>

                                <!-- Campaign Details -->
                                <div class="grid grid-cols-2 gap-4 mb-4">
                                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3">
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm text-gray-600 dark:text-gray-400">Usage</span>
                                            <span class="text-sm font-semibold text-gray-900 dark:text-white">
                                                {{ $campaign['usage_count'] }}/{{ $campaign['max_usage'] }}
                                            </span>
                                        </div>
                                        <div class="mt-2 w-full bg-gray-200 dark:bg-gray-600 rounded-full h-2">
                                            <div class="bg-purple-500 h-2 rounded-full" style="width: {{ ($campaign['usage_count'] / $campaign['max_usage']) * 100 }}%"></div>
                                        </div>
                                    </div>

                                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3">
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm text-gray-600 dark:text-gray-400">Revenue</span>
                                            <span class="text-sm font-semibold text-gray-900 dark:text-white">
                                                ${{ number_format($campaign['revenue_generated'], 2) }}
                                            </span>
                                        </div>
                                        <div class="mt-2 flex items-center">
                                            <svg class="w-4 h-4 text-green-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                            </svg>
                                            <span class="text-xs text-green-600 dark:text-green-400">{{ $campaign['conversion_rate'] }}% conversion</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Campaign Timeline -->
                                <div class="flex items-center justify-between text-sm text-gray-600 dark:text-gray-400 mb-4">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        <span>{{ $campaign['start_date']->format('M j') }} - {{ $campaign['end_date']->format('M j, Y') }}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                                        </svg>
                                        <span>Budget: ${{ number_format($campaign['budget'], 2) }}</span>
                                    </div>
                                </div>

                                <!-- Campaign Actions -->
                                <div class="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-gray-600">
                                    <div class="flex space-x-2">
                                        <button wire:click="openEditModal({{ $campaign['id'] }})"
                                                class="text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 font-medium text-sm">
                                            Edit
                                        </button>
                                        <button wire:click="duplicateCampaign({{ $campaign['id'] }})"
                                                class="text-purple-600 hover:text-purple-700 dark:text-purple-400 dark:hover:text-purple-300 font-medium text-sm">
                                            Duplicate
                                        </button>
                                        @if($campaign['status'] === 'active')
                                            <button wire:click="pauseCampaign({{ $campaign['id'] }})"
                                                    class="text-yellow-600 hover:text-yellow-700 dark:text-yellow-400 dark:hover:text-yellow-300 font-medium text-sm">
                                                Pause
                                            </button>
                                        @elseif($campaign['status'] === 'paused')
                                            <button wire:click="resumeCampaign({{ $campaign['id'] }})"
                                                    class="text-green-600 hover:text-green-700 dark:text-green-400 dark:hover:text-green-300 font-medium text-sm">
                                                Resume
                                            </button>
                                        @endif
                                    </div>
                                    <button wire:click="deleteCampaign({{ $campaign['id'] }})"
                                            class="text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 font-medium text-sm">
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Analytics Tab -->
        @if($activeTab === 'analytics')
            <div class="space-y-8">
                <!-- Performance Overview -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Top Performing Campaigns</h3>
                        <div class="space-y-4">
                            @foreach($topPerformingCampaigns as $campaign)
                                <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <div>
                                        <p class="font-medium text-gray-900 dark:text-white">{{ $campaign['title'] }}</p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $campaign['promo_code'] }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white">${{ number_format($campaign['revenue'], 2) }}</p>
                                        <p class="text-xs text-green-600 dark:text-green-400">{{ $campaign['conversion_rate'] }}% conversion</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Revenue Trend</h3>
                        <div class="h-64 bg-gray-50 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                            <div class="text-center">
                                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                                <p class="text-gray-500 dark:text-gray-400">Chart integration placeholder</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Audience Insights -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Audience Insights</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div>
                            <h4 class="font-medium text-gray-900 dark:text-white mb-3">Audience Segments</h4>
                            <div class="space-y-2">
                                @foreach($audienceInsights['segments'] as $segment => $count)
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-600 dark:text-gray-400 capitalize">{{ str_replace('_', ' ', $segment) }}</span>
                                        <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ number_format($count) }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div>
                            <h4 class="font-medium text-gray-900 dark:text-white mb-3">Age Demographics</h4>
                            <div class="space-y-2">
                                @foreach($audienceInsights['demographics'] as $age => $percentage)
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ str_replace('_', '-', $age) }}</span>
                                        <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $percentage }}%</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div>
                            <h4 class="font-medium text-gray-900 dark:text-white mb-3">Interests</h4>
                            <div class="space-y-2">
                                @foreach($audienceInsights['interests'] as $interest => $percentage)
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-600 dark:text-gray-400 capitalize">{{ $interest }}</span>
                                        <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $percentage }}%</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Marketing Tools Tab -->
        @if($activeTab === 'tools')
            <div class="space-y-8">
                <!-- Tools Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($marketingTools as $tool)
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 p-6 hover:shadow-2xl transition-all duration-300">
                            <div class="flex items-center mb-4">
                                <div class="p-3 bg-{{ $tool['color'] }}-100 dark:bg-{{ $tool['color'] }}-900/50 rounded-lg mr-4">
                                    <svg class="w-6 h-6 text-{{ $tool['color'] }}-600 dark:text-{{ $tool['color'] }}-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        @if($tool['icon'] === 'mail')
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                        @elseif($tool['icon'] === 'calendar')
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        @elseif($tool['icon'] === 'globe')
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                                        @elseif($tool['icon'] === 'chart-bar')
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                        @elseif($tool['icon'] === 'star')
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                        @else
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                                        @endif
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <h3 class="font-semibold text-gray-900 dark:text-white">{{ $tool['name'] }}</h3>
                                    @if($tool['status'] === 'available')
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300">
                                            Available
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/50 dark:text-yellow-300">
                                            Coming Soon
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <p class="text-gray-600 dark:text-gray-400 mb-4">{{ $tool['description'] }}</p>

                            <button class="w-full bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white py-2 rounded-lg font-medium transition-all duration-300 {{ $tool['status'] === 'coming_soon' ? 'opacity-50 cursor-not-allowed' : '' }}"
                                {{ $tool['status'] === 'coming_soon' ? 'disabled' : '' }}>
                                {{ $tool['status'] === 'available' ? 'Launch Tool' : 'Notify Me' }}
                            </button>
                        </div>
                    @endforeach
                </div>

                <!-- Campaign Templates -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Campaign Templates</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        @foreach($campaignTemplates as $template)
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors cursor-pointer"
                                 wire:click="useTemplate('{{ $template['name'] }}')">
                                <div class="flex items-center justify-between mb-2">
                                    <h4 class="font-medium text-gray-900 dark:text-white">{{ $template['name'] }}</h4>
                                    <span class="text-sm text-purple-600 dark:text-purple-400 font-semibold">
                                        {{ $template['suggested_discount'] }}{{ $template['discount_type'] === 'percentage' ? '%' : '$' }} off
                                    </span>
                                </div>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">{{ $template['description'] }}</p>
                                <div class="flex items-center text-xs text-gray-500 dark:text-gray-400">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    {{ $template['duration'] }} days
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <!-- Social Media Tab -->
        @if($activeTab === 'social')
            <div class="space-y-8">
                <!-- Social Media Overview -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 p-6">
                        <div class="flex items-center">
                            <div class="p-3 bg-blue-100 dark:bg-blue-900/50 rounded-lg mr-4">
                                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($socialMetrics['total_reach']) }}</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Total Reach</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 p-6">
                        <div class="flex items-center">
                            <div class="p-3 bg-green-100 dark:bg-green-900/50 rounded-lg mr-4">
                                <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($socialMetrics['total_engagement']) }}</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Engagement</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 p-6">
                        <div class="flex items-center">
                            <div class="p-3 bg-purple-100 dark:bg-purple-900/50 rounded-lg mr-4">
                                <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($socialMetrics['total_clicks']) }}</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Clicks</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 p-6">
                        <div class="flex items-center">
                            <div class="p-3 bg-orange-100 dark:bg-orange-900/50 rounded-lg mr-4">
                                <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($socialMetrics['total_shares']) }}</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Shares</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Platform Performance -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Platform Performance</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        @foreach($socialMetrics['platforms'] as $platform => $metrics)
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="font-medium text-gray-900 dark:text-white capitalize">{{ $platform }}</h4>
                                    <div class="p-2 bg-gray-200 dark:bg-gray-600 rounded-lg">
                                        @if($platform === 'facebook')
                                            <div class="w-4 h-4 bg-blue-600 rounded"></div>
                                        @elseif($platform === 'twitter')
                                            <div class="w-4 h-4 bg-sky-500 rounded"></div>
                                        @elseif($platform === 'instagram')
                                            <div class="w-4 h-4 bg-pink-600 rounded"></div>
                                        @else
                                            <div class="w-4 h-4 bg-blue-700 rounded"></div>
                                        @endif
                                    </div>
                                </div>
                                <div class="space-y-2">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600 dark:text-gray-400">Reach</span>
                                        <span class="font-semibold text-gray-900 dark:text-white">{{ number_format($metrics['reach']) }}</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600 dark:text-gray-400">Engagement</span>
                                        <span class="font-semibold text-gray-900 dark:text-white">{{ number_format($metrics['engagement']) }}</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600 dark:text-gray-400">Clicks</span>
                                        <span class="font-semibold text-gray-900 dark:text-white">{{ number_format($metrics['clicks']) }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Create Campaign Modal -->
    @if($showCreateModal)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeCreateModal"></div>

                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                    <div class="bg-gradient-to-r from-purple-500 to-pink-500 px-6 py-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-bold text-white">Create New Campaign</h3>
                            <button wire:click="closeCreateModal" class="text-white hover:text-gray-200">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <form wire:submit.prevent="createCampaign" class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Campaign Title</label>
                                <input wire:model="campaignTitle" type="text"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 dark:bg-gray-700 dark:text-white"
                                       placeholder="Enter campaign title">
                                @error('campaignTitle') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Campaign Type</label>
                                <select wire:model="campaignType"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 dark:bg-gray-700 dark:text-white">
                                    <option value="discount">Discount</option>
                                    <option value="free_shipping">Free Shipping</option>
                                    <option value="bundle">Bundle Deal</option>
                                    <option value="buy_x_get_y">Buy X Get Y</option>
                                </select>
                                @error('campaignType') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description</label>
                                <textarea wire:model="campaignDescription" rows="3"
                                          class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 dark:bg-gray-700 dark:text-white"
                                          placeholder="Describe your campaign..."></textarea>
                                @error('campaignDescription') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Discount Type</label>
                                <select wire:model="discountType"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 dark:bg-gray-700 dark:text-white">
                                    <option value="percentage">Percentage</option>
                                    <option value="fixed">Fixed Amount</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Discount Value</label>
                                <input wire:model="discountValue" type="number" step="0.01"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 dark:bg-gray-700 dark:text-white"
                                       placeholder="Enter discount value">
                                @error('discountValue') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Start Date</label>
                                <input wire:model="startDate" type="date"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 dark:bg-gray-700 dark:text-white">
                                @error('startDate') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">End Date</label>
                                <input wire:model="endDate" type="date"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 dark:bg-gray-700 dark:text-white">
                                @error('endDate') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Promo Code</label>
                                <div class="flex">
                                    <input wire:model="promoCode" type="text"
                                           class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-l-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 dark:bg-gray-700 dark:text-white">
                                    <button type="button" wire:click="generatePromoCode"
                                            class="px-4 py-2 bg-purple-600 text-white rounded-r-lg hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500">
                                        Generate
                                    </button>
                                </div>
                                @error('promoCode') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Max Usage</label>
                                <input wire:model="maxUsage" type="number"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 dark:bg-gray-700 dark:text-white"
                                       placeholder="Leave empty for unlimited">
                                @error('maxUsage') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Target Audience</label>
                                <select wire:model="targetAudience"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 dark:bg-gray-700 dark:text-white">
                                    <option value="all">All Customers</option>
                                    <option value="subscribers">Subscribers Only</option>
                                    <option value="new_customers">New Customers</option>
                                    <option value="returning_customers">Returning Customers</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Budget (Optional)</label>
                                <input wire:model="campaignBudget" type="number" step="0.01"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 dark:bg-gray-700 dark:text-white"
                                       placeholder="Enter campaign budget">
                                @error('campaignBudget') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end space-x-3">
                            <button type="button" wire:click="closeCreateModal"
                                    class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                                Cancel
                            </button>
                            <button type="submit"
                                    class="px-6 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-medium">
                                Create Campaign
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
