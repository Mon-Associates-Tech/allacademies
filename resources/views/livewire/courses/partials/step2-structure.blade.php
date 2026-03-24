{{-- Step 2: Chapters & Sections --}}
@if($currentStep === 2)
<div class="backdrop-blur-xl bg-white/5 border border-white/10 rounded-3xl p-8 shadow-2xl">
    <div class="flex items-center justify-between mb-8">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-gradient-to-br from-amber-500 to-orange-500 rounded-2xl flex items-center justify-center shadow-lg shadow-amber-500/30">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-white">Course Structure</h2>
                <p class="text-slate-400">Organize your content into chapters and sections</p>
            </div>
        </div>
    </div>

    {{-- Add Chapter Form --}}
    <div class="mb-8 p-6 bg-gradient-to-r from-purple-600/10 to-cyan-600/10 border border-purple-500/20 rounded-2xl">
        <h3 class="text-lg font-semibold text-white mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            Add New Chapter
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="md:col-span-2">
                <input type="text" wire:model="newChapterTitle" placeholder="Chapter title..."
                       class="w-full px-5 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 transition-all duration-300">
            </div>
            <button wire:click="addChapter" class="px-6 py-3 bg-gradient-to-r from-purple-600 to-cyan-600 rounded-xl text-white font-semibold shadow-lg shadow-purple-500/30 hover:shadow-purple-500/50 hover:scale-105 transition-all duration-300 flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add Chapter
            </button>
        </div>
        <div class="mt-3">
            <textarea wire:model="newChapterDescription" placeholder="Chapter description (optional)..." rows="2"
                      class="w-full px-5 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 transition-all duration-300 resize-none text-sm"></textarea>
        </div>
    </div>

    {{-- Chapters List --}}
    @if($chapters->isEmpty())
        <div class="text-center py-16 bg-white/5 rounded-2xl border border-dashed border-white/20">
            <div class="w-20 h-20 mx-auto bg-gradient-to-br from-slate-700 to-slate-800 rounded-2xl flex items-center justify-center mb-4">
                <svg class="w-10 h-10 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <h3 class="text-xl font-semibold text-white mb-2">No chapters yet</h3>
            <p class="text-slate-400">Start building your course by adding your first chapter above</p>
        </div>
    @else
        <div class="space-y-4">
            @foreach($chapters as $chapter)
                <div class="group bg-white/5 border border-white/10 rounded-2xl overflow-hidden hover:border-purple-500/30 transition-all duration-300" wire:key="chapter-{{ $chapter->id }}">
                    {{-- Chapter Header --}}
                    <div class="p-5 flex items-center justify-between">
                        <div class="flex items-center gap-4 flex-1">
                            <div class="w-12 h-12 bg-gradient-to-br from-amber-500/20 to-orange-500/20 border border-amber-500/30 rounded-xl flex items-center justify-center">
                                <span class="text-amber-400 font-bold">{{ $loop->iteration }}</span>
                            </div>
                            <div class="flex-1">
                                @if($editingChapterId === $chapter->id)
                                    <input type="text" wire:model="newChapterTitle"
                                           class="w-full px-4 py-2 bg-white/10 border border-purple-500/50 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-purple-500/30">
                                @else
                                    <h4 class="text-lg font-semibold text-white">{{ $chapter->title }}</h4>
                                    @if($chapter->description)
                                        <p class="text-sm text-slate-400 mt-1">{{ Str::limit($chapter->description, 100) }}</p>
                                    @endif
                                @endif
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            {{-- Reorder Buttons --}}
                            <button wire:click="reorderChapter({{ $chapter->id }}, 'up')" class="p-2 text-slate-400 hover:text-white hover:bg-white/10 rounded-lg transition-all" title="Move Up">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                            </button>
                            <button wire:click="reorderChapter({{ $chapter->id }}, 'down')" class="p-2 text-slate-400 hover:text-white hover:bg-white/10 rounded-lg transition-all" title="Move Down">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            {{-- Edit/Save --}}
                            @if($editingChapterId === $chapter->id)
                                <button wire:click="updateChapter({{ $chapter->id }})" class="p-2 text-emerald-400 hover:text-emerald-300 hover:bg-emerald-500/10 rounded-lg transition-all">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                </button>
                            @else
                                <button wire:click="editChapter({{ $chapter->id }})" class="p-2 text-slate-400 hover:text-cyan-400 hover:bg-cyan-500/10 rounded-lg transition-all">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                            @endif
                            {{-- Select for Sections --}}
                            <button wire:click="selectChapter({{ $chapter->id }})" class="p-2 text-slate-400 hover:text-purple-400 hover:bg-purple-500/10 rounded-lg transition-all {{ $selectedChapterId === $chapter->id ? 'text-purple-400 bg-purple-500/10' : '' }}" title="Manage Sections">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                            </button>
                            {{-- Delete --}}
                            <button wire:click="deleteChapter({{ $chapter->id }})" wire:confirm="Are you sure you want to delete this chapter?" class="p-2 text-slate-400 hover:text-pink-400 hover:bg-pink-500/10 rounded-lg transition-all">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                    </div>

                    {{-- Sections Panel (when chapter is selected) --}}
                    @if($selectedChapterId === $chapter->id)
                        <div class="border-t border-white/10 bg-white/5 p-5">
                            {{-- Add Section Form --}}
                            <div class="flex gap-3 mb-4">
                                <input type="text" wire:model="newSectionTitle" placeholder="New section title..."
                                       class="flex-1 px-4 py-2.5 bg-white/5 border border-white/10 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/20 transition-all duration-300 text-sm">
                                <button wire:click="addSection" class="px-5 py-2.5 bg-gradient-to-r from-cyan-600 to-blue-600 rounded-xl text-white font-medium shadow-lg shadow-cyan-500/20 hover:shadow-cyan-500/40 transition-all duration-300 flex items-center gap-2 text-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                    Add Section
                                </button>
                            </div>

                            {{-- Sections List --}}
                            @if($sections->isEmpty())
                                <p class="text-center text-slate-500 py-4 text-sm italic">No sections in this chapter yet</p>
                            @else
                                <div class="space-y-2">
                                    @foreach($sections as $section)
                                        <div class="flex items-center gap-3 p-3 bg-white/5 rounded-xl border border-white/5 hover:border-cyan-500/30 transition-all group" wire:key="section-{{ $section->id }}">
                                            <div class="w-8 h-8 bg-cyan-500/20 rounded-lg flex items-center justify-center">
                                                <span class="text-cyan-400 text-sm font-semibold">{{ $loop->iteration }}</span>
                                            </div>
                                            <span class="flex-1 text-slate-300">{{ $section->title }}</span>
                                            <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                                <button wire:click="selectSection({{ $section->id }})" class="p-1.5 text-slate-400 hover:text-purple-400 hover:bg-purple-500/10 rounded-lg transition-all {{ $selectedSectionId === $section->id ? 'text-purple-400 bg-purple-500/10' : '' }}" title="Add Content">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                                </button>
                                                <button wire:click="deleteSection({{ $section->id }})" wire:confirm="Delete this section?" class="p-1.5 text-slate-400 hover:text-pink-400 hover:bg-pink-500/10 rounded-lg transition-all">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</div>
@endif
