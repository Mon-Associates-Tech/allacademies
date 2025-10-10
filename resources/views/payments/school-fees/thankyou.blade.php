<x-layouts.app page-name="Payment Successful">
    <div class="min-h-screen flex flex-col justify-center items-center bg-gray-50 px-4">
        <div class="bg-white shadow-lg rounded-2xl p-8 text-center max-w-md w-full">
            <!-- Success Icon -->
            <div class="flex justify-center mb-4">
                <svg class="w-16 h-16 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12l2 2l4-4m5 2a9 9 0 1 1-18 0a9 9 0 0 1 18 0z"/>
                </svg>
            </div>

            <!-- Title -->
            <h2 class="text-2xl font-semibold text-gray-800 mb-2">Payment Successful!</h2>
            <p class="text-gray-600 mb-6">Thank you for completing your payment.</p>

            <!-- Payment Details Card -->
            <div class="bg-gray-100 rounded-xl p-4 mb-6 text-left">
                <p class="text-sm text-gray-700 mb-1">
                    <span class="font-semibold">Student:</span>
                    {{ $student->user->name ?? 'Unknown Student' }}
                </p>
                <p class="text-sm text-gray-700 mb-1">
                    <span class="font-semibold">Amount Paid:</span>
                    ₵{{ number_format($amount ?? 0, 2) }}
                </p>
                <p class="text-sm text-gray-700">
                    <span class="font-semibold">Reference:</span>
                    {{ $reference ?? 'N/A' }}
                </p>
            </div>

            <!-- Go Back Button -->
            <a href="{{ url('/dashboard') }}"
               class="inline-flex items-center px-6 py-2 bg-violet-600 text-white font-semibold rounded-lg hover:bg-violet-700 transition duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Go Back to Dashboard
            </a>
        </div>
    </div>
</x-layouts.app>
