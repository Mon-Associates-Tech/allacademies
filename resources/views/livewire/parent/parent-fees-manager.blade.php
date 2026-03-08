<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
    <!-- Page Header -->
    <div class="sm:flex sm:justify-between sm:items-center mb-8">
        <div class="mb-4 sm:mb-0">
            <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">Fee Payments</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Manage and pay fees for your wards</p>
        </div>

        @if($this->currentTerm)
            <div class="text-right">
                <div class="text-sm text-gray-500 dark:text-gray-400">Current Term</div>
                <div class="text-lg font-semibold text-gray-800 dark:text-gray-100">{{ $this->currentTerm->name }}</div>
            </div>
        @endif
    </div>

    <!-- Overview Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Wards</p>
                    <p class="text-3xl font-bold text-gray-800 dark:text-gray-100 mt-1">{{ $this->wards->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Paid This Term</p>
                    <p class="text-3xl font-bold text-green-600 dark:text-green-400 mt-1">
                        GHS {{ number_format($this->studentsWithFees->sum('totalPaid'), 2) }}
                    </p>
                </div>
                <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Outstanding Balance</p>
                    <p class="text-3xl font-bold text-orange-600 dark:text-orange-400 mt-1">
                        GHS {{ number_format($this->studentsWithFees->sum('remainingAmount'), 2) }}
                    </p>
                </div>
                <div class="w-12 h-12 bg-orange-100 dark:bg-orange-900 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Students/Wards List -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100">Your Wards</h2>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Select a ward to view details or make payment</p>
        </div>

        <div class="p-6">
            @if($this->studentsWithFees->isEmpty())
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <p class="mt-4 text-gray-500 dark:text-gray-400">No wards found</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($this->studentsWithFees as $data)
                        @php
                            $student = $data['student'];
                            $isSelected = $selectedStudentId == $student->id;
                        @endphp
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-6 hover:shadow-md transition {{ $isSelected ? 'ring-2 ring-violet-500' : '' }}">
                            <div class="flex items-start justify-between">
                                <div class="flex-1 cursor-pointer" wire:click="selectStudent({{ $student->id }})">
                                    <div class="flex items-center space-x-4">
                                        <div class="w-16 h-16 bg-gradient-to-br from-violet-500 to-purple-600 rounded-full flex items-center justify-center text-white text-xl font-bold">
                                            {{ substr($student->user->name, 0, 2) }}
                                        </div>
                                        <div>
                                            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">{{ $student->user->name }}</h3>
                                            <div class="flex items-center space-x-4 mt-1 text-sm text-gray-600 dark:text-gray-400">
                                                <span>{{ $student->academicLevel->name ?? 'N/A' }}</span>
                                                <span>•</span>
                                                <span>{{ $student->academicGroup->name ?? 'N/A' }}</span>
                                                @if($student->student_id)
                                                    <span>•</span>
                                                    <span>ID: {{ $student->student_id }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="ml-6">
                                    <button wire:click="openPaymentModal({{ $student->id }}, 'school_fee')"
                                            class="px-4 py-2 bg-violet-600 text-white rounded-lg hover:bg-violet-700 transition flex items-center space-x-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                        </svg>
                                        <span>Pay Fees</span>
                                    </button>
                                </div>
                            </div>

                            <!-- Fee Summary -->
                            <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
                                <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4">
                                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">Total Fee</p>
                                    <p class="text-lg font-bold text-gray-800 dark:text-gray-100 mt-1">
                                        GHS {{ number_format($data['termTotalAmount'], 2) }}
                                    </p>
                                </div>
                                <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4">
                                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">Paid</p>
                                    <p class="text-lg font-bold text-green-600 dark:text-green-400 mt-1">
                                        GHS {{ number_format($data['totalPaid'], 2) }}
                                    </p>
                                </div>
                                <div class="bg-orange-50 dark:bg-orange-900/20 rounded-lg p-4">
                                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">Remaining</p>
                                    <p class="text-lg font-bold text-orange-600 dark:text-orange-400 mt-1">
                                        GHS {{ number_format($data['remainingAmount'], 2) }}
                                    </p>
                                </div>
                                <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
                                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">Progress</p>
                                    <div class="flex items-center mt-1">
                                        <div class="flex-1 bg-gray-200 dark:bg-gray-700 rounded-full h-2 mr-2">
                                            <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $data['termTotalAmount'] > 0 ? ($data['totalPaid'] / $data['termTotalAmount'] * 100) : 0 }}%"></div>
                                        </div>
                                        <span class="text-sm font-semibold text-gray-800 dark:text-gray-100">
                                            {{ $data['termTotalAmount'] > 0 ? round($data['totalPaid'] / $data['termTotalAmount'] * 100) : 0 }}%
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- Payment History Section -->
    @if($selectedStudentId && $this->paymentHistory->isNotEmpty())
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm mt-8">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100">
                    Payment History - {{ $this->selectedStudent->user->name }}
                </h2>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Reference</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($this->paymentHistory as $payment)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                {{ $payment->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                                {{ $payment->payment_category }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-600 dark:text-gray-400">
                                {{ $payment->reference }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900 dark:text-gray-100">
                                GHS {{ number_format($payment->amount, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($payment->status === 'succeeded')
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                            Paid
                                        </span>
                                @elseif($payment->status === 'pending')
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                            Pending
                                        </span>
                                @else
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                            Failed
                                        </span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Payment Modal -->
    @if($showPaymentModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closePaymentModal"></div>

                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form wire:submit.prevent="$dispatch('processPayment')" method="POST" action="{{ route('parent.fees.initialize') }}">
                        @csrf
                        <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100" id="modal-title">
                                        Pay Fees for {{ $this->selectedStudent->user->name ?? 'Student' }}
                                    </h3>

                                    <div class="mt-6 space-y-4">
                                        <input type="hidden" name="student_id" value="{{ $selectedStudentId }}">

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Payment Type</label>
                                            <select wire:model.live="paymentType" name="payment_type" required
                                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-700 dark:text-white focus:border-violet-500 focus:ring-violet-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100">
                                                <option value="school_fee">School Fee</option>
                                                <option value="school_payment">School Payment</option>
                                            </select>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Amount (GHS)</label>
                                            <input type="number" wire:model="amount" name="amount" step="0.01" min="1" required
                                                   class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-700 dark:text-white focus:border-violet-500 focus:ring-violet-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100"
                                                   placeholder="Enter amount">
                                            @error('amount')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        @if($this->selectedStudent)
                                            <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
                                                <div class="text-sm">
                                                    <div class="flex justify-between mb-2">
                                                        <span class="text-gray-600 dark:text-gray-400">Total Fee:</span>
                                                        <span class="font-semibold text-gray-800 dark:text-gray-100">
                                                            GHS {{ number_format($this->getStudentFeeData($this->selectedStudent)['termTotalAmount'], 2) }}
                                                        </span>
                                                    </div>
                                                    <div class="flex justify-between mb-2">
                                                        <span class="text-gray-600 dark:text-gray-400">Paid:</span>
                                                        <span class="font-semibold text-green-600 dark:text-green-400">
                                                            GHS {{ number_format($this->getStudentFeeData($this->selectedStudent)['totalPaid'], 2) }}
                                                        </span>
                                                    </div>
                                                    <div class="flex justify-between pt-2 border-t border-blue-200 dark:border-blue-700">
                                                        <span class="font-semibold text-gray-700 dark:text-gray-300">Remaining:</span>
                                                        <span class="font-bold text-orange-600 dark:text-orange-400">
                                                            GHS {{ number_format($this->getStudentFeeData($this->selectedStudent)['remainingAmount'], 2) }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-900 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="submit"
                                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-violet-600 text-base font-medium text-white hover:bg-violet-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-violet-500 sm:ml-3 sm:w-auto sm:text-sm">
                                Proceed to Payment
                            </button>
                            <button type="button" wire:click="closePaymentModal"
                                    class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-violet-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
