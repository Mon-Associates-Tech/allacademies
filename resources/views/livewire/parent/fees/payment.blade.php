<x-layouts.app>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-4xl mx-auto">
        <!-- Back Button -->
        <div class="mb-6">
            <a href="{{ route('parent.fees.index') }}"
               class="inline-flex items-center text-sm text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Fees
            </a>
        </div>

        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">Make Payment</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                @if($selectedStudent)
                    Complete the form below to pay fees for {{ $selectedStudent->user->name }}
                @else
                    Select a student and complete the form to process your payment
                @endif
            </p>
        </div>

        <!-- Error Messages -->
        @if($errors->any())
            <div class="mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-red-600 dark:text-red-400 mr-2 mt-0.5" fill="none" stroke="currentColor"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <p class="text-red-800 dark:text-red-200 font-semibold">Please correct the following errors:</p>
                        <ul class="mt-2 list-disc list-inside text-sm text-red-700 dark:text-red-300">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Payment Form -->
            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100">Payment Details</h2>
                    </div>

                    <div class="p-6">
                        <!-- Student Selection Form (if not already selected) -->
                        @if(!$selectedStudent)
                            <form action="{{ route('parent.fees.payment') }}" method="GET" class="mb-6" id="studentSelectionForm">
                                <div>
                                    <label for="student"
                                           class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Select Student <span class="text-red-500">*</span>
                                    </label>
                                    <div class="flex space-x-3">
                                        <select name="student_id" id="student" required
                                                class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-violet-500 focus:ring-violet-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100">
                                            <option value="">Choose a ward...</option>
                                            @foreach($students as $student)
                                                <option value="{{ $student->id }}">
                                                    {{ $student->user->name }}
                                                    - {{ $student->academicLevel->name ?? 'N/A' }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <button type="submit"
                                                class="px-6 py-2 bg-violet-600 text-white rounded-lg hover:bg-violet-700 transition">
                                            Continue
                                        </button>
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Select the student you want
                                        to pay for</p>
                                </div>
                            </form>

                            <script>
                                // Alternative: Redirect with student ID in the path
                                document.getElementById('studentSelectionForm').addEventListener('submit', function(e) {
                                    e.preventDefault();
                                    const studentId = document.getElementById('student').value;
                                    if (studentId) {
                                        window.location.href = "{{ route('parent.fees.payment', '') }}/" + studentId;
                                    }
                                });
                            </script>
                        @else
                            <!-- Payment Form -->
                            <form action="{{ route('parent.fees.initialize') }}" method="POST" class="space-y-6">
                                @csrf
                                <input type="hidden" name="student_id" value="{{ $selectedStudent->id }}">

                                <!-- Selected Student Display -->
                                <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-3">
                                            <div
                                                class="w-10 h-10 bg-gradient-to-br from-violet-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold text-sm">
                                                {{ strtoupper(substr($selectedStudent->user->name, 0, 2)) }}
                                            </div>
                                            <div>
                                                <p class="font-semibold text-gray-800 dark:text-gray-100">{{ $selectedStudent->user->name }}</p>
                                                <p class="text-xs text-gray-600 dark:text-gray-400">
                                                    {{ $selectedStudent->academicLevel->name ?? 'N/A' }}
                                                    - {{ $selectedStudent->academicGroup->name ?? 'N/A' }}
                                                </p>
                                            </div>
                                        </div>
                                        <a href="{{ route('parent.fees.payment') }}"
                                           class="text-sm text-violet-600 hover:text-violet-700 dark:text-violet-400">
                                            Change Student
                                        </a>
                                    </div>
                                </div>

                                <!-- Payment Type -->
                                <div>
                                    <label for="payment_type"
                                           class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Payment Type <span class="text-red-500">*</span>
                                    </label>
                                    <select name="payment_type" id="payment_type" required
                                            onchange="togglePaymentStructure()"
                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-violet-500 focus:ring-violet-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100">
                                        <option
                                            value="school_fee" {{ $paymentType === 'school_fee' ? 'selected' : '' }}>
                                            School Fee (Term Fees)
                                        </option>
                                        <option
                                            value="school_payment" {{ $paymentType === 'school_payment' ? 'selected' : '' }}>
                                            School Payment (Other Payments)
                                        </option>
                                    </select>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                        School Fee for term fees, School Payment for other fees like books, uniforms,
                                        etc.
                                    </p>
                                </div>

                                <!-- Payment Structure (shown only for school_payment) -->
                                <div id="payment_structure_field" style="display: {{ $paymentType === 'school_payment' ? 'block' : 'none' }};">
                                    <label for="payment_structure_id"
                                           class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Select Payment Item <span class="text-red-500">*</span>
                                    </label>
                                    <select name="payment_structure_id" id="payment_structure_id"
                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-violet-500 focus:ring-violet-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100"
                                            onchange="updateAmountFromStructure()">
                                        <option value="">Choose payment item...</option>
                                        @foreach($paymentStructures as $structure)
                                            <option value="{{ $structure->id }}"
                                                    data-amount="{{ $structure->amount }}"
                                                    data-description="{{ $structure->description }}">
                                                {{ $structure->name }} - GHS {{ number_format($structure->amount, 2) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400" id="structure_description">
                                        Select the type of payment you want to make
                                    </p>
                                </div>

                                <script>
                                    function togglePaymentStructure() {
                                        const paymentType = document.getElementById('payment_type').value;
                                        const structureField = document.getElementById('payment_structure_field');
                                        const structureSelect = document.getElementById('payment_structure_id');

                                        if (paymentType === 'school_payment') {
                                            structureField.style.display = 'block';
                                            structureSelect.required = true;
                                        } else {
                                            structureField.style.display = 'none';
                                            structureSelect.required = false;
                                            structureSelect.value = '';
                                        }
                                    }

                                    function updateAmountFromStructure() {
                                        const structureSelect = document.getElementById('payment_structure_id');
                                        const selectedOption = structureSelect.options[structureSelect.selectedIndex];
                                        const amountInput = document.getElementById('amount');
                                        const descriptionText = document.getElementById('structure_description');

                                        if (selectedOption.value) {
                                            const amount = selectedOption.getAttribute('data-amount');
                                            const description = selectedOption.getAttribute('data-description');

                                            if (amount) {
                                                amountInput.value = amount;
                                            }

                                            if (description) {
                                                descriptionText.textContent = description;
                                            } else {
                                                descriptionText.textContent = 'Selected payment item';
                                            }
                                        } else {
                                            descriptionText.textContent = 'Select the type of payment you want to make';
                                        }
                                    }

                                    // Initialize on page load
                                    document.addEventListener('DOMContentLoaded', function() {
                                        togglePaymentStructure();
                                    });
                                </script>

                                <!-- Amount -->
                                <div>
                                <div>
                                    <label for="amount"
                                           class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Amount (GHS) <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <div
                                            class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span
                                                class="text-gray-500 dark:text-gray-400 sm:text-sm font-semibold">GHS</span>
                                        </div>
                                        <input type="number" name="amount" id="amount" step="0.01" min="1"
                                               value="{{ old('amount', $remainingAmount > 0 ? $remainingAmount : '') }}"
                                               required
                                               class="block w-full pl-16 pr-12 py-3 text-lg rounded-md border-gray-300 shadow-sm focus:border-violet-500 focus:ring-violet-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100"
                                               placeholder="0.00">
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                        Enter the amount you want to pay. Partial payments are accepted.
                                    </p>
                                </div>

                                <!-- Quick Amount Buttons -->
                                @if($remainingAmount > 0)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Quick Select Amount
                                        </label>
                                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                            <button type="button"
                                                    onclick="document.getElementById('amount').value = '{{ $remainingAmount }}'"
                                                    class="px-4 py-3 bg-violet-100 dark:bg-violet-900/30 text-violet-700 dark:text-violet-300 rounded-lg hover:bg-violet-200 dark:hover:bg-violet-900/50 transition text-sm font-medium border border-violet-300 dark:border-violet-700">
                                                <div class="text-xs text-violet-600 dark:text-violet-400">Full</div>
                                                <div class="font-bold">
                                                    GHS {{ number_format($remainingAmount, 2) }}</div>
                                            </button>
                                            @if($remainingAmount >= 100)
                                                <button type="button"
                                                        onclick="document.getElementById('amount').value = '{{ round($remainingAmount / 2, 2) }}'"
                                                        class="px-4 py-3 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition text-sm font-medium border border-gray-300 dark:border-gray-600">
                                                    <div class="text-xs text-gray-600 dark:text-gray-400">Half</div>
                                                    <div class="font-bold">
                                                        GHS {{ number_format($remainingAmount / 2, 2) }}</div>
                                                </button>
                                                <button type="button"
                                                        onclick="document.getElementById('amount').value = '{{ round($remainingAmount / 3, 2) }}'"
                                                        class="px-4 py-3 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition text-sm font-medium border border-gray-300 dark:border-gray-600">
                                                    <div class="text-xs text-gray-600 dark:text-gray-400">One Third
                                                    </div>
                                                    <div class="font-bold">
                                                        GHS {{ number_format($remainingAmount / 3, 2) }}</div>
                                                </button>
                                                <button type="button"
                                                        onclick="document.getElementById('amount').value = '{{ round($remainingAmount / 4, 2) }}'"
                                                        class="px-4 py-3 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition text-sm font-medium border border-gray-300 dark:border-gray-600">
                                                    <div class="text-xs text-gray-600 dark:text-gray-400">Quarter</div>
                                                    <div class="font-bold">
                                                        GHS {{ number_format($remainingAmount / 4, 2) }}</div>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                @endif

                                <!-- Payment Information -->
                                <div
                                    class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                                    <div class="flex items-start">
                                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-2 mt-0.5" fill="none"
                                             stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <div class="text-sm">
                                            <p class="font-semibold text-blue-800 dark:text-blue-200">Payment
                                                Information</p>
                                            <ul class="mt-2 space-y-1 text-blue-700 dark:text-blue-300">
                                                <li>• You will be redirected to Paystack to complete your payment
                                                    securely
                                                </li>
                                                <li>• A receipt will be generated after successful payment</li>
                                                <li>• Payment confirmation may take a few moments</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <div class="pt-4">
                                    <button type="submit"
                                            class="w-full flex justify-center items-center px-6 py-4 bg-violet-600 text-white rounded-lg hover:bg-violet-700 transition font-semibold text-lg shadow-lg hover:shadow-xl">
                                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                        </svg>
                                        Proceed to Secure Payment
                                    </button>
                                </div>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Summary Sidebar -->
            <div class="lg:col-span-1">
                @if($selectedStudent)
                    <!-- Student Info -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">Student Information</h3>

                        <div class="flex items-center space-x-3 mb-4">
                            <div
                                class="w-14 h-14 bg-gradient-to-br from-violet-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold text-lg">
                                {{ strtoupper(substr($selectedStudent->user->name, 0, 2)) }}
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800 dark:text-gray-100">{{ $selectedStudent->user->name }}</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $selectedStudent->academicLevel->name ?? 'N/A' }}</p>
                            </div>
                        </div>

                        <div class="space-y-2 text-sm border-t border-gray-200 dark:border-gray-700 pt-4">
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Class:</span>
                                <span
                                    class="font-medium text-gray-800 dark:text-gray-100">{{ $selectedStudent->academicGroup->name ?? 'N/A' }}</span>
                            </div>
                            @if($selectedStudent->student_id)
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Student ID:</span>
                                    <span
                                        class="font-medium font-mono text-gray-800 dark:text-gray-100">{{ $selectedStudent->student_id }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Fee Summary -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">Fee Summary</h3>

                        @if($currentTerm)
                            <div class="mb-4 pb-4 border-b border-gray-200 dark:border-gray-700">
                                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">Current Term</p>
                                <p class="text-sm font-semibold text-gray-800 dark:text-gray-100">{{ $currentTerm->name }}</p>
                            </div>
                        @endif

                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Total Fee:</span>
                                <span class="font-semibold text-gray-800 dark:text-gray-100">
                                GHS {{ number_format($termTotalAmount, 2) }}
                            </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Amount Paid:</span>
                                <span class="font-semibold text-green-600 dark:text-green-400">
                                GHS {{ number_format($totalPaid, 2) }}
                            </span>
                            </div>
                            <div class="flex justify-between pt-3 border-t-2 border-gray-200 dark:border-gray-700">
                                <span class="font-semibold text-gray-700 dark:text-gray-300">Balance Due:</span>
                                <span class="font-bold text-lg text-orange-600 dark:text-orange-400">
                                GHS {{ number_format($remainingAmount, 2) }}
                            </span>
                            </div>
                        </div>

                        <!-- Progress Bar -->
                        @if($termTotalAmount > 0)
                            <div class="mt-6">
                                <div class="flex justify-between text-xs mb-2">
                                    <span class="text-gray-600 dark:text-gray-400 font-medium">Payment Progress</span>
                                    <span class="font-bold text-gray-800 dark:text-gray-100">
                                    {{ round(($totalPaid / $termTotalAmount) * 100) }}%
                                </span>
                                </div>
                                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                                    <div
                                        class="bg-gradient-to-r from-violet-600 to-purple-600 h-3 rounded-full transition-all duration-500"
                                        style="width: {{ min(($totalPaid / $termTotalAmount) * 100, 100) }}%"></div>
                                </div>
                            </div>
                        @endif
                    </div>
                @else
                    <!-- No Student Selected -->
                    <div
                        class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-6">
                        <div class="text-center">
                            <svg class="w-16 h-16 text-blue-600 dark:text-blue-400 mx-auto mb-4" fill="none"
                                 stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p class="text-blue-800 dark:text-blue-200 font-semibold text-lg mb-2">Select a Student</p>
                            <p class="text-sm text-blue-700 dark:text-blue-300">
                                Please select a student from the dropdown to view their fee details and proceed with the
                                payment.
                            </p>
                        </div>
                    </div>
                @endif

                <!-- Security Notice -->
                <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-green-600 dark:text-green-400 mr-2 mt-0.5 flex-shrink-0" fill="none"
                             stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        <div>
                            <p class="text-xs font-semibold text-gray-700 dark:text-gray-300">Secure Payment Gateway</p>
                            <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                                Your payment is processed securely through Paystack. We never store your card details.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>>
