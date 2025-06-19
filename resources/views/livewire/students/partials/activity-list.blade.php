<!-- List View -->
<div class="space-y-4">
    @forelse($activityLogs as $activity)
        @php
            $activityColor = $this->getActivityTypeColor($activity->description);
        @endphp

        <div class="bg-white dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600 p-6 shadow-sm hover:shadow-md transition-all duration-200 hover:border-{{ $activityColor }}-300">
            <div class="flex items-start justify-between">
                <div class="flex items-start space-x-4 flex-1">
                    <!-- Activity Icon -->
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 rounded-full bg-{{ $activityColor }}-500 flex items-center justify-center text-white">
                            @include('livewire.students.partials.activity-icon', ['description' => $activity->description, 'size' => 'w-5 h-5'])
                        </div>
                    </div>

                    <!-- Activity Content -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white truncate">
                                {{ $activity->description }}
                            </h3>
                            @if($activity->log_name)
                                <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-{{ $activityColor }}-100 text-{{ $activityColor }}-800 dark:bg-{{ $activityColor }}-900 dark:text-{{ $activityColor }}-200">
                                    {{ ucfirst($activity->log_name) }}
                                </span>
                            @endif
                        </div>

                        <div class="flex items-center space-x-4 text-sm text-gray-500 dark:text-gray-400 mb-3">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                                </svg>
                                {{ $activity->created_at->format('M j, Y') }}
                            </span>
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                </svg>
                                {{ $activity->created_at->format('g:i A') }}
                            </span>
                            <span>{{ $activity->created_at->diffForHumans() }}</span>
                        </div>

                        <!-- Activity Properties (Limited) -->
                        @if($activity->properties && count($activity->properties) > 0)
                            <div class="flex flex-wrap gap-2">
                                @foreach(collect($activity->properties)->take(3) as $key => $value)
                                    @if(is_string($value) || is_numeric($value))
                                        <span class="inline-flex items-center px-2 py-1 bg-gray-100 dark:bg-gray-600 text-gray-700 dark:text-gray-300 text-xs rounded-md">
                                            <span class="font-medium">{{ str_replace('_', ' ', ucfirst($key)) }}:</span>
                                            <span class="ml-1 truncate max-w-20">{{ $value }}</span>
                                        </span>
                                    @endif
                                @endforeach
                                @if(count($activity->properties) > 3)
                                    <span class="inline-flex items-center px-2 py-1 bg-gray-100 dark:bg-gray-600 text-gray-500 dark:text-gray-400 text-xs rounded-md">
                                        +{{ count($activity->properties) - 3 }} more
                                    </span>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center space-x-2 ml-4">
                    <button
                        wire:click="viewActivityDetails({{ $activity->id }})"
                        class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-{{ $activityColor }}-500 dark:bg-gray-600 dark:border-gray-500 dark:text-gray-200 dark:hover:bg-gray-500">
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path>
                        </svg>
                        View
                    </button>
                </div>
            </div>
        </div>
    @empty
        @include('livewire.students.partials.no-activities')
    @endforelse
</div>
