<div class="flex items-center space-x-3">
    {{-- Progress Circle/Checkmark --}}
    <div class="flex-shrink-0">
        @if($isComplete)
            {{-- Completed Checkmark --}}
            <div class="w-8 h-8 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
            </div>
        @else
            {{-- Progress Circle --}}
            <div class="relative w-8 h-8">
                <svg class="w-8 h-8 transform -rotate-90" viewBox="0 0 36 36">
                    {{-- Background Circle --}}
                    <circle
                        cx="18"
                        cy="18"
                        r="15"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="3"
                        class="text-gray-200 dark:text-gray-700"
                    />
                    {{-- Progress Circle --}}
                    <circle
                        cx="18"
                        cy="18"
                        r="15"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="3"
                        stroke-linecap="round"
                        class="{{ $progressColor }}"
                        stroke-dasharray="{{ $progressPercentage * 0.94 }}, 100"
                    />
                </svg>
                {{-- Percentage Text --}}
                <div class="absolute inset-0 flex items-center justify-center">
                    <span class="text-xs font-medium text-gray-600 dark:text-gray-400">
                        {{ number_format($progressPercentage, 0) }}
                    </span>
                </div>
            </div>
        @endif
    </div>

    {{-- Progress Info --}}
    <div class="flex-1 min-w-0">
        <div class="flex items-center justify-between">
            <span class="text-sm font-medium text-gray-900 dark:text-white truncate">
                {{ $section->title }}
            </span>
        </div>
        <div class="flex items-center space-x-2 mt-1">
            {{-- Status Text --}}
            <span class="text-xs {{ $isComplete ? 'text-green-600 dark:text-green-400' : 'text-gray-500 dark:text-gray-400' }}">
                {{ $statusText }}
            </span>

            {{-- Progress Bar (optional, for more detail) --}}
            @if(!$isComplete && $totalContents > 0)
                <div class="flex-1 max-w-24">
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1.5">
                        <div
                            class="h-1.5 rounded-full transition-all duration-300 {{ $progressColor }}"
                            style="width: {{ $progressPercentage }}%"
                        ></div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Status Icon --}}
    <div class="flex-shrink-0">
        @if($isComplete)
            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                Complete
            </span>
        @elseif($progressPercentage > 0)
            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400">
                In Progress
            </span>
        @else
            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400">
                Not Started
            </span>
        @endif
    </div>
</div>
