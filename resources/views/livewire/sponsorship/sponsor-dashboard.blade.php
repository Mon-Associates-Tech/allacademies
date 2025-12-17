<div>
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Sponsor Dashboard</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-2">Manage your sponsorship offers and review applications</p>
    </div>

    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Total Offers</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $totalOffers }}</p>
                </div>
                <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-full">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Open Offers</p>
                    <p class="text-2xl font-bold text-green-600 dark:text-green-400 mt-1">{{ $openOffers }}</p>
                </div>
                <div class="p-3 bg-green-100 dark:bg-green-900 rounded-full">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Pending Bids</p>
                    <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400 mt-1">{{ $pendingBids }}</p>
                </div>
                <div class="p-3 bg-yellow-100 dark:bg-yellow-900 rounded-full">
                    <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Total Committed</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">GHS {{ number_format($totalCommitted, 2) }}</p>
                </div>
                <div class="p-3 bg-purple-100 dark:bg-purple-900 rounded-full">
                    <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="mb-6 border-b border-gray-200 dark:border-gray-700">
        <nav class="-mb-px flex space-x-8">
            <button wire:click="$set('activeTab', 'offers')" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'offers' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400' }}">
                My Offers
            </button>
            <button wire:click="$set('activeTab', 'bids')" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'bids' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400' }}">
                Applications
                @if($pendingBids > 0)
                    <span class="ml-2 px-2 py-1 text-xs bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200 rounded-full">{{ $pendingBids }}</span>
                @endif
            </button>
        </nav>
    </div>

    @if($activeTab === 'offers')
        <!-- Offers Tab -->
        <div class="flex items-center justify-between mb-6">
            <input type="text" wire:model.live="search" placeholder="Search offers..." class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500">

            <a href="{{ route('sponsor.offers.create') }}" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                + Create Offer
            </a>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Offer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Bids</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Expires</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($offers as $offer)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $offer->title }}</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ Str::limit($offer->description, 50) }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                                GHS {{ number_format($offer->amount_offered, 2) }}
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $statusColors = [
                                        'open' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                                        'closed' => 'bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-200',
                                        'fulfilled' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                                    ];
                                @endphp
                                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $statusColors[$offer->status] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ ucfirst($offer->status) }}
                                    </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                                {{ $offer->bids_count ?? 0 }} applications
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                                {{ $offer->expires_at ? $offer->expires_at->format('M d, Y') : 'No expiration' }}
                            </td>
                            <td class="px-6 py-4 text-right text-sm font-medium space-x-2">
                                <a href="{{ route('sponsor.offers.edit', $offer) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400">Edit</a>
                                @if($offer->status === 'open')
                                    <button wire:click="closeOffer({{ $offer->id }})" class="text-gray-600 hover:text-gray-900 dark:text-gray-400">Close</button>
                                @endif
                                <button wire:click="deleteOffer({{ $offer->id }})" class="text-red-600 hover:text-red-900 dark:text-red-400">Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                No offers found. Create your first offer to start sponsoring!
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            @if($offers->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $offers->links() }}
                </div>
            @endif
        </div>
    @else
        <!-- Bids Tab -->
        <div class="mb-6">
            <input type="text" wire:model.live="search" placeholder="Search applications..." class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500">
        </div>

        <div class="space-y-4">
            @forelse($bids as $bid)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $bid->sponsorshipProgram->name }}</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                For offer: <span class="font-medium">{{ $bid->sponsorOffer->title }}</span>
                            </p>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">
                                By: {{ $bid->user->name }} ({{ $bid->user->email }})
                            </p>
                        </div>
                        @php
                            $bidStatusColors = [
                                'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                                'accepted' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                                'rejected' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                            ];
                        @endphp
                        <span class="px-3 py-1 text-xs font-medium rounded-full {{ $bidStatusColors[$bid->status] ?? 'bg-gray-100 text-gray-800' }}">
                            {{ ucfirst($bid->status) }}
                        </span>
                    </div>

                    @if($bid->message)
                        <div class="mb-4 p-4 bg-gray-50 dark:bg-gray-700 rounded">
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $bid->message }}</p>
                        </div>
                    @endif

                    <div class="grid grid-cols-3 gap-4 mb-4 text-sm">
                        <div>
                            <p class="text-gray-600 dark:text-gray-400">Program Goal</p>
                            <p class="font-bold text-gray-900 dark:text-white">GHS {{ number_format($bid->sponsorshipProgram->amount_goal, 2) }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600 dark:text-gray-400">Beneficiaries</p>
                            <p class="font-bold text-gray-900 dark:text-white">{{ $bid->sponsorshipProgram->affected_individuals ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600 dark:text-gray-400">Applied</p>
                            <p class="font-bold text-gray-900 dark:text-white">{{ $bid->created_at->diffForHumans() }}</p>
                        </div>
                    </div>

                    @if($bid->status === 'pending')
                        <div class="flex items-center gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <button wire:click="acceptBid({{ $bid->id }})" class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                                Accept Application
                            </button>
                            <button wire:click="openRejectBidModal({{ $bid->id }})" class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                                Reject Application
                            </button>
                        </div>
                    @endif
                </div>
            @empty
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-12 text-center">
                    <p class="text-gray-500 dark:text-gray-400">No applications yet</p>
                </div>
            @endforelse
        </div>

        @if($bids->hasPages())
            <div class="mt-6">
                {{ $bids->links() }}
            </div>
        @endif
    @endif
</div>
