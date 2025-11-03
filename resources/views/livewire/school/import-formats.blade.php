<x-layouts.app>
    <div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-gray-50 dark:from-gray-900 dark:via-gray-900 dark:to-gray-800">
        <!-- Hero Section -->
        <div class="bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 text-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
                <div class="text-center">
                    <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-white/10 backdrop-blur-sm mb-6">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                    </div>
                    <h1 class="text-4xl font-extrabold sm:text-5xl md:text-6xl">
                        Data Import Templates
                    </h1>
                    <p class="mt-4 text-xl text-indigo-100 max-w-3xl mx-auto">
                        Download CSV templates and learn the correct format for importing students, teachers, librarians, administrators, and parents into your system.
                    </p>
                </div>
            </div>
        </div>

        <!-- Quick Navigation -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-8">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex flex-wrap items-center justify-center gap-4">
                    <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Quick Jump:</span>
                    @foreach($formats as $type => $format)
                        <a href="#{{ $type }}" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-indigo-50 to-purple-50 dark:from-indigo-900/20 dark:to-purple-900/20 text-indigo-700 dark:text-indigo-300 rounded-lg hover:from-indigo-100 hover:to-purple-100 dark:hover:from-indigo-900/40 dark:hover:to-purple-900/40 transition">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                            {{ $format['title'] }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Important Guidelines Banner -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="bg-gradient-to-r from-blue-50 to-cyan-50 dark:from-blue-900/20 dark:to-cyan-900/20 border-2 border-blue-200 dark:border-blue-800 rounded-2xl p-8 shadow-lg">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-4 flex-1">
                        <h3 class="text-lg font-bold text-blue-900 dark:text-blue-300 mb-3">📋 Before You Import - Important Guidelines</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="flex items-start space-x-2">
                                <svg class="w-5 h-5 text-green-600 dark:text-green-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-sm text-blue-800 dark:text-blue-200"><strong>File Format:</strong> CSV or Excel (.csv, .xlsx, .xls)</span>
                            </div>
                            <div class="flex items-start space-x-2">
                                <svg class="w-5 h-5 text-green-600 dark:text-green-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-sm text-blue-800 dark:text-blue-200"><strong>File Size:</strong> Maximum 10MB per file</span>
                            </div>
                            <div class="flex items-start space-x-2">
                                <svg class="w-5 h-5 text-green-600 dark:text-green-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-sm text-blue-800 dark:text-blue-200"><strong>Headers:</strong> First row must contain exact column names</span>
                            </div>
                            <div class="flex items-start space-x-2">
                                <svg class="w-5 h-5 text-green-600 dark:text-green-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-sm text-blue-800 dark:text-blue-200"><strong>Encoding:</strong> UTF-8 encoding recommended</span>
                            </div>
                            <div class="flex items-start space-x-2">
                                <svg class="w-5 h-5 text-green-600 dark:text-green-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-sm text-blue-800 dark:text-blue-200"><strong>Dates:</strong> Use YYYY-MM-DD format (e.g., 2024-01-15)</span>
                            </div>
                            <div class="flex items-start space-x-2">
                                <svg class="w-5 h-5 text-green-600 dark:text-green-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-sm text-blue-800 dark:text-blue-200"><strong>Unique Emails:</strong> Each email must be unique across the system</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Import Format Cards -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-16 space-y-12">
            @foreach($formats as $type => $format)
                <div id="{{ $type }}" class="scroll-mt-8">
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden transform hover:scale-[1.01] transition duration-300">
                        <!-- Card Header -->
                        <div class="bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 px-8 py-6">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-4">
                                    <div class="p-3 bg-white/20 backdrop-blur-sm rounded-xl">
                                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h2 class="text-2xl font-bold text-white">{{ $format['title'] }}</h2>
                                        <p class="text-indigo-100 mt-1">{{ $format['description'] }}</p>
                                    </div>
                                </div>

                                <a href="{{ route('school.download-template', ['type' => $type]) }}" class="inline-flex items-center px-6 py-3 bg-white text-indigo-600 rounded-xl font-semibold hover:bg-indigo-50 transition shadow-lg hover:shadow-xl">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                    </svg>
                                    Download Template
                                </a>
                            </div>
                        </div>

                        <!-- Card Body -->
                        <div class="p-8">
                            <!-- Required Columns -->
                            <div class="mb-8">
                                <div class="flex items-center mb-4">
                                    <div class="p-2 bg-red-100 dark:bg-red-900/30 rounded-lg mr-3">
                                        <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Required Columns</h3>
                                    <span class="ml-3 inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                        Must Include
                                    </span>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    @foreach($format['required_columns'] as $column => $desc)
                                        <div class="flex items-start p-4 bg-red-50 dark:bg-red-900/10 border-l-4 border-red-500 rounded-r-lg">
                                            <svg class="w-5 h-5 text-red-500 mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v3.586L7.707 9.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 10.586V7z" clip-rule="evenodd"/>
                                            </svg>
                                            <div>
                                                <code class="text-sm font-mono font-semibold text-red-700 dark:text-red-300 bg-red-100 dark:bg-red-900/30 px-2 py-1 rounded">{{ $column }}</code>
                                                <p class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $desc }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Optional Columns -->
                            <div>
                                <div class="flex items-center mb-4">
                                    <div class="p-2 bg-green-100 dark:bg-green-900/30 rounded-lg mr-3">
                                        <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Optional Columns</h3>
                                    <span class="ml-3 inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                        Recommended
                                    </span>
                                </div>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($format['optional_columns'] as $column)
                                        <span class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-300 rounded-lg font-mono text-sm hover:from-green-100 hover:to-emerald-100 dark:hover:from-green-900/40 dark:hover:to-emerald-900/40 transition">
                                            <svg class="w-4 h-4 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                            {{ $column }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Example Row -->
                            <div class="mt-8 p-6 bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900/50 dark:to-gray-800/50 rounded-xl border border-gray-200 dark:border-gray-700">
                                <div class="flex items-center mb-3">
                                    <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Example Data Format:</h4>
                                </div>
                                <div class="overflow-x-auto">
                                    <div class="inline-block min-w-full align-middle">
                                        <code class="text-xs font-mono bg-white dark:bg-gray-800 p-4 rounded-lg block border border-gray-200 dark:border-gray-700 whitespace-pre text-gray-800 dark:text-gray-200">{{ $type }}_example.csv</code>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Additional Resources -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
            <div class="bg-gradient-to-r from-purple-600 to-indigo-600 rounded-2xl shadow-2xl p-8 text-white">
                <div class="text-center">
                    <h2 class="text-2xl font-bold mb-4">Need Help?</h2>
                    <p class="text-purple-100 mb-6 max-w-2xl mx-auto">
                        If you have questions about the import process or need assistance with data formatting, our support team is here to help.
                    </p>
                    <div class="flex flex-wrap items-center justify-center gap-4">
                        <a href="{{ route('school.comprehensive-view') }}" class="inline-flex items-center px-6 py-3 bg-white text-indigo-600 rounded-xl font-semibold hover:bg-indigo-50 transition shadow-lg">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Back to Dashboard
                        </a>
                        <a href="mailto:support@allacademies.com" class="inline-flex items-center px-6 py-3 bg-purple-700 text-white rounded-xl font-semibold hover:bg-purple-800 transition">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            Contact Support
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
