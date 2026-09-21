@php
    use Carbon\Carbon;
    use App\Enums\SubscriptionStatus;

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

<x-layouts.app title="My Subscriptions" page-name="Subscriptions">

    @if ($subscriptions->count())
        <!-- Summary Stats -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div
                class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 p-5 transition-all hover:shadow-md">
                <div class="flex items-center gap-4">
                    <div class="flex-shrink-0">
                        <div
                            class="w-10 h-10 bg-indigo-50 dark:bg-indigo-900/30 rounded-xl flex items-center justify-center ring-1 ring-inset ring-indigo-600/10 dark:ring-indigo-400/20">
                            <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Total Subscriptions</p>
                        <p class="text-2xl font-bold text-slate-900 dark:text-slate-100 mt-1">{{ $totalSubscriptions }}</p>
                        <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">{{ $totalRegularSubscriptions }}
                            courses, {{ $totalBookSubscriptions }} books</p>
                    </div>
                </div>
            </div>

            <div
                class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 p-5 transition-all hover:shadow-md">
                <div class="flex items-center gap-4">
                    <div class="flex-shrink-0">
                        <div
                            class="w-10 h-10 bg-emerald-50 dark:bg-emerald-900/30 rounded-xl flex items-center justify-center ring-1 ring-inset ring-emerald-600/10 dark:ring-emerald-400/20">
                            <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none"
                                 stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Active Subscriptions</p>
                        <p class="text-2xl font-bold text-slate-900 dark:text-slate-100 mt-1">{{ $totalActiveSubscriptions }}</p>
                        <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">{{ $activeRegularSubscriptions }}
                            courses, {{ $activeBookSubscriptions }} books</p>
                    </div>
                </div>
            </div>

            <div
                class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 p-5 transition-all hover:shadow-md">
                <div class="flex items-center gap-4">
                    <div class="flex-shrink-0">
                        <div
                            class="w-10 h-10 bg-amber-50 dark:bg-amber-900/30 rounded-xl flex items-center justify-center ring-1 ring-inset ring-amber-600/10 dark:ring-amber-400/20">
                            <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Pending Payments</p>
                        <p class="text-2xl font-bold text-slate-900 dark:text-slate-100 mt-1">{{ $totalPendingPayments }}</p>
                        <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">{{ $pendingRegularPayments }}
                            courses, {{ $pendingBookPayments }} books</p>
                    </div>
                </div>
            </div>

            <div
                class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 p-5 transition-all hover:shadow-md">
                <div class="flex items-center gap-4">
                    <div class="flex-shrink-0">
                        <div
                            class="w-10 h-10 bg-blue-50 dark:bg-blue-900/30 rounded-xl flex items-center justify-center ring-1 ring-inset ring-blue-600/10 dark:ring-blue-400/20">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Total Value</p>
                        <p class="text-2xl font-bold text-slate-900 dark:text-slate-100 mt-1">
                            GHS {{ number_format($totalAmount, 2) }}</p>
                        <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">
                            GHS {{ number_format($totalPaidAmount, 2) }} paid</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Financial Breakdown -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
            <div
                class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="p-2 bg-indigo-50 dark:bg-indigo-900/30 rounded-lg">
                        <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                        </svg>
                    </div>
                    <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Total Value</p>
                </div>
                <p class="text-3xl font-bold text-slate-900 dark:text-slate-100 mb-4">
                    GHS {{ number_format($totalAmount, 2) }}</p>
                <div class="space-y-2 pt-4 border-t border-slate-100 dark:border-slate-800">
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500 dark:text-slate-400">Course Subscriptions</span>
                        <span
                            class="font-semibold text-slate-700 dark:text-slate-200">GHS {{ number_format($totalRegularAmount, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500 dark:text-slate-400">Book Subscriptions</span>
                        <span
                            class="font-semibold text-slate-700 dark:text-slate-200">GHS {{ number_format($totalBookAmount, 2) }}</span>
                    </div>
                </div>
            </div>

            <div
                class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="p-2 bg-emerald-50 dark:bg-emerald-900/30 rounded-lg">
                        <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Paid Amount</p>
                </div>
                <p class="text-3xl font-bold text-emerald-600 dark:text-emerald-400 mb-4">
                    GHS {{ number_format($totalPaidAmount, 2) }}</p>
                <div class="space-y-2 pt-4 border-t border-slate-100 dark:border-slate-800">
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500 dark:text-slate-400">Paid Courses</span>
                        <span
                            class="font-semibold text-slate-700 dark:text-slate-200">GHS {{ number_format($paidRegularAmount, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500 dark:text-slate-400">Paid Books</span>
                        <span
                            class="font-semibold text-slate-700 dark:text-slate-200">GHS {{ number_format($paidBookAmount, 2) }}</span>
                    </div>
                </div>
            </div>

            <div
                class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="p-2 bg-amber-50 dark:bg-amber-900/30 rounded-lg">
                        <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Unpaid Amount</p>
                </div>
                <p class="text-3xl font-bold text-amber-600 dark:text-amber-400 mb-4">
                    GHS {{ number_format($totalUnpaidAmount, 2) }}</p>
                <div class="space-y-2 pt-4 border-t border-slate-100 dark:border-slate-800">
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500 dark:text-slate-400">Unpaid Courses</span>
                        <span
                            class="font-semibold text-slate-700 dark:text-slate-200">GHS {{ number_format($unpaidRegularAmount, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500 dark:text-slate-400">Unpaid Books</span>
                        <span
                            class="font-semibold text-slate-700 dark:text-slate-200">GHS {{ number_format($unpaidBookAmount, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Subscriptions Table -->
        <div
            class="bg-white dark:bg-slate-900 shadow-sm rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden">
            <div
                class="px-6 py-4 border-b border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <h3 class="text-base font-semibold text-slate-900 dark:text-slate-100">Subscription History</h3>

                    @if(!empty($filterSchools) || !empty($filterTeams))
                        <div x-data="initSubscriptions($el)"
                             data-teams='@json(($filterTeams ?? collect())->map(fn($t) => ["id" => $t->id, "name" => $t->name])->values(), JSON_HEX_APOS)'
                             data-selected-team='{{ request('team_id', '') }}' class="relative">
                            <button type="button" @click="open = !open"
                                    class="inline-flex items-center gap-2 px-3.5 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 rounded-xl shadow-sm text-sm font-medium hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor"
                                     viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2"></path>
                                </svg>
                                Filters
                            </button>

                            <div x-show="open" @click.outside="open = false" x-cloak
                                 class="absolute z-50 mt-2 right-0 w-72 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-xl p-4">
                                <form method="GET" action="{{ route('subscriptions.index') }}" class="space-y-4">
                                    @if(!empty($filterSchools))
                                        <div>
                                            <label
                                                class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">School</label>
                                            <select name="school_id" @change="fetchTeams($event.target.value)"
                                                    class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-shadow">
                                                <option value="">All Schools</option>
                                                @foreach($filterSchools as $s)
                                                    <option
                                                        value="{{ $s->id }}" {{ request('school_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endif

                                    <div>
                                        <label
                                            class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">Type</label>
                                        <select name="type"
                                                class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-shadow">
                                            <option value="">All</option>
                                            <option
                                                value="regular" {{ request('type') == 'regular' ? 'selected' : '' }}>
                                                Content Subscriptions
                                            </option>
                                            <option value="book" {{ request('type') == 'book' ? 'selected' : '' }}>Book
                                                Subscriptions
                                            </option>
                                        </select>
                                    </div>

                                    @if(!empty($filterTeams))
                                        <div>
                                            <label
                                                class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">Team</label>
                                            <select name="team_id" x-model="selectedTeam"
                                                    class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-shadow">
                                                <option value="">All Teams</option>
                                                <template x-for="team in teams" :key="team.id">
                                                    <option :value="team.id" x-text="team.name"
                                                            :selected="team.id == selectedTeam"></option>
                                                </template>
                                            </select>
                                        </div>
                                    @endif

                                    <div class="flex justify-end gap-2 pt-2">
                                        <button type="submit"
                                                class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors">
                                            Apply
                                        </button>
                                        <a href="{{ route('subscriptions.index') }}"
                                           class="px-4 py-2 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-lg text-sm font-medium hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors">Reset</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="flex items-center gap-3">
                    @if(!auth()->user()->hasAnyRole(['student']))
                        <x-link.primary :to="route('subscriptions.create')"
                                        class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-xl text-sm font-medium hover:bg-indigo-700 transition-colors shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            New Course Subscription
                        </x-link.primary>
                    @endif

                    @if(in_array(Auth::user()->email, special_access_emails()))
                        <button
                            id="toggleTestMode"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-medium transition-all duration-200 border
                            @if(session('TESTING_SUBSCRIPTIONS', false))
                                bg-amber-50 dark:bg-amber-900/20 border-amber-200 dark:border-amber-800 text-amber-700 dark:text-amber-400 hover:bg-amber-100 dark:hover:bg-amber-900/30
                            @else
                                bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700
                            @endif">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                            </svg>
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
                <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-800">
                    <thead class="bg-slate-50/50 dark:bg-slate-900/50">
                    <tr>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                            Type & Details
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                            Package & Content
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                            Amount & Status
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                            Duration
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-right text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-slate-900 divide-y divide-slate-100 dark:divide-slate-800">
                    @foreach ($subscriptions as $subscription)
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-4">
                                    <div class="flex-shrink-0">
                                        @if($subscription['type'] === 'regular')
                                            <div
                                                class="h-10 w-10 rounded-xl bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center ring-1 ring-inset ring-indigo-600/10 dark:ring-indigo-400/20">
                                                <svg class="h-5 w-5 text-indigo-600 dark:text-indigo-400" fill="none"
                                                     stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          stroke-width="2"
                                                          d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                                </svg>
                                            </div>
                                        @else
                                            <div
                                                class="h-10 w-10 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center ring-1 ring-inset ring-emerald-600/10 dark:ring-emerald-400/20">
                                                <svg class="h-5 w-5 text-emerald-600 dark:text-emerald-400" fill="none"
                                                     stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          stroke-width="2"
                                                          d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="text-sm font-semibold text-slate-900 dark:text-slate-100">
                                            {{ $subscription['reference'] }}
                                        </div>
                                        <div class="mt-1 flex items-center gap-2">
                                            @if($subscription['type'] === 'regular')
                                                <span
                                                    class="inline-flex items-center rounded-md bg-indigo-50 dark:bg-indigo-900/30 px-2 py-1 text-xs font-medium text-indigo-700 dark:text-indigo-300 ring-1 ring-inset ring-indigo-700/10 dark:ring-indigo-400/20">
                                                    Course
                                                </span>
                                            @else
                                                <span
                                                    class="inline-flex items-center rounded-md bg-emerald-50 dark:bg-emerald-900/30 px-2 py-1 text-xs font-medium text-emerald-700 dark:text-emerald-300 ring-1 ring-inset ring-emerald-700/10 dark:ring-emerald-400/20">
                                                    Book
                                                </span>
                                            @endif
                                            <span class="text-xs text-slate-400 dark:text-slate-500">
                                                {{ $subscription['created_at'] }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm">
                                    @if($subscription['type'] === 'regular')
                                        <div
                                            class="font-semibold text-slate-900 dark:text-slate-100">{{ $subscription['package']->value ?? $subscription['package'] }}</div>
                                        <div class="mt-1 text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                            </svg>
                                            <span>{{ $subscription['subjects'] ?: 'No subjects selected' }}</span>
                                            @if($subscription['subject_count'] > 0)
                                                <span
                                                    class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400">
                                                    {{ $subscription['subject_count'] }}
                                                </span>
                                            @endif
                                        </div>
                                    @else
                                        <div
                                            class="font-semibold text-slate-900 dark:text-slate-100">{{ $subscription['book_title'] }}</div>
                                        <div class="mt-1 text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                            </svg>
                                            <span>{{ $subscription['book_author'] }}</span>
                                        </div>
                                        <div class="mt-1 text-xs text-slate-400 dark:text-slate-500">
                                            {{ $subscription['book_category'] }}
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-semibold text-slate-900 dark:text-slate-100">
                                    {{ $subscription['currency'] }} {{ number_format($subscription['amount'], 2) }}
                                </div>
                                <div class="mt-2">
                                    @if($subscription['type'] === 'regular')
                                        @if($subscription['status'] === SubscriptionStatus::PAID)
                                            <span
                                                class="inline-flex items-center rounded-md bg-emerald-50 dark:bg-emerald-900/30 px-2 py-1 text-xs font-medium text-emerald-700 dark:text-emerald-300 ring-1 ring-inset ring-emerald-600/20 dark:ring-emerald-400/20">
                                                Paid
                                            </span>
                                        @elseif($subscription['status'] === SubscriptionStatus::UNPAID)
                                            <span
                                                class="inline-flex items-center rounded-md bg-amber-50 dark:bg-amber-900/30 px-2 py-1 text-xs font-medium text-amber-700 dark:text-amber-300 ring-1 ring-inset ring-amber-600/20 dark:ring-amber-400/20">
                                                Pending
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center rounded-md bg-slate-100 dark:bg-slate-800 px-2 py-1 text-xs font-medium text-slate-700 dark:text-slate-300 ring-1 ring-inset ring-slate-600/20 dark:ring-slate-400/20">
                                                {{ $subscription['status']->value ?? $subscription['status'] }}
                                            </span>
                                        @endif
                                    @else
                                        @if($subscription['status'] === 'active')
                                            <span
                                                class="inline-flex items-center rounded-md bg-emerald-50 dark:bg-emerald-900/30 px-2 py-1 text-xs font-medium text-emerald-700 dark:text-emerald-300 ring-1 ring-inset ring-emerald-600/20 dark:ring-emerald-400/20">
                                                Active
                                            </span>
                                        @elseif($subscription['status'] === 'pending_payment')
                                            <span
                                                class="inline-flex items-center rounded-md bg-amber-50 dark:bg-amber-900/30 px-2 py-1 text-xs font-medium text-amber-700 dark:text-amber-300 ring-1 ring-inset ring-amber-600/20 dark:ring-amber-400/20">
                                                Pending Payment
                                            </span>
                                        @elseif($subscription['status'] === 'cancelled')
                                            <span
                                                class="inline-flex items-center rounded-md bg-rose-50 dark:bg-rose-900/30 px-2 py-1 text-xs font-medium text-rose-700 dark:text-rose-300 ring-1 ring-inset ring-rose-600/20 dark:ring-rose-400/20">
                                                Cancelled
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center rounded-md bg-slate-100 dark:bg-slate-800 px-2 py-1 text-xs font-medium text-slate-700 dark:text-slate-300 ring-1 ring-inset ring-slate-600/20 dark:ring-slate-400/20">
                                                {{ $subscription['status'] }}
                                            </span>
                                        @endif
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500 dark:text-slate-400">
                                <div
                                    class="font-medium text-slate-700 dark:text-slate-300">{{ $subscription['duration_in_months'] }}
                                    months
                                </div>
                                @if($subscription['expires_at'])
                                    <div
                                        class="mt-1 text-xs flex items-center gap-1 text-slate-400 dark:text-slate-500">
                                        <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor"
                                             viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        Expires {{ Carbon::parse($subscription['expires_at'])->format('M d, Y') }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-3">
                                    @if($subscription['type'] === 'regular')
                                        <a href="{{ route('subscriptions.show', $subscription['model']) }}"
                                           class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 font-medium transition-colors">
                                            View
                                        </a>
                                        @if($subscription['status'] === SubscriptionStatus::UNPAID)
                                            <form method="POST"
                                                  action="{{ route('subscriptions.destroy', $subscription['model']) }}"
                                                  class="inline"
                                                  onsubmit="return confirm('Are you sure you want to cancel this subscription?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="text-rose-600 dark:text-rose-400 hover:text-rose-800 dark:hover:text-rose-300 font-medium transition-colors">
                                                    Cancel
                                                </button>
                                            </form>
                                        @endif
                                    @else
                                        @if($subscription['status'] === 'pending_payment')
                                            <button
                                                onclick="showBookPaymentDetails('{{ $subscription['reference'] }}', '{{ $subscription['amount'] }}', '{{ $subscription['book_title'] }}')"
                                                class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 font-medium transition-colors">
                                                Pay Now
                                            </button>
                                        @elseif($subscription['status'] === 'active')
                                            <span
                                                class="text-emerald-600 dark:text-emerald-400 font-medium flex items-center gap-1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                     viewBox="0 0 24 24"><path stroke-linecap="round"
                                                                               stroke-linejoin="round" stroke-width="2"
                                                                               d="M5 13l4 4L19 7"/></svg>
                                                Active
                                            </span>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="bg-white dark:bg-slate-900 px-6 py-4 border-t border-slate-200 dark:border-slate-800">
                {{ $subscriptions->links() }}
            </div>
        </div>
    @else
        <!-- Empty State -->
        <div
            class="text-center py-16 bg-white dark:bg-slate-900 rounded-2xl border border-dashed border-slate-300 dark:border-slate-700">
            <div
                class="mx-auto h-16 w-16 bg-slate-50 dark:bg-slate-800 rounded-full flex items-center justify-center mb-4">
                <svg class="h-8 w-8 text-slate-400 dark:text-slate-500" fill="none" stroke="currentColor"
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100">No subscriptions yet</h3>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400 max-w-sm mx-auto">Get started by creating your
                first course or book subscription to track your learning journey.</p>
            <div class="mt-8 flex justify-center gap-3">
                <x-link.primary :to="route('subscriptions.create')"
                                class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-xl text-sm font-medium hover:bg-indigo-700 transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    New Course Subscription
                </x-link.primary>
                <x-link.secondary :to="route('books.index')"
                                  class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 rounded-xl text-sm font-medium hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    Browse Books
                </x-link.secondary>
            </div>
        </div>
    @endif

    <!-- Book Payment Modal -->
    <div id="bookPaymentModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog"
         aria-modal="true">
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div
                    class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-slate-900 text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md border border-slate-200 dark:border-slate-800">
                    <div class="px-6 py-6">
                        <div class="text-center">
                            <div
                                class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-indigo-50 dark:bg-indigo-900/30 mb-4">
                                <svg class="h-6 w-6 text-indigo-600 dark:text-indigo-400" fill="none"
                                     stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100" id="modal-title">
                                Complete Book Subscription Payment</h3>
                            <p class="text-sm text-slate-500 dark:text-slate-400 mt-2">Use the details below to complete
                                your payment securely.</p>

                            <div
                                class="mt-6 bg-slate-50 dark:bg-slate-800/50 rounded-xl p-4 text-left space-y-3 border border-slate-100 dark:border-slate-700">
                                <div class="flex justify-between text-sm">
                                    <span class="text-slate-500 dark:text-slate-400">Book:</span>
                                    <span class="font-medium text-slate-900 dark:text-slate-100 text-right"
                                          id="modalBookTitle"></span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-slate-500 dark:text-slate-400">Amount:</span>
                                    <span class="font-semibold text-slate-900 dark:text-slate-100">GHS <span
                                            id="modalAmount"></span></span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-slate-500 dark:text-slate-400">Reference:</span>
                                    <span
                                        class="font-mono text-xs bg-slate-200 dark:bg-slate-700 px-2 py-0.5 rounded text-slate-700 dark:text-slate-300"
                                        id="modalReference"></span>
                                </div>
                            </div>

                            <div
                                class="mt-6 space-y-2 text-sm text-slate-600 dark:text-slate-400 bg-indigo-50 dark:bg-indigo-900/20 p-4 rounded-xl border border-indigo-100 dark:border-indigo-800/30">
                                <p class="flex items-center gap-2">
                                    <span>📱</span> Dial <span
                                        class="font-mono font-semibold text-indigo-700 dark:text-indigo-300">*772*30#</span>
                                    to pay
                                </p>
                                <p class="flex items-center gap-2">
                                    <span>🏪</span> Merchant Code: <span
                                        class="font-mono font-semibold text-indigo-700 dark:text-indigo-300">1326001</span>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div
                        class="bg-slate-50 dark:bg-slate-800/50 px-6 py-4 sm:flex sm:flex-row-reverse gap-3 border-t border-slate-100 dark:border-slate-800">
                        <button type="button" onclick="closeBookPaymentModal()"
                                class="inline-flex w-full justify-center rounded-xl bg-slate-900 dark:bg-slate-700 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-slate-800 dark:hover:bg-slate-600 sm:w-auto transition-colors">
                            Close
                        </button>
                    </div>
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

                    const originalContent = this.innerHTML;
                    this.innerHTML = '<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-current" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Processing...';
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
                                location.reload();
                            } else {
                                alert('Error: ' + (data.message || 'Failed to toggle test mode'));
                                this.innerHTML = originalContent;
                                this.disabled = false;
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('An error occurred while toggling test mode');
                            this.innerHTML = originalContent;
                            this.disabled = false;
                        });
                });
            }
        });
    </script>

</x-layouts.app>
