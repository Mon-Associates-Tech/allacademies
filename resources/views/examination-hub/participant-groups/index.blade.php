<x-layouts.app>
    <x-examination-hub.navigation active='participant-groups' />
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-7"
         style="font-family: 'system-ui', -apple-system, sans-serif;">

        {{-- ── PAGE HEADER ── --}}
        <div class="overflow-hidden"
             style="border-radius: 2px; background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); box-shadow: 0 4px 24px rgba(0,0,0,0.15);">
            <div class="h-1 w-full" style="background: linear-gradient(90deg, #4c1d95, #7c3aed, #a78bfa);"></div>
            <div class="px-7 py-6 flex items-center justify-between">
                <h1 class="text-2xl font-bold text-white leading-snug"
                    style="letter-spacing: -0.02em; font-family: 'Georgia', serif;">
                    Participant Groups
                </h1>
                <div class="flex gap-3">
                    <a href="{{ route('examination-hub.participant-groups.import') }}"
                       class="inline-flex items-center gap-2 px-4 py-2 text-xs font-bold text-white uppercase tracking-wider transition-opacity hover:opacity-90"
                       style="background: linear-gradient(135deg, #1d4ed8, #3b82f6); border-radius: 2px; letter-spacing: 0.08em;">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                        Import CSV
                    </a>
                    <a href="{{ route('examination-hub.participant-groups.create') }}"
                       class="inline-flex items-center gap-2 px-4 py-2 text-xs font-bold text-white uppercase tracking-wider transition-opacity hover:opacity-90"
                       style="background: linear-gradient(135deg, #065f46, #059669); border-radius: 2px; letter-spacing: 0.08em;">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Create Group
                    </a>
                </div>
            </div>
        </div>

        {{-- ── FLASH MESSAGES ── --}}
        @if(session('success'))
            <div class="overflow-hidden" style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                <div class="h-1 w-full" style="background: linear-gradient(90deg, #065f46, #059669, #10b981);"></div>
                <div class="bg-white dark:bg-slate-900 px-5 py-4 text-sm text-emerald-700 dark:text-emerald-400 font-medium flex items-center gap-3">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ session('success') }}
                </div>
            </div>
        @endif

        @if(session('warning'))
            <div class="overflow-hidden" style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                <div class="h-1 w-full" style="background: linear-gradient(90deg, #b45309, #d97706, #fbbf24);"></div>
                <div class="bg-white dark:bg-slate-900 px-5 py-4 text-sm text-amber-700 dark:text-amber-400 font-medium flex items-start gap-3">
                    <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <div>
                        {{ session('warning') }}
                        @if(session('import_errors'))
                            <ul class="mt-2 list-disc list-inside text-xs text-slate-500 dark:text-slate-400 font-normal">
                                @foreach(session('import_errors') as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        @if($groups->isEmpty())
            {{-- ── EMPTY STATE ── --}}
            <div class="bg-white dark:bg-slate-900 overflow-hidden" style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                    <div class="w-1 h-5" style="background: linear-gradient(180deg, #7c3aed, #a78bfa); border-radius: 1px;"></div>
                    <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider" style="letter-spacing: 0.1em;">Participant Groups</h2>
                </div>
                <div class="p-12 text-center">
                    <div class="w-16 h-16 mx-auto flex items-center justify-center mb-4" style="background: linear-gradient(135deg, #7c3aed, #a78bfa); border-radius: 2px;">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2" style="letter-spacing: -0.02em;">No participant groups yet</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mb-6">Create your first group or import participants from a CSV file</p>
                    <div class="flex gap-3 justify-center">
                        <a href="{{ route('examination-hub.participant-groups.create') }}"
                           class="inline-flex items-center gap-2 px-5 py-2.5 text-xs font-bold text-white uppercase tracking-wider transition-opacity hover:opacity-90"
                           style="background: linear-gradient(135deg, #065f46, #059669); border-radius: 2px; letter-spacing: 0.08em;">
                            Create Group
                        </a>
                        <a href="{{ route('examination-hub.participant-groups.import') }}"
                           class="inline-flex items-center gap-2 px-5 py-2.5 text-xs font-bold text-white uppercase tracking-wider transition-opacity hover:opacity-90"
                           style="background: linear-gradient(135deg, #1d4ed8, #3b82f6); border-radius: 2px; letter-spacing: 0.08em;">
                            Import CSV
                        </a>
                    </div>
                </div>
            </div>
        @else
            {{-- ── GROUPS LIST ── --}}
            <div class="bg-white dark:bg-slate-900 overflow-hidden" style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-1 h-5" style="background: linear-gradient(180deg, #7c3aed, #a78bfa); border-radius: 1px;"></div>
                        <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider" style="letter-spacing: 0.1em;">All Groups</h2>
                    </div>
                    <span class="text-xs font-mono font-medium px-2.5 py-1 border border-slate-200 dark:border-slate-600 bg-gradient-to-br from-slate-50 to-slate-100 dark:from-slate-700 dark:to-slate-800 text-slate-700 dark:text-slate-300" style="border-radius: 2px;">
                        {{ $groups->count() }} Total
                    </span>
                </div>

                <div class="p-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                        @foreach($groups as $group)
                            <div class="bg-slate-50 dark:bg-slate-800/50 overflow-hidden transition-all hover:shadow-md" style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.04);">
                                <div class="p-5">
                                    <div class="flex items-start justify-between mb-3">
                                        <div class="flex-1 min-w-0">
                                            <h3 class="text-base font-bold text-slate-900 dark:text-white truncate" style="letter-spacing: -0.01em;">
                                                {{ $group->name }}
                                            </h3>
                                            @if($group->description)
                                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 line-clamp-2">{{ $group->description }}</p>
                                            @endif
                                        </div>
                                        <div class="w-9 h-9 flex-shrink-0 flex items-center justify-center ml-3" style="background: linear-gradient(135deg, #7c3aed, #a78bfa); border-radius: 2px;">
                                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                            </svg>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-4 mb-4 text-xs">
                                        <div class="flex items-center gap-1.5 text-slate-600 dark:text-slate-300">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                            </svg>
                                            <span class="font-semibold">{{ $group->members_count }}</span>
                                            <span class="text-slate-500 dark:text-slate-400">{{ Str::plural('participant', $group->members_count) }}</span>
                                        </div>
                                        <div class="text-slate-400 dark:text-slate-500">
                                            Created {{ $group->created_at->diffForHumans() }}
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-2 pt-3 border-t border-slate-200 dark:border-slate-700/50">
                                        <a href="{{ route('examination-hub.participant-groups.show', $group) }}"
                                           class="flex-1 text-center py-1.5 text-xs font-semibold text-white transition-opacity hover:opacity-90"
                                           style="background: linear-gradient(135deg, #1d4ed8, #3b82f6); border-radius: 2px;">
                                            View
                                        </a>
                                            <a href="{{ route('examination-hub.participant-groups.edit', $group) }}"
                                               onclick="window.location.href='{{ route('examination-hub.participant-groups.edit', $group) }}'"
                                               role="link"
                                               class="flex-1 text-center py-1.5 text-xs font-semibold text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600 transition-colors hover:bg-slate-50 dark:hover:bg-slate-700"
                                               style="border-radius: 2px;">
                                            Edit
                                        </a>
                                        <form action="{{ route('examination-hub.participant-groups.destroy', $group) }}"
                                              method="POST"
                                              onsubmit="return confirm('Are you sure? This will delete the group and all its members.')"
                                              class="flex-1">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="w-full py-1.5 text-xs font-semibold text-red-600 dark:text-red-400 bg-white dark:bg-slate-800 border border-red-200 dark:border-red-800/50 transition-colors hover:bg-red-50 dark:hover:bg-red-900/20"
                                                    style="border-radius: 2px;">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                @if($groups->hasPages())
                    <div class="px-5 py-4 border-t border-slate-100 dark:border-slate-800">
                        {{ $groups->links() }}
                    </div>
                @endif
            </div>
        @endif

    </div>
</x-layouts.app>