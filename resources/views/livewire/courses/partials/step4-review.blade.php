{{-- Step 4: Review --}}
@if($currentStep === 4)
<div class="backdrop-blur-xl bg-white/5 border border-white/10 rounded-3xl p-8 shadow-2xl">
    <div class="flex items-center gap-4 mb-8">
        <div class="w-14 h-14 bg-gradient-to-br from-emerald-500 to-teal-500 rounded-2xl flex items-center justify-center shadow-lg shadow-emerald-500/30">
            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div>
            <h2 class="text-2xl font-bold text-white">Review Your Course</h2>
            <p class="text-slate-400">Make sure everything looks good before publishing</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Left Column: Course Summary --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Course Overview Card --}}
            <div class="bg-gradient-to-br from-white/10 to-white/5 border border-white/10 rounded-2xl overflow-hidden">
                {{-- Thumbnail Header --}}
                <div class="relative h-48 bg-gradient-to-br from-purple-600/30 to-cyan-600/30">
                    @if($existingThumbnail)
                        <img src="{{ asset('storage/' . $existingThumbnail) }}" class="w-full h-full object-cover">
                    @elseif($thumbnail)
                        <img src="{{ $thumbnail->temporaryUrl() }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center">
                            <svg class="w-16 h-16 text-white/30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                    @endif
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 to-transparent"></div>
                    <div class="absolute bottom-4 left-4 right-4">
                        <h3 class="text-2xl font-bold text-white">{{ $title ?: 'Untitled Course' }}</h3>
                    </div>
                </div>

                {{-- Course Details --}}
                <div class="p-6">
                    <p class="text-slate-400 mb-4">{{ $description ?: 'No description provided.' }}</p>

                    {{-- Tags --}}
                    <div class="flex flex-wrap gap-2">
                        {{-- Difficulty --}}
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-medium
                            @switch($difficulty_level)
                                @case('beginner') bg-emerald-500/20 text-emerald-400 @break
                                @case('intermediate') bg-amber-500/20 text-amber-400 @break
                                @case('advanced') bg-red-500/20 text-red-400 @break
                            @endswitch">
                            @switch($difficulty_level)
                                @case('beginner') 🌱 @break
                                @case('intermediate') 🌿 @break
                                @case('advanced') 🌳 @break
                            @endswitch
                            {{ ucfirst($difficulty_level) }}
                        </span>

                        {{-- Price --}}
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-medium {{ $is_free ? 'bg-cyan-500/20 text-cyan-400' : 'bg-amber-500/20 text-amber-400' }}">
                            {{ $is_free ? '🆓 Free' : '💰 ₦' . number_format($price, 2) }}
                        </span>

                        {{-- Audience --}}
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-medium bg-purple-500/20 text-purple-400">
                            {{ $audience === 'public' ? '🌍 Public' : '🏫 School Only' }}
                        </span>
                    </div>

                    {{-- Objectives --}}
                    @if($objectives)
                        <div class="mt-6 pt-6 border-t border-white/10">
                            <h4 class="text-sm font-semibold text-slate-400 uppercase tracking-wider mb-3">Learning Objectives</h4>
                            <p class="text-slate-300">{{ $objectives }}</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Course Structure --}}
            <div class="bg-white/5 border border-white/10 rounded-2xl p-6">
                <h4 class="text-lg font-semibold text-white mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    Course Structure
                </h4>

                @if($chapters->isEmpty())
                    <div class="text-center py-8 bg-white/5 rounded-xl border border-dashed border-white/20">
                        <p class="text-slate-500">No chapters added yet</p>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach($chapters as $chapter)
                            <div class="bg-white/5 rounded-xl p-4" wire:key="review-chapter-{{ $chapter->id }}">
                                <div class="flex items-center gap-3 mb-3">
                                    <div class="w-8 h-8 bg-amber-500/20 rounded-lg flex items-center justify-center">
                                        <span class="text-amber-400 font-bold text-sm">{{ $loop->iteration }}</span>
                                    </div>
                                    <h5 class="font-semibold text-white">{{ $chapter->title }}</h5>
                                    <span class="ml-auto text-xs text-slate-500">{{ $chapter->sections->count() }} sections</span>
                                </div>

                                @if($chapter->sections->isNotEmpty())
                                    <div class="ml-11 space-y-2">
                                        @foreach($chapter->sections as $section)
                                            <div class="flex items-center gap-2 text-sm">
                                                <div class="w-1.5 h-1.5 bg-cyan-500 rounded-full"></div>
                                                <span class="text-slate-400">{{ $section->title }}</span>
                                                @if($section->contents->count() > 0)
                                                    <span class="ml-auto px-2 py-0.5 bg-white/10 rounded-full text-xs text-slate-500">
                                                        {{ $section->contents->count() }} items
                                                    </span>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- Right Column: Stats & Actions --}}
        <div class="space-y-6">
            {{-- Quick Stats --}}
            <div class="bg-white/5 border border-white/10 rounded-2xl p-6">
                <h4 class="text-sm font-semibold text-slate-400 uppercase tracking-wider mb-4">Course Stats</h4>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400">Chapters</span>
                        <span class="text-white font-semibold">{{ $chapters->count() }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400">Sections</span>
                        <span class="text-white font-semibold">{{ $chapters->sum(fn($c) => $c->sections->count()) }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400">Content Items</span>
                        <span class="text-white font-semibold">{{ $chapters->sum(fn($c) => $c->sections->sum(fn($s) => $s->contents->count())) }}</span>
                    </div>
                </div>
            </div>

            {{-- Readiness Check --}}
            <div class="bg-white/5 border border-white/10 rounded-2xl p-6">
                <h4 class="text-sm font-semibold text-slate-400 uppercase tracking-wider mb-4">Readiness Check</h4>
                <div class="space-y-3">
                    @php
                        $hasTitle = !empty($title);
                        $hasChapters = $chapters->isNotEmpty();
                        $hasSections = $chapters->sum(fn($c) => $c->sections->count()) > 0;
                        $hasContent = $chapters->sum(fn($c) => $c->sections->sum(fn($s) => $s->contents->count())) > 0;
                    @endphp

                    <div class="flex items-center gap-3">
                        <div class="w-6 h-6 rounded-full flex items-center justify-center {{ $hasTitle ? 'bg-emerald-500/20 text-emerald-400' : 'bg-slate-500/20 text-slate-500' }}">
                            @if($hasTitle)
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            @else
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            @endif
                        </div>
                        <span class="{{ $hasTitle ? 'text-slate-300' : 'text-slate-500' }}">Course title</span>
                    </div>

                    <div class="flex items-center gap-3">
                        <div class="w-6 h-6 rounded-full flex items-center justify-center {{ $hasChapters ? 'bg-emerald-500/20 text-emerald-400' : 'bg-slate-500/20 text-slate-500' }}">
                            @if($hasChapters)
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            @else
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            @endif
                        </div>
                        <span class="{{ $hasChapters ? 'text-slate-300' : 'text-slate-500' }}">At least one chapter</span>
                    </div>

                    <div class="flex items-center gap-3">
                        <div class="w-6 h-6 rounded-full flex items-center justify-center {{ $hasSections ? 'bg-emerald-500/20 text-emerald-400' : 'bg-slate-500/20 text-slate-500' }}">
                            @if($hasSections)
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            @else
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            @endif
                        </div>
                        <span class="{{ $hasSections ? 'text-slate-300' : 'text-slate-500' }}">At least one section</span>
                    </div>

                    <div class="flex items-center gap-3">
                        <div class="w-6 h-6 rounded-full flex items-center justify-center {{ $hasContent ? 'bg-emerald-500/20 text-emerald-400' : 'bg-amber-500/20 text-amber-400' }}">
                            @if($hasContent)
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            @else
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01"/></svg>
                            @endif
                        </div>
                        <span class="{{ $hasContent ? 'text-slate-300' : 'text-amber-400' }}">{{ $hasContent ? 'Content added' : 'Add content (optional)' }}</span>
                    </div>
                </div>
            </div>

            {{-- Publication Status --}}
            @if($course && $course->exists)
                <div class="bg-white/5 border border-white/10 rounded-2xl p-6">
                    <h4 class="text-sm font-semibold text-slate-400 uppercase tracking-wider mb-4">Publication</h4>

                    <div class="mb-4">
                        <div class="flex items-center gap-2 mb-2">
                            <div class="w-3 h-3 rounded-full {{ $course->status === 'published' ? 'bg-emerald-500' : ($course->status === 'draft' ? 'bg-amber-500' : 'bg-slate-500') }}"></div>
                            <span class="text-white font-medium">{{ ucfirst($course->status) }}</span>
                        </div>
                        @if($course->published_at)
                            <p class="text-xs text-slate-500">Published {{ $course->published_at->diffForHumans() }}</p>
                        @endif
                    </div>

                    <div class="space-y-2">
                        @if($course->status !== 'published')
                            <button wire:click="publishCourse" class="w-full px-4 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 rounded-xl text-white font-semibold shadow-lg shadow-emerald-500/30 hover:shadow-emerald-500/50 transition-all duration-300 flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Publish Course
                            </button>
                        @else
                            <button wire:click="unpublishCourse" class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-slate-300 font-medium hover:bg-white/20 transition-all duration-300 flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                Unpublish
                            </button>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Quick Actions --}}
            <div class="bg-gradient-to-br from-purple-600/20 to-cyan-600/20 border border-purple-500/30 rounded-2xl p-6">
                <h4 class="text-sm font-semibold text-slate-400 uppercase tracking-wider mb-4">Quick Actions</h4>
                <div class="space-y-2">
                    <button wire:click="goToStep(1)" class="w-full px-4 py-2.5 bg-white/10 rounded-xl text-slate-300 text-sm hover:bg-white/20 transition-all flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Edit Course Details
                    </button>
                    <button wire:click="goToStep(2)" class="w-full px-4 py-2.5 bg-white/10 rounded-xl text-slate-300 text-sm hover:bg-white/20 transition-all flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        Edit Structure
                    </button>
                    <button wire:click="goToStep(3)" class="w-full px-4 py-2.5 bg-white/10 rounded-xl text-slate-300 text-sm hover:bg-white/20 transition-all flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        Add More Content
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
