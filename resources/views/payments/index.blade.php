<x-layouts.app title="Payments">
    <x-slot name="breadcrumb">
        <x-breadcrumb />
    </x-slot>

    @can('administrate')
        <x-slot name="action">
            <x-link.primary :to="route('payments.create')">New Payment</x-link.primary>
        </x-slot>
    @endcan

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
        <!-- Total Payments -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Payments</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ number_format($stats['total_payments']) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Amount -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Revenue</dt>
                            <dd class="text-lg font-medium text-gray-900">GHS {{ number_format($stats['total_amount'], 2) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- This Month -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-purple-500 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">This Month</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ number_format($stats['this_month']) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Payments -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-yellow-500 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Pending</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ number_format($stats['pending_payments']) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filter Controls -->
    <div class="mb-6 bg-white p-6 shadow rounded-lg">
        <form method="GET" action="{{ route('payments.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <!-- Search Input -->
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                    <input type="text"
                           id="search"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Reference, amount, or user..."
                           class="w-full rounded-md border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <!-- Status Filter -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" id="status" class="w-full rounded-md border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="succeeded" {{ request('status') === 'succeeded' ? 'selected' : '' }}>Succeeded</option>
                        <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>

                <!-- Payment Type Filter -->
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                    <select name="type" id="type" class="w-full rounded-md border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All Types</option>
                        <option value="subscription" {{ request('type') === 'subscription' ? 'selected' : '' }}>Subscription</option>
                        <option value="book_subscription" {{ request('type') === 'book_subscription' ? 'selected' : '' }}>Book Subscription</option>
                    </select>
                </div>

                <!-- Currency Filter -->
                <div>
                    <label for="currency" class="block text-sm font-medium text-gray-700 mb-1">Currency</label>
                    <select name="currency" id="currency" class="w-full rounded-md border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All Currencies</option>
                        <option value="GHS" {{ request('currency') === 'GHS' ? 'selected' : '' }}>GHS</option>
                        <option value="USD" {{ request('currency') === 'USD' ? 'selected' : '' }}>USD</option>
                        <option value="EUR" {{ request('currency') === 'EUR' ? 'selected' : '' }}>EUR</option>
                    </select>
                </div>
            </div>

            <!-- Date Range -->
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div>
                    <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                    <input type="date"
                           id="date_from"
                           name="date_from"
                           value="{{ request('date_from') }}"
                           class="w-full rounded-md border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label for="date_to" class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
                    <input type="date"
                           id="date_to"
                           name="date_to"
                           value="{{ request('date_to') }}"
                           class="w-full rounded-md border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div class="flex items-end space-x-2">
                    <button type="submit" class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        Apply Filters
                    </button>
                    <a href="{{ route('payments.index') }}" class="flex-1 text-center bg-gray-100 text-gray-700 px-4 py-2 rounded-md text-sm font-medium hover:bg-gray-200">
                        Clear
                    </a>
                </div>
            </div>
        </form>
    </div>

    @if ($payments->count())
        <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-lg overflow-hidden">
            <x-table>
                <x-slot name="head">
                    <tr>
                        <x-table.th>Payment Info</x-table.th>
                        <x-table.th>Customer</x-table.th>
                        <x-table.th>Amount</x-table.th>
                        <x-table.th>Type</x-table.th>
                        <x-table.th>Status</x-table.th>
                        <x-table.th>Date</x-table.th>
                        <x-table.th><span class="sr-only">Actions</span></x-table.th>
                    </tr>
                </x-slot>

                @foreach ($payments as $payment)
                    <tr class="hover:bg-gray-50">
                        <x-table.td>
                            <div>
                                <div class="font-medium text-gray-900">{{ $payment->reference }}</div>
                                @if($payment->gateway_reference)
                                    <div class="text-sm text-gray-500">Gateway: {{ $payment->gateway_reference }}</div>
                                @endif
                            </div>
                        </x-table.td>

                        <x-table.td>
                            <div class="flex items-center space-x-3">
                                @php
                                    $user = $payment->subscription?->user ?? $payment->bookSubscription?->user;
                                @endphp

                                @if($user)
                                    <!-- Avatar -->
                                    <div class="flex-shrink-0">
                                        @if($user->avatar)
                                            <img class="h-8 w-8 rounded-full object-cover"
                                                 src="{{ $user->avatar }}"
                                                 alt="{{ $user->name }}">
                                        @else
                                            <div class="h-8 w-8 rounded-full bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center">
                                                <span class="text-white font-medium text-xs">
                                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="min-w-0 flex-1">
                                        <p class="font-medium text-gray-900 truncate">{{ $user->name }}</p>
                                        <p class="text-sm text-gray-500 truncate">{{ $user->email }}</p>
                                    </div>
                                @else
                                    <span class="text-gray-500 italic">Unknown user</span>
                                @endif
                            </div>
                        </x-table.td>

                        <x-table.td>
                            <div class="font-medium text-gray-900">
                                {{ $payment->currency }} {{ number_format($payment->amount, 2) }}
                            </div>
                        </x-table.td>

                        <x-table.td>
                            <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium
                                {{ $payment->subscription_id ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700' }}">
                                @if($payment->subscription_id)
                                    <svg class="mr-1 h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Subscription
                                @elseif($payment->book_subscription_id)
                                    <svg class="mr-1 h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    Book Subscription
                                    @if($payment->bookSubscription?->book)
                                        <div class="text-xs text-gray-500 mt-1">{{ $payment->bookSubscription->book->title }}</div>
                                    @endif
                                @else
                                    Other
                                @endif
                            </span>
                        </x-table.td>

                        <x-table.td>
                            <span @class([
                                "inline-flex items-center rounded-md px-2 py-1 text-xs font-medium",
                                'bg-green-100 text-green-700' => $payment->status === 'succeeded',
                                'bg-yellow-100 text-yellow-700' => $payment->status === 'pending',
                                'bg-red-100 text-red-700' => $payment->status === 'failed',
                                'bg-gray-100 text-gray-700' => $payment->status === 'cancelled'
                            ])>
                                @if($payment->status === 'succeeded')
                                    <svg class="mr-1 h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                @elseif($payment->status === 'pending')
                                    <svg class="mr-1 h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                    </svg>
                                @elseif($payment->status === 'failed')
                                    <svg class="mr-1 h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                @endif
                               {{ $payment->status }}
                            </span>
                        </x-table.td>

                        <x-table.td>
                            <div>
                                <div class="text-sm text-gray-900">{{ $payment->created_at->format('M j, Y') }}</div>
                                <div class="text-xs text-gray-500">{{ $payment->created_at->format('g:i A') }}</div>
                            </div>
                        </x-table.td>

                        <x-table.td action>
                            <div class="flex items-center space-x-2">
                                <!-- Quick actions dropdown -->
                                <div class="relative" x-data="{ open: false }">
                                    <button @click="open = !open"
                                            class="p-1 text-gray-400 hover:text-gray-600">
                                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/>
                                        </svg>
                                    </button>

                                    <div x-show="open"
                                         @click.away="open = false"
                                         x-transition
                                         class="absolute right-0 mt-1 w-48 bg-white rounded-md shadow-lg border border-gray-200 z-10">
                                        <div class="py-1">
                                            <button class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                View Details
                                            </button>
                                            <button class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                Download Receipt
                                            </button>
                                            @if($payment->status === 'pending')
                                                <button class="block w-full text-left px-4 py-2 text-sm text-blue-600 hover:bg-blue-50">
                                                    Mark as Paid
                                                </button>
                                            @endif
                                            @if($payment->status === 'failed')
                                                <button class="block w-full text-left px-4 py-2 text-sm text-green-600 hover:bg-green-50">
                                                    Retry Payment
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </x-table.td>
                    </tr>
                @endforeach
            </x-table>
        </div>

        <!-- Enhanced pagination with info -->
        <div class="mt-6 flex items-center justify-between">
            <div class="text-sm text-gray-700">
                Showing {{ $payments->firstItem() }} to {{ $payments->lastItem() }} of {{ $payments->total() }} payments
            </div>
            {{ $payments->links() }}
        </div>
    @else
        <div class="text-center py-12 bg-white rounded-lg shadow">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
            </svg>
            <h3 class="mt-4 text-lg font-medium text-gray-900">No payments found</h3>
            <p class="mt-2 text-sm text-gray-500">
                @if(request()->hasAny(['search', 'status', 'type', 'currency', 'date_from', 'date_to']))
                    Try adjusting your search or filter criteria.
                @else
                    No payments have been recorded yet.
                @endif
            </p>
            @if(request()->hasAny(['search', 'status', 'type', 'currency', 'date_from', 'date_to']))
                <div class="mt-4">
                    <a href="{{ route('payments.index') }}" class="text-blue-600 hover:text-blue-500 text-sm font-medium">
                        Clear all filters
                    </a>
                </div>
            @endif
        </div>
    @endif
</x-layouts.app>
