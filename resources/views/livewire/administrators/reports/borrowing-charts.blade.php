<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <!-- Return Status Chart -->
    <div class="bg-gray-50 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Return Status Distribution</h3>
        <div class="h-64">
            <canvas id="returnStatusChart"></canvas>
        </div>
        <div class="mt-4 grid grid-cols-3 gap-4 text-center">
            <div class="bg-white rounded-lg p-3">
                <div class="text-2xl font-bold text-red-600">{{ $chartData['returnStatus']['overdue'] }}</div>
                <div class="text-sm text-gray-600">Overdue</div>
            </div>
            <div class="bg-white rounded-lg p-3">
                <div class="text-2xl font-bold text-green-600">{{ $chartData['returnStatus']['onTime'] }}</div>
                <div class="text-sm text-gray-600">On Time</div>
            </div>
            <div class="bg-white rounded-lg p-3">
                <div class="text-2xl font-bold text-yellow-600">{{ $chartData['returnStatus']['late'] }}</div>
                <div class="text-sm text-gray-600">Late</div>
            </div>
        </div>
    </div>

    <!-- Daily Borrowings Trend -->
    <div class="bg-gray-50 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Daily Borrowings Trend</h3>
        <div class="h-64">
            <canvas id="dailyBorrowingsChart"></canvas>
        </div>
        <div class="mt-4 text-center">
            <div class="text-sm text-gray-600">
                Total Borrowings: <span class="font-semibold">{{ collect($chartData['dailyBorrowings']['data'] ?? [])->sum() }}</span>
            </div>
        </div>
    </div>

    <!-- Category Distribution -->
    <div class="lg:col-span-2 bg-gray-50 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Borrowings by Book Category</h3>
        <div class="h-64">
            <canvas id="categoryBorrowingsChart"></canvas>
        </div>
    </div>
</div>
