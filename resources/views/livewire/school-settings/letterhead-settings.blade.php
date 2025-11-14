<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Letterhead Template Settings</h1>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                Choose a professional letterhead template for your school documents such as invoices, receipts, report cards, and more.
            </p>
        </div>

        <!-- Flash Messages -->
        @if (session()->has('success'))
            <div class="mb-6 rounded-md bg-green-50 dark:bg-green-900 p-4">
                <div class="flex">
                    <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <p class="ml-3 text-sm font-medium text-green-800 dark:text-green-200">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="mb-6 rounded-md bg-red-50 dark:bg-red-900 p-4">
                <div class="flex">
                    <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                    <p class="ml-3 text-sm font-medium text-red-800 dark:text-red-200">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Template Selection -->
            <div>
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">Available Templates</h2>

                    <div class="space-y-4">
                        @foreach($availableTemplates as $key => $template)
                            <div wire:click="$set('selectedTemplate', '{{ $key }}')"
                                 wire:mouseenter="previewLetterhead('{{ $key }}')"
                                 class="relative flex items-start p-4 border-2 rounded-lg cursor-pointer transition-all
                                        {{ $selectedTemplate === $key ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20' : 'border-gray-200 dark:border-gray-700 hover:border-indigo-300 dark:hover:border-indigo-700' }}">

                                <!-- Color Preview -->
                                <div class="flex-shrink-0">
                                    <div class="w-12 h-12 rounded-lg"
                                         style="background: {{ $template['preview_color'] }}"></div>
                                </div>

                                <!-- Template Info -->
                                <div class="ml-4 flex-1">
                                    <div class="flex items-center justify-between">
                                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                            {{ $template['name'] }}
                                        </h3>
                                        @if($selectedTemplate === $key)
                                            <svg class="h-6 w-6 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                        @endif
                                    </div>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                        {{ $template['description'] }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Save Button -->
                    <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <button wire:click="saveTemplate" type="button"
                                class="w-full inline-flex justify-center items-center px-6 py-3 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Save Template Selection
                        </button>
                    </div>
                </div>
            </div>

            <!-- Preview -->
            <div>
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 sticky top-8">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">Preview</h2>

                    @if($school && $previewTemplate)
                        <div class="border border-gray-300 dark:border-gray-600 rounded-lg overflow-hidden bg-white">
                            <div class="transform scale-75 origin-top-left" style="width: 133.33%;">
                                @include('components.letterheads.' . $previewTemplate, [
                                    'school' => $school,
                                    'title' => 'Sample Document'
                                ])

                                <!-- Sample Content -->
                                <div style="padding: 0 20px 20px;">
                                    <p style="font-size: 14px; color: #6b7280; line-height: 1.6;">
                                        This is a preview of how your letterhead will appear on documents such as invoices,
                                        receipts, report cards, certificates, and other official school documents.
                                    </p>
                                    <div style="margin-top: 20px; padding: 15px; background: #f9fafb; border-radius: 4px;">
                                        <p style="font-size: 13px; color: #374151; margin: 0;">
                                            <strong>Sample Content Area</strong><br>
                                            Your document content will appear here...
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-12 text-gray-500 dark:text-gray-400">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <p class="mt-2">Hover over a template to preview</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
