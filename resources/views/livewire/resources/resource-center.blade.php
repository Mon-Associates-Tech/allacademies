<div style="font-family: 'system-ui', -apple-system, sans-serif;">

    {{-- ══════════════════════════════════════════════════════════
         PAGE HEADER
    ══════════════════════════════════════════════════════════ --}}
    <div class="overflow-hidden mb-7"
         style="border-radius: 2px; background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); box-shadow: 0 4px 24px rgba(0,0,0,0.15);">
        <div class="h-1 w-full" style="background: linear-gradient(90deg, #b45309, #d97706, #fbbf24);"></div>
        <div class="px-7 py-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <p class="text-xs font-medium tracking-widest text-amber-400 uppercase mb-1" style="letter-spacing: 0.15em;">Educational Resources</p>
                <h1 class="text-2xl font-bold text-white leading-snug" style="letter-spacing: -0.02em; font-family: 'Georgia', serif;">
                    Visual Activities
                </h1>
                <p class="text-sm text-slate-400 mt-1">Browse and download audio &amp; video resources</p>
            </div>
            @if($this->canUpload())
                <a href="{{ route('educational-resources.create') }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white flex-shrink-0 transition-all"
                   style="border-radius: 2px; background: linear-gradient(135deg, #b45309, #d97706); box-shadow: 0 2px 10px rgba(180,83,9,0.3);">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Upload Media
                </a>
            @endif
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════
         FILTER PANEL
    ══════════════════════════════════════════════════════════ --}}
    <div class="bg-white dark:bg-slate-900 mb-6 overflow-hidden"
         style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">

        <div class="px-5 py-3.5 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
            <div class="w-1 h-5" style="background: linear-gradient(180deg, #b45309, #fbbf24); border-radius: 1px;"></div>
            <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider" style="letter-spacing: 0.1em;">Search &amp; Filter</h2>
        </div>

        <div class="p-5 space-y-5">

            {{-- Primary filters --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">

                {{-- Search --}}
                <div class="lg:col-span-2">
                    <label for="search" class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5" style="font-size: 10px; letter-spacing: 0.1em;">Search</label>
                    <div class="relative">
                        <input
                            type="text"
                            id="search"
                            wire:model.live.debounce.300ms="search"
                            placeholder="Search by title or description…"
                            class="w-full pl-9 pr-4 py-2.5 text-sm bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all"
                            style="border-radius: 2px;"
                        >
                        <svg class="absolute left-3 top-3 h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                </div>

                {{-- Tag search --}}
                <div>
                    <label for="tagSearch" class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5" style="font-size: 10px; letter-spacing: 0.1em;">Search by Tags</label>
                    <input
                        type="text"
                        id="tagSearch"
                        wire:model.live.debounce.300ms="tagSearch"
                        placeholder="Tags, comma separated…"
                        class="w-full px-3 py-2.5 text-sm bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all"
                        style="border-radius: 2px;"
                    >
                </div>

                {{-- Format --}}
                <div>
                    <label for="format" class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5" style="font-size: 10px; letter-spacing: 0.1em;">Format</label>
                    <select
                        id="format"
                        wire:model.live="format"
                        class="w-full px-3 py-2.5 text-sm bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all"
                        style="border-radius: 2px;"
                    >
                        <option value="">All Formats</option>
                        @foreach($formats as $fmt)
                            <option value="{{ $fmt }}">{{ ucfirst($fmt) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Collapsible academic hierarchy --}}
            <div x-data="{ expanded: false }" class="border border-slate-100 dark:border-slate-800" style="border-radius: 2px;">
                <button
                    type="button"
                    x-on:click="expanded = !expanded"
                    class="w-full flex items-center justify-between px-4 py-3 text-sm font-medium text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors"
                    style="border-radius: 2px;"
                >
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                        </svg>
                        <span class="text-xs font-bold uppercase tracking-wider" style="letter-spacing: 0.1em;">Academic Filters</span>
                        @if($academicGroupId || $academicLevelId || $academicSubjectId || $topicId || $subtopicId)
                            <span class="inline-flex items-center px-2 py-0.5 text-xs font-semibold text-amber-700 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800"
                                  style="border-radius: 2px;">
                                Active
                            </span>
                        @endif
                    </div>
                    <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': expanded }"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div x-show="expanded" x-collapse class="border-t border-slate-100 dark:border-slate-800 px-4 pb-4 pt-4 space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        {{-- Academic Group --}}
                        <div>
                            <label for="academicGroupId" class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5" style="font-size: 10px; letter-spacing: 0.1em;">Academic Group</label>
                            <select id="academicGroupId" wire:model.live="academicGroupId"
                                    class="w-full px-3 py-2.5 text-sm bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all disabled:opacity-50"
                                    style="border-radius: 2px;">
                                <option value="">All Groups</option>
                                @foreach($academicGroups as $group)
                                    <option value="{{ $group->id }}">{{ $group->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Academic Level --}}
                        <div>
                            <label for="academicLevelId" class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5" style="font-size: 10px; letter-spacing: 0.1em;">Academic Level</label>
                            <select id="academicLevelId" wire:model.live="academicLevelId"
                                    class="w-full px-3 py-2.5 text-sm bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all disabled:opacity-50"
                                    style="border-radius: 2px;"
                                    @if(!$academicGroupId) disabled @endif>
                                <option value="">All Levels</option>
                                @foreach($academicLevels as $level)
                                    <option value="{{ $level->id }}">{{ $level->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Subject --}}
                        <div>
                            <label for="academicSubjectId" class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5" style="font-size: 10px; letter-spacing: 0.1em;">Subject</label>
                            <select id="academicSubjectId" wire:model.live="academicSubjectId"
                                    class="w-full px-3 py-2.5 text-sm bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all disabled:opacity-50"
                                    style="border-radius: 2px;"
                                    @if(!$academicLevelId) disabled @endif>
                                <option value="">All Subjects</option>
                                @foreach($academicSubjects as $subject)
                                    <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    @if($academicSubjectId)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="topicId" class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5" style="font-size: 10px; letter-spacing: 0.1em;">Topic</label>
                                <select id="topicId" wire:model.live="topicId"
                                        class="w-full px-3 py-2.5 text-sm bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all"
                                        style="border-radius: 2px;">
                                    <option value="">All Topics</option>
                                    @foreach($topics as $topic)
                                        <option value="{{ $topic->id }}">{{ $topic->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            @if($topicId && $subtopics->count() > 0)
                                <div>
                                    <label for="subtopicId" class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5" style="font-size: 10px; letter-spacing: 0.1em;">Subtopic</label>
                                    <select id="subtopicId" wire:model.live="subtopicId"
                                            class="w-full px-3 py-2.5 text-sm bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all"
                                            style="border-radius: 2px;">
                                        <option value="">All Subtopics</option>
                                        @foreach($subtopics as $subtopic)
                                            <option value="{{ $subtopic->id }}">{{ $subtopic->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            {{-- Toolbar: view mode + sort + clear --}}
            <div class="flex flex-wrap items-center justify-between gap-3 pt-1">
                <div class="flex items-center gap-4">

                    {{-- View mode toggle --}}
                    <div class="flex items-center border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 p-0.5" style="border-radius: 2px;">
                        <button wire:click="setViewMode('grid')"
                                title="Grid View"
                                class="p-2 transition-all"
                                style="border-radius: 2px; {{ $viewMode === 'grid' ? 'background: linear-gradient(135deg,#b45309,#d97706); box-shadow: 0 1px 4px rgba(180,83,9,0.3);' : '' }}">
                            <svg class="w-4 h-4 {{ $viewMode === 'grid' ? 'text-white' : 'text-slate-500 dark:text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                            </svg>
                        </button>
                        <button wire:click="setViewMode('list')"
                                title="List View"
                                class="p-2 transition-all"
                                style="border-radius: 2px; {{ $viewMode === 'list' ? 'background: linear-gradient(135deg,#b45309,#d97706); box-shadow: 0 1px 4px rgba(180,83,9,0.3);' : '' }}">
                            <svg class="w-4 h-4 {{ $viewMode === 'list' ? 'text-white' : 'text-slate-500 dark:text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                            </svg>
                        </button>
                    </div>

                    {{-- Sort buttons --}}
                    <div class="flex items-center gap-1">
                        <span class="text-xs text-slate-400 dark:text-slate-500 mr-1 uppercase tracking-wider" style="font-size: 10px;">Sort:</span>
                        @foreach([
                            ['key' => 'created_at', 'label' => 'Date'],
                            ['key' => 'title',      'label' => 'Title'],
                            ['key' => 'view_count', 'label' => 'Views'],
                        ] as $s)
                            <button wire:click="setSort('{{ $s['key'] }}')"
                                    class="inline-flex items-center gap-0.5 px-2.5 py-1 text-xs font-medium transition-all"
                                    style="border-radius: 2px; {{ $sortBy === $s['key'] ? 'background:linear-gradient(135deg,#0f172a,#1e293b);color:#fff;' : 'color:#64748b;background:transparent;' }}">
                                {{ $s['label'] }}
                                @if($sortBy === $s['key'])
                                    <svg class="w-3 h-3 {{ $sortDirection === 'desc' ? '' : 'rotate-180' }} transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                @endif
                            </button>
                        @endforeach
                    </div>
                </div>

                <button wire:click="clearFilters"
                        class="text-xs font-medium text-slate-500 dark:text-slate-400 hover:text-amber-700 dark:hover:text-amber-400 transition-colors">
                    Clear Filters
                </button>
            </div>
        </div>
    </div>

    {{-- ── Results count ── --}}
    <div class="mb-4 flex items-center gap-2">
        <div class="h-3 w-0.5 bg-amber-500" style="border-radius: 1px;"></div>
        <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">
            Showing {{ $resources->firstItem() ?? 0 }}–{{ $resources->lastItem() ?? 0 }}
            <span class="text-slate-400 dark:text-slate-500">of</span>
            {{ $resources->total() }} resources
        </p>
    </div>

    {{-- ══════════════════════════════════════════════════════════
         RESOURCE LISTING
    ══════════════════════════════════════════════════════════ --}}
    @php
        $formatConfig = [
            'video' => ['color' => '#dc2626', 'bg' => '#fef2f2', 'border' => '#fecaca', 'text' => '#991b1b',
                        'icon'  => 'M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
            'pdf'   => ['color' => '#c2410c', 'bg' => '#fff7ed', 'border' => '#fed7aa', 'text' => '#9a3412',
                        'icon'  => 'M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z'],
            'image' => ['color' => '#059669', 'bg' => '#ecfdf5', 'border' => '#a7f3d0', 'text' => '#065f46',
                        'icon'  => 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z'],
        ];
        $defaultFmt = ['color' => '#2563eb', 'bg' => '#eff6ff', 'border' => '#bfdbfe', 'text' => '#1d4ed8',
                       'icon'  => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'];
    @endphp

    @if($resources->count() > 0)

        {{-- ── GRID VIEW ── --}}
        @if($viewMode === 'grid')
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
                @foreach($resources as $resource)
                    @php $fmt = $formatConfig[$resource->format] ?? $defaultFmt; @endphp
                    <a href="{{ route('educational-resources.show', $resource) }}"
                       class="group bg-white dark:bg-slate-900 overflow-hidden flex flex-col transition-all duration-200 hover:-translate-y-0.5"
                       style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.07); box-shadow: 0 1px 6px rgba(0,0,0,0.05);"
                       onmouseover="this.style.boxShadow='0 6px 20px rgba(0,0,0,0.1)'"
                       onmouseout="this.style.boxShadow='0 1px 6px rgba(0,0,0,0.05)'">

                        {{-- Preview area --}}
                        <div class="relative h-36 flex items-center justify-center"
                             style="background: linear-gradient(135deg, {{ $fmt['bg'] }}, #fff);">
                            <svg class="w-14 h-14" style="color: {{ $fmt['color'] }}; opacity: 0.7;"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                @foreach(explode(' M', $fmt['icon']) as $i => $path)
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                          d="{{ $i === 0 ? $path : 'M' . $path }}"/>
                                @endforeach
                            </svg>

                            {{-- Format badge --}}
                            <span class="absolute top-2.5 right-2.5 px-2 py-0.5 text-xs font-bold uppercase tracking-wide border"
                                  style="border-radius: 2px; letter-spacing: 0.08em; color: {{ $fmt['text'] }}; background: {{ $fmt['bg'] }}; border-color: {{ $fmt['border'] }};">
                                {{ ucfirst($resource->format) }}
                            </span>

                            {{-- Hover overlay --}}
                            <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-200"
                                 style="background: rgba(15,23,42,0.55);">
                                <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-white px-3 py-1.5"
                                      style="background: linear-gradient(135deg,#b45309,#d97706); border-radius: 2px;">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    View Resource
                                </span>
                            </div>
                        </div>

                        {{-- Card body --}}
                        <div class="flex flex-col flex-1 p-4">
                            <h3 class="font-semibold text-slate-900 dark:text-white text-sm leading-snug line-clamp-2 mb-1.5 group-hover:text-amber-700 dark:group-hover:text-amber-400 transition-colors">
                                {{ $resource->title }}
                            </h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mb-3">
                                {{ $resource->academicSubject->name ?? 'N/A' }}
                            </p>

                            @if($resource->tags && count($resource->tags) > 0)
                                <div class="flex flex-wrap gap-1 mb-3">
                                    @foreach(array_slice($resource->tags, 0, 3) as $tag)
                                        <span class="px-1.5 py-0.5 text-xs bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700"
                                              style="border-radius: 2px;">{{ $tag }}</span>
                                    @endforeach
                                    @if(count($resource->tags) > 3)
                                        <span class="px-1.5 py-0.5 text-xs bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-500 border border-slate-200 dark:border-slate-700"
                                              style="border-radius: 2px;">+{{ count($resource->tags) - 3 }}</span>
                                    @endif
                                </div>
                            @endif

                            <div class="mt-auto flex items-center justify-between text-xs text-slate-400 dark:text-slate-500 pt-3 border-t border-slate-50 dark:border-slate-800">
                                <span>{{ $resource->formatted_file_size }}</span>
                                <span class="flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    {{ number_format($resource->view_count) }}
                                </span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

        {{-- ── LIST VIEW ── --}}
        @else
            <div class="bg-white dark:bg-slate-900 overflow-hidden"
                 style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">

                {{-- List header --}}
                <div class="hidden md:grid grid-cols-12 px-5 py-2.5 border-b border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
                    <div class="col-span-5 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Resource</div>
                    <div class="col-span-3 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Tags</div>
                    <div class="col-span-2 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider text-center" style="letter-spacing: 0.08em;">Views</div>
                    <div class="col-span-2 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider text-center" style="letter-spacing: 0.08em;">Downloads</div>
                </div>

                <div class="divide-y divide-slate-50 dark:divide-slate-800">
                    @foreach($resources as $resource)
                        @php $fmt = $formatConfig[$resource->format] ?? $defaultFmt; @endphp
                        <a href="{{ route('educational-resources.show', $resource) }}"
                           class="flex items-center gap-4 px-5 py-3.5 hover:bg-amber-50/40 dark:hover:bg-slate-800/40 transition-colors group">

                            {{-- Format icon badge --}}
                            <div class="flex-shrink-0 w-10 h-10 flex items-center justify-center border"
                                 style="border-radius: 2px; background: {{ $fmt['bg'] }}; border-color: {{ $fmt['border'] }};">
                                <svg class="w-5 h-5" style="color: {{ $fmt['color'] }};" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    @foreach(explode(' M', $fmt['icon']) as $i => $path)
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="{{ $i === 0 ? $path : 'M' . $path }}"/>
                                    @endforeach
                                </svg>
                            </div>

                            {{-- Title + meta --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-0.5">
                                    <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-200 truncate group-hover:text-amber-700 dark:group-hover:text-amber-400 transition-colors">
                                        {{ $resource->title }}
                                    </h3>
                                    <span class="hidden sm:inline-block text-xs font-bold px-1.5 py-0.5 border flex-shrink-0"
                                          style="border-radius: 2px; color: {{ $fmt['text'] }}; background: {{ $fmt['bg'] }}; border-color: {{ $fmt['border'] }};">
                                        {{ ucfirst($resource->format) }}
                                    </span>
                                </div>
                                <p class="text-xs text-slate-500 dark:text-slate-400">
                                    {{ $resource->academicSubject->name ?? 'N/A' }}
                                    <span class="text-slate-300 dark:text-slate-700 mx-1">·</span>
                                    {{ $resource->formatted_file_size }}
                                </p>
                            </div>

                            {{-- Tags --}}
                            <div class="hidden md:flex items-center gap-1.5 w-40 flex-shrink-0">
                                @if($resource->tags && count($resource->tags) > 0)
                                    @foreach(array_slice($resource->tags, 0, 2) as $tag)
                                        <span class="px-1.5 py-0.5 text-xs bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700 truncate"
                                              style="border-radius: 2px; max-width: 80px;">{{ $tag }}</span>
                                    @endforeach
                                @else
                                    <span class="text-xs text-slate-300 dark:text-slate-600">—</span>
                                @endif
                            </div>

                            {{-- Views + downloads --}}
                            <div class="flex items-center gap-5 text-xs text-slate-400 dark:text-slate-500 flex-shrink-0">
                                <span class="flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    {{ number_format($resource->view_count) }}
                                </span>
                                <span class="flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                    </svg>
                                    {{ number_format($resource->download_count) }}
                                </span>
                            </div>

                            {{-- Chevron --}}
                            <svg class="w-4 h-4 text-slate-300 dark:text-slate-600 group-hover:text-amber-500 transition-colors flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- ── PAGINATION ── --}}
        <div class="mt-6">
            {{ $resources->links() }}
        </div>

    @else

        {{-- ── EMPTY STATE ── --}}
        <div class="bg-white dark:bg-slate-900 overflow-hidden"
             style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
            <div class="px-6 py-16 flex flex-col items-center text-center">
                <div class="w-16 h-16 flex items-center justify-center mb-5"
                     style="background: linear-gradient(135deg, #f1f5f9, #e2e8f0); border-radius: 2px; border: 1px solid rgba(0,0,0,0.06);">
                    <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="text-base font-bold text-slate-900 dark:text-white mb-1" style="letter-spacing: -0.01em;">No resources found</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 mb-6 max-w-xs">
                    Try adjusting your search terms or filters to find what you're looking for.
                </p>
                <button wire:click="clearFilters"
                        class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white transition-all"
                        style="border-radius: 2px; background: linear-gradient(135deg, #0f172a, #1e293b); box-shadow: 0 2px 8px rgba(0,0,0,0.15);">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    Clear All Filters
                </button>
            </div>
        </div>

    @endif

</div>