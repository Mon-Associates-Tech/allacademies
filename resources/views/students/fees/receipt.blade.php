<x-layouts.app>
    <x-slot name="header">
        <div class="flex items-center justify-between print:hidden">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Payment Receipt') }}
            </h2>
            <button onclick="window.print()"
                    class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Print receipt
            </button>
        </div>
    </x-slot>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Source+Serif+4:wght@600;700&display=swap');
        .receipt-serif { font-family: 'Source Serif 4', ui-serif, Georgia, 'Times New Roman', serif; }
        @media print {
            @page { margin: 1.2cm; }
            body { background: #fff !important; }
            #receipt, #receipt * { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
    </style>

    <div class="py-12 print:py-0">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div id="receipt" class="bg-[#F6F7F4] border border-[#D7DED8] rounded-lg">

                <!-- Letterhead -->
                <div class="px-10 pt-10 pb-6 text-center">
                    <div aria-hidden="true" class="mx-auto w-12 h-12 rounded-full border-2 border-[#1F5A44] flex items-center justify-center mb-4">
                        <span class="receipt-serif text-lg font-semibold text-[#1F5A44]">{{ strtoupper(substr($payment->student->school->name ?? 'S', 0, 1)) }}</span>
                    </div>
                    <h3 class="receipt-serif text-2xl font-semibold text-[#1B2420] tracking-tight">{{ $payment->student->school->name ?? 'School Name' }}</h3>
                    <p class="mt-2 text-[11px] font-semibold uppercase tracking-[0.15em] text-[#1F5A44]">Official receipt · Payment confirmed</p>
                </div>

                <!-- Meta strip -->
                <dl class="grid grid-cols-3 gap-4 px-10 py-6 text-center border-t border-[#D7DED8]">
                    <div>
                        <dt class="text-[10px] uppercase tracking-widest text-[#6B7280]">Receipt no.</dt>
                        <dd class="mt-1 font-mono text-sm text-[#1B2420]">{{ $payment->reference }}</dd>
                    </div>
                    <div>
                        <dt class="text-[10px] uppercase tracking-widest text-[#6B7280]">Date</dt>
                        <dd class="mt-1 font-mono text-sm text-[#1B2420]">{{ $payment->created_at->format('F d, Y h:i A') }}</dd>
                    </div>
                    <div>
                        <dt class="text-[10px] uppercase tracking-widest text-[#6B7280]">Term</dt>
                        <dd class="mt-1 font-mono text-sm text-[#1B2420]">{{ $payment->academicPeriod->name ?? 'N/A' }}</dd>
                    </div>
                </dl>

                <!-- Billed to -->
                <dl class="px-10 py-6 border-t border-[#D7DED8]">
                    <dt class="text-[10px] uppercase tracking-widest text-[#6B7280] mb-2">Billed to</dt>
                    <dd class="text-base font-medium text-[#1B2420]">{{ $payment->student->user->name }}</dd>
                    <dd class="mt-1 text-sm text-[#6B7280]">Student ID {{ $payment->student->student_id }} · {{ $payment->student->academicLevel->name ?? 'N/A' }}</dd>
                </dl>

                <!-- Line item + total -->
                <div class="px-10 py-6 border-t border-[#D7DED8]">
                    <div class="relative rounded-md bg-[#E3ECE6] px-6 py-5">
                        <div class="flex items-center justify-between text-sm text-[#1B2420]">
                            <span>School fees payment — {{ $payment->academicPeriod->name ?? 'current term' }}</span>
                            <span class="font-mono tabular-nums">₵{{ number_format($payment->amount, 2) }}</span>
                        </div>
                        <div class="mt-4 pt-4 border-t-4 border-double border-[#1F5A44]/40 flex items-center justify-between">
                            <span class="text-sm font-semibold uppercase tracking-wide text-[#1B2420]">Total paid</span>
                            <span class="font-mono text-2xl font-bold text-[#1F5A44] tabular-nums">₵{{ number_format($payment->amount, 2) }}</span>
                        </div>

                        <div aria-hidden="true" class="pointer-events-none select-none absolute -top-4 -right-4 w-24 h-24 rounded-full border-[3px] border-double border-[#1F5A44] flex flex-col items-center justify-center rotate-[-9deg] opacity-80 mix-blend-multiply">
                            <span class="receipt-serif text-[13px] font-bold tracking-wider text-[#1F5A44] leading-none">PAID</span>
                            <span class="text-[7px] tracking-[0.2em] text-[#1F5A44] mt-1">IN FULL</span>
                        </div>
                    </div>
                </div>

                <!-- Method / status -->
                <div class="px-10 py-6 border-t border-[#D7DED8] flex items-center justify-between text-xs text-[#6B7280]">
                    <span>Paid via Paystack</span>
                    <span class="inline-flex items-center gap-1.5 font-medium text-[#1F5A44]">
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        Completed
                    </span>
                </div>

                <!-- Footer -->
                <div class="px-10 py-8 border-t border-[#D7DED8] text-center">
                    <p class="text-xs text-[#6B7280]">This is a computer-generated receipt and does not require a signature.</p>
                    <a href="{{ route('students.fees.index') }}"
                       class="print:hidden mt-5 inline-flex items-center px-5 py-2.5 border border-[#1F5A44] rounded-md text-xs font-semibold uppercase tracking-wider text-[#1F5A44] hover:bg-[#1F5A44] hover:text-white transition">
                        View all payments
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
