<div class="space-y-6">
    <!-- Header with Toggle -->
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Payment Management</h1>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    {{ $viewMode === 'transactions' ? 'View all payment transactions' : 'View student payment status' }}
                </p>
            </div>
            <div class="mt-4 md:mt-0 flex gap-3">
                <a href="{{ route('admin.payments.setup') }}" 
                   class="px-4 py-2 bg-violet-600 text-white rounded-md hover:bg-violet-700 focus:outline-none focus:ring-2 focus:ring-violet-500">
                    <svg class="w-5 h-5 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Setup Payments
                </a>
                <button wire:click="toggleViewMode" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <svg class="w-5 h-5 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                    </svg>
                    {{ $viewMode === 'transactions' ? 'View Student Status' : 'View Transactions' }}
                </button>
            </div>
        </div>

        <!-- Statistics Cards -->
        @if($viewMode === 'transactions')
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-green-600 dark:text-green-400">Total Collected</p>
                            <p class="text-2xl font-bold text-green-700 dark:text-green-300">
                                GHS {{ number_format($stats['total_collected'] ?? 0, 2) }}
                            </p>
                        </div>
                        <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <p class="mt-2 text-xs text-green-600 dark:text-green-400">
                        {{ $stats['succeeded_count'] ?? 0 }} successful transactions
                    </p>
                </div>

                <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-yellow-600 dark:text-yellow-400">Pending</p>
                            <p class="text-2xl font-bold text-yellow-700 dark:text-yellow-300">
                                GHS {{ number_format($stats['pending_amount'] ?? 0, 2) }}
                            </p>
                        </div>
                        <svg class="w-8 h-8 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <p class="mt-2 text-xs text-yellow-600 dark:text-yellow-400">
                        {{ $stats['pending_count'] ?? 0 }} pending payments
                    </p>
                </div>

                <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-blue-600 dark:text-blue-400">This Month</p>
                            <p class="text-2xl font-bold text-blue-700 dark:text-blue-300">
                                GHS {{ number_format($stats['this_month'] ?? 0, 2) }}
                            </p>
                        </div>
                        <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                    </div>
                </div>

                <div class="bg-red-50 dark:bg-red-900/20 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-red-600 dark:text-red-400">Failed</p>
                            <p class="text-2xl font-bold text-red-700 dark:text-red-300">
                                {{ $stats['failed_count'] ?? 0 }}
                            </p>
                        </div>
                        <svg class="w-8 h-8 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-blue-600 dark:text-blue-400">Total Expected</p>
                            <p class="text-2xl font-bold text-blue-700 dark:text-blue-300">
                                GHS {{ number_format($stats['total_expected'] ?? 0, 2) }}
                            </p>
                        </div>
                        <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                </div>

                <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-green-600 dark:text-green-400">Collected</p>
                            <p class="text-2xl font-bold text-green-700 dark:text-green-300">
                                GHS {{ number_format($stats['total_collected'] ?? 0, 2) }}
                            </p>
                        </div>
                        <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <p class="mt-2 text-xs text-green-600 dark:text-green-400">
                        {{ $stats['paid_count'] ?? 0 }} fully paid
                    </p>
                </div>

                <div class="bg-orange-50 dark:bg-orange-900/20 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-orange-600 dark:text-orange-400">Outstanding</p>
                            <p class="text-2xl font-bold text-orange-700 dark:text-orange-300">
                                GHS {{ number_format($stats['total_outstanding'] ?? 0, 2) }}
                            </p>
                        </div>
                        <svg class="w-8 h-8 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <p class="mt-2 text-xs text-orange-600 dark:text-orange-400">
                        {{ $stats['partial_count'] ?? 0 }} partial, {{ $stats['unpaid_count'] ?? 0 }} unpaid
                    </p>
                </div>

                <div class="bg-red-50 dark:bg-red-900/20 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-red-600 dark:text-red-400">Overdue</p>
                            <p class="text-2xl font-bold text-red-700 dark:text-red-300">
                                {{ $stats['overdue_count'] ?? 0 }}
                            </p>
                        </div>
                        <svg class="w-8 h-8 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Filters Section -->
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Search -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Search</label>
                <input type="text" wire:model.live.debounce.300ms="searchTerm"
                       placeholder="Name, email, reference..."
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Payment Type -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Payment Type</label>
                <select wire:model.live="filterPaymentType" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                    <option value="">All Types</option>
                    @foreach($filterOptions['payment_types'] as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Status -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                <select wire:model.live="filterStatus" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                    <option value="">All Status</option>
                    @if($viewMode === 'transactions')
                        <option value="succeeded">Succeeded</option>
                        <option value="pending">Pending</option>
                        <option value="failed">Failed</option>
                    @else
                        <option value="no_obligations">No Obligations</option>
                        <option value="paid">Fully Paid</option>
                        <option value="partial">Partial Payment</option>
                        <option value="unpaid">Unpaid</option>
                        <option value="overdue">Overdue</option>
                    @endif
                </select>
            </div>

            <!-- Academic Group -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Academic Group</label>
                <select wire:model.live="filterAcademicGroup" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                    <option value="">All Groups</option>
                    @foreach($filterOptions['academic_groups'] as $group)
                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Academic Level -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Academic Level</label>
                <select wire:model.live="filterAcademicLevel" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                    <option value="">All Levels</option>
                    @foreach($filterOptions['academic_levels'] as $level)
                        <option value="{{ $level->id }}">{{ $level->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Academic Year -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Academic Year</label>
                <select wire:model.live="filterAcademicYear" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                    <option value="">All Years</option>
                    @foreach($filterOptions['academic_years'] as $year)
                        <option value="{{ $year->id }}">{{ $year->getDisplayName() }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Academic Period -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Academic Period</label>
                <select wire:model.live="filterAcademicPeriod" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                    <option value="">All Periods</option>
                    @foreach($filterOptions['academic_periods'] as $period)
                        <option value="{{ $period->id }}">{{ $period->name }}</option>
                    @endforeach
                </select>
            </div>

            @if($viewMode === 'transactions')
                <!-- Date From -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date From</label>
                    <input type="date" wire:model.live="filterDateFrom"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                </div>
            @endif
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-between pt-4 mt-4 border-t border-gray-200 dark:border-gray-700">
            <button wire:click="clearFilters" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200">
                Clear Filters
            </button>
        </div>
    </div>

    <!-- Data Table -->
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden">
        @if($data->count() > 0)
            <div class="overflow-x-auto">
                @if($viewMode === 'transactions')
                    @include('livewire.administrators.partials.transactions-table', ['payments' => $data])
                @else
                    @include('livewire.administrators.partials.student-payments-table', ['studentPayments' => $data])
                @endif
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $data->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No data found</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Try adjusting your filters to find what you're looking for.
                </p>
            </div>
        @endif
    </div>
</div>
