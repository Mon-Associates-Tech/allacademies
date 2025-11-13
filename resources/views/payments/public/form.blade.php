<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Payment Form - {{ $student->getSchoolForUser()->name }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-gradient-to-br from-violet-50 via-purple-50 to-blue-50 dark:from-gray-900 dark:via-purple-900 dark:to-gray-900">
<div class="min-h-screen py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        <!-- School Header Banner -->
        <div class="bg-white dark:bg-gray-800 rounded-t-2xl shadow-lg overflow-hidden">
            <!-- School Banner with Gradient -->
            <div class="relative h-32 bg-gradient-to-r from-violet-600 via-purple-600 to-blue-600">
                <div class="absolute inset-0 bg-black/10"></div>
                <div class="absolute inset-0 flex items-center justify-center">
                    <div class="text-center">
                        @if($student->getSchoolForUser()->logo)
                            <img src="{{ asset('storage/' . $student->getSchoolForUser()->logo) }}"
                                 alt="{{ $student->getSchoolForUser()->name }}"
                                 class="h-20 w-20 mx-auto mb-2 rounded-full border-4 border-white shadow-lg">
                        @else
                            <div class="h-20 w-20 mx-auto mb-2 rounded-full border-4 border-white shadow-lg bg-white flex items-center justify-center">
                                    <span class="text-3xl font-bold text-violet-600">
                                        {{ substr($student->getSchoolForUser()->name, 0, 1) }}
                                    </span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- School Information -->
            <div class="px-6 py-4 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                <div class="text-center">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ $student->getSchoolForUser()->name }}
                    </h1>
                    @if($student->getSchoolForUser()->code)
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                            School Code: {{ $student->getSchoolForUser()->code }}
                        </p>
                    @endif
                    <div class="flex items-center justify-center gap-4 mt-2 text-sm text-gray-600 dark:text-gray-400">
                        @if($student->getSchoolForUser()->phone)
                            <span class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                    </svg>
                                    {{ $student->getSchoolForUser()->phone }}
                                </span>
                        @endif
                        @if($student->getSchoolForUser()->email)
                            <span class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                    {{ $student->getSchoolForUser()->email }}
                                </span>
                        @endif
                    </div>
                    @if($student->getSchoolForUser()->address)
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                            {{ $student->getSchoolForUser()->address }}
                        </p>
                    @endif
                </div>
            </div>

            <!-- Payment Title -->
            <div class="px-6 py-4 bg-gradient-to-r from-violet-50 to-purple-50 dark:from-gray-700 dark:to-gray-800 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-xl font-bold text-center text-gray-900 dark:text-white">
                    School Fees Payment Portal
                </h2>
                <p class="text-center text-sm text-gray-600 dark:text-gray-400 mt-1">
                    Secure online payment system
                </p>
            </div>
        </div>

        <!-- Student Information Card -->
        <div class="bg-white dark:bg-gray-800 shadow-lg p-6 border-x border-gray-200 dark:border-gray-700">
            <div class="flex items-center space-x-2 mb-3">
                <svg class="w-5 h-5 text-violet-600 dark:text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Student Information</h3>
            </div>
            <div class="flex items-start space-x-4">
                <div class="flex-shrink-0">
                    @if($student->user->profile_photo_url)
                        <img class="h-20 w-20 rounded-full border-4 border-violet-100 dark:border-violet-900"
                             src="{{ $student->user->profile_photo_url }}"
                             alt="{{ $student->user->name }}">
                    @else
                        <div class="h-20 w-20 rounded-full bg-gradient-to-br from-violet-500 to-purple-600 flex items-center justify-center text-white font-bold text-2xl border-4 border-violet-100 dark:border-violet-900 shadow-lg">
                            {{ substr($student->user->name, 0, 1) }}
                        </div>
                    @endif
                </div>
                <div class="flex-1">
                    <h4 class="text-xl font-bold text-gray-900 dark:text-white">{{ $student->user->name }}</h4>
                    <div class="mt-3 grid grid-cols-2 gap-3 text-sm">
                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-2">
                            <span class="text-gray-500 dark:text-gray-400 block text-xs">Student ID</span>
                            <span class="font-semibold text-gray-900 dark:text-white">{{ $student->student_id }}</span>
                        </div>
                        @if($student->academicGroup)
                            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-2">
                                <span class="text-gray-500 dark:text-gray-400 block text-xs">Academic Group</span>
                                <span class="font-semibold text-gray-900 dark:text-white">{{ $student->academicGroup->name }}</span>
                            </div>
                        @endif
                        @if($student->academicLevel)
                            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-2">
                                <span class="text-gray-500 dark:text-gray-400 block text-xs">Academic Level</span>
                                <span class="font-semibold text-gray-900 dark:text-white">{{ $student->academicLevel->name }}</span>
                            </div>
                        @endif
                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-2">
                            <span class="text-gray-500 dark:text-gray-400 block text-xs">Email</span>
                            <span class="font-semibold text-gray-900 dark:text-white text-xs">{{ $student->user->email }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Form -->
        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-b-2xl p-6 border-x border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center space-x-2 mb-6">
                <svg class="w-5 h-5 text-violet-600 dark:text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Payment Details</h3>
            </div>

            @if ($errors->any())
                <div class="mb-6 bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 text-red-700 dark:text-red-400 px-4 py-3 rounded-r">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <ul class="list-disc list-inside text-sm">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('payments.public.initialize') }}" id="paymentForm">
                @csrf
                <input type="hidden" name="student_id" value="{{ $student->id }}">

                <div class="space-y-6">
                    <!-- Payment Type -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            Payment Type <span class="text-red-500">*</span>
                        </label>
                        <select name="payment_type"
                                id="payment_type"
                                required
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-violet-500 focus:border-violet-500 shadow-sm">
                            <option value="">Select payment type</option>
                            @foreach($paymentOptions as $key => $option)
                                <option value="{{ $key }}"
                                        data-amount="{{ $option['amount'] }}"
                                        data-allow-custom="{{ $option['allow_custom'] ? 'true' : 'false' }}">
                                    {{ $option['name'] }} - GHS {{ number_format($option['amount'], 2) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Amount -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            Amount (GHS) <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 dark:text-gray-400 text-lg font-semibold">₵</span>
                            </div>
                            <input type="number"
                                   name="amount"
                                   id="amount"
                                   step="0.01"
                                   min="1"
                                   required
                                   placeholder="0.00"
                                   class="w-full pl-8 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-violet-500 focus:border-violet-500 shadow-sm text-lg font-semibold">
                        </div>
                        <input type="hidden" name="fixed_amount" id="fixed_amount">
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400" id="amount_hint">
                            <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                            Select a payment type to see the amount
                        </p>
                    </div>

                    <!-- Payer Information -->
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                        <div class="flex items-center space-x-2 mb-4">
                            <svg class="w-5 h-5 text-violet-600 dark:text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            <h4 class="text-md font-semibold text-gray-900 dark:text-white">Payer Information</h4>
                        </div>

                        <div class="space-y-4">
                            <!-- Payer Type -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                    I am <span class="text-red-500">*</span>
                                </label>
                                <select name="payer_type"
                                        id="payer_type"
                                        required
                                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-violet-500 focus:border-violet-500 shadow-sm">
                                    <option value="parent">Parent/Guardian</option>
                                    <option value="student">The Student</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>

                            <!-- Payer Name (for 'other' type) -->
                            <div id="payer_name_field" style="display: none;">
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                    Your Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                       name="payer_name"
                                       placeholder="Enter your full name"
                                       class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-violet-500 focus:border-violet-500 shadow-sm">
                            </div>

                            <!-- Email -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                    Email Address <span class="text-red-500">*</span>
                                </label>
                                <input type="email"
                                       name="payer_email"
                                       required
                                       placeholder="your@email.com"
                                       class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-violet-500 focus:border-violet-500 shadow-sm">
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400 flex items-center">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                    Payment receipt will be sent to this email
                                </p>
                            </div>

                            <!-- Phone -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                    Phone Number (Optional)
                                </label>
                                <input type="tel"
                                       name="payer_phone"
                                       placeholder="+233 XX XXX XXXX"
                                       class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-violet-500 focus:border-violet-500 shadow-sm">
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-between items-center pt-6 border-t border-gray-200 dark:border-gray-700">
                        <a href="{{ route('payments.public.lookup') }}"
                           class="inline-flex items-center text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white font-medium">
                            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Back
                        </a>
                        <button type="submit"
                                class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-violet-600 to-purple-600 hover:from-violet-700 hover:to-purple-700 text-white rounded-lg font-semibold shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            Proceed to Secure Payment
                        </button>
                    </div>

                    <!-- Security Badge -->
                    <div class="text-center pt-4 border-t border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-center text-xs text-gray-500 dark:text-gray-400">
                            <svg class="w-4 h-4 text-green-500 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                            </svg>
                            Secured by SSL encryption • Powered by Paystack
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Handle payment type selection
    document.getElementById('payment_type').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const amount = selectedOption.dataset.amount;
        const allowCustom = selectedOption.dataset.allowCustom === 'true';

        const amountInput = document.getElementById('amount');
        const fixedAmountInput = document.getElementById('fixed_amount');
        const amountHint = document.getElementById('amount_hint');

        if (amount) {
            amountInput.value = amount;
            fixedAmountInput.value = amount;

            if (allowCustom) {
                amountInput.removeAttribute('readonly');
                amountInput.max = amount;
                amountHint.innerHTML = `<svg class="w-4 h-4 inline mr-1 text-blue-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>You can pay a custom amount up to GHS ${parseFloat(amount).toFixed(2)}`;
                amountHint.classList.remove('text-gray-500');
                amountHint.classList.add('text-blue-600', 'dark:text-blue-400');
            } else {
                amountInput.setAttribute('readonly', 'readonly');
                amountHint.innerHTML = `<svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>Fixed amount: GHS ${parseFloat(amount).toFixed(2)}`;
                amountHint.classList.remove('text-blue-600', 'dark:text-blue-400');
                amountHint.classList.add('text-gray-500');
            }
        }
    });

    // Handle payer type selection
    document.getElementById('payer_type').addEventListener('change', function() {
        const payerNameField = document.getElementById('payer_name_field');
        const payerNameInput = payerNameField.querySelector('input');

        if (this.value === 'other') {
            payerNameField.style.display = 'block';
            payerNameInput.setAttribute('required', 'required');
        } else {
            payerNameField.style.display = 'none';
            payerNameInput.removeAttribute('required');
            payerNameInput.value = '';
        }
    });
</script>
</body>
</html>
