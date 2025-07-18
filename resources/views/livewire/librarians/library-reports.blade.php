<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-2">Library Reports</h1>
        <p class="text-gray-600 dark:text-gray-400">Generate detailed reports and analyze library performance</p>
    </div>

    <!-- Report Controls -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <!-- Report Type -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Report Type</label>
                <select
                    wire:model.live="reportType"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300"
                >
                    <option value="overview">Overview</option>
                    <option value="borrowing">Borrowing Activity</option>
                    <option value="returns">Returns</option>
                    <option value="overdue">Overdue Books</option>
                    <option value="popular_books">Popular Books</option>
                    <option value="student_activity">Student Activity</option>
                    <option value="inventory">Inventory</option>
                </select>
            </div>

            <!-- Date Range -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date Range</label>
                <select
                    wire:model.live="dateRange"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300"
                >
                    <option value="7">Last 7 days</option>
                    <option value="30">Last 30 days</option>
                    <option value="90">Last 90 days</option>
                    <option value="365">Last year</option>
                </select>
            </div>

            <!-- Start Date -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Start Date</label>
                <input
                    type="date"
                    wire:model.live="startDate"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300"
                >
            </div>

            <!-- End Date -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">End Date</label>
                <input
                    type="date"
                    wire:model.live="endDate"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300"
                >
            </div>

            <!-- Export Button -->
            <div class="flex items-end">
                <button
                    wire:click="exportReport"
                    class="w-full px-4 py-2 text-sm font-medium text-white bg-violet-600 rounded-lg hover:bg-violet-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-violet-500"
                >
                    Export Report
                </button>
            </div>
        </div>
    </div>

    <!-- Report Content -->
    <div class="space-y-6">
        @if ($reportType === 'overview')
            @include('livewire.librarians.reports.overview')
        @elseif ($reportType === 'borrowing')
            @include('livewire.librarians.reports.borrowing')
        @elseif ($reportType === 'returns')
            @include('livewire.librarians.reports.returns')
        @elseif ($reportType === 'overdue')
            @include('livewire.librarians.reports.overdue')
        @elseif ($reportType === 'popular_books')
            @include('livewire.librarians.reports.popular-books')
        @elseif ($reportType === 'student_activity')
            @include('livewire.librarians.reports.student-activity')
        @elseif ($reportType === 'inventory')
            @include('livewire.librarians.reports.inventory')
        @endif
    </div>
</div>
