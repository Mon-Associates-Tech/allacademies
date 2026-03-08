<div class="min-h-screen py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-xl mx-auto">
        <!-- Progress Steps -->
        <div class="mb-8">
            <div class="flex items-center justify-center space-x-4">
                @foreach([1 => 'Code', 2 => 'Type', 3 => 'Register', 4 => 'Verify', 5 => 'Start'] as $step => $label)
                    <div class="flex items-center">
                        <div class="flex items-center justify-center w-8 h-8 rounded-full text-sm font-medium
                            {{ $currentStep >= $step ? 'bg-indigo-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-500' }}">
                            {{ $step }}
                        </div>
                        @if($step < 5)
                            <div class="w-8 h-0.5 {{ $currentStep > $step ? 'bg-indigo-600' : 'bg-gray-200 dark:bg-gray-700' }}"></div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Card -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8">
            <!-- Flash Messages -->
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 rounded-lg text-green-700 dark:text-green-300">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 rounded-lg text-red-700 dark:text-red-300">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Step 1: Access Code -->
            @if($currentStep === 1)
                <div class="text-center mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Join Assignment</h2>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">Enter the access code provided by your instructor</p>
                </div>

                <form wire:submit.prevent="validateAccessCode" class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Access Code</label>
                        <input type="text" wire:model="accessCode" placeholder="Enter code (e.g., ABC123)"
                            class="w-full px-4 py-3 text-center text-2xl font-mono tracking-widest uppercase border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white">
                        @error('accessCode') <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>
                    <button type="submit" class="w-full py-3 px-4 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-xl transition-colors">
                        Continue
                    </button>
                </form>
            @endif

            <!-- Step 2: Participant Type -->
            @if($currentStep === 2)
                <div class="text-center mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $assignmentInfo['title'] ?? 'Assignment' }}</h2>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">How would you like to participate?</p>
                </div>

                @if($assignmentInfo)
                    <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl space-y-2 text-sm">
                        <p><span class="font-medium">Duration:</span> {{ $assignmentInfo['duration'] }}</p>
                        <p><span class="font-medium">Questions:</span> {{ $assignmentInfo['questions_count'] }}</p>
                        <p><span class="font-medium">Total Marks:</span> {{ $assignmentInfo['total_marks'] }}</p>
                    </div>
                @endif

                <div class="space-y-4">
                    @if($isStudent)
                        <button wire:click="selectParticipantType('student')" class="w-full p-4 border-2 border-indigo-500 bg-indigo-50 dark:bg-indigo-900/30 rounded-xl text-left hover:bg-indigo-100 dark:hover:bg-indigo-900/50 transition-colors">
                            <div class="font-medium text-indigo-700 dark:text-indigo-300">Continue as Student</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Use your student account</div>
                        </button>
                    @endif
                    <button wire:click="selectParticipantType('guest')" class="w-full p-4 border-2 border-gray-200 dark:border-gray-600 rounded-xl text-left hover:border-gray-300 dark:hover:border-gray-500 transition-colors">
                        <div class="font-medium text-gray-900 dark:text-white">Join as Guest</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Register with your email</div>
                    </button>
                </div>
                @error('participantType') <p class="mt-4 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror

                <button wire:click="goBack" class="mt-6 w-full py-2 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
                    ← Back
                </button>
            @endif

            <!-- Step 3: Guest Registration -->
            @if($currentStep === 3)
                <div class="text-center mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Register</h2>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">Enter your details to continue</p>
                </div>

                <form wire:submit.prevent="registerParticipant" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Full Name</label>
                        <input type="text" wire:model="name" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
                        @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email</label>
                        <input type="email" wire:model="email" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
                        @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Phone (Optional)</label>
                        <input type="tel" wire:model="phone" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
                    </div>
                    <button type="submit" class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-xl transition-colors">
                        Continue
                    </button>
                </form>
                <button wire:click="goBack" class="mt-4 w-full py-2 text-gray-600 dark:text-gray-400 hover:text-gray-900">← Back</button>
            @endif

            <!-- Step 4: Verification -->
            @if($currentStep === 4)
                <div class="text-center">
                    <div class="w-16 h-16 mx-auto mb-4 bg-indigo-100 dark:bg-indigo-900/50 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Check Your Email</h2>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">We sent a verification link to <strong>{{ $email }}</strong></p>

                    <div class="mt-8 space-y-4">
                        <button wire:click="checkVerification" class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-xl">
                            I've Verified My Email
                        </button>
                        <button wire:click="resendVerification" class="w-full py-2 text-indigo-600 hover:text-indigo-700">
                            Resend Verification Email
                        </button>
                    </div>
                    @error('verification') <p class="mt-4 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            @endif

            <!-- Step 5: Ready to Start -->
            @if($currentStep === 5)
                <div class="text-center">
                    <div class="w-16 h-16 mx-auto mb-4 bg-green-100 dark:bg-green-900/50 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Ready to Begin!</h2>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">{{ $assignmentInfo['title'] ?? 'Assignment' }}</p>

                    @if($assignmentInfo)
                        <div class="mt-6 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl text-left space-y-2 text-sm">
                            <p><span class="font-medium">Duration:</span> {{ $assignmentInfo['duration'] }}</p>
                            <p><span class="font-medium">Questions:</span> {{ $assignmentInfo['questions_count'] }}</p>
                            @if($assignmentInfo['proctoring_enabled'])
                                <p class="text-amber-600 dark:text-amber-400">⚠️ Proctoring is enabled</p>
                            @endif
                        </div>
                    @endif

                    <button wire:click="startAssignment" class="mt-8 w-full py-3 bg-green-600 hover:bg-green-700 text-white font-medium rounded-xl transition-colors">
                        Start Assignment
                    </button>
                    @error('general') <p class="mt-4 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            @endif
        </div>
    </div>
</div>
