<x-layouts.app>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
        <!-- Page Header -->
        <div class="sm:flex sm:justify-between sm:items-center mb-8">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">Fee Payments</h1>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Manage and pay fees for your wards</p>
            </div>

            @if($currentTerm)
                <div class="text-right">
                    <div class="text-sm text-gray-500 dark:text-gray-400">Current Term</div>
                    <div class="text-lg font-semibold text-gray-800 dark:text-gray-100">{{ $currentTerm->name }}</div>
                </div>
            @endif
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="mb-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-green-600 dark:text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-green-800 dark:text-green-200">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-red-600 dark:text-red-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-red-800 dark:text-red-200">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        @if(session('info'))
            <div class="mb-6 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-blue-800 dark:text-blue-200">{{ session('info') }}</p>
                </div>
            </div>
        @endif

        <!-- Overview Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Total Wards</p>
                        <p class="text-3xl font-bold text-gray-800 dark:text-gray-100 mt-1">{{ $studentsWithFees->count() }}</p>
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
                            GHS {{ number_format($studentsWithFees->sum('totalPaid'), 2) }}
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
                            GHS {{ number_format($studentsWithFees->sum('remainingAmount'), 2) }}
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
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Click "Pay Fees" to make a payment for any ward</p>
            </div>

            <div class="p-6">
                @if($studentsWithFees->isEmpty())
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        <p class="mt-4 text-gray-500 dark:text-gray-400">No wards found</p>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach($studentsWithFees as $data)
                            @php
                                $student = $data['student'];
                                $percentagePaid = $data['termTotalAmount'] > 0
                                    ? ($data['totalPaid'] / $data['termTotalAmount'] * 100)
                                    : 0;
                            @endphp
                            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-6 hover:shadow-md transition">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-4">
                                            <div class="w-16 h-16 bg-gradient-to-br from-violet-500 to-purple-600 rounded-full flex items-center justify-center text-white text-xl font-bold">
                                                {{ strtoupper(substr($student->user->name, 0, 2)) }}
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

                                    <div class="ml-6 flex flex-col space-y-2">
                                        <a href="{{ route('parent.fees.payment', ['student' => $student->id, 'type' => 'school_fee']) }}"
                                           class="px-4 py-2 bg-violet-600 text-white rounded-lg hover:bg-violet-700 transition flex items-center justify-center space-x-2 text-sm">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                            </svg>
                                            <span>Pay School Fee</span>
                                        </a>
                                        <a href="{{ route('parent.fees.payment', ['student' => $student->id, 'type' => 'school_payment']) }}"
                                           class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center justify-center space-x-2 text-sm">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            <span>Other Payment</span>
                                        </a>
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
                                                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ round($percentagePaid) }}%"></div>
                                            </div>
                                            <span class="text-sm font-semibold text-gray-800 dark:text-gray-100">
                                            {{ round($percentagePaid) }}%
                                        </span>
                                        </div>
                                    </div>
                                </div>

                                @if($data['feeStructure'])
                                    <div class="mt-4 text-xs text-gray-500 dark:text-gray-400">
                                        <span>Fee Structure: {{ $data['feeStructure']->name ?? 'Standard Fee' }}</span>
                                        @if($currentTerm)
                                            <span class="ml-4">• Term: {{ $currentTerm->name }}</span>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- Help Section -->
        <div class="mt-8 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-6">
            <div class="flex items-start">
                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400 mt-1 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <h3 class="text-lg font-semibold text-blue-800 dark:text-blue-200">Need Help?</h3>
                    <p class="text-sm text-blue-700 dark:text-blue-300 mt-1">
                        If you have any questions about fee payments or need assistance, please contact the school administration.
                    </p>
                    <div class="mt-3 text-sm text-blue-600 dark:text-blue-400">
                        <p>• Payments are processed securely through Paystack</p>
                        <p>• You'll receive a receipt after successful payment</p>
                        <p>• Partial payments are accepted</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>

