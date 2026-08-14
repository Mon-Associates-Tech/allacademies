@php use Carbon\Carbon; @endphp
@php use App\Enums\SubscriptionStatus; @endphp
<x-layouts.app title="My Subscriptions" page-name="Subscriptions">

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

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Subscriptions</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $totalSubscriptions }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $totalRegularSubscriptions }}
                            courses, {{ $totalBookSubscriptions }} books</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Active Subscriptions</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $totalActiveSubscriptions }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $activeRegularSubscriptions }}
                            courses, {{ $activeBookSubscriptions }} books</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-yellow-100 dark:bg-yellow-900 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Pending Payments</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $totalPendingPayments }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $pendingRegularPayments }}
                            courses, {{ $pendingBookPayments }} books</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-indigo-100 dark:bg-indigo-900 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Value</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">GHS {{ number_format($totalAmount, 2) }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">GHS {{ number_format($totalPaidAmount, 2) }} paid</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Financial Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Value</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">GHS {{ number_format($totalAmount, 2) }}</p>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-indigo-100 dark:bg-indigo-900 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500 dark:text-gray-400">Course Subscriptions:</span>
                        <span class="font-medium text-gray-900 dark:text-gray-100">GHS {{ number_format($totalRegularAmount, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500 dark:text-gray-400">Book Subscriptions:</span>
                        <span class="font-medium text-gray-900 dark:text-gray-100">GHS {{ number_format($totalBookAmount, 2) }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Paid Amount</p>
                        <p class="text-2xl font-bold text-green-600 dark:text-green-400">GHS {{ number_format($totalPaidAmount, 2) }}</p>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500 dark:text-gray-400">Paid Courses:</span>
                        <span class="font-medium text-gray-900 dark:text-gray-100">GHS {{ number_format($paidRegularAmount, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500 dark:text-gray-400">Paid Books:</span>
                        <span class="font-medium text-gray-900 dark:text-gray-100">GHS {{ number_format($paidBookAmount, 2) }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Unpaid Amount</p>
                        <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">GHS {{ number_format($totalUnpaidAmount, 2) }}</p>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-yellow-100 dark:bg-yellow-900 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500 dark:text-gray-400">Unpaid Courses:</span>
                        <span class="font-medium text-gray-900 dark:text-gray-100">GHS {{ number_format($unpaidRegularAmount, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500 dark:text-gray-400">Unpaid Books:</span>
                        <span class="font-medium text-gray-900 dark:text-gray-100">GHS {{ number_format($unpaidBookAmount, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>


        <!-- Subscriptions Table -->
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-6 flex justify-between py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                <div class="flex items-center space-x-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Subscription History</h3>

                    {{-- Filters: School / Team --}}
                    @if(!empty($filterSchools) || !empty($filterTeams))
                        <form method="GET" action="{{ route('subscriptions.index') }}" class="flex items-center space-x-2">
                            @if(!empty($filterSchools))
                                <select name="school_id" class="px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm">
                                    <option value="">All Schools</option>
                                    @foreach($filterSchools as $s)
                                        <option value="{{ $s->id }}" {{ request('school_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                                    @endforeach
                                </select>
                            @endif

                            @if(!empty($filterTeams))
                                <select name="team_id" class="px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm">
                                    <option value="">All Teams</option>
                                    @foreach($filterTeams as $t)
                                        <option value="{{ $t->id }}" {{ request('team_id') == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                                    @endforeach
                                </select>
                            @endif

                            <button type="submit" class="px-3 py-2 rounded-lg text-sm bg-indigo-600 text-white">Filter</button>
                            <a href="{{ route('subscriptions.index') }}" class="px-3 py-2 rounded-lg text-sm bg-gray-200 dark:bg-gray-700">Reset</a>
                        </form>
                    @endif
                </div>

                <div class="flex space-x-3">
                    @if(!auth()->user()->hasAnyRole(['student']))
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

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Type & Details
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Package & Content
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Amount & Status
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Duration
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach ($subscriptions as $subscription)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        @if($subscription['type'] === 'regular')
                                            <div
                                                class="h-10 w-10 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center">
                                                <svg class="h-5 w-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
                                                     viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                          d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                                </svg>
                                            </div>
                                        @else
                                            <div
                                                class="h-10 w-10 rounded-full bg-green-100 dark:bg-green-900 flex items-center justify-center">
                                                <svg class="h-5 w-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor"
                                                     viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                          d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ $subscription['reference'] }}
                                        </div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            @if($subscription['type'] === 'regular')
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200">
                                                        Course Subscription
                                                    </span>
                                            @else
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">
                                                        Book Subscription
                                                    </span>
                                            @endif
                                        </div>
                                        <div class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                                            {{ $subscription['created_at'] }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 dark:text-gray-100">
                                    @if($subscription['type'] === 'regular')
                                        <div
                                            class="font-medium">{{ $subscription['package']->value ?? $subscription['package'] }}</div>
                                        <div class="text-gray-500 dark:text-gray-400">
                                            {{ $subscription['subjects'] ?: 'No subjects selected' }}
                                            @if($subscription['subject_count'] > 0)
                                                <span class="text-xs">({{ $subscription['subject_count'] }} subjects)</span>
                                            @endif
                                        </div>
                                    @else
                                        <div class="font-medium">{{ $subscription['book_title'] }}</div>
                                        <div class="text-gray-500 dark:text-gray-400">
                                            by {{ $subscription['book_author'] }}
                                        </div>
                                        <div class="text-xs text-gray-400 dark:text-gray-500">
                                            {{ $subscription['book_category'] }}
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                    {{ $subscription['currency'] }} {{ number_format($subscription['amount'], 2) }}
                                </div>
                                <div class="text-sm">
                                    @if($subscription['type'] === 'regular')
                                        @if($subscription['status'] === SubscriptionStatus::PAID)
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">
                                                    Paid
                                                </span>
                                        @elseif($subscription['status'] === SubscriptionStatus::UNPAID)
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200">
                                                    Pending
                                                </span>
                                        @else
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-200">
                                                    {{ $subscription['status']->value ?? $subscription['status'] }}
                                                </span>
                                        @endif
                                    @else
                                        @if($subscription['status'] === 'active')
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">
                                                    Active
                                                </span>
                                        @elseif($subscription['status'] === 'pending_payment')
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200">
                                                    Pending Payment
                                                </span>
                                        @elseif($subscription['status'] === 'cancelled')
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200">
                                                    Cancelled
                                                </span>
                                        @else
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-200">
                                                    {{ $subscription['status'] }}
                                                </span>
                                        @endif
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
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
                                       class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">
                                        View Details
                                    </a>
                                    @if($subscription['status'] === SubscriptionStatus::UNPAID)
                                        <form method="POST"
                                              action="{{ route('subscriptions.destroy', $subscription['model']) }}"
                                              class="inline ml-2">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300"
                                                    onclick="return confirm('Are you sure you want to delete this subscription?')">
                                                Cancel
                                            </button>
                                        </form>
                                    @endif
                                @else
                                    @if($subscription['status'] === 'pending_payment')
                                        <button
                                            onclick="showBookPaymentDetails('{{ $subscription['reference'] }}', '{{ $subscription['amount'] }}', '{{ $subscription['book_title'] }}')"
                                            class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300">
                                            Pay Now
                                        </button>
                                    @elseif($subscription['status'] === 'active')
                                        <span class="text-green-600 dark:text-green-400">Active</span>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="bg-white dark:bg-gray-800 px-4 py-3 border-t border-gray-200 dark:border-gray-700 sm:px-6">
                {{ $subscriptions->links() }}
            </div>
        </div>
    @else
        <!-- Empty State -->
        <div class="text-center py-12 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
            <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No subscriptions</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Get started by creating your first subscription.</p>
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
         class="fixed inset-0 bg-gray-600 dark:bg-gray-900 bg-opacity-50 dark:bg-opacity-75 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border border-gray-200 dark:border-gray-700 w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
            <div class="mt-3 text-center">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Complete Book Subscription Payment</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Use the details below to complete your payment:</p>
                    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg text-left space-y-2">
                        <div class="text-gray-900 dark:text-gray-100"><strong>Book:</strong> <span id="modalBookTitle"></span></div>
                        <div class="text-gray-900 dark:text-gray-100"><strong>Amount:</strong> GHS <span id="modalAmount"></span></div>
                        <div class="text-gray-900 dark:text-gray-100"><strong>Reference:</strong> <span id="modalReference" class="font-mono"></span></div>
                    </div>
                    <div class="mt-4 text-sm text-gray-600 dark:text-gray-400">
                        <p>📱 Dial *772*30# to pay</p>
                        <p>🏪 Merchant Code: 1326001</p>
                    </div>
                </div>
                <div class="items-center px-4 py-3">
                    <button onclick="closeBookPaymentModal()"
                            class="px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-800 dark:text-gray-200 text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-400 dark:hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-300">
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
