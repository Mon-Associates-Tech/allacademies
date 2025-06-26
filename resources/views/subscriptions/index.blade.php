<x-layouts.app title="My Subscriptions" page-name="Subscriptions">
    <!-- Payment Information Banner -->
    <div class="rounded-lg bg-blue-50 p-6 mb-6 border border-blue-200">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-6 w-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="ml-3 flex-1">
                <h3 class="text-lg font-medium text-blue-900 mb-2">Payment Information</h3>
                <div class="text-sm text-blue-800 space-y-1">
                    <p>📱 Dial <strong class="font-semibold">*772*30#</strong> to pay for any subscription</p>
                    <p>🏪 Merchant Code: <strong class="font-semibold bg-blue-100 px-2 py-1 rounded">1326001</strong></p>
                    <p>📋 Please use the subscription reference as payment reference</p>
                </div>
            </div>
        </div>
    </div>

    @if ($subscriptions->count())
        <!-- Summary Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            @php
                $totalSubscriptions = $subscriptions->total();
                $activeSubscriptions = $subscriptions->where('status', \App\Enums\SubscriptionStatus::PAID->value)->where('expires_at', '>', now())->count();
                $pendingPayments = $subscriptions->where('status', \App\Enums\SubscriptionStatus::UNPAID->value)->count();
                $totalAmount = $subscriptions->where('status', \App\Enums\SubscriptionStatus::PAID->value)->sum('amount');
            @endphp

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Subscriptions</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $totalSubscriptions }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Active Subscriptions</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $activeSubscriptions }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Pending Payments</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $pendingPayments }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Paid</p>
                        <p class="text-2xl font-bold text-gray-900">GHS {{ number_format($totalAmount, 2) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Subscriptions Table -->
        <div class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden">
            <div class="px-6 flex justify-between py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="text-lg font-medium text-gray-900">Subscription History</h3>
                    <x-link.primary :to="route('subscriptions.create')">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        New Subscription
                    </x-link.primary>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Subscription Details
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Package & Amount
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Duration
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Subjects
                            </th>
                            <th scope="col" class="relative px-6 py-3">
                                <span class="sr-only">Actions</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($subscriptions as $subscription)
                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                                <svg class="h-5 w-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <a href="{{ route('subscriptions.show', $subscription) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-900 transition-colors duration-150">
                                                {{ $subscription->reference }}
                                            </a>
                                            <div class="text-sm text-gray-500">{{ $subscription->beneficiaries }} {{ Str::plural('beneficiary', $subscription->beneficiaries) }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs bg-indigo-50 text-indigo-500 mb-1">
                                            {{ ucfirst(str_replace([':', '_'], [' ', ' '], $subscription->package)) }}
                                        </span>
                                    </div>
                                    <div class="text-sm font-semibold text-gray-900">{{ $subscription->currency }} {{ number_format($subscription->amount, 2) }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span @class([
                                        'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                        'bg-red-100 text-red-800' => \App\Enums\SubscriptionStatus::UNPAID->value === $subscription->status,
                                        'bg-yellow-100 text-yellow-800' => \App\Enums\SubscriptionStatus::PART_PAID->value === $subscription->status,
                                        'bg-green-100 text-green-800' => \App\Enums\SubscriptionStatus::PAID->value === $subscription->status,
                                    ])>
                                        @if(\App\Enums\SubscriptionStatus::UNPAID->value === $subscription->status)
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        @elseif(\App\Enums\SubscriptionStatus::PART_PAID->value === $subscription->status)
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        @else
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        @endif
                                        {{ ucfirst(str_replace('-', ' ', $subscription->status->value)) }}
                                    </span>
                                    @if(\App\Enums\SubscriptionStatus::PAID->value === $subscription->status && $subscription->expires_at < now())
                                        <div class="mt-1">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                Expired
                                            </span>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <div class="space-y-1">
                                        <div>{{ $subscription->expires_at->format('M j, Y') }}</div>
                                        <div class="text-xs text-gray-500">
                                            @if($subscription->expires_at > now())
                                                {{ $subscription->expires_at->diffForHumans(['parts' => 2]) }}
                                            @else
                                                Expired {{ $subscription->expires_at->diffForHumans() }}
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @if($subscription->academicSubjects->count() > 0)
                                        <div class="flex items-center">
                                            <span class="text-sm font-medium text-gray-900">{{ $subscription->academicSubjects->count() }}</span>
                                            <span class="ml-1 text-xs text-gray-500">{{ Str::plural('subject', $subscription->academicSubjects->count()) }}</span>
                                        </div>
                                        @if($subscription->academicSubjects->count() <= 3)
                                            <div class="mt-1 space-y-1">
                                                @foreach($subscription->academicSubjects->take(3) as $subject)
                                                    <div class="text-xs text-gray-600">{{ $subject->name }}</div>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="mt-1">
                                                <span class="text-xs text-gray-500">{{ $subscription->academicSubjects->take(2)->pluck('name')->join(', ') }} and {{ $subscription->academicSubjects->count() - 2 }} more</span>
                                            </div>
                                        @endif
                                    @else
                                        <span class="text-gray-400">No subjects</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center space-x-2">
                                        @if (\App\Enums\SubscriptionStatus::UNPAID === $subscription->status)
                                            <button type="button"
                                                    class="text-indigo-600 hover:text-indigo-900 transition-colors duration-150"
                                                    onclick="copyToClipboard('{{ $subscription->reference }}')"
                                                    title="Copy reference">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                                </svg>
                                            </button>
                                            <x-action name="delete" :to="route('subscriptions.destroy', ['subscription' => $subscription])">
                                                Are you sure you want to delete subscription {{ $subscription->reference }}?
                                            </x-action>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-6">
            {{ $subscriptions->links() }}
        </div>
    @else
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No subscriptions</h3>
            <p class="mt-1 text-sm text-gray-500">Get started by creating your first subscription.</p>
            <div class="mt-6">
                <x-link.primary :to="route('subscriptions.create')">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    New Subscription
                </x-link.primary>
            </div>
        </div>
    @endif

    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                // Show a toast or notification
                alert('Reference copied to clipboard: ' + text);
            }, function(err) {
                console.error('Could not copy text: ', err);
            });
        }
    </script>
</x-layouts.app>
