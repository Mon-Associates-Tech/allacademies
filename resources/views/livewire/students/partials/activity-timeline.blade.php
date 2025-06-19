<!-- Timeline Container -->
<div class="relative">
    @forelse($activityLogs as $index => $activity)
        @php
            $isToday = $activity->created_at->isToday();
            $isYesterday = $activity->created_at->isYesterday();
            $showDateLabel = $index === 0 ||
                !$activityLogs[$index - 1]->created_at->isSameDay($activity->created_at);

            $activityColor = $this->getActivityTypeColor($activity->description);
        @endphp

            <!-- Date Label -->
        @if($showDateLabel)
            <div class="relative flex items-center mb-6 mt-8 first:mt-0">
                <div class="flex-grow border-t border-gray-300 dark:border-gray-600"></div>
                <div class="mx-4 px-4 py-2 bg-gray-100 dark:bg-gray-700 rounded-full">
                    <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                        @if($isToday)
                            Today - {{ $activity->created_at->format('M j, Y') }}
                        @elseif($isYesterday)
                            Yesterday - {{ $activity->created_at->format('M j, Y') }}
                        @else
                            {{ $activity->created_at->format('l, M j, Y') }}
                        @endif
                    </span>
                </div>
                <div class="flex-grow border-t border-gray-300 dark:border-gray-600"></div>
            </div>
        @endif

        <!-- Timeline Item -->
        <div class="relative flex items-start mb-8 group">
            <!-- Timeline Line -->
            @if(!$loop->last)
                <div class="absolute left-6 top-12 w-0.5 h-full bg-gray-200 dark:bg-gray-600 group-hover:bg-{{ $activityColor }}-300 transition-colors duration-200"></div>
            @endif

            <!-- Timeline Icon -->
            <div class="relative z-10 flex-shrink-0">
                <div class="w-12 h-12 rounded-full bg-{{ $activityColor }}-500 flex items-center justify-center text-white shadow-lg group-hover:scale-110 transition-all duration-200">
                    @include('livewire.students.partials.activity-icon', ['description' => $activity->description])
                </div>

                <!-- Pulse Animation for Recent Activities -->
                @if($activity->created_at->diffInHours() < 2)
                    <div class="absolute inset-0 rounded-full bg-{{ $activityColor }}-400 animate-ping opacity-25"></div>
                @endif
            </div>

            <!-- Timeline Content -->
            <div class="ml-6 flex-1">
                <div class="bg-white dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600 p-6 shadow-sm hover:shadow-md transition-all duration-200 group-hover:border-{{ $activityColor }}-300">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">
                                {{ $activity->description }}
                            </h3>
                            <div class="flex items-center space-x-3 text-sm text-gray-500 dark:text-gray-400">
                                <span class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                    </svg>
                                    {{ $activity->created_at->format('g:i A') }}
                                </span>
                                <span>•</span>
                                <span>{{ $activity->created_at->diffForHumans() }}</span>
                                @if($activity->log_name)
                                    <span>•</span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-{{ $activityColor }}-100 text-{{ $activityColor }}-800 dark:bg-{{ $activityColor }}-900 dark:text-{{ $activityColor }}-200">
                                        {{ ucfirst($activity->log_name) }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <button
                            wire:click="viewActivityDetails({{ $activity->id }})"
                            class="ml-4 inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-{{ $activityColor }}-500 dark:bg-gray-600 dark:border-gray-500 dark:text-gray-200 dark:hover:bg-gray-500">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path>
                            </svg>
                            Details
                        </button>
                    </div>

                    <!-- Activity Properties Preview -->
                    @if($activity->properties && count($activity->properties) > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                            @foreach(collect($activity->properties)->take(6) as $key => $value)
                                @if(is_string($value) || is_numeric($value))
                                    <div class="bg-gray-50 dark:bg-gray-600 rounded-md p-3">
                                        <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                                            {{ str_replace('_', ' ', $key) }}
                                        </div>
                                        <div class="text-sm font-medium text-gray-900 dark:text-white mt-1 truncate">
                                            {{ $value }}
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                            @if(count($activity->properties) > 6)
                                <div class="bg-gray-50 dark:bg-gray-600 rounded-md p-3 flex items-center justify-center">
                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                        +{{ count($activity->properties) - 6 }} more properties
                                    </span>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @empty
        @include('livewire.students.partials.no-activities')
    @endforelse
</div>
