<div class="space-y-6">
    <!-- Header with improved layout -->
    <div class="bg-gradient-to-r from-indigo-600 via-purple-600 to-blue-700 rounded-xl p-6 text-white shadow-lg">
        <div class="sm:flex sm:items-center sm:justify-between">
            <div class="flex items-center space-x-4">
                <!-- Avatar in header -->
                <div class="relative">
                    @if($currentAvatar)
                    <img src="{{ Storage::url($currentAvatar) }}" alt="Profile Picture"
                        class="w-16 h-16 rounded-full object-cover border-4 border-white/20">
                    @else
                    <div
                        class="w-16 h-16 rounded-full bg-white/20 flex items-center justify-center border-4 border-white/20">
                        <svg class="w-8 h-8 text-white/70" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z">
                            </path>
                        </svg>
                    </div>
                    @endif
                    @if($isEditing)
                    <div class="absolute -bottom-1 -right-1 bg-white rounded-full p-1">
                        <svg class="w-4 h-4 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                        </svg>
                    </div>
                    @endif
                </div>
                <div>
                    <h2 class="text-2xl font-bold">{{ $name ?: 'Student Profile' }}</h2>
                    <p class="text-indigo-100">
                        @if($studentGroup)
                        {{ $studentGroup->name }} •
                        @endif
                        Member since {{ $student?->created_at?->format('M Y') }}
                    </p>
                </div>
            </div>
            <div class="mt-4 sm:mt-0">
                <button wire:click="toggleEdit"
                    class="inline-flex items-center px-6 py-3 border border-transparent text-sm font-medium rounded-lg text-white
                               @if($isEditing) bg-red-500/20 hover:bg-red-500/30 border-red-300/20 @else bg-white/20 hover:bg-white/30 border-white/20 @endif
                               focus:outline-none focus:ring-2 focus:ring-white/50 transition duration-150 ease-in-out backdrop-blur-sm">
                    @if($isEditing)
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                    Cancel Changes
                    @else
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                        </path>
                    </svg>
                    Edit Profile
                    @endif
                </button>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg relative dark:bg-green-900 dark:border-green-700 dark:text-green-200"
        role="alert">
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                    clip-rule="evenodd" />
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    </div>
    @endif

    @if (session()->has('error'))
    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg relative dark:bg-red-900 dark:border-red-700 dark:text-red-200"
        role="alert">
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                    clip-rule="evenodd" />
            </svg>
            <span>{{ session('error') }}</span>
        </div>
    </div>
    @endif

    <!-- Profile Stats Dashboard -->
    @if(!empty($profileStats))
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-blue-100 dark:bg-blue-900 rounded-full p-3">
                    <svg class="w-6 h-6 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Assessments</h3>
                    <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $profileStats['total_assessments']
                        }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-green-100 dark:bg-green-900 rounded-full p-3">
                    <svg class="w-6 h-6 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Average Score</h3>
                    <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $profileStats['average_score']
                        }}%</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-purple-100 dark:bg-purple-900 rounded-full p-3">
                    <svg class="w-6 h-6 text-purple-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">This Month</h3>
                    <p class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{
                        $profileStats['this_month_assessments'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-indigo-100 dark:bg-indigo-900 rounded-full p-3">
                    <svg class="w-6 h-6 text-indigo-500" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 2L3 7v11a2 2 0 002 2h4v-6h2v6h4a2 2 0 002-2V7l-7-5z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Member Since</h3>
                    <p class="text-lg font-bold text-indigo-600 dark:text-indigo-400">{{ $profileStats['member_since']
                        }}</p>
                </div>
            </div>
        </div>
    </div>
    @endif
    {{-- school fees payement details --}}

    <div class="w-full bg-white shadow-md rounded-xl p-6 mt-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-800">School Fees Payment Details</h3>

            <a href="{{ route('feepayment.form', $student->id) }}"
                class="flex items-center px-4 py-2 bg-violet-600 text-white text-sm font-medium rounded-lg hover:bg-violet-700 transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
                Pay Fees
            </a>
        </div>

        <div class="overflow-x-auto">

            @if($feeDetails)
            <div
                class="mt-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-3">Current Term Fees</h3>
                <table
                    class="w-full text-sm text-left text-gray-500 dark:text-gray-400 border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                    <thead class="bg-gray-100 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-2">Academic Group</th>
                            <th class="px-4 py-2">Academic Level</th>
                            <th class="px-4 py-2">Total Amount</th>
                            <th class="px-4 py-2">Paid</th>
                            <th class="px-4 py-2">Remaining</th>
                            <th class="px-4 py-2">Status</th>
                            <th class="px-4 py-2">Payment Method</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="bg-white dark:bg-gray-800">
                            <td class="px-4 py-2 text-gray-900 dark:text-gray-100">
                                {{ $student->academicGroup->name ?? 'N/A' }}
                            </td>
                            <td class="px-4 py-2 text-gray-900 dark:text-gray-100">
                                {{ $student->academicLevel->name ?? 'N/A' }}
                            </td>
                            <td class="px-4 py-2 font-medium text-gray-900 dark:text-gray-100">
                                ₵{{ number_format($feeDetails->term_total_amount ?? $feeDetails->amount ?? 0, 2) }}
                            </td>
                            <td class="px-4 py-2">₵{{ number_format($feeDetails->total_paid ?? 0, 2) }}</td>
                            <td class="px-4 py-2">₵{{ number_format($feeDetails->remaining ?? 0, 2) }}</td>
                            <td class="px-4 py-2">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold 
                            @if(strtolower($feeDetails->status) === 'completed')
                                bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100
                            @elseif(strtolower($feeDetails->status) === 'part payment')
                                bg-yellow-100 text-yellow-800 dark:bg-yellow-700 dark:text-yellow-100
                            @else
                                bg-orange-100 text-orange-800 dark:bg-orange-700 dark:text-orange-100
                            @endif">
                                    {{ ucfirst($feeDetails->status ?? 'Pending') }}
                                </span>
                            </td>
                            <td class="px-4 py-2">{{ $feeDetails->payment_method ?? 'Momo' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            @endif



            @if($paymentHistory->isNotEmpty())
            <div
                class="mt-8 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-3">Payment History</h3>
                <table
                    class="w-full text-sm text-left text-gray-500 dark:text-gray-400 border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                    <thead class="bg-gray-100 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-2">Amount</th>
                            <th class="px-4 py-2">Currency</th>
                            <th class="px-4 py-2">Status</th>
                            <th class="px-4 py-2">Payer Name</th>
                            <th class="px-4 py-2">Term</th>
                            <th class="px-4 py-2">Academic Group</th>
                            <th class="px-4 py-2">Academic Level</th>
                            <th class="px-4 py-2">Date Paid</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($paymentHistory as $payment)
                        <tr class="bg-white dark:bg-gray-800 border-b dark:border-gray-700">
                            <td class="px-4 py-2 font-medium text-gray-900 dark:text-gray-100">
                                ₵{{ number_format($payment->amount, 2) }}
                            </td>
                            <td class="px-4 py-2">{{ $payment->currency ?? 'GHS' }}</td>
                           <td class="px-4 py-2">
    <span class="px-3 py-1 rounded-full text-xs font-semibold 
        @if(strtolower($payment->status) === 'completed' || strtolower($payment->status) === 'succeeded')
            bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100
        @elseif(strtolower($payment->status) === 'pending')
            bg-orange-100 text-orange-800 dark:bg-orange-700 dark:text-orange-100
        @else
            bg-yellow-100 text-yellow-800 dark:bg-yellow-700 dark:text-yellow-100
        @endif">
        {{ ucfirst($payment->status ?? 'Pending') }}
    </span>
</td>

                            <td class="px-4 py-2">
                                {{ $payment->payer->name ?? 'N/A' }}
                            </td>
                            <td class="px-4 py-2">{{ $payment->academicPeriod->name ?? 'N/A' }}</td>
                            <td class="px-4 py-2">{{ $payment->student->academicGroup->name ?? 'N/A' }}</td>
                            <td class="px-4 py-2">{{ $payment->student->academicLevel->name ?? 'N/A' }}</td>
                            <td class="px-4 py-2">{{ $payment->created_at->format('M d, Y') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="mt-8 text-sm text-gray-500 dark:text-gray-400 italic">
                No payment history found.
            </div>
            @endif

        </div>
    </div>



    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Profile Form - 2 columns -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Personal Information -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-xl border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white flex items-center">
                        <svg class="w-5 h-5 mr-2 text-indigo-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                clip-rule="evenodd" />
                        </svg>
                        Personal Information
                    </h3>
                </div>

                <form wire:submit.prevent="updateProfile" class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Name -->
                        <div>
                            <label for="name"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Full Name
                                *</label>
                            <input wire:model.defer="name" type="text" id="name" @if(!$isEditing) disabled @endif
                                class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm @if(!$isEditing) bg-gray-50 dark:bg-gray-800 @endif">
                            @error('name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email Address
                                *</label>
                            <input wire:model.defer="email" type="email" id="email" @if(!$isEditing) disabled @endif
                                class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm @if(!$isEditing) bg-gray-50 dark:bg-gray-800 @endif">
                            @error('email')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Phone -->
                        <div>
                            <label for="phone"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Phone
                                Number</label>
                            <input wire:model.defer="phone" type="tel" id="phone" @if(!$isEditing) disabled @endif
                                class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm @if(!$isEditing) bg-gray-50 dark:bg-gray-800 @endif">
                            @error('phone')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Date of Birth -->
                        <div>
                            <label for="date_of_birth"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date of
                                Birth</label>
                            <input wire:model.defer="date_of_birth" type="date" id="date_of_birth" @if(!$isEditing)
                                disabled @endif
                                class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm @if(!$isEditing) bg-gray-50 dark:bg-gray-800 @endif">
                            @error('date_of_birth')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Address - Full width -->
                        <div class="md:col-span-2">
                            <label for="address"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Address</label>
                            <textarea wire:model.defer="address" id="address" rows="3" @if(!$isEditing) disabled @endif
                                class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm @if(!$isEditing) bg-gray-50 dark:bg-gray-800 @endif"></textarea>
                            @error('address')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Bio - New field -->
                        <div class="md:col-span-2">
                            <label for="bio"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Bio</label>
                            <textarea wire:model.defer="bio" id="bio" rows="3" placeholder="Tell us about yourself..."
                                @if(!$isEditing) disabled @endif
                                class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm @if(!$isEditing) bg-gray-50 dark:bg-gray-800 @endif"></textarea>
                            @error('bio')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Emergency Contacts -->
                        <div>
                            <label for="emergency_contact_name"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Emergency
                                Contact Name</label>
                            <input wire:model.defer="emergency_contact_name" type="text" id="emergency_contact_name"
                                @if(!$isEditing) disabled @endif
                                class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm @if(!$isEditing) bg-gray-50 dark:bg-gray-800 @endif">
                            @error('emergency_contact_name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="emergency_contact_phone"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Emergency
                                Contact Phone</label>
                            <input wire:model.defer="emergency_contact_phone" type="tel" id="emergency_contact_phone"
                                @if(!$isEditing) disabled @endif
                                class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm @if(!$isEditing) bg-gray-50 dark:bg-gray-800 @endif">
                            @error('emergency_contact_phone')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    @if($isEditing)
                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" wire:click="toggleEdit"
                            class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium py-2 px-4 rounded-lg transition duration-150 ease-in-out">
                            Cancel
                        </button>
                        <button type="submit"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-6 rounded-lg transition duration-150 ease-in-out">
                            Save Changes
                        </button>
                    </div>
                    @endif
                </form>
            </div>
        </div>

        <!-- Sidebar - 1 column -->
        <div class="space-y-6">
            <!-- Avatar Section -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-xl border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white flex items-center">
                        <svg class="w-5 h-5 mr-2 text-indigo-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z"
                                clip-rule="evenodd" />
                        </svg>
                        Profile Picture
                    </h3>
                </div>

                <div class="p-6">
                    <div class="flex flex-col items-center">
                        <div class="relative">
                            @if($currentAvatar)
                            <img src="{{ Storage::url($currentAvatar) }}" alt="Profile Picture"
                                class="w-32 h-32 rounded-full object-cover border-4 border-gray-200 dark:border-gray-600">
                            @else
                            <div
                                class="w-32 h-32 rounded-full bg-gray-200 dark:bg-gray-600 flex items-center justify-center border-4 border-gray-200 dark:border-gray-600">
                                <svg class="w-16 h-16 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z">
                                    </path>
                                </svg>
                            </div>
                            @endif
                        </div>

                        @if($isEditing)
                        <div class="mt-4 w-full">
                            <input wire:model="avatar" type="file" accept="image/*" class="block w-full text-sm text-gray-500 dark:text-gray-400
                                              file:mr-4 file:py-2 file:px-4
                                              file:rounded-full file:border-0
                                              file:text-sm file:font-semibold
                                              file:bg-indigo-50 file:text-indigo-700
                                              hover:file:bg-indigo-100">
                            @error('avatar')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror

                            @if($currentAvatar)
                            <button wire:click="removeAvatar" type="button"
                                class="mt-2 w-full bg-red-600 hover:bg-red-700 text-white text-sm font-medium py-2 px-4 rounded-lg transition duration-150 ease-in-out">
                                Remove Photo
                            </button>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Student Group Info -->
            @if($studentGroup)
            <div class="bg-white dark:bg-gray-800 shadow rounded-xl border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white flex items-center">
                        <svg class="w-5 h-5 mr-2 text-indigo-500" fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z" />
                        </svg>
                        Student Group
                    </h3>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Group:</span>
                            <span class="text-sm text-gray-900 dark:text-gray-100">{{ $studentGroup->name }}</span>
                        </div>
                        @if($studentGroup->description)
                        <div>
                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Description:</span>
                            <p class="text-sm text-gray-900 dark:text-gray-100 mt-1">{{ $studentGroup->description }}
                            </p>
                        </div>
                        @endif
                        @if($studentGroup->teacher)
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Teacher:</span>
                            <span class="text-sm text-gray-900 dark:text-gray-100">{{ $studentGroup->teacher->user->name
                                }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <!-- Recent Activity -->
            @if($recentActivity && $recentActivity->count() > 0)
            <div class="bg-white dark:bg-gray-800 shadow rounded-xl border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white flex items-center">
                        <svg class="w-5 h-5 mr-2 text-indigo-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                clip-rule="evenodd" />
                        </svg>
                        Recent Activity
                    </h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        @foreach($recentActivity as $activity)
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0 w-2 h-2 bg-indigo-500 rounded-full mt-2"></div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                    {{ $activity->title ?? "Assessment #{$activity->id}" }}
                                </p>
                                <div class="flex items-center space-x-2 mt-1">
                                    @if($activity->score !== null)
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                        {{ $activity->score }}%
                                    </span>
                                    @endif
                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $activity->created_at->diffForHumans() }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>