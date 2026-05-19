<div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6" style="font-family: 'system-ui', -apple-system, sans-serif;">
        
        {{-- ── PAGE HEADER ── --}}
        <div class="overflow-hidden" style="border-radius: 2px; background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); box-shadow: 0 4px 24px rgba(0,0,0,0.15);">
            <div class="h-1 w-full" style="background: linear-gradient(90deg, #7c3aed, #a78bfa, #c084fc);"></div>
            <div class="px-7 py-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 flex items-center justify-center" style="background: linear-gradient(135deg, #7c3aed, #a78bfa); border-radius: 2px;">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-white leading-snug" style="letter-spacing: -0.02em; font-family: 'Georgia', serif;">
                                Notifications
                            </h1>
                            <p class="text-sm text-slate-300 mt-1">
                                @if($this->counts['unread'] > 0)
                                    <span class="inline-flex items-center gap-2">
                                        <span class="w-2 h-2 bg-red-500 rounded-full animate-pulse"></span>
                                        {{ $this->counts['unread'] }} unread of {{ $this->counts['all'] }} total
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-2">
                                        <span class="w-2 h-2 bg-emerald-500 rounded-full"></span>
                                        All caught up!
                                    </span>
                                @endif
                            </p>
                        </div>
                    </div>
                    
                    @if($this->counts['unread'] > 0)
                    <button wire:click="markAllAsRead" class="px-4 py-2 text-sm font-semibold text-white transition-colors" style="background: linear-gradient(135deg, #7c3aed, #a78bfa); border-radius: 2px; box-shadow: 0 2px 8px rgba(124,58,237,0.3);" onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Mark All Read
                        </span>
                    </button>
                    @endif
                </div>
            </div>
        </div>

        {{-- ── METRICS STRIP ── --}}
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
            {{-- Total --}}
            <div class="bg-white dark:bg-slate-900 px-5 py-5 flex items-center gap-4" style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                <div class="w-11 h-11 flex-shrink-0 flex items-center justify-center" style="background: linear-gradient(135deg, #7c3aed, #a78bfa); border-radius: 2px;">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="font-size: 10px; letter-spacing: 0.1em;">Total</p>
                    <p class="text-3xl font-bold text-slate-900 dark:text-white mt-0.5" style="letter-spacing: -0.04em;">{{ $this->counts['all'] }}</p>
                </div>
            </div>

            {{-- Unread --}}
            <div class="bg-white dark:bg-slate-900 px-5 py-5 flex items-center gap-4" style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                <div class="w-11 h-11 flex-shrink-0 flex items-center justify-center" style="background: linear-gradient(135deg, #dc2626, #ef4444); border-radius: 2px;">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="font-size: 10px; letter-spacing: 0.1em;">Unread</p>
                    <p class="text-3xl font-bold text-slate-900 dark:text-white mt-0.5" style="letter-spacing: -0.04em;">{{ $this->counts['unread'] }}</p>
                </div>
            </div>

            @if(auth()->user()->student)
            {{-- Assignments --}}
            <div class="bg-white dark:bg-slate-900 px-5 py-5 flex items-center gap-4" style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                <div class="w-11 h-11 flex-shrink-0 flex items-center justify-center" style="background: linear-gradient(135deg, #2563eb, #3b82f6); border-radius: 2px;">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="font-size: 10px; letter-spacing: 0.1em;">Assignments</p>
                    <p class="text-3xl font-bold text-slate-900 dark:text-white mt-0.5" style="letter-spacing: -0.04em;">{{ $this->counts['assignment'] }}</p>
                </div>
            </div>
            @endif

            @if(auth()->user()->teacher)
            {{-- Submissions --}}
            <div class="bg-white dark:bg-slate-900 px-5 py-5 flex items-center gap-4" style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                <div class="w-11 h-11 flex-shrink-0 flex items-center justify-center" style="background: linear-gradient(135deg, #7c3aed, #a78bfa); border-radius: 2px;">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="font-size: 10px; letter-spacing: 0.1em;">Submissions</p>
                    <p class="text-3xl font-bold text-slate-900 dark:text-white mt-0.5" style="letter-spacing: -0.04em;">{{ $this->counts['submission'] }}</p>
                </div>
            </div>
            @endif

            {{-- Assessments --}}
            <div class="bg-white dark:bg-slate-900 px-5 py-5 flex items-center gap-4" style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                <div class="w-11 h-11 flex-shrink-0 flex items-center justify-center" style="background: linear-gradient(135deg, #059669, #10b981); border-radius: 2px;">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="font-size: 10px; letter-spacing: 0.1em;">Assessments</p>
                    <p class="text-3xl font-bold text-slate-900 dark:text-white mt-0.5" style="letter-spacing: -0.04em;">{{ $this->counts['assessment'] }}</p>
                </div>
            </div>
        </div>

        {{-- ── SEARCH & FILTERS ── --}}
        <div class="bg-white dark:bg-slate-900 overflow-hidden" style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
            <div class="p-5 space-y-4">
                {{-- Search Bar --}}
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input wire:model.live.debounce.300ms="search" type="text" class="block w-full pl-12 pr-4 py-3 border border-slate-200 dark:border-slate-700 rounded-sm bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all" style="border-radius: 2px;" placeholder="Search notifications...">
                </div>

                {{-- Filter Tabs - Read Status --}}
                <div class="flex flex-wrap gap-2">
                    <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider self-center mr-2" style="letter-spacing: 0.08em;">Status:</span>
                    <button wire:click="setFilter('all')" class="px-4 py-2 text-sm font-medium transition-all {{ $filter === 'all' ? 'text-white' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}" style="border-radius: 2px; {{ $filter === 'all' ? 'background: linear-gradient(135deg, #7c3aed, #a78bfa); box-shadow: 0 2px 8px rgba(124,58,237,0.3);' : '' }}">
                        All <span class="ml-1 px-2 py-0.5 text-xs rounded-sm {{ $filter === 'all' ? 'bg-white/20' : 'bg-slate-200 dark:bg-slate-700' }}">{{ $this->counts['all'] }}</span>
                    </button>
                    <button wire:click="setFilter('unread')" class="px-4 py-2 text-sm font-medium transition-all {{ $filter === 'unread' ? 'text-white' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}" style="border-radius: 2px; {{ $filter === 'unread' ? 'background: linear-gradient(135deg, #dc2626, #ef4444); box-shadow: 0 2px 8px rgba(220,38,38,0.3);' : '' }}">
                        Unread <span class="ml-1 px-2 py-0.5 text-xs rounded-sm {{ $filter === 'unread' ? 'bg-white/20' : 'bg-slate-200 dark:bg-slate-700' }}">{{ $this->counts['unread'] }}</span>
                    </button>
                    <button wire:click="setFilter('read')" class="px-4 py-2 text-sm font-medium transition-all {{ $filter === 'read' ? 'text-white' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}" style="border-radius: 2px; {{ $filter === 'read' ? 'background: linear-gradient(135deg, #059669, #10b981); box-shadow: 0 2px 8px rgba(5,150,105,0.3);' : '' }}">
                        Read <span class="ml-1 px-2 py-0.5 text-xs rounded-sm {{ $filter === 'read' ? 'bg-white/20' : 'bg-slate-200 dark:bg-slate-700' }}">{{ $this->counts['read'] }}</span>
                    </button>
                </div>

                {{-- Filter Tabs - Type --}}
                <div class="flex flex-wrap gap-2">
                    <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider self-center mr-2" style="letter-spacing: 0.08em;">Type:</span>
                    <button wire:click="setType('all')" class="px-4 py-2 text-sm font-medium transition-all {{ $type === 'all' ? 'text-white' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}" style="border-radius: 2px; {{ $type === 'all' ? 'background: linear-gradient(135deg, #1e293b, #334155); box-shadow: 0 2px 8px rgba(30,41,59,0.3);' : '' }}">
                        All Types
                    </button>
                    
                    @if(auth()->user()->student)
                    <button wire:click="setType('assignment')" class="px-4 py-2 text-sm font-medium transition-all {{ $type === 'assignment' ? 'text-white' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}" style="border-radius: 2px; {{ $type === 'assignment' ? 'background: linear-gradient(135deg, #2563eb, #3b82f6); box-shadow: 0 2px 8px rgba(37,99,235,0.3);' : '' }}">
                        📚 Assignments <span class="ml-1 px-2 py-0.5 text-xs rounded-sm {{ $type === 'assignment' ? 'bg-white/20' : 'bg-slate-200 dark:bg-slate-700' }}">{{ $this->counts['assignment'] }}</span>
                    </button>
                    @endif

                    @if(auth()->user()->teacher)
                    <button wire:click="setType('submission')" class="px-4 py-2 text-sm font-medium transition-all {{ $type === 'submission' ? 'text-white' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}" style="border-radius: 2px; {{ $type === 'submission' ? 'background: linear-gradient(135deg, #7c3aed, #a78bfa); box-shadow: 0 2px 8px rgba(124,58,237,0.3);' : '' }}">
                        📝 Submissions <span class="ml-1 px-2 py-0.5 text-xs rounded-sm {{ $type === 'submission' ? 'bg-white/20' : 'bg-slate-200 dark:bg-slate-700' }}">{{ $this->counts['submission'] }}</span>
                    </button>
                    @endif

                    <button wire:click="setType('assessment')" class="px-4 py-2 text-sm font-medium transition-all {{ $type === 'assessment' ? 'text-white' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}" style="border-radius: 2px; {{ $type === 'assessment' ? 'background: linear-gradient(135deg, #059669, #10b981); box-shadow: 0 2px 8px rgba(5,150,105,0.3);' : '' }}">
                        🎯 Assessments <span class="ml-1 px-2 py-0.5 text-xs rounded-sm {{ $type === 'assessment' ? 'bg-white/20' : 'bg-slate-200 dark:bg-slate-700' }}">{{ $this->counts['assessment'] }}</span>
                    </button>
                    
                    <button wire:click="setType('other')" class="px-4 py-2 text-sm font-medium transition-all {{ $type === 'other' ? 'text-white' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}" style="border-radius: 2px; {{ $type === 'other' ? 'background: linear-gradient(135deg, #64748b, #94a3b8); box-shadow: 0 2px 8px rgba(100,116,139,0.3);' : '' }}">
                        🔔 Other <span class="ml-1 px-2 py-0.5 text-xs rounded-sm {{ $type === 'other' ? 'bg-white/20' : 'bg-slate-200 dark:bg-slate-700' }}">{{ $this->counts['other'] }}</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- ── NOTIFICATIONS LIST ── --}}
        <div class="bg-white dark:bg-slate-900 overflow-hidden" style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                <div class="w-1 h-5" style="background: linear-gradient(180deg, #7c3aed, #a78bfa); border-radius: 1px;"></div>
                <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider" style="letter-spacing: 0.1em;">
                    @if($search)
                        Search Results
                    @elseif($filter === 'unread')
                        Unread Notifications
                    @else
                        All Notifications
                    @endif
                </h2>
            </div>
            
            <div class="divide-y divide-slate-100 dark:divide-slate-800">
                @forelse($this->notifications as $notification)
                <div wire:key="notification-{{ $notification['id'] }}" class="group hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors {{ !$notification['read_at'] ? 'bg-violet-50/30 dark:bg-violet-900/10' : '' }}">
                    <div class="px-6 py-5">
                        <div class="flex items-start gap-4">
                            {{-- Icon --}}
                            <div class="flex-shrink-0">
                                @if($notification['category'] === 'assignment')
                                <div class="w-11 h-11 flex items-center justify-center" style="background: linear-gradient(135deg, #2563eb, #3b82f6); border-radius: 2px;">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                                @elseif($notification['category'] === 'submission')
                                <div class="w-11 h-11 flex items-center justify-center" style="background: linear-gradient(135deg, #7c3aed, #a78bfa); border-radius: 2px;">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                @elseif($notification['category'] === 'assessment')
                                <div class="w-11 h-11 flex items-center justify-center" style="background: linear-gradient(135deg, #059669, #10b981); border-radius: 2px;">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                    </svg>
                                </div>
                                @else
                                <div class="w-11 h-11 flex items-center justify-center" style="background: linear-gradient(135deg, #64748b, #94a3b8); border-radius: 2px;">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                @endif
                            </div>

                            {{-- Content --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-4 mb-2">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-1">
                                            <h3 class="text-base font-semibold text-slate-900 dark:text-slate-100">
                                                {{ $notification['title'] }}
                                            </h3>
                                            @if(!$notification['read_at'])
                                            <span class="inline-flex items-center px-2 py-0.5 text-xs font-semibold text-violet-700 dark:text-violet-300" style="background: rgba(124,58,237,0.1); border-radius: 2px;">
                                                New
                                            </span>
                                            @endif
                                        </div>
                                        <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                                            {{ $notification['message'] }}
                                        </p>
                                    </div>

                                    {{-- Actions --}}
                                    <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                        @if(!$notification['read_at'])
                                        <button wire:click="markAsRead('{{ $notification['type'] }}', '{{ $notification['original_id'] }}')" class="p-2 text-violet-600 dark:text-violet-400 hover:text-violet-800 dark:hover:text-violet-300 hover:bg-violet-50 dark:hover:bg-violet-900/20 transition-colors" style="border-radius: 2px;" title="Mark as read">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </button>
                                        @endif
                                        <button wire:click="deleteNotification('{{ $notification['type'] }}', '{{ $notification['original_id'] }}')" wire:confirm="Are you sure you want to delete this notification?" class="p-2 text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors" style="border-radius: 2px;" title="Delete">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-slate-500 dark:text-slate-400">
                                    <div class="flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span>{{ $notification['created_at']->diffForHumans() }}</span>
                                    </div>
                                    @if(isset($notification['data']['subject']))
                                    <span class="text-slate-300 dark:text-slate-600">•</span>
                                    <span>{{ $notification['data']['subject'] }}</span>
                                    @endif
                                    @if(isset($notification['data']['teacher']))
                                    <span class="text-slate-300 dark:text-slate-600">•</span>
                                    <span>{{ $notification['data']['teacher'] }}</span>
                                    @endif
                                    @if(isset($notification['data']['student_name']))
                                    <span class="text-slate-300 dark:text-slate-600">•</span>
                                    <span>{{ $notification['data']['student_name'] }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                {{-- Empty State --}}
                <div class="px-6 py-16 text-center">
                    <div class="w-20 h-20 mx-auto mb-6 flex items-center justify-center" style="background: linear-gradient(135deg, #f1f5f9, #e2e8f0); border-radius: 2px;">
                        <svg class="w-10 h-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2">
                        @if($search)
                            No matching notifications
                        @elseif($filter === 'unread')
                            You're all caught up!
                        @else
                            No notifications yet
                        @endif
                    </h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 max-w-md mx-auto">
                        @if($search)
                            No notifications match "{{ $search }}". Try a different search term.
                        @elseif($filter === 'unread')
                            Great job! You've read all your notifications.
                        @else
                            When you receive notifications, they'll appear here.
                        @endif
                    </p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Loading State --}}
    <div wire:loading.delay class="fixed inset-0 bg-white/80 dark:bg-slate-900/80 backdrop-blur-sm flex items-center justify-center z-50">
        <div class="bg-white dark:bg-slate-800 px-6 py-4 shadow-xl flex items-center gap-4" style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06);">
            <div class="w-6 h-6 border-2 border-violet-200 dark:border-violet-800 border-t-violet-600 rounded-full animate-spin"></div>
            <div>
                <p class="text-sm font-semibold text-slate-900 dark:text-white">Loading...</p>
                <p class="text-xs text-slate-500 dark:text-slate-400">Please wait</p>
            </div>
        </div>
    </div>
</div>