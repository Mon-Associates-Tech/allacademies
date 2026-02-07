<x-layouts.app>
    {{-- Print-specific styles to hide non-certificate elements --}}
    <style>
        @media print {
            /* Hide all navigation, headers, footers, and UI elements */
            nav,
            header,
            footer,
            aside,
            .no-print,
            [data-school-switcher],
            [x-data*="schoolSwitcher"],
            .school-switcher,
            #school-switcher,
            .sidebar,
            .navigation,
            .breadcrumb,
            .actions-section,
            button,
            a[href],
            .print-hide {
                display: none !important;
            }

            /* Reset page background and margins */
            body,
            html {
                background: white !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            /* Make the certificate container full width */
            .print-container {
                width: 100% !important;
                max-width: none !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            /* Style the certificate for print */
            .certificate-print {
                display: block !important;
                width: 100% !important;
                max-width: none !important;
                margin: 0 auto !important;
                padding: 20mm !important;
                box-shadow: none !important;
                border: none !important;
                page-break-inside: avoid !important;
            }

            /* Ensure colors print correctly */
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                color-adjust: exact !important;
            }

            /* Hide the details section below certificate */
            .certificate-details {
                display: none !important;
            }
        }
    </style>

    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8 print-container">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Breadcrumb (hidden on print) --}}
            <nav class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 mb-6 print-hide">
                <a href="{{ route('my-learning.index') }}" class="hover:text-gray-700 dark:hover:text-gray-300">My Learning</a>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                <a href="{{ route('my-learning.certificates') }}" class="hover:text-gray-700 dark:hover:text-gray-300">Certificates</a>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                <span class="text-gray-900 dark:text-white">Certificate</span>
            </nav>

            {{-- Page Header (hidden on print) --}}
            <div class="mb-8 print-hide">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Certificate of Completion</h1>
                <p class="mt-1 text-gray-600 dark:text-gray-400">Your achievement for completing the course</p>
            </div>

            {{-- Certificate Card --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden mb-6 certificate-print">
                {{-- Certificate Preview --}}
                <div class="aspect-[1.414/1] bg-gradient-to-br from-indigo-50 to-purple-50 dark:from-indigo-900/20 dark:to-purple-900/20 p-8 flex items-center justify-center">
                    <div class="w-full max-w-2xl bg-white dark:bg-gray-800 rounded-lg shadow-lg border-4 border-indigo-100 dark:border-indigo-800 p-8 text-center">
                        {{-- Issuing Organization Header --}}
                        <div class="mb-4">
                            <h1 class="text-lg font-bold text-indigo-700 dark:text-indigo-300 uppercase tracking-wider">
                                {{ config('app.name') }}
                            </h1>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Online Learning Platform</p>
                        </div>

                        {{-- Certificate Header --}}
                        <div class="mb-6">
                            <div class="w-16 h-16 mx-auto mb-4 bg-indigo-100 dark:bg-indigo-900 rounded-full flex items-center justify-center">
                                <svg class="w-8 h-8 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                                </svg>
                            </div>
                            <h2 class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider mb-2">Certificate of Completion</h2>
                            <p class="text-gray-500 dark:text-gray-400 text-sm">This is to certify that</p>
                        </div>

                        {{-- Recipient Name --}}
                        <div class="mb-6">
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-white border-b-2 border-indigo-200 dark:border-indigo-700 pb-2 inline-block px-8">
                                {{ $certificate->recipient_name }}
                            </h3>
                        </div>

                        {{-- Course Info --}}
                        <div class="mb-6">
                            <p class="text-gray-500 dark:text-gray-400 text-sm mb-2">has successfully completed the course</p>
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ $certificate->course->title ?? 'Course' }}
                            </h4>
                        </div>

                        {{-- Date & Certificate Number --}}
                        <div class="flex items-center justify-center gap-8 text-sm text-gray-500 dark:text-gray-400 mb-6">
                            <div>
                                <span class="block text-xs uppercase tracking-wider mb-1">Issue Date</span>
                                <span class="font-medium text-gray-900 dark:text-white">{{ $certificate->issue_date->format('F d, Y') }}</span>
                            </div>
                            <div>
                                <span class="block text-xs uppercase tracking-wider mb-1">Certificate No.</span>
                                <span class="font-medium text-gray-900 dark:text-white">{{ $certificate->certificate_number }}</span>
                            </div>
                        </div>

                        {{-- Issuing Organization Footer --}}
                        <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Issued by</p>
                            <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ config('app.name') }}</p>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                                Verification: {{ $certificate->verification_code }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Certificate Details (hidden on print) --}}
                <div class="p-6 border-t border-gray-200 dark:border-gray-700 certificate-details print-hide">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Left Column --}}
                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Recipient</label>
                                <p class="text-gray-900 dark:text-white font-medium">{{ $certificate->recipient_name }}</p>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Course</label>
                                <p class="text-gray-900 dark:text-white font-medium">{{ $certificate->course->title ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Certificate Number</label>
                                <p class="text-gray-900 dark:text-white font-mono">{{ $certificate->certificate_number }}</p>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Issued By</label>
                                <p class="text-gray-900 dark:text-white font-medium">{{ config('app.name') }}</p>
                            </div>
                        </div>

                        {{-- Right Column --}}
                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Issue Date</label>
                                <p class="text-gray-900 dark:text-white font-medium">{{ $certificate->issue_date->format('F d, Y') }}</p>
                            </div>
                            @if($certificate->expiry_date)
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Expiry Date</label>
                                    <p class="text-gray-900 dark:text-white font-medium">{{ $certificate->expiry_date->format('F d, Y') }}</p>
                                </div>
                            @endif
                            <div>
                                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Verification Code</label>
                                <p class="text-gray-900 dark:text-white font-mono text-sm">{{ $certificate->verification_code }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Actions (hidden on print) --}}
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 print-hide actions-section">
                <div class="flex items-center gap-3">
                    @if($certificate->pdf_path)
                        <a href="{{ route('lms.courses.certificate.download', $certificate->course) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            Download PDF
                        </a>
                    @endif
                    <button onclick="window.print()" class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 font-medium rounded-lg border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                        </svg>
                        Print
                    </button>
                </div>

                {{-- Verification Link --}}
                <div class="text-center sm:text-right">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Verify this certificate at:</p>
                    <a href="{{ route('certificates.verify', $certificate->verification_code) }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline font-mono">
                        {{ route('certificates.verify', $certificate->verification_code) }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
