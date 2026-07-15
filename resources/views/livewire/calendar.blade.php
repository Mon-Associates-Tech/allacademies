<div class="calendar-component" style="font-family: 'system-ui', -apple-system, sans-serif;">
    
    {{-- ── PAGE HEADER ── --}}
    <div class="overflow-hidden mb-6" style="border-radius: 2px; background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); box-shadow: 0 4px 24px rgba(0,0,0,0.15);">
        <div class="h-1 w-full" style="background: linear-gradient(90deg, #2563eb, #3b82f6, #60a5fa);"></div>
        <div class="px-7 py-6">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 flex items-center justify-center" style="background: linear-gradient(135deg, #2563eb, #3b82f6); border-radius: 2px;">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-white leading-snug" style="letter-spacing: -0.02em; font-family: 'Georgia', serif;">
                            Calendar
                        </h1>
                        <p class="text-sm text-slate-300 mt-1">
                            {{ ucfirst($currentView) }} View • {{ $currentDate?->format('F Y') }}
                        </p>
                    </div>
                </div>
                
                <div class="flex items-center gap-2">
                    <button @click="$dispatch('open-modal', { name: 'create-event-modal' })" class="px-4 py-2 text-sm font-semibold text-white transition-colors" style="background: linear-gradient(135deg, #2563eb, #3b82f6); border-radius: 2px; box-shadow: 0 2px 8px rgba(37,99,235,0.3);" onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Event
                        </span>
                    </button>
                    <button @click="$dispatch('open-modal', { name: 'create-note-modal' })" class="px-4 py-2 text-sm font-semibold text-white transition-colors" style="background: linear-gradient(135deg, #059669, #10b981); border-radius: 2px; box-shadow: 0 2px 8px rgba(5,150,105,0.3);" onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Note
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ── CONTROLS CARD ── --}}
    <div class="bg-white dark:bg-slate-900 overflow-hidden mb-6" style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
        
        {{-- Search and Filters Row --}}
        <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800">
            <div class="flex flex-col lg:flex-row gap-4">
                {{-- Search Input --}}
                <div class="flex-1 relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input wire:model.live.debounce.300ms="searchQuery" type="text" placeholder="Search events..." class="block w-full pl-12 pr-10 py-3 border border-slate-200 dark:border-slate-700 rounded-sm bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all text-sm" style="border-radius: 2px;">
                    @if($searchQuery)
                    <button wire:click="$set('searchQuery', '')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                    @endif
                </div>

                {{-- Time Filter --}}
                <div class="flex flex-wrap items-center gap-2">
                    <select wire:model.live="timeFilter" class="px-4 py-3 border border-slate-200 dark:border-slate-700 rounded-sm bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm" style="border-radius: 2px;">
                        <option value="all">All Events</option>
                        <option value="upcoming">Upcoming</option>
                        <option value="past">Past</option>
                    </select>

                    <button wire:click="toggleCustomDateRange" class="inline-flex items-center px-4 py-3 text-sm font-medium transition-colors" style="border-radius: 2px; {{ $showCustomDateRange ? 'background: linear-gradient(135deg, #2563eb, #3b82f6); color: white; box-shadow: 0 2px 8px rgba(37,99,235,0.3);' : 'background: white; color: #475569; border: 1px solid rgba(0,0,0,0.06);' }}" onmouseover="if(!{{ $showCustomDateRange ? 'true' : 'false' }}) this.style.background='#f1f5f9'" onmouseout="if(!{{ $showCustomDateRange ? 'true' : 'false' }}) this.style.background='white'">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Date Range
                    </button>

                    @if($hasActiveFilters)
                    <button wire:click="clearAllFilters" class="inline-flex items-center px-4 py-3 text-sm font-medium text-red-600 dark:text-red-400 transition-colors hover:bg-red-50 dark:hover:bg-red-900/20" style="border-radius: 2px; border: 1px solid rgba(220,38,38,0.2);">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Clear
                    </button>
                    @endif
                </div>
            </div>

            {{-- Custom Date Range Picker --}}
            @if($showCustomDateRange)
            <div class="mt-4 p-4 bg-slate-50 dark:bg-slate-800/50" style="border-radius: 2px;">
                <div class="flex flex-col sm:flex-row items-end gap-3">
                    <div class="flex-1 w-full">
                        <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1 uppercase tracking-wider" style="letter-spacing: 0.08em;">From</label>
                        <input type="date" wire:model="customStartDate" class="w-full px-3 py-2 border border-slate-200 dark:border-slate-700 rounded-sm bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm" style="border-radius: 2px;">
                    </div>
                    <div class="flex-1 w-full">
                        <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1 uppercase tracking-wider" style="letter-spacing: 0.08em;">To</label>
                        <input type="date" wire:model="customEndDate" class="w-full px-3 py-2 border border-slate-200 dark:border-slate-700 rounded-sm bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm" style="border-radius: 2px;">
                    </div>
                    <button wire:click="applyCustomDateRange" class="px-6 py-2 text-sm font-medium text-white transition-colors" style="background: linear-gradient(135deg, #2563eb, #3b82f6); border-radius: 2px; box-shadow: 0 2px 8px rgba(37,99,235,0.3);" onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
                        Apply
                    </button>
                </div>
            </div>
            @endif
        </div>

        {{-- Navigation and View Switcher Row --}}
        <div class="px-5 py-4">
            <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                {{-- Date Navigation --}}
                <div class="flex items-center gap-1 bg-slate-100 dark:bg-slate-800 p-1" style="border-radius: 2px;">
                    <button wire:click="previousPeriod" class="p-2 rounded-sm hover:bg-white dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 transition-colors" style="border-radius: 2px;" title="Previous {{ $currentView }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </button>

                    <button wire:click="today" class="px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-200 hover:bg-white dark:hover:bg-slate-700 rounded-sm transition-colors" style="border-radius: 2px;">
                        Today
                    </button>

                    <button wire:click="nextPeriod" class="p-2 rounded-sm hover:bg-white dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 transition-colors" style="border-radius: 2px;" title="Next {{ $currentView }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                </div>

                {{-- Current Date Display --}}
                <div class="text-center">
                    <span class="text-lg font-bold text-slate-900 dark:text-white" style="letter-spacing: -0.02em;">
                        {{ $currentDate?->format('F Y') }}
                    </span>
                    @if($searchQuery)
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                        Searching: "{{ $searchQuery }}"
                    </p>
                    @endif
                </div>

                {{-- View Switcher --}}
                <div class="flex items-center bg-slate-100 dark:bg-slate-800 p-1" style="border-radius: 2px;">
                    <button wire:click="changeView('month')" class="px-4 py-2 text-sm font-medium rounded-sm transition-colors {{ $view === 'month' ? 'text-white' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white' }}" style="border-radius: 2px; {{ $view === 'month' ? 'background: linear-gradient(135deg, #2563eb, #3b82f6); box-shadow: 0 2px 8px rgba(37,99,235,0.3);' : '' }}">
                        Month
                    </button>
                    <button wire:click="changeView('week')" class="px-4 py-2 text-sm font-medium rounded-sm transition-colors {{ $view === 'week' ? 'text-white' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white' }}" style="border-radius: 2px; {{ $view === 'week' ? 'background: linear-gradient(135deg, #2563eb, #3b82f6); box-shadow: 0 2px 8px rgba(37,99,235,0.3);' : '' }}">
                        Week
                    </button>
                    <button wire:click="changeView('day')" class="px-4 py-2 text-sm font-medium rounded-sm transition-colors {{ $view === 'day' ? 'text-white' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white' }}" style="border-radius: 2px; {{ $view === 'day' ? 'background: linear-gradient(135deg, #2563eb, #3b82f6); box-shadow: 0 2px 8px rgba(37,99,235,0.3);' : '' }}">
                        Day
                    </button>
                    <button wire:click="changeView('list')" class="px-4 py-2 text-sm font-medium rounded-sm transition-colors {{ $view === 'list' ? 'text-white' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white' }}" style="border-radius: 2px; {{ $view === 'list' ? 'background: linear-gradient(135deg, #2563eb, #3b82f6); box-shadow: 0 2px 8px rgba(37,99,235,0.3);' : '' }}">
                        List
                    </button>
                </div>
            </div>
        </div>

        {{-- Event Type Filter (collapsible) --}}
        @if(count($availableEventTypes) > 0)
        <div x-data="{ showFilters: false }" class="border-t border-slate-100 dark:border-slate-800">
            <button @click="showFilters = !showFilters" class="w-full px-5 py-3 flex items-center justify-between text-sm text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                <span class="flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    Filter by Event Type
                    @if(count($selectedEventTypes) > 0)
                    <span class="ml-2 px-2 py-0.5 text-xs text-white" style="background: linear-gradient(135deg, #2563eb, #3b82f6); border-radius: 2px;">
                        {{ count($selectedEventTypes) }} selected
                    </span>
                    @endif
                </span>
                <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': showFilters }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>

            <div x-show="showFilters" x-collapse class="px-5 py-4 bg-slate-50 dark:bg-slate-800/50">
                <div class="flex flex-wrap gap-2">
                    @foreach($availableEventTypes as $typeName => $typeClass)
                    <button wire:click="toggleEventType('{{ $typeName }}')" class="inline-flex items-center px-3 py-2 rounded-sm text-sm font-medium transition-colors" style="border-radius: 2px; {{ in_array($typeName, $selectedEventTypes) || empty($selectedEventTypes) ? 'color: white;' : 'background: transparent; color: #64748b;' }} background-color: {{ in_array($typeName, $selectedEventTypes) || empty($selectedEventTypes) ? ($eventTypeColors[$typeName] ?? '#6B7280') : 'transparent' }}; border: 2px solid {{ $eventTypeColors[$typeName] ?? '#6B7280' }};" onmouseover="if(!(in_array('{{ $typeName }}', @js($selectedEventTypes)) || @js(empty($selectedEventTypes)))) this.style.background='rgba(0,0,0,0.05)'" onmouseout="if(!(in_array('{{ $typeName }}', @js($selectedEventTypes)) || @js(empty($selectedEventTypes)))) this.style.background='transparent'">
                        <span class="w-2 h-2 rounded-full mr-2" style="background-color: {{ $eventTypeColors[$typeName] ?? '#6B7280' }}"></span>
                        {{ $typeName }}
                        @if(isset($eventCounts[$typeName]))
                        <span class="ml-1.5 text-xs opacity-75">({{ $eventCounts[$typeName] }})</span>
                        @endif
                    </button>
                    @endforeach

                    @if(count($selectedEventTypes) > 0)
                    <button wire:click="clearEventTypeFilter" class="inline-flex items-center px-3 py-2 rounded-sm text-sm font-medium text-slate-600 dark:text-slate-400 transition-colors hover:bg-slate-200 dark:hover:bg-slate-700" style="border-radius: 2px; background: #e2e8f0;">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Clear Filters
                    </button>
                    @endif
                </div>
            </div>
        </div>
        @endif
    </div>

    {{-- ── CALENDAR CONTENT ── --}}
    <div class="bg-white dark:bg-slate-900 overflow-hidden" style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
        @if($view === 'month')
            @include('livewire.calendar-views.month')
        @elseif($view === 'week')
            @include('livewire.calendar-views.week')
        @elseif($view === 'day')
            @include('livewire.calendar-views.day')
        @elseif($view === 'list')
            @include('livewire.calendar-views.list')
        @endif
    </div>

    {{-- Modals remain the same but with updated styling --}}
    <!-- Create Event Modal -->
    <x-modal-component name="create-event-modal" title="Create Event" size="xl" :show="$showCreateModal">
        <form wire:submit.prevent="createEvent">
            @if ($errors->any())
            <div class="mb-4 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800" style="border-radius: 2px;">
                <h3 class="text-sm font-medium text-red-800 dark:text-red-200 mb-2">Validation Errors:</h3>
                <ul class="text-sm text-red-700 dark:text-red-300 space-y-1">
                    @foreach ($errors->all() as $error)
                    <li>• {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            <div class="space-y-4">
                <x-form.input name="eventTitle" label="Title" wire:model="eventTitle" :required="true" />

                <x-form.markdown-editor name="eventDescription" wire-model="eventDescription" :value="$eventDescription" label="Description" :height="200" />

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <x-form.datetime-input name="eventStartDate" label="Start Date" wire:model="eventStartDate" :required="true" />
                    <x-form.datetime-input name="eventEndDate" label="End Date" wire:model="eventEndDate" />
                </div>

                <div class="flex items-center">
                    <input type="checkbox" wire:model="eventAllDay" id="eventAllDay" class="rounded text-blue-600 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600" style="border-radius: 2px;">
                    <label for="eventAllDay" class="ml-2 text-sm text-gray-700 dark:text-gray-300">All Day Event</label>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Color</label>
                        <input type="color" wire:model="eventColor" class="w-full h-10 cursor-pointer" style="border-radius: 2px;">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Visibility</label>
                        <select wire:model="eventVisibility" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-sm dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent" style="border-radius: 2px;">
                            <option value="private">Private</option>
                            <option value="public">Public</option>
                            <option value="shared">Shared</option>
                        </select>
                    </div>
                </div>

                <!-- Reminder Section -->
                <div x-data="{ showReminderOptions: @entangle('enableReminder') }" class="bg-slate-50 dark:bg-slate-800/50 p-4" style="border-radius: 2px;">
                    <div class="flex items-center">
                        <input type="checkbox" wire:model.live="enableReminder" id="enableReminder" class="rounded text-blue-600 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600" style="border-radius: 2px;">
                        <label for="enableReminder" class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                </svg>
                                Set Reminder
                            </span>
                        </label>
                    </div>

                    <div x-show="showReminderOptions" x-collapse class="mt-4 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Remind me</label>
                            <select wire:model="reminderMinutesBefore" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-sm dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent" style="border-radius: 2px;">
                                @foreach($reminderTimeOptions as $minutes => $label)
                                <option value="{{ $minutes }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Notification Channels</label>
                            <div class="space-y-2">
                                <label class="flex items-center">
                                    <input type="checkbox" value="email" wire:model="reminderChannels" class="rounded text-blue-600 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600" style="border-radius: 2px;">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300 flex items-center">
                                        <svg class="w-4 h-4 mr-1.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                        </svg>
                                        Email
                                    </span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" value="database" wire:model="reminderChannels" class="rounded text-blue-600 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600" style="border-radius: 2px;">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300 flex items-center">
                                        <svg class="w-4 h-4 mr-1.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                        </svg>
                                        In-App Notification
                                    </span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" value="sms" wire:model="reminderChannels" class="rounded text-blue-600 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600" style="border-radius: 2px;">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300 flex items-center">
                                        <svg class="w-4 h-4 mr-1.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                        </svg>
                                        SMS (if configured)
                                    </span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <x-slot name="footer">
                <div class="flex justify-end space-x-3">
                    <button type="button" @click="$dispatch('close-modal', { name: 'create-event-modal' })" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors" style="border-radius: 2px;">
                        Cancel
                    </button>
                    <button type="button" wire:click="createEvent" class="px-4 py-2 text-sm font-medium text-white transition-colors" style="background: linear-gradient(135deg, #2563eb, #3b82f6); border-radius: 2px; box-shadow: 0 2px 8px rgba(37,99,235,0.3);" onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
                        Create Event
                    </button>
                </div>
            </x-slot>
        </form>
    </x-modal-component>

    <!-- Create Note Modal -->
    <x-modal-component name="create-note-modal" title="Create Note" size="xl" :show="$showCreateNoteModal">
        <form wire:submit.prevent="createNote" name="create-note-form">
            @if ($errors->any())
            <div class="mb-4 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800" style="border-radius: 2px;">
                <h3 class="text-sm font-medium text-red-800 dark:text-red-200 mb-2">Validation Errors:</h3>
                <ul class="text-sm text-red-700 dark:text-red-300 space-y-1">
                    @foreach ($errors->all() as $error)
                    <li>• {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            <div class="space-y-4">
                <x-form.input name="noteTitle" label="Title" wire:model="noteTitle" :required="true" />

                <x-form.markdown-editor name="noteContent" wire-model="noteContent" :value="$noteContent" label="Content" :height="200" :required="true" />

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Book</label>
                        <select wire:model="noteBookId" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-sm dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-green-500 focus:border-transparent" style="border-radius: 2px;">
                            <option value="">Select a book</option>
                            @foreach(\App\Models\Book::all() as $book)
                            <option value="{{ $book->id }}">{{ $book->title }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Subject</label>
                        <select wire:model="noteSubjectId" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-sm dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-green-500 focus:border-transparent" style="border-radius: 2px;">
                            <option value="">Select a subject</option>
                            @foreach(\App\Models\AcademicSubject::all() as $subject)
                            <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" wire:model="noteIsPublic" id="noteIsPublic" class="rounded text-green-600 focus:ring-green-500" style="border-radius: 2px;">
                    <label for="noteIsPublic" class="ml-2 text-sm text-gray-700 dark:text-gray-300">Make Public</label>
                </div>

                <!-- Calendar Integration Section -->
                <div x-data="{ showCalendarFields: false }" class="bg-slate-50 dark:bg-slate-800/50 p-4" style="border-radius: 2px;">
                    <div class="flex items-center">
                        <input type="checkbox" x-model="showCalendarFields" wire:click="toggleCalendarIntegration" id="add_to_calendar_note" class="rounded text-green-600 focus:ring-green-500" style="border-radius: 2px;">
                        <label for="add_to_calendar_note" class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                            Add to Calendar
                        </label>
                    </div>

                    <div x-show="showCalendarFields" x-collapse class="mt-4 space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <x-form.datetime-input name="noteEventStartDate" label="Start Date" wire:model="eventStartDate" />
                            <x-form.datetime-input name="noteEventEndDate" label="End Date" wire:model="eventEndDate" />
                        </div>

                        <div class="flex items-center">
                            <input type="checkbox" wire:model="eventAllDay" id="noteEventAllDay" class="rounded text-green-600 focus:ring-green-500 dark:bg-gray-700 dark:border-gray-600" style="border-radius: 2px;">
                            <label for="noteEventAllDay" class="ml-2 text-sm text-gray-700 dark:text-gray-300">All Day</label>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Color</label>
                                <input type="color" wire:model="eventColor" class="w-full h-10 cursor-pointer" style="border-radius: 2px;">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Visibility</label>
                                <select wire:model="eventVisibility" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-sm dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-green-500 focus:border-transparent" style="border-radius: 2px;">
                                    <option value="private">Private</option>
                                    <option value="public">Public</option>
                                    <option value="shared">Shared</option>
                                </select>
                            </div>
                        </div>

                        <!-- Reminder Section for Note Calendar Event -->
                        <div class="bg-white dark:bg-gray-700 p-3" style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06);">
                            <div class="flex items-center">
                                <input type="checkbox" wire:model.live="enableReminder" id="noteEnableReminder" class="rounded text-green-600 focus:ring-green-500 dark:bg-gray-700 dark:border-gray-600" style="border-radius: 2px;">
                                <label for="noteEnableReminder" class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                    <span class="flex items-center">
                                        <svg class="w-4 h-4 mr-1.5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                        </svg>
                                        Set Reminder
                                    </span>
                                </label>
                            </div>

                            @if($enableReminder)
                            <div class="mt-3 space-y-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Remind me</label>
                                    <select wire:model="reminderMinutesBefore" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-sm dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm" style="border-radius: 2px;">
                                        @foreach($reminderTimeOptions as $minutes => $label)
                                        <option value="{{ $minutes }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Notify via</label>
                                    <div class="flex flex-wrap gap-3">
                                        <label class="flex items-center">
                                            <input type="checkbox" value="email" wire:model="reminderChannels" class="rounded text-green-600 focus:ring-green-500 dark:bg-gray-700 dark:border-gray-600" style="border-radius: 2px;">
                                            <span class="ml-1.5 text-sm text-gray-700 dark:text-gray-300">Email</span>
                                        </label>
                                        <label class="flex items-center">
                                            <input type="checkbox" value="database" wire:model="reminderChannels" class="rounded text-green-600 focus:ring-green-500 dark:bg-gray-700 dark:border-gray-600" style="border-radius: 2px;">
                                            <span class="ml-1.5 text-sm text-gray-700 dark:text-gray-300">In-App</span>
                                        </label>
                                        <label class="flex items-center">
                                            <input type="checkbox" value="sms" wire:model="reminderChannels" class="rounded text-green-600 focus:ring-green-500 dark:bg-gray-700 dark:border-gray-600" style="border-radius: 2px;">
                                            <span class="ml-1.5 text-sm text-gray-700 dark:text-gray-300">SMS</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <x-slot name="footer">
                <div class="flex justify-end space-x-3">
                    <button type="button" @click="$dispatch('close-modal', { name: 'create-note-modal' })" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors" style="border-radius: 2px;">
                        Cancel
                    </button>
                    <button type="button" wire:click="createNote" class="px-4 py-2 text-sm font-medium text-white transition-colors" style="background: linear-gradient(135deg, #059669, #10b981); border-radius: 2px; box-shadow: 0 2px 8px rgba(5,150,105,0.3);" onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
                        Create Note
                    </button>
                </div>
            </x-slot>
        </form>
    </x-modal-component>

    <!-- Event View Modal -->
    <x-modal-component name="view-event-modal" :title="$selectedEvent ? ($selectedEvent->event_type_name === 'Note' ? 'Note Details' : 'Event Details') : 'Details'" size="xl" :show="$showViewModal">
        @if($selectedEvent)
        <div class="space-y-4">
            <div class="flex items-center space-x-3">
                <div class="w-4 h-4 rounded-full flex-shrink-0" style="background-color: {{ $selectedEvent->color ?: '#3b82f6' }}; border-radius: 2px;"></div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $selectedEvent->title }}</h3>
            </div>

            <div class="flex items-center space-x-2">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200" style="border-radius: 2px;">
                    {{ $selectedEvent->event_type_name }}
                </span>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $selectedEvent->visibility === 'public' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : ($selectedEvent->visibility === 'shared' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200') }}" style="border-radius: 2px;">
                    {{ ucfirst($selectedEvent->visibility) }}
                </span>
            </div>

            @if($selectedEvent->description)
            <div>
                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Description</label>
                <x-prose-content :content="$selectedEvent->description" class="text-gray-900 dark:text-white" />
            </div>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Start</label>
                    <p class="text-gray-900 dark:text-white">
                        @if($selectedEvent->all_day)
                        {{ $selectedEvent->start_date?->format('M d, Y') }}
                        @else
                        {{ $selectedEvent->start_date?->format('M d, Y g:i A') }}
                        @endif
                    </p>
                </div>
                @if($selectedEvent->end_date)
                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">End</label>
                    <p class="text-gray-900 dark:text-white">
                        @if($selectedEvent->all_day)
                        {{ $selectedEvent->end_date?->format('M d, Y') }}
                        @else
                        {{ $selectedEvent->end_date?->format('M d, Y g:i A') }}
                        @endif
                    </p>
                </div>
                @endif
            </div>

            @if($selectedEvent->all_day)
            <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                All Day Event
            </div>
            @endif
        </div>

        <x-slot name="footer">
            <div class="flex justify-between w-full">
                <button type="button" @click="$dispatch('close-modal', { name: 'view-event-modal' })" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors" style="border-radius: 2px;">
                    Close
                </button>
                @if($selectedEvent->canUserEdit(auth()->id()))
                <button type="button" @click="$dispatch('close-modal', { name: 'view-event-modal' }); $dispatch('open-modal', { name: 'edit-event-modal' })" class="px-4 py-2 text-sm font-medium text-white transition-colors" style="background: linear-gradient(135deg, #2563eb, #3b82f6); border-radius: 2px; box-shadow: 0 2px 8px rgba(37,99,235,0.3);" onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
                    <span class="flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                        </svg>
                        Edit {{ $selectedEvent->event_type_name === 'Note' ? 'Note' : 'Event' }}
                    </span>
                </button>
                @endif
            </div>
        </x-slot>
        @else
        <div class="flex flex-col items-center justify-center py-8" wire:loading.class-remove="hidden">
            <svg class="animate-spin h-8 w-8 text-blue-500 mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <p class="text-gray-500 dark:text-gray-400">Loading event details...</p>
        </div>
        <x-slot name="footer">
            <button type="button" @click="$dispatch('close-modal', { name: 'view-event-modal' })" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors" style="border-radius: 2px;">
                Close
            </button>
        </x-slot>
        @endif
    </x-modal-component>

    <!-- Event Edit Modal -->
    <x-modal-component name="edit-event-modal" :title="$selectedEvent ? ($selectedEvent->event_type_name === 'Note' ? 'Edit Note' : 'Edit Event') : 'Edit'" size="xl" :show="$showEditModal">
        @if($selectedEvent)
        <form wire:submit.prevent="updateEvent">
            <div class="space-y-4">
                <x-form.input name="editEventTitle" label="Title" wire:model="eventTitle" :required="true" />

                <x-form.markdown-editor name="editEventDescription" wire-model="eventDescription" :value="$eventDescription" label="Description" :height="200" />

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <x-form.datetime-input name="editEventStartDate" label="Start Date" wire:model="eventStartDate" :required="true" />
                    <x-form.datetime-input name="editEventEndDate" label="End Date" wire:model="eventEndDate" />
                </div>

                <div class="flex items-center">
                    <input type="checkbox" wire:model="eventAllDay" id="editEventAllDay" class="rounded text-blue-600 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600" style="border-radius: 2px;">
                    <label for="editEventAllDay" class="ml-2 text-sm text-gray-700 dark:text-gray-300">All Day Event</label>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Color</label>
                        <input type="color" wire:model="eventColor" class="w-full h-10 cursor-pointer" style="border-radius: 2px;">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Visibility</label>
                        <select wire:model="eventVisibility" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-sm dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent" style="border-radius: 2px;">
                            <option value="private">Private</option>
                            <option value="public">Public</option>
                            <option value="shared">Shared</option>
                        </select>
                    </div>
                </div>
            </div>

            <x-slot name="footer">
                <div class="flex flex-col-reverse sm:flex-row justify-between w-full gap-3">
                    <button type="button" wire:click="deleteEvent" wire:confirm="Are you sure you want to delete this event?" class="px-4 py-2 text-sm font-medium text-white transition-colors hover:opacity-90" style="background: linear-gradient(135deg, #dc2626, #ef4444); border-radius: 2px; box-shadow: 0 2px 8px rgba(220,38,38,0.3);">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Delete
                        </span>
                    </button>
                    <div class="flex space-x-3">
                        <button type="button" @click="$dispatch('close-modal', { name: 'edit-event-modal' })" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors" style="border-radius: 2px;">
                            Cancel
                        </button>
                        <button type="button" wire:click="updateEvent" class="px-4 py-2 text-sm font-medium text-white transition-colors" style="background: linear-gradient(135deg, #2563eb, #3b82f6); border-radius: 2px; box-shadow: 0 2px 8px rgba(37,99,235,0.3);" onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
                            Update {{ $selectedEvent->event_type_name === 'Note' ? 'Note' : 'Event' }}
                        </button>
                    </div>
                </div>
            </x-slot>
        </form>
        @endif
    </x-modal-component>
</div>