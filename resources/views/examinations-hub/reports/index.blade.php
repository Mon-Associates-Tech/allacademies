<x-layouts.app>
    <x-examinations-hub.navigation active="reports" />
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Performance Reports</h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">Generate comprehensive performance analysis with AI or standard reporting</p>
        </div>

        <!-- Info Card -->
        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-6 mb-8">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">About Performance Reports</h3>
                    <div class="mt-2 text-sm text-blue-700 dark:text-blue-300">
                        <p>Our reporting system analyzes your examination data and generates comprehensive insights including:</p>
                        <ul class="list-disc list-inside mt-2 space-y-1">
                            <li>Subject performance analysis with high and low performers</li>
                            <li>Grade distribution and trends</li>
                            <li>Configured participants vs actual turnout analysis</li>
                            <li>Daily submission patterns and engagement metrics</li>
                            <li>Top and bottom performer identification</li>
                            <li>Actionable recommendations for improvement</li>
                        </ul>
                        <p class="mt-3"><strong>Choose between:</strong></p>
                        <ul class="list-disc list-inside mt-1 space-y-1">
                            <li><strong>AI-Powered:</strong> Advanced insights with natural language analysis (uses AI tokens)</li>
                            <li><strong>Standard:</strong> Structured data-driven report (no AI tokens required)</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Report Generation Form -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Generate New Report</h2>
            </div>

            <form method="POST" action="{{ route('examinations-hub.reports.generate') }}" class="p-6">
                @csrf

                @if($errors->any())
                    <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                        <p class="text-sm text-red-600 dark:text-red-400">{{ $errors->first() }}</p>
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Start Date
                        </label>
                        <input type="date" 
                               name="start_date" 
                               id="start_date" 
                               value="{{ old('start_date', now()->subMonth()->format('Y-m-d')) }}"
                               required
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white">
                    </div>

                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            End Date
                        </label>
                        <input type="date" 
                               name="end_date" 
                               id="end_date" 
                               value="{{ old('end_date', now()->format('Y-m-d')) }}"
                               required
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white">
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                        Report Type
                    </label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <label class="relative flex items-start p-4 border-2 border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors has-[:checked]:border-indigo-600 has-[:checked]:bg-indigo-50 dark:has-[:checked]:bg-indigo-900/20">
                            <input type="radio" name="use_ai" value="1" checked class="mt-1 h-4 w-4 text-indigo-600 focus:ring-indigo-500">
                            <div class="ml-3">
                                <div class="flex items-center gap-2">
                                    <svg class="h-5 w-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                                    </svg>
                                    <span class="font-semibold text-gray-900 dark:text-white">AI-Powered Report</span>
                                </div>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Advanced insights with natural language analysis and contextual recommendations</p>
                                <p class="text-xs text-yellow-600 dark:text-yellow-400 mt-2">⚠️ Uses 2,000-4,000 AI tokens</p>
                            </div>
                        </label>
                        
                        <label class="relative flex items-start p-4 border-2 border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors has-[:checked]:border-green-600 has-[:checked]:bg-green-50 dark:has-[:checked]:bg-green-900/20">
                            <input type="radio" name="use_ai" value="0" class="mt-1 h-4 w-4 text-green-600 focus:ring-green-500">
                            <div class="ml-3">
                                <div class="flex items-center gap-2">
                                    <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <span class="font-semibold text-gray-900 dark:text-white">Standard Report</span>
                                </div>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Structured data-driven report with key metrics and performance indicators</p>
                                <p class="text-xs text-green-600 dark:text-green-400 mt-2">✓ No AI tokens required</p>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-lg p-4 mb-6">
                    <div class="flex items-start">
                        <svg class="h-5 w-5 text-gray-600 dark:text-gray-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div class="ml-3">
                            <p class="text-sm text-gray-700 dark:text-gray-300">
                                <strong>Tip:</strong> Use standard reports for quick overviews and AI-powered reports for deeper insights and recommendations.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <a href="{{ route('examinations-hub.dashboard') }}" 
                       class="px-6 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium flex items-center">
                        <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Generate Report
                    </button>
                </div>
            </form>
        </div>

        <!-- Quick Date Presets -->
        <div class="mt-6 grid grid-cols-2 md:grid-cols-4 gap-4">
            <button onclick="setDateRange(7)" 
                    class="px-4 py-3 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 text-sm font-medium text-gray-700 dark:text-gray-300">
                Last 7 Days
            </button>
            <button onclick="setDateRange(30)" 
                    class="px-4 py-3 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 text-sm font-medium text-gray-700 dark:text-gray-300">
                Last 30 Days
            </button>
            <button onclick="setDateRange(90)" 
                    class="px-4 py-3 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 text-sm font-medium text-gray-700 dark:text-gray-300">
                Last 3 Months
            </button>
            <button onclick="setDateRange(365)" 
                    class="px-4 py-3 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 text-sm font-medium text-gray-700 dark:text-gray-300">
                Last Year
            </button>
        </div>

    </div>
</div>

<script>
function setDateRange(days) {
    const endDate = new Date();
    const startDate = new Date();
    startDate.setDate(startDate.getDate() - days);
    
    document.getElementById('start_date').value = startDate.toISOString().split('T')[0];
    document.getElementById('end_date').value = endDate.toISOString().split('T')[0];
}
</script>
</x-layouts.app>
