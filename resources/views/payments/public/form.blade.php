<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Payment - {{ $studentsData->first()['student']->getSchoolForUser()->name ?? 'School Fees' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="antialiased bg-gray-100 dark:bg-gray-900 min-h-screen">

@php $school = $studentsData->first()['student']->getSchoolForUser(); @endphp

<div class="min-h-screen" x-data="{
    totalAmount: 0,
    updateTotal() {
        this.totalAmount = Array.from(document.querySelectorAll('.payment-amount'))
            .reduce((sum, input) => sum + (parseFloat(input.value) || 0), 0);
    },
    setAmount(index, select) {
        const option = select.options[select.selectedIndex];
        const amount = option.dataset.amount;
        const allowCustom = option.dataset.allowCustom === 'true';
        const input = document.getElementById('amount_' + index);

        if (amount && input) {
            input.value = amount;
            input.readOnly = !allowCustom;
            input.classList.toggle('bg-gray-100', !allowCustom);
            input.classList.toggle('dark:bg-gray-600', !allowCustom);
        }
        this.updateTotal();
    }
}">

    <!-- Compact Header -->
    <header class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    @if($school->logo)
                        <img src="{{ asset('storage/' . $school->logo) }}" alt="{{ $school->name }}" class="h-12 w-12 rounded-lg object-cover">
                    @else
                        <div class="h-12 w-12 rounded-lg bg-gradient-to-br from-violet-500 to-purple-600 flex items-center justify-center">
                            <span class="text-xl font-bold text-white">{{ substr($school->name, 0, 1) }}</span>
                        </div>
                    @endif
                    <div>
                        <h1 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $school->name }}</h1>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Fee Payment Portal</p>
                    </div>
                </div>
                <a href="{{ route('payments.public.lookup') }}" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-4xl mx-auto px-4 sm:px-6 py-8">
        <form method="POST" action="{{ route('payments.public.initialize') }}">
            @csrf

            <!-- Error Messages -->
            @if ($errors->any())
                <div class="mb-6 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 rounded-xl p-4">
                    <div class="flex">
                        <svg class="h-5 w-5 text-red-400 mr-3 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        <ul class="text-sm text-red-600 dark:text-red-400 list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            @if(session('warning'))
                <div class="mb-6 bg-amber-50 dark:bg-amber-900/30 border border-amber-200 dark:border-amber-800 rounded-xl p-4">
                    <div class="flex items-center">
                        <svg class="h-5 w-5 text-amber-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        <p class="text-sm text-amber-700 dark:text-amber-300">{{ session('warning') }}</p>
                    </div>
                </div>
            @endif

            <!-- Students & Payments Section -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden mb-6">
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <h2 class="text-base font-semibold text-gray-900 dark:text-white flex items-center">
                            <svg class="w-5 h-5 mr-2 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            Students ({{ $studentsData->count() }})
                        </h2>
                        <span class="text-sm text-gray-500 dark:text-gray-400">Specify amount for each student</span>
                    </div>
                </div>

                <div class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach($studentsData as $index => $data)
                        @php
                            $student = $data['student'];
                            $options = $data['options'];
                        @endphp
                        <div class="p-6 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                            <input type="hidden" name="payments[{{ $index }}][student_id]" value="{{ $student->id }}">

                            <div class="flex flex-col lg:flex-row lg:items-start gap-6">
                                <!-- Student Info -->
                                <div class="flex items-center space-x-4 lg:w-1/3">
                                    <div class="flex-shrink-0">
                                        @if($student->user->profile_photo_url ?? null)
                                            <img class="h-12 w-12 rounded-full object-cover" src="{{ $student->user->profile_photo_url }}" alt="">
                                        @else
                                            <div class="h-12 w-12 rounded-full bg-gradient-to-br from-violet-400 to-purple-500 flex items-center justify-center text-white font-semibold text-lg">
                                                {{ strtoupper(substr($student->user->name, 0, 1)) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $student->user->name }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $student->student_id }}</p>
                                        @if($student->academicLevel)
                                            <p class="text-xs text-violet-600 dark:text-violet-400">{{ $student->academicLevel->name }}</p>
                                        @endif
                                    </div>
                                </div>

                                <!-- Payment Fields -->
                                <div class="flex-1 grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5">Fee Type</label>
                                        <select name="payments[{{ $index }}][payment_type]"
                                                id="payment_type_{{ $index }}"
                                                required
                                                x-init="$nextTick(() => { setAmount({{ $index }}, $el); updateTotal(); })"
                                                @change="setAmount({{ $index }}, $event.target)"
                                                class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-violet-500 focus:border-violet-500 shadow-sm">
                                            @foreach($options as $key => $option)
                                                <option value="{{ $key }}"
                                                        data-amount="{{ $option['amount'] }}"
                                                        data-allow-custom="{{ $option['allow_custom'] ? 'true' : 'false' }}"
                                                    {{ ($option['is_default'] ?? false) ? 'selected' : '' }}>
                                                    {{ $option['name'] }} @if($option['amount'] > 0)- GHS {{ number_format($option['amount'], 2) }}@endif
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5">Amount (GHS)</label>
                                        <div class="relative">
                                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm font-medium">₵</span>
                                            <input type="number"
                                                   id="amount_{{ $index }}"
                                                   name="payments[{{ $index }}][amount]"
                                                   step="0.01"
                                                   min="1"
                                                   required
                                                   value="{{ $options['tuition']['amount'] ?? '' }}"
                                                   @input="updateTotal()"
                                                   placeholder="0.00"
                                                   class="payment-amount w-full text-sm pl-8 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-violet-500 focus:border-violet-500 shadow-sm">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Payer Information -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden mb-6">
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-base font-semibold text-gray-900 dark:text-white flex items-center">
                        <svg class="w-5 h-5 mr-2 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Your Information
                    </h2>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">I am a <span class="text-red-500">*</span></label>
                            <select name="payer_type" required class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-violet-500 focus:border-violet-500 shadow-sm">
                                <option value="parent">Parent / Guardian</option>
                                <option value="student">Student</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Email Address <span class="text-red-500">*</span></label>
                            <input type="email" name="payer_email" required placeholder="you@example.com" class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-violet-500 focus:border-violet-500 shadow-sm">
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Receipt will be sent here</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Full Name</label>
                            <input type="text" name="payer_name" placeholder="Your name" class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-violet-500 focus:border-violet-500 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Phone Number</label>
                            <input type="tel" name="payer_phone" placeholder="+233 XX XXX XXXX" class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-violet-500 focus:border-violet-500 shadow-sm">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Summary & Submit -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="p-6">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div class="text-center sm:text-left">
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Total Amount</p>
                            <p class="text-3xl font-bold text-gray-900 dark:text-white">
                                GHS <span x-text="totalAmount.toFixed(2)">0.00</span>
                            </p>
                        </div>

                        <button type="submit"
                                :disabled="totalAmount <= 0"
                                :class="{ 'opacity-50 cursor-not-allowed': totalAmount <= 0 }"
                                class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-4 text-base font-semibold text-white bg-gradient-to-r from-violet-600 to-purple-600 hover:from-violet-700 hover:to-purple-700 rounded-xl shadow-lg hover:shadow-xl transition-all duration-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            Proceed to Payment
                        </button>
                    </div>

                    <!-- Security Note -->
                    <div class="mt-6 pt-4 border-t border-gray-100 dark:border-gray-700">
                        <div class="flex items-center justify-center text-xs text-gray-400 dark:text-gray-500">
                            <svg class="w-4 h-4 mr-1.5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                            </svg>
                            <span>Secured with SSL encryption • Powered by Paystack</span>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </main>

    <!-- Footer -->
    <footer class="max-w-4xl mx-auto px-4 sm:px-6 py-6 text-center">
        <p class="text-sm text-gray-500 dark:text-gray-400">
            Questions? Contact {{ $school->name }} administration
            @if($school->phone) at {{ $school->phone }} @endif
        </p>
    </footer>
</div>

</body>
</html>
