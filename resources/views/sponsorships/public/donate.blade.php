<x-layouts.guest>
    <div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Back Link -->
            <a href="{{ route('sponsorships.projects.index') }}"
               class="inline-flex items-center text-violet-600 hover:text-violet-700 mb-6">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Programs
            </a>

            <!-- Program Summary Card -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 mb-8">
                <div class="flex items-start justify-between mb-4">
                    <div>
                    <span
                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-violet-100 text-violet-800 dark:bg-violet-900 dark:text-violet-200 mb-2">
                        {{ $program->code }}
                    </span>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $program->name }}</h1>
                    </div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                    {{ $program->type === 'emergency' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                    {{ ucfirst($program->type) }}
                </span>
                </div>

                <p class="text-gray-600 dark:text-gray-300 mb-4">{{ $program->description }}</p>

                <!-- Progress -->
                <div class="space-y-2">
                    <div class="flex justify-between text-sm font-medium">
                        <span
                            class="text-gray-500 dark:text-gray-400">Raised: GHS {{ number_format($program->amount_raised, 2) }}</span>
                        <span class="text-violet-600">{{ $program->progress_percentage }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3 overflow-hidden">
                        <div class="bg-gradient-to-r from-blue-500 to-violet-600 h-3 rounded-full"
                             style="width: {{ $program->progress_percentage }}%"></div>
                    </div>
                    <div class="flex justify-between text-sm text-gray-500 dark:text-gray-400">
                        <span>Goal: GHS {{ number_format($program->amount_goal, 2) }}</span>
                        <span>Needed: GHS {{ number_format($program->amount_left, 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- Donation Form -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Make a Donation</h2>

                @if($errors->any())
                    <div
                        class="mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                        <ul class="list-disc list-inside text-red-600 dark:text-red-400 text-sm">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('sponsorships.programs.contribute.initialize', $program) }}" method="POST"
                      id="donationForm">
                    @csrf
                    <input type="hidden" name="sponsorship_program_id" value="{{ $program->id }}">

                    <!-- Amount -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Donation Amount (GHS) <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">GHS</span>
                            <input type="number" name="amount" id="amount" step="0.01" min="1"
                                   value="{{ old('amount') }}"
                                   class="w-full pl-14 pr-4 py-3 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-violet-500 focus:ring-violet-500"
                                   placeholder="0.00" required>
                        </div>
                        <!-- Quick amounts -->
                        <div class="flex flex-wrap gap-2 mt-3">
                            @foreach([50, 100, 200, 500, 1000] as $quickAmount)
                                <button type="button"
                                        onclick="document.getElementById('amount').value = {{ $quickAmount }}; updateFeeBreakdown();"
                                        class="px-4 py-2 text-sm font-medium text-violet-600 bg-violet-50 hover:bg-violet-100 rounded-lg transition-colors">
                                    GHS {{ number_format($quickAmount) }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <!-- Platform Fee Notice -->
                    <div
                        class="mb-6 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-blue-500 mt-0.5 mr-3 flex-shrink-0" fill="none"
                                 stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div class="text-sm">
                                <p class="font-medium text-blue-800 dark:text-blue-200">Platform
                                    Fee: {{ $platformFeePercentage }}</p>
                                <p class="text-blue-600 dark:text-blue-300 mt-1">
                                    A small platform fee helps us maintain and improve our services. You can choose to
                                    cover this fee so the benefactor receives the full amount.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Cover Fee Option -->
                    <div class="mb-6">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" name="sponsor_covers_fee" id="sponsorCoversFee" value="1"
                                   class="w-5 h-5 text-violet-600 border-gray-300 rounded focus:ring-violet-500"
                                   onchange="updateFeeBreakdown()">
                            <span class="ml-3 text-sm text-gray-700 dark:text-gray-300">
                            I want to cover the platform fee so the benefactor receives the full amount
                        </span>
                        </label>
                    </div>

                    <!-- Fee Breakdown -->
                    <div id="feeBreakdown" class="mb-6 bg-gray-50 dark:bg-gray-700 rounded-lg p-4 hidden">
                        <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Payment Summary</h3>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Donation Amount:</span>
                                <span class="font-medium text-gray-900 dark:text-white"
                                      id="displayAmount">GHS 0.00</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Platform Fee (1%):</span>
                                <span class="font-medium text-gray-900 dark:text-white" id="displayFee">GHS 0.00</span>
                            </div>
                            <div class="border-t border-gray-200 dark:border-gray-600 pt-2 mt-2">
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">You Pay:</span>
                                    <span class="font-bold text-violet-600" id="displayTotal">GHS 0.00</span>
                                </div>
                                <div class="flex justify-between mt-1">
                                    <span class="text-gray-600 dark:text-gray-400">Benefactor Receives:</span>
                                    <span class="font-bold text-green-600" id="displayNet">GHS 0.00</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payer Details -->
                    <div class="space-y-4 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Your Name
                            </label>
                            <input type="text" name="payer_name"
                                   value="{{ old('payer_name', auth()->user()->name ?? '') }}"
                                   class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-violet-500 focus:ring-violet-500"
                                   placeholder="Enter your name">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Email Address <span class="text-red-500">*</span>
                            </label>
                            <input type="email" name="payer_email"
                                   value="{{ old('payer_email', auth()->user()->email ?? '') }}"
                                   class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-violet-500 focus:ring-violet-500"
                                   placeholder="your@email.com" required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Phone Number
                            </label>
                            <input type="tel" name="payer_phone" value="{{ old('payer_phone') }}"
                                   class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-violet-500 focus:ring-violet-500"
                                   placeholder="+233 XX XXX XXXX">
                        </div>
                    </div>

                    <!-- Submit -->
                    <button type="submit"
                            class="w-full py-3 px-4 bg-violet-600 hover:bg-violet-700 text-white font-medium rounded-lg transition-colors shadow-lg hover:shadow-xl">
                        Proceed to Payment
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function updateFeeBreakdown() {
            const amount = parseFloat(document.getElementById('amount').value) || 0;
            const coversFee = document.getElementById('sponsorCoversFee').checked;
            const breakdown = document.getElementById('feeBreakdown');

            if (amount > 0) {
                breakdown.classList.remove('hidden');

                const fee = amount * 0.01;
                const total = coversFee ? amount + fee : amount;
                const net = coversFee ? amount : amount - fee;

                document.getElementById('displayAmount').textContent = 'GHS ' + amount.toFixed(2);
                document.getElementById('displayFee').textContent = 'GHS ' + fee.toFixed(2);
                document.getElementById('displayTotal').textContent = 'GHS ' + total.toFixed(2);
                document.getElementById('displayNet').textContent = 'GHS ' + net.toFixed(2);
            } else {
                breakdown.classList.add('hidden');
            }
        }

        document.getElementById('amount').addEventListener('input', updateFeeBreakdown);
    </script>
</x-layouts.guest>
