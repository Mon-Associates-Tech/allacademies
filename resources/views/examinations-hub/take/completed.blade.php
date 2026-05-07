<x-layouts.exam>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-green-50 to-blue-100 dark:from-gray-900 dark:to-gray-800 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full">
            <div class="bg-white dark:bg-gray-800 shadow-2xl rounded-lg p-8 text-center">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 dark:bg-green-900 mb-4">
                    <svg class="h-10 w-10 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>

                <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                    Examination Submitted!
                </h2>
                
                <p class="text-gray-600 dark:text-gray-400 mb-6">
                    Your responses have been successfully submitted for <strong>{{ $exam->title }}</strong>
                </p>

                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-6">
                    <p class="text-sm text-blue-800 dark:text-blue-200">
                        @if($exam->canShowResults())
                            Your results will be available shortly. Please check back later.
                        @else
                            Your results will be released by the examiner. You will be notified when they are available.
                        @endif
                    </p>
                </div>

                <div class="space-y-3">
                    <a href="{{ route('examinations-hub.take.join') }}" class="block w-full px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium">
                        Take Another Exam
                    </a>
                    
                    <a href="{{ url('/') }}" class="block w-full px-6 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
                        Return to Home
                    </a>
                </div>
            </div>

            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Thank you for participating!
                </p>
            </div>
        </div>
    </div>
</x-layouts.exam>
