@extends('components.layouts.guest')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto">
            <!-- Breadcrumb -->
            <nav class="mb-6">
                <ol class="flex items-center space-x-2 text-sm text-gray-600 dark:text-gray-400">
                    <li><a href="{{ route('sponsorship.programs.index') }}" class="hover:text-blue-600">Programs</a></li>
                    <li><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg></li>
                    <li><a href="{{ route('sponsorship.programs.show', $program) }}" class="hover:text-blue-600">{{ $program->name }}</a></li>
                    <li><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg></li>
                    <li class="text-gray-900 dark:text-white">Contribute</li>
                </ol>
            </nav>

            <!-- Program Summary -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-2">{{ $program->name }}</h2>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-400">By {{ $program->user->name }}</span>
                    <span class="text-gray-600 dark:text-gray-400">GHS {{ number_format($program->amount_raised, 2) }} raised of GHS {{ number_format($program->amount_goal, 2) }}</span>
                </div>
            </div>

            <!-- Donation Form -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-8">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Make a Contribution</h1>

                @if($errors->any())
                    <div class="mb-6 p-4 bg-red-50 dark:bg-red-900 border border-red-200 dark:border-red-700 rounded-lg">
                        <ul class="list-disc list-inside text-sm text-red-800 dark:text-red-200">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('sponsorship.programs.contribute.initialize', $program) }}" id="donationForm">
                    @csrf
                    <input type="hidden" name="sponsorship_program_id" value="{{ $program->id }}">

                    <!-- Amount -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Contribution Amount (GHS) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="amount" id="amount" step="0.01" min="1" required
                               class="w-full px-4 py-3 text-lg border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500"
                               placeholder="0.00" value="{{ old('amount') }}">
                    </div>

                    <!-- Sponsor Covers Fee -->
                    <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900 border border-blue-200 dark:border-blue-700 rounded-lg">
                        <label class="flex items-start cursor-pointer">
                            <input type="checkbox" name="sponsor_covers_fee" id="sponsor_covers_fee" value="1"
                                   class="mt-1 rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500">
                            <div class="ml-3">
                                <span class="text-sm font-medium text-gray-900 dark:text-white">Cover the platform fee</span>
                                <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                                    A {{ $platformFeePercentage }} platform fee helps us maintain and improve the service.
                                    Check this box to cover this fee so 100% of your contribution goes to the program.
                                </p>
                            </div>
                        </label>
                    </div>

                    <!-- Your Information -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Your Information</h3>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Full Name
                                </label>
                                <input type="text" name="payer_name"
                                       value="{{ auth()->check() ? auth()->user()->name : old('payer_name') }}"
                                       class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500"
                                       placeholder="John Doe">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Email Address <span class="text-red-500">*</span>
                                </label>
                                <input type="email" name="payer_email" required
                                       value="{{ auth()->check() ? auth()->user()->email : old('payer_email') }}"
                                       class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500"
                                       placeholder="john@example.com">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Phone Number (Optional)
                                </label>
                                <input type="tel" name="payer_phone" value="{{ old('payer_phone') }}"
                                       class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500"
                                       placeholder="+233 XX XXX XXXX">
                            </div>
                        </div>
                    </div>

                    <!-- Payment Summary -->
                    <div id="paymentSummary" class="mb-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hidden">
                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Payment Summary</h4>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Contribution Amount:</span>
                                <span class="font-medium text-gray-900 dark:text-white" id="summaryAmount">GHS 0.00</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Platform Fee ({{ $platformFeePercentage }}):</span>
                                <span class="font-medium text-gray-900 dark:text-white" id="summaryFee">GHS 0.00</span>
                            </div>
                            <div class="flex justify-between pt-2 border-t border-gray-200 dark:border-gray-600">
                                <span class="font-semibold text-gray-900 dark:text-white">Total Charged:</span>
                                <span class="font-bold text-blue-600 dark:text-blue-400" id="summaryTotal">GHS 0.00</span>
                            </div>
                            <div class="flex justify-between text-xs pt-2">
                                <span class="text-gray-600 dark:text-gray-400">Benefactor Receives:</span>
                                <span class="font-medium text-green-600 dark:text-green-400" id="summaryNet">GHS 0.00</span>
                            </div>
                        </div>
                    </div>

                    <!-- Terms -->
                    <div class="mb-6">
                        <label class="flex items-start">
                            <input type="checkbox" required class="mt-1 rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">
                            I agree to the <a href="#" class="text-blue-600 hover:underline">Terms of Service</a> and confirm that my contribution is voluntary.
                        </span>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit"
                            class="w-full px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition disabled:opacity-50 disabled:cursor-not-allowed">
                        Proceed to Payment
                    </button>

                    <p class="text-xs text-center text-gray-500 dark:text-gray-400 mt-4">
                        You will be redirected to Paystack to complete the payment securely.
                    </p>
                </form>
            </div>

            <!-- Security Note -->
            <div class="mt-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-green-600 dark:text-green-400 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                    <div>
                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-1">Secure Payment</h4>
                        <p class="text-xs text-gray-600 dark:text-gray-400">
                            Your payment information is processed securely through Paystack. We do not store your card details.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const amountInput = document.getElementById('amount');
            const coversFeeCheckbox = document.getElementById('sponsor_covers_fee');
            const summary = document.getElementById('paymentSummary');

            function updateSummary() {
                const amount = parseFloat(amountInput.value) || 0;
                const coversFee = coversFeeCheckbox.checked;
                const platformFeePercent = {{ $platformFeePercentage }} / 100;

                if (amount > 0) {
                    const platformFee = amount * platformFeePercent;
                    const total = coversFee ? amount + platformFee : amount;
                    const netAmount = coversFee ? amount : amount - platformFee;

                    document.getElementById('summaryAmount').textContent = 'GHS ' + amount.toFixed(2);
                    document.getElementById('summaryFee').textContent = 'GHS ' + platformFee.toFixed(2);
                    document.getElementById('summaryTotal').textContent = 'GHS ' + total.toFixed(2);
                    document.getElementById('summaryNet').textContent = 'GHS ' + netAmount.toFixed(2);
                    summary.classList.remove('hidden');
                } else {
                    summary.classList.add('hidden');
                }
            }

            amountInput.addEventListener('input', updateSummary);
            coversFeeCheckbox.addEventListener('change', updateSummary);

            // Initial update
            updateSummary();
        });
    </script>
@endsection
