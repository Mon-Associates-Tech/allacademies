@php
    $type = $notificationData['type'] ?? 'generic';
    $category = $notificationData['category'] ?? 'general';
    $data = $notificationData['data'] ?? [];
@endphp

@if($type === 'assignment' && !empty($data))
    <!-- Assignment Details -->
    <div class="space-y-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 flex items-center">
            <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
            </svg>
            Assignment Details
        </h2>

        <!-- Info Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <!-- Subject -->
            @if(!empty($data['subject']))
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 rounded-xl p-5 border border-blue-200 dark:border-blue-700/50">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-blue-600 dark:text-blue-400 uppercase tracking-wide">Subject</p>
                            <p class="text-base font-semibold text-blue-900 dark:text-blue-100">{{ $data['subject'] }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Teacher -->
            @if(!empty($data['teacher']))
                <div class="bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20 rounded-xl p-5 border border-purple-200 dark:border-purple-700/50">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-purple-500 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-purple-600 dark:text-purple-400 uppercase tracking-wide">Teacher</p>
                            <p class="text-base font-semibold text-purple-900 dark:text-purple-100">{{ $data['teacher'] }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Type -->
            @if(!empty($data['type']))
                <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 dark:from-indigo-900/20 dark:to-indigo-800/20 rounded-xl p-5 border border-indigo-200 dark:border-indigo-700/50">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-indigo-500 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-indigo-600 dark:text-indigo-400 uppercase tracking-wide">Type</p>
                            <p class="text-base font-semibold text-indigo-900 dark:text-indigo-100 capitalize">{{ $data['type'] }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Duration -->
            @if(!empty($data['duration_in_minutes']))
                <div class="bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 rounded-xl p-5 border border-green-200 dark:border-green-700/50">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-green-600 dark:text-green-400 uppercase tracking-wide">Duration</p>
                            <p class="text-base font-semibold text-green-900 dark:text-green-100">{{ $data['duration_in_minutes'] }} minutes</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Total Marks -->
            @if(!empty($data['total_marks']))
                <div class="bg-gradient-to-br from-amber-50 to-amber-100 dark:from-amber-900/20 dark:to-amber-800/20 rounded-xl p-5 border border-amber-200 dark:border-amber-700/50">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-amber-500 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-amber-600 dark:text-amber-400 uppercase tracking-wide">Total Marks</p>
                            <p class="text-base font-semibold text-amber-900 dark:text-amber-100">{{ $data['total_marks'] }} points</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Schedule Section -->
        @if(!empty($data['starts_at']) || !empty($data['ends_at']))
            <div class="bg-gradient-to-r from-orange-50 to-red-50 dark:from-orange-900/20 dark:to-red-900/20 rounded-xl p-6 border border-orange-200 dark:border-orange-700/50">
                <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    Schedule
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @if(!empty($data['starts_at']))
                        <div class="flex items-start space-x-3">
                            <div class="w-8 h-8 bg-green-500 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-green-600 dark:text-green-400">Starts At</p>
                                <p class="text-base font-semibold text-gray-900 dark:text-gray-100">
                                    {{ \Carbon\Carbon::parse($data['starts_at'])->format('M j, Y') }}
                                </p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ \Carbon\Carbon::parse($data['starts_at'])->format('g:i A') }}
                                    <span class="text-gray-400 dark:text-gray-500">•</span>
                                    {{ \Carbon\Carbon::parse($data['starts_at'])->diffForHumans() }}
                                </p>
                            </div>
                        </div>
                    @endif

                    @if(!empty($data['ends_at']))
                        <div class="flex items-start space-x-3">
                            <div class="w-8 h-8 bg-red-500 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-red-600 dark:text-red-400">Ends At</p>
                                <p class="text-base font-semibold text-gray-900 dark:text-gray-100">
                                    {{ \Carbon\Carbon::parse($data['ends_at'])->format('M j, Y') }}
                                </p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ \Carbon\Carbon::parse($data['ends_at'])->format('g:i A') }}
                                    <span class="text-gray-400 dark:text-gray-500">•</span>
                                    {{ \Carbon\Carbon::parse($data['ends_at'])->diffForHumans() }}
                                </p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>

@elseif($type === 'assessment' && !empty($data))
    <!-- Assessment/Quiz Details -->
    <div class="space-y-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 flex items-center">
            <svg class="w-5 h-5 mr-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Quiz Results
        </h2>

        <!-- Score Display -->
        @if(isset($data['score']))
            <div class="bg-gradient-to-br from-purple-50 to-indigo-50 dark:from-purple-900/20 dark:to-indigo-900/20 rounded-2xl p-8 border border-purple-200 dark:border-purple-700/50 text-center">
                <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-gradient-to-br from-purple-500 to-indigo-600 mb-4 shadow-lg">
                    <span class="text-3xl font-bold text-white">{{ $data['score'] }}%</span>
                </div>
                <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">Your Score</p>
                @if(isset($data['correct_answers']) && isset($data['total_questions']))
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                        {{ $data['correct_answers'] }} out of {{ $data['total_questions'] }} questions correct
                    </p>
                @endif
            </div>
        @endif

        <!-- Info Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <!-- Book/Content -->
            @if(!empty($data['book_title']))
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 rounded-xl p-5 border border-blue-200 dark:border-blue-700/50">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-blue-600 dark:text-blue-400 uppercase tracking-wide">Content</p>
                            <p class="text-base font-semibold text-blue-900 dark:text-blue-100">{{ $data['book_title'] }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Subject -->
            @if(!empty($data['subject']))
                <div class="bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 rounded-xl p-5 border border-green-200 dark:border-green-700/50">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-green-600 dark:text-green-400 uppercase tracking-wide">Subject</p>
                            <p class="text-base font-semibold text-green-900 dark:text-green-100">{{ $data['subject'] }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Difficulty -->
            @if(!empty($data['difficulty']))
                <div class="bg-gradient-to-br from-amber-50 to-amber-100 dark:from-amber-900/20 dark:to-amber-800/20 rounded-xl p-5 border border-amber-200 dark:border-amber-700/50">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-amber-500 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-amber-600 dark:text-amber-400 uppercase tracking-wide">Difficulty</p>
                            <p class="text-base font-semibold text-amber-900 dark:text-amber-100 capitalize">{{ $data['difficulty'] }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Time Spent -->
            @if(!empty($data['time_spent']))
                <div class="bg-gradient-to-br from-cyan-50 to-cyan-100 dark:from-cyan-900/20 dark:to-cyan-800/20 rounded-xl p-5 border border-cyan-200 dark:border-cyan-700/50">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-cyan-500 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-cyan-600 dark:text-cyan-400 uppercase tracking-wide">Time Spent</p>
                            <p class="text-base font-semibold text-cyan-900 dark:text-cyan-100">
                                {{ floor($data['time_spent'] / 60) }}m {{ $data['time_spent'] % 60 }}s
                            </p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

@elseif($type === 'generic' && !empty($data))
    <!-- Generic Notification Details -->
    @if(is_array($data) && count($data) > 0)
        <div class="space-y-4">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 flex items-center">
                <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Additional Details
            </h2>

            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-5 border border-gray-200 dark:border-gray-600">
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($data as $key => $value)
                        @if(!in_array($key, ['title', 'message', 'url', 'action_url']) && !is_array($value) && !is_null($value))
                            <div>
                                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                                    {{ str_replace('_', ' ', ucfirst($key)) }}
                                </dt>
                                <dd class="mt-1 text-sm font-medium text-gray-900 dark:text-gray-100">
                                    @php
                                        $displayValue = $value;

                                        // Handle boolean values - convert 0/1/true/false to Yes/No
                                        if ($value === true || $value === 1 || $value === '1') {
                                            $displayValue = 'Yes';
                                        } elseif ($value === false || $value === 0 || $value === '0') {
                                            $displayValue = 'No';
                                        }
                                        // Handle date strings - check if it looks like a date/datetime
                                        elseif (is_string($value) && !empty($value)) {
                                            // Check for common date patterns (ISO 8601, MySQL datetime, etc.)
                                            $datePatterns = [
                                                '/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}/',  // ISO 8601
                                                '/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', // MySQL datetime
                                                '/^\d{4}-\d{2}-\d{2}$/',                    // Date only
                                            ];

                                            $isDate = false;
                                            foreach ($datePatterns as $pattern) {
                                                if (preg_match($pattern, $value)) {
                                                    $isDate = true;
                                                    break;
                                                }
                                            }

                                            if ($isDate) {
                                                try {
                                                    $carbonDate = \Carbon\Carbon::parse($value);
                                                    // Format based on whether it has time component
                                                    if ($carbonDate->format('H:i:s') === '00:00:00') {
                                                        $displayValue = $carbonDate->format('M j, Y');
                                                    } else {
                                                        $displayValue = $carbonDate->format('M j, Y \a\t g:i A');
                                                    }
                                                } catch (\Exception $e) {
                                                    // Keep original value if parsing fails
                                                    $displayValue = $value;
                                                }
                                            }
                                        }
                                    @endphp
                                    {!! $displayValue !!}
                                </dd>
                            </div>
                        @endif
                    @endforeach
                </dl>
            </div>
        </div>
    @endif
@endif
