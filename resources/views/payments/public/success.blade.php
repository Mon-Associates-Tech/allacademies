<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Payment Successful - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-gray-50 dark:bg-gray-900">
<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full">
        <!-- Success Icon -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-green-100 dark:bg-green-900/20 mb-4">
                <svg class="w-12 h-12 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                Payment Successful!
            </h1>
            <p class="text-gray-600 dark:text-gray-400">
                Your payment has been processed successfully
            </p>
        </div>

        <!-- Payment Details Card -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 mb-6">
            <dl class="space-y-4">
                <div class="flex justify-between">
                    <dt class="text-sm text-gray-500 dark:text-gray-400">Reference</dt>
                    <dd class="text-sm font-medium text-gray-900 dark:text-white font-mono">
                        {{ $payment->reference }}
                    </dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-sm text-gray-500 dark:text-gray-400">Amount Paid</dt>
                    <dd class="text-2xl font-bold text-green-600 dark:text-green-400">
                        {{ $payment->currency }} {{ number_format($payment->amount, 2) }}
                    </dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-sm text-gray-500 dark:text-gray-400">Student</dt>
                    <dd class="text-sm font-medium text-gray-900 dark:text-white">
                        {{ $payment->student->user->name }}
                    </dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-sm text-gray-500 dark:text-gray-400">Payment Type</dt>
                    <dd class="text-sm font-medium text-gray-900 dark:text-white">
                        {{ ucfirst(str_replace('_', ' ', $payment->payment_type)) }}
                    </dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-sm text-gray-500 dark:text-gray-400">Date</dt>
                    <dd class="text-sm font-medium text-gray-900 dark:text-white">
                        {{ $payment->paid_at->format('M d, Y h:i A') }}
                    </dd>
                </div>
            </dl>
        </div>

        <!-- Actions -->
        <div class="space-y-3">
            <button onclick="window.print()"
                    class="w-full bg-violet-600 hover:bg-violet-700 text-white px-6 py-3 rounded-lg font-semibold">
                Print Receipt
            </button>
            <a href="{{ route('payments.public.lookup') }}"
               class="block w-full text-center bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-900 dark:text-white px-6 py-3 rounded-lg font-semibold">
                Make Another Payment
            </a>
        </div>

        <!-- Note -->
        <div class="mt-6 text-center">
            <p class="text-sm text-gray-600 dark:text-gray-400">
                A receipt has been sent to <span class="font-medium">{{ $payment->payer_email }}</span>
            </p>
        </div>
    </div>
</div>

<!-- Print Styles -->
<style>
    @media print {
        body * {
            visibility: hidden;
        }
        .max-w-md, .max-w-md * {
            visibility: visible;
        }
        .max-w-md {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
        button, a {
            display: none !important;
        }
    }
</style>
</body>
</html>
