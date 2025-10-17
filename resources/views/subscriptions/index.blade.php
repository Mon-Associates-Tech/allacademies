@php use Carbon\Carbon; @endphp
@php use App\Enums\SubscriptionStatus; @endphp
<x-layouts.app title="My Subscriptions" page-name="Subscriptions">
    <!-- Payment Information Banner -->
  

    @if ($subscriptions->count())
        <!-- Summary Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            @php
                // Regular subscriptions stats
                $activeRegularSubscriptions = $regularSubscriptions->where('status', SubscriptionStatus::PAID->value)->where('expires_at', '>', now())->count();
                $activeBookSubscriptions = $bookSubscriptions->where('status', 'active')->where('end_date', '>', now())->count();
                $totalActiveSubscriptions = $activeRegularSubscriptions + $activeBookSubscriptions;

                $pendingRegularPayments = $regularSubscriptions->where('status', SubscriptionStatus::UNPAID->value)->count();
                $pendingBookPayments = $bookSubscriptions->where('status', 'pending_payment')->count();
                $totalPendingPayments = $pendingRegularPayments + $pendingBookPayments;

                // Financial stats
                $totalRegularAmount = $regularSubscriptions->sum('amount');
                $totalBookAmount = $bookSubscriptions->sum('annual_fee');
                $totalAmount = $totalRegularAmount + $totalBookAmount;

                $paidRegularAmount = $regularSubscriptions->where('status', SubscriptionStatus::PAID->value)->sum('amount');
                $paidBookAmount = $bookSubscriptions->where('status', 'active')->sum('annual_fee');
                $totalPaidAmount = $paidRegularAmount + $paidBookAmount;

                $unpaidRegularAmount = $regularSubscriptions->where('status', SubscriptionStatus::UNPAID->value)->sum('amount');
                $unpaidBookAmount = $bookSubscriptions->where('status', 'pending_payment')->sum('annual_fee');
                $totalUnpaidAmount = $unpaidRegularAmount + $unpaidBookAmount;

                // Count stats
                $totalRegularSubscriptions = $regularSubscriptions->count();
                $totalBookSubscriptions = $bookSubscriptions->count();
            @endphp

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Subscriptions</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $totalSubscriptions }}</p>
                        <p class="text-xs text-gray-500">{{ $totalRegularSubscriptions }}
                            courses, {{ $totalBookSubscriptions }} books</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Active Subscriptions</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $totalActiveSubscriptions }}</p>
                        <p class="text-xs text-gray-500">{{ $activeRegularSubscriptions }}
                            courses, {{ $activeBookSubscriptions }} books</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Pending Payments</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $totalPendingPayments }}</p>
                        <p class="text-xs text-gray-500">{{ $pendingRegularPayments }}
                            courses, {{ $pendingBookPayments }} books</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Value</p>
                        <p class="text-2xl font-bold text-gray-900">GHS {{ number_format($totalAmount, 2) }}</p>
                        <p class="text-xs text-gray-500">GHS {{ number_format($totalPaidAmount, 2) }} paid</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Financial Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Value</p>
                        <p class="text-2xl font-bold text-gray-900">GHS {{ number_format($totalAmount, 2) }}</p>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Course Subscriptions:</span>
                        <span class="font-medium">GHS {{ number_format($totalRegularAmount, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Book Subscriptions:</span>
                        <span class="font-medium">GHS {{ number_format($totalBookAmount, 2) }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Paid Amount</p>
                        <p class="text-2xl font-bold text-green-600">GHS {{ number_format($totalPaidAmount, 2) }}</p>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Paid Courses:</span>
                        <span class="font-medium">GHS {{ number_format($paidRegularAmount, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Paid Books:</span>
                        <span class="font-medium">GHS {{ number_format($paidBookAmount, 2) }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Unpaid Amount</p>
                        <p class="text-2xl font-bold text-yellow-600">GHS {{ number_format($totalUnpaidAmount, 2) }}</p>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Unpaid Courses:</span>
                        <span class="font-medium">GHS {{ number_format($unpaidRegularAmount, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Unpaid Books:</span>
                        <span class="font-medium">GHS {{ number_format($unpaidBookAmount, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>


        <!-- Subscriptions Table -->
        <div class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden">
            <div class="px-6 flex justify-between py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="text-lg font-medium text-gray-900">Subscription History</h3>
                <div class="flex space-x-3">
                    @if(!auth()->user()->hasAnyRole(['subscriber', 'student']))
                    <x-link.primary :to="route('subscriptions.create')">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        New Course Subscription
                    </x-link.primary>
                    @endif

                    @if(in_array(Auth::user()->email, special_access_emails()))
                        <button
                            id="toggleTestMode"
                            class="px-4 py-2 rounded-lg font-medium transition-colors
                    @if(session('TESTING_SUBSCRIPTIONS', false))
                        bg-yellow-500 hover:bg-yellow-600 text-white
                    @else
                        bg-gray-200 hover:bg-gray-300 text-gray-800
                    @endif
                    dark:@if(session('TESTING_SUBSCRIPTIONS', false))
                        bg-yellow-600 hover:bg-yellow-700
                    @else
                        bg-gray-700 hover:bg-gray-600 text-white
                    @endif">
                            @if(session('TESTING_SUBSCRIPTIONS', false))
                                Disable Test Mode
                            @else
                                Enable Test Mode
                            @endif
                        </button>
                    @endif
                </div>


            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                <tr>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Type & Details
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Package & Content
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Amount & Status
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Duration
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                @foreach ($subscriptions as $subscription)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    @if($subscription['type'] === 'regular')
                                        <div
                                            class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                            <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                            </svg>
                                        </div>
                                    @else
                                        <div
                                            class="h-10 w-10 rounded-full bg-green-100 flex items-center justify-center">
                                            <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $subscription['reference'] }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        @if($subscription['type'] === 'regular')
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                        Course Subscription
                                                    </span>
                                        @else
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        Book Subscription
                                                    </span>
                                        @endif
                                    </div>
                                    <div class="text-xs text-gray-400 mt-1">
                                        {{ $subscription['created_at'] }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">
                                @if($subscription['type'] === 'regular')
                                    <div
                                        class="font-medium">{{ $subscription['package']->value ?? $subscription['package'] }}</div>
                                    <div class="text-gray-500">
                                        {{ $subscription['subjects'] ?: 'No subjects selected' }}
                                        @if($subscription['subject_count'] > 0)
                                            <span class="text-xs">({{ $subscription['subject_count'] }} subjects)</span>
                                        @endif
                                    </div>
                                @else
                                    <div class="font-medium">{{ $subscription['book_title'] }}</div>
                                    <div class="text-gray-500">
                                        by {{ $subscription['book_author'] }}
                                    </div>
                                    <div class="text-xs text-gray-400">
                                        {{ $subscription['book_category'] }}
                                    </div>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">
                                {{ $subscription['currency'] }} {{ number_format($subscription['amount'], 2) }}
                            </div>
                            <div class="text-sm">
                                @if($subscription['type'] === 'regular')
                                    @if($subscription['status'] === SubscriptionStatus::PAID)
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    Paid
                                                </span>
                                    @elseif($subscription['status'] === SubscriptionStatus::UNPAID)
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                    Pending
                                                </span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                    {{ $subscription['status']->value ?? $subscription['status'] }}
                                                </span>
                                    @endif
                                @else
                                    @if($subscription['status'] === 'active')
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    Active
                                                </span>
                                    @elseif($subscription['status'] === 'pending_payment')
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                    Pending Payment
                                                </span>
                                    @elseif($subscription['status'] === 'cancelled')
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    Cancelled
                                                </span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                    {{ $subscription['status'] }}
                                                </span>
                                    @endif
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <div>{{ $subscription['duration_in_months'] }} months</div>
                            @if($subscription['expires_at'])
                                <div class="text-xs">
                                    Expires: {{ Carbon::parse($subscription['expires_at'])->format('M d, Y') }}
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            @if($subscription['type'] === 'regular')
                                <a href="{{ route('subscriptions.show', $subscription['model']) }}"
                                   class="text-indigo-600 hover:text-indigo-900">
                                    View Details
                                </a>
                                @if($subscription['status'] === SubscriptionStatus::UNPAID)
                                    <form method="POST"
                                          action="{{ route('subscriptions.destroy', $subscription['model']) }}"
                                          class="inline ml-2">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="text-red-600 hover:text-red-900"
                                                onclick="return confirm('Are you sure you want to delete this subscription?')">
                                            Cancel
                                        </button>
                                    </form>
                                @endif
                            @else
                                @if($subscription['status'] === 'pending_payment')
                                    <button
                                        onclick="showBookPaymentDetails('{{ $subscription['reference'] }}', '{{ $subscription['amount'] }}', '{{ $subscription['book_title'] }}')"
                                        class="text-blue-600 hover:text-blue-900">
                                        Pay Now
                                    </button>
                                @elseif($subscription['status'] === 'active')
                                    <span class="text-green-600">Active</span>
                                @endif
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
            {{ $subscriptions->links() }}
        </div>
        </div>
    @else
        <!-- Empty State -->
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No subscriptions</h3>
            <p class="mt-1 text-sm text-gray-500">Get started by creating your first subscription.</p>
            <div class="mt-6 flex justify-center space-x-3">
                <x-link.primary :to="route('subscriptions.create')">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    New Course Subscription
                </x-link.primary>
                <x-link.secondary :to="route('books.index')">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    Browse Books
                </x-link.secondary>
            </div>
        </div>
    @endif

    <!-- Book Payment Modal -->
    <div id="bookPaymentModal"
         class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Complete Book Subscription Payment</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500 mb-4">Use the details below to complete your payment:</p>
                    <div class="bg-gray-50 p-4 rounded-lg text-left space-y-2">
                        <div><strong>Book:</strong> <span id="modalBookTitle"></span></div>
                        <div><strong>Amount:</strong> GHS <span id="modalAmount"></span></div>
                        <div><strong>Reference:</strong> <span id="modalReference" class="font-mono"></span></div>
                    </div>
                    <div class="mt-4 text-sm text-gray-600">
                        <p>📱 Dial *772*30# to pay</p>
                        <p>🏪 Merchant Code: 1326001</p>
                    </div>
                </div>
                <div class="items-center px-4 py-3">
                    <button onclick="closeBookPaymentModal()"
                            class="px-4 py-2 bg-gray-300 text-gray-800 text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showBookPaymentDetails(reference, amount, bookTitle) {
            document.getElementById('modalReference').textContent = reference;
            document.getElementById('modalAmount').textContent = parseFloat(amount).toFixed(2);
            document.getElementById('modalBookTitle').textContent = bookTitle;
            document.getElementById('bookPaymentModal').classList.remove('hidden');
        }

        function closeBookPaymentModal() {
            document.getElementById('bookPaymentModal').classList.add('hidden');
        }
    </script>


    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggleButton = document.getElementById('toggleTestMode');

            if (toggleButton) {
                toggleButton.addEventListener('click', function (e) {
                    e.preventDefault();

                    // Add loading state
                    const originalText = this.textContent;
                    this.innerHTML = 'Processing...';
                    this.disabled = true;

                    fetch('{{ route("subscriptions.toggle-test-mode") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Reload the page to reflect changes
                                location.reload();
                            } else {
                                alert('Error: ' + (data.message || 'Failed to toggle test mode'));
                                this.textContent = originalText;
                                this.disabled = false;
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('An error occurred while toggling test mode');
                            this.textContent = originalText;
                            this.disabled = false;
                        });
                });
            }
        });
    </script>

</x-layouts.app>
