<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <div class="py-6 px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6">
            <div class="sm:flex sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Document Generator</h2>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        {{ $student->user->name }} - {{ $student->academicLevel->name ?? 'N/A' }}
                    </p>
                </div>
                <div class="mt-4 sm:mt-0">
                    <a href="{{ route('students.show', $student) }}"
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:bg-gray-600">
                        Back to Student
                    </a>
                </div>
            </div>
        </div>

        <!-- Flash Messages -->
        @if (session()->has('success'))
            <div class="mb-4 rounded-md bg-green-50 dark:bg-green-900 p-4">
                <div class="flex">
                    <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <p class="ml-3 text-sm font-medium text-green-800 dark:text-green-200">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="mb-4 rounded-md bg-red-50 dark:bg-red-900 p-4">
                <div class="flex">
                    <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                    <p class="ml-3 text-sm font-medium text-red-800 dark:text-red-200">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        @if (session()->has('info'))
            <div class="mb-4 rounded-md bg-blue-50 dark:bg-blue-900 p-4">
                <div class="flex">
                    <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                    </svg>
                    <p class="ml-3 text-sm font-medium text-blue-800 dark:text-blue-200">{{ session('info') }}</p>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column: Document Type Selection -->
            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Document Type</h3>
                        <nav class="space-y-2">
                            <button wire:click="$set('documentType', 'report-card')"
                                    class="w-full flex items-center px-4 py-3 text-sm font-medium rounded-md transition {{ $documentType === 'report-card' ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-200' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700' }}">
                                <svg class="mr-3 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Report Card
                            </button>

                            <button wire:click="$set('documentType', 'id-card')"
                                    class="w-full flex items-center px-4 py-3 text-sm font-medium rounded-md transition {{ $documentType === 'id-card' ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-200' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700' }}">
                                <svg class="mr-3 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                                </svg>
                                Student ID Card
                            </button>

                            <button wire:click="$set('documentType', 'library-card')"
                                    class="w-full flex items-center px-4 py-3 text-sm font-medium rounded-md transition {{ $documentType === 'library-card' ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-200' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700' }}">
                                <svg class="mr-3 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                                Library Card
                            </button>

                            <button wire:click="$set('documentType', 'attendance-report')"
                                    class="w-full flex items-center px-4 py-3 text-sm font-medium rounded-md transition {{ $documentType === 'attendance-report' ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-200' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700' }}">
                                <svg class="mr-3 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                </svg>
                                Attendance Report
                            </button>
                        </nav>
                    </div>

                    <!-- Existing Documents -->
                    <div class="border-t border-gray-200 dark:border-gray-700 p-6">
                        <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-3">Existing Documents</h4>

                        @if($documentType === 'report-card')
                            <div class="space-y-2">
                                @forelse($reportCards->take(5) as $card)
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-gray-600 dark:text-gray-400">
                                            {{ $card->academicYear->name ?? 'N/A' }} - {{ $card->term }}
                                        </span>
                                        <button wire:click="downloadReportCard({{ $card->id }})"
                                                class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400">
                                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                            </svg>
                                        </button>
                                    </div>
                                @empty
                                    <p class="text-sm text-gray-500 dark:text-gray-400">No report cards found</p>
                                @endforelse
                            </div>
                        @elseif($documentType === 'id-card')
                            <div class="space-y-2">
                                @forelse($idCards->take(5) as $card)
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-gray-600 dark:text-gray-400">
                                            {{ $card->card_number }} ({{ ucfirst($card->status) }})
                                        </span>
                                        <button wire:click="downloadIdCard({{ $card->id }})"
                                                class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400">
                                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                            </svg>
                                        </button>
                                    </div>
                                @empty
                                    <p class="text-sm text-gray-500 dark:text-gray-400">No ID cards found</p>
                                @endforelse
                            </div>
                        @elseif($documentType === 'library-card')
                            <div class="space-y-2">
                                @forelse($libraryCards->take(5) as $card)
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-gray-600 dark:text-gray-400">
                                            {{ $card->card_number }} ({{ ucfirst($card->status) }})
                                        </span>
                                        <button wire:click="downloadLibraryCard({{ $card->id }})"
                                                class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400">
                                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                            </svg>
                                        </button>
                                    </div>
                                @empty
                                    <p class="text-sm text-gray-500 dark:text-gray-400">No library cards found</p>
                                @endforelse
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right Column: Document Form -->
            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <div class="p-6">
                        <!-- Report Card Form -->
                        @if($documentType === 'report-card')
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-6">Generate Report Card</h3>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Academic Year *
                                        </label>
                                        <select wire:model.live="selectedAcademicYearId"
                                                class="block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                            <option value="">Select Academic Year</option>
                                            @foreach($academicYears as $year)
                                                <option value="{{ $year->id }}">{{ $year->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('selectedAcademicYearId') <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Term/Semester *
                                        </label>
                                        <select wire:model.live="selectedTerm"
                                                class="block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                            <option value="Term 1">Term 1</option>
                                            <option value="Term 2">Term 2</option>
                                            <option value="Term 3">Term 3</option>
                                            <option value="Semester 1">Semester 1</option>
                                            <option value="Semester 2">Semester 2</option>
                                        </select>
                                        @error('selectedTerm') <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <!-- Grades Table -->
                                <div class="overflow-x-auto mb-6">
                                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                        <thead class="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Subject</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Assignments (40%)</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Quizzes (10%)</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Final Exam (50%)</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Total</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Grade</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Remarks</th>
                                        </tr>
                                        </thead>
                                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        @foreach($grades as $subjectId => $grade)
                                            <tr>
                                                <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ $grade['subject_name'] }}
                                                </td>
                                                <td class="px-4 py-3">
                                                    <div class="flex items-center space-x-2">
                                                        <input type="number" wire:model.blur="grades.{{ $subjectId }}.assessments_score"
                                                               class="w-24 rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                                               min="0" max="40" step="0.1" placeholder="0–40">
                                                        <span class="text-[10px] px-1.5 py-0.5 rounded bg-indigo-50 text-indigo-700 border border-indigo-200 dark:bg-indigo-900/30 dark:text-indigo-300 dark:border-indigo-800">auto</span>
                                                    </div>
                                                    @error("grades.{$subjectId}.assessments_score") <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                                                </td>
                                                <td class="px-4 py-3">
                                                    <input type="number" wire:model.blur="grades.{{ $subjectId }}.quizzes_score"
                                                           class="w-24 rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                                           min="0" max="10" step="0.1" placeholder="0–10">
                                                    @error("grades.{$subjectId}.quizzes_score") <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                                                </td>
                                                <td class="px-4 py-3">
                                                    <input type="number" wire:model.blur="grades.{{ $subjectId }}.final_exam_score"
                                                           class="w-24 rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                                           min="0" max="50" step="0.1" placeholder="0–50">
                                                    @error("grades.{$subjectId}.final_exam_score") <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                                                </td>
                                                <td class="px-4 py-3 text-sm font-semibold text-gray-900 dark:text-white">
                                                    {{ number_format($grade['total_score'], 1) }}
                                                </td>
                                                <td class="px-4 py-3 text-sm font-semibold text-gray-900 dark:text-white">
                                                    {{ $grade['grade_label'] }}
                                                </td>
                                                <td class="px-4 py-3">
                                                    <input type="text" wire:model="grades.{{ $subjectId }}.remarks"
                                                           class="w-32 rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                                           placeholder="Remarks">
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Attendance Summary -->
                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-6">
                                    <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-3">Attendance Summary</h4>
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                        <div class="text-center">
                                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $attendanceSummary['total'] }}</p>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Total Sessions</p>
                                        </div>
                                        <div class="text-center">
                                            <p class="text-2xl font-bold text-green-600">{{ $attendanceSummary['present'] }}</p>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Present</p>
                                        </div>
                                        <div class="text-center">
                                            <p class="text-2xl font-bold text-red-600">{{ $attendanceSummary['absent'] }}</p>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Absent</p>
                                        </div>
                                        <div class="text-center">
                                            <p class="text-2xl font-bold text-indigo-600">{{ $attendanceSummary['rate'] }}%</p>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Attendance Rate</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="flex justify-end space-x-3">
                                    <button wire:click="preview" type="button"
                                            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:bg-gray-600">
                                        <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        Preview
                                    </button>
                                    <button wire:click="downloadReportCard" type="button"
                                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                                        <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                        </svg>
                                        Generate & Download
                                    </button>
                                </div>
                            </div>

                            <!-- ID Card Form -->
                        @elseif($documentType === 'id-card')
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-6">Generate Student ID Card</h3>

                                <div class="space-y-6 mb-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Card Validity (Months)
                                        </label>
                                        <select wire:model="cardExpiryMonths"
                                                class="block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                            <option value="12">12 Months (1 Year)</option>
                                            <option value="24">24 Months (2 Years)</option>
                                            <option value="36">36 Months (3 Years)</option>
                                            <option value="48">48 Months (4 Years)</option>
                                        </select>
                                    </div>

                                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                        <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-3">Student Information</h4>
                                        <dl class="space-y-2">
                                            <div class="flex justify-between">
                                                <dt class="text-sm text-gray-500 dark:text-gray-400">Name:</dt>
                                                <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $student->user->name }}</dd>
                                            </div>
                                            <div class="flex justify-between">
                                                <dt class="text-sm text-gray-500 dark:text-gray-400">Student ID:</dt>
                                                <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $student->student_id }}</dd>
                                            </div>
                                            <div class="flex justify-between">
                                                <dt class="text-sm text-gray-500 dark:text-gray-400">Academic Level:</dt>
                                                <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $student->academicLevel->name ?? 'N/A' }}</dd>
                                            </div>
                                            <div class="flex justify-between">
                                                <dt class="text-sm text-gray-500 dark:text-gray-400">Group:</dt>
                                                <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $student->studentGroup->name ?? 'N/A' }}</dd>
                                            </div>
                                        </dl>
                                    </div>
                                </div>

                                <div class="flex justify-end space-x-3">
                                    <button wire:click="preview" type="button"
                                            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:bg-gray-600">
                                        <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        Preview
                                    </button>
                                    <button wire:click="downloadIdCard" type="button"
                                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                                        <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                        </svg>
                                        Generate & Download
                                    </button>
                                </div>
                            </div>

                            <!-- Library Card Form -->
                        @elseif($documentType === 'library-card')
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-6">Generate Library Card</h3>

                                <div class="space-y-6 mb-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Card Type
                                        </label>
                                        <select wire:model="libraryCardType"
                                                class="block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                            <option value="student">Student</option>
                                            <option value="premium">Premium</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Card Validity (Months)
                                        </label>
                                        <select wire:model="libraryCardExpiryMonths"
                                                class="block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                            <option value="12">12 Months (1 Year)</option>
                                            <option value="24">24 Months (2 Years)</option>
                                            <option value="36">36 Months (3 Years)</option>
                                        </select>
                                    </div>

                                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                        <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-3">Student Information</h4>
                                        <dl class="space-y-2">
                                            <div class="flex justify-between">
                                                <dt class="text-sm text-gray-500 dark:text-gray-400">Name:</dt>
                                                <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $student->user->name }}</dd>
                                            </div>
                                            <div class="flex justify-between">
                                                <dt class="text-sm text-gray-500 dark:text-gray-400">Student ID:</dt>
                                                <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $student->student_id }}</dd>
                                            </div>
                                            <div class="flex justify-between">
                                                <dt class="text-sm text-gray-500 dark:text-gray-400">Academic Level:</dt>
                                                <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $student->academicLevel->name ?? 'N/A' }}</dd>
                                            </div>
                                        </dl>
                                    </div>
                                </div>

                                <div class="flex justify-end space-x-3">
                                    <button wire:click="preview" type="button"
                                            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:bg-gray-600">
                                        <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        Preview
                                    </button>
                                    <button wire:click="downloadLibraryCard" type="button"
                                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                                        <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                        </svg>
                                        Generate & Download
                                    </button>
                                </div>
                            </div>

                            <!-- Attendance Report -->
                        @elseif($documentType === 'attendance-report')
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-6">Generate Attendance Report</h3>

                                <div class="mb-6">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Academic Year
                                    </label>
                                    <select wire:model.live="selectedAcademicYearId"
                                            class="block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                        <option value="">Select Academic Year</option>
                                        @foreach($academicYears as $year)
                                            <option value="{{ $year->id }}">{{ $year->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6 mb-6">
                                    <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-4">Attendance Summary</h4>
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                                        <div class="text-center">
                                            <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $attendanceSummary['total'] }}</p>
                                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Total Sessions</p>
                                        </div>
                                        <div class="text-center">
                                            <p class="text-3xl font-bold text-green-600">{{ $attendanceSummary['present'] }}</p>
                                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Present</p>
                                        </div>
                                        <div class="text-center">
                                            <p class="text-3xl font-bold text-red-600">{{ $attendanceSummary['absent'] }}</p>
                                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Absent</p>
                                        </div>
                                        <div class="text-center">
                                            <p class="text-3xl font-bold text-indigo-600">{{ $attendanceSummary['rate'] }}%</p>
                                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Attendance Rate</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex justify-end space-x-3">
                                    <button wire:click="preview" type="button"
                                            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:bg-gray-600">
                                        <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        Preview
                                    </button>
                                    <button wire:click="downloadAttendanceReport" type="button"
                                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                                        <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                        </svg>
                                        Generate & Download
                                    </button>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Preview Panel -->
                @if($previewMode && $previewData)
                    <div class="mt-6 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Preview</h3>
                                <button wire:click="$set('previewMode', false)"
                                        class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300">
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <div class="border-2 border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                                @if($documentType === 'report-card')
                                    @include('livewire.students.previews.report-card-preview')
                                @elseif($documentType === 'id-card')
                                    @include('livewire.students.previews.id-card-preview')
                                @elseif($documentType === 'library-card')
                                    @include('livewire.students.previews.library-card-preview')
                                @elseif($documentType === 'attendance-report')
                                    @include('livewire.students.previews.attendance-report-preview')
                                @endif
                            </div>

                            <div class="mt-4 flex justify-center">
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    This is a preview. Click "Generate & Download" to create the final PDF document.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
