{{-- Step 3: Content --}}
@if($currentStep === 3)
<div class="backdrop-blur-xl bg-white/5 border border-white/10 rounded-3xl p-8 shadow-2xl">
    <div class="flex items-center gap-4 mb-8">
        <div class="w-14 h-14 bg-gradient-to-br from-pink-500 to-rose-500 rounded-2xl flex items-center justify-center shadow-lg shadow-pink-500/30">
            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/></svg>
        </div>
        <div>
            <h2 class="text-2xl font-bold text-white">Add Content</h2>
            <p class="text-slate-400">Add videos, audio, text, quizzes and more to your sections</p>
        </div>
    </div>

    @if($chapters->isEmpty())
        <div class="text-center py-16 bg-white/5 rounded-2xl border border-dashed border-white/20">
            <div class="w-20 h-20 mx-auto bg-gradient-to-br from-slate-700 to-slate-800 rounded-2xl flex items-center justify-center mb-4">
                <svg class="w-10 h-10 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <h3 class="text-xl font-semibold text-white mb-2">No structure yet</h3>
            <p class="text-slate-400">Please add chapters and sections first in the previous step</p>
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Left Panel: Section Selector --}}
            <div class="lg:col-span-1">
                <div class="sticky top-8">
                    <h3 class="text-sm font-semibold text-slate-400 uppercase tracking-wider mb-4">Select Section</h3>
                    <div class="space-y-3 max-h-[500px] overflow-y-auto pr-2 custom-scrollbar">
                        @foreach($chapters as $chapter)
                            <div class="space-y-2">
                                <div class="flex items-center gap-2 text-amber-400 font-medium text-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                                    {{ $chapter->title }}
                                </div>
                                @foreach($chapter->sections as $section)
                                    <button wire:click="selectSection({{ $section->id }})"
                                            class="w-full text-left pl-6 pr-3 py-2.5 rounded-xl text-sm transition-all duration-300
                                            {{ $selectedSectionId === $section->id
                                                ? 'bg-gradient-to-r from-pink-600/30 to-rose-600/30 border border-pink-500/50 text-white'
                                                : 'bg-white/5 border border-transparent text-slate-400 hover:bg-white/10 hover:text-white' }}">
                                        <div class="flex items-center justify-between">
                                            <span>{{ $section->title }}</span>
                                            @if($section->contents->count() > 0)
                                                <span class="px-2 py-0.5 bg-white/10 rounded-full text-xs">{{ $section->contents->count() }}</span>
                                            @endif
                                        </div>
                                    </button>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Right Panel: Content Editor --}}
            <div class="lg:col-span-2">
                @if($selectedSectionId)
                    {{-- Content Type Selector --}}
                    <div class="mb-6">
                        <h3 class="text-sm font-semibold text-slate-400 uppercase tracking-wider mb-4">Content Type</h3>
                        <div class="grid grid-cols-5 gap-3">
                            @foreach([
                                'video' => ['icon' => 'M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z', 'color' => 'from-red-500 to-orange-500', 'label' => 'Video'],
                                'audio' => ['icon' => 'M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3', 'color' => 'from-purple-500 to-indigo-500', 'label' => 'Audio'],
                                'text' => ['icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'color' => 'from-cyan-500 to-blue-500', 'label' => 'Text'],
                                'quiz' => ['icon' => 'M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => 'from-emerald-500 to-teal-500', 'label' => 'Quiz'],
                                'feedback' => ['icon' => 'M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z', 'color' => 'from-amber-500 to-yellow-500', 'label' => 'Feedback']
                            ] as $type => $config)
                                <button wire:click="$set('newContentType', '{{ $type }}')"
                                        class="relative p-4 rounded-xl border-2 transition-all duration-300 group
                                        {{ $newContentType === $type
                                            ? 'bg-gradient-to-br ' . $config['color'] . ' border-transparent shadow-lg'
                                            : 'bg-white/5 border-white/10 hover:border-white/30' }}">
                                    <svg class="w-6 h-6 mx-auto mb-2 {{ $newContentType === $type ? 'text-white' : 'text-slate-400 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $config['icon'] }}"/>
                                    </svg>
                                    <span class="text-xs font-medium {{ $newContentType === $type ? 'text-white' : 'text-slate-400 group-hover:text-white' }}">{{ $config['label'] }}</span>
                                </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- Content Form --}}
                    @if($newContentType)
                        <div class="p-6 bg-gradient-to-r from-pink-600/10 to-rose-600/10 border border-pink-500/20 rounded-2xl mb-6">
                            <div class="space-y-4">
                                {{-- Title --}}
                                <div>
                                    <label class="block text-sm font-semibold text-slate-300 mb-2">Content Title <span class="text-pink-400">*</span></label>
                                    <input type="text" wire:model="newContentTitle" placeholder="Enter content title..."
                                           class="w-full px-5 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20 transition-all duration-300">
                                </div>

                                {{-- Video/Audio Fields --}}
                                @if(in_array($newContentType, ['video', 'audio']))
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-300 mb-2">Media URL or Upload</label>
                                        <input type="url" wire:model="newContentMediaUrl" placeholder="https://youtube.com/... or upload below"
                                               class="w-full px-5 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20 transition-all duration-300">
                                        <div class="mt-3">
                                            <label class="flex items-center justify-center gap-3 px-6 py-4 bg-white/5 border-2 border-dashed border-white/20 rounded-xl cursor-pointer hover:border-pink-500/50 hover:bg-pink-500/5 transition-all duration-300">
                                                <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                                <span class="text-slate-400">Upload {{ $newContentType }} file</span>
                                                <input type="file" wire:model="newContentMedia" accept="{{ $newContentType === 'video' ? 'video/*' : 'audio/*' }}" class="hidden">
                                            </label>
                                        </div>
                                    </div>
                                @endif

                                {{-- Text Content --}}
                                @if($newContentType === 'text')
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-300 mb-2">Content Body</label>
                                        <textarea wire:model="newContentBody" rows="8" placeholder="Write your content here..."
                                                  class="w-full px-5 py-4 bg-white/5 border border-white/10 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20 transition-all duration-300 resize-none"></textarea>
                                    </div>
                                @endif

                                {{-- Feedback Field --}}
                                @if($newContentType === 'feedback')
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-300 mb-2">Feedback Prompt <span class="text-pink-400">*</span></label>
                                        <textarea wire:model="feedbackPrompt" rows="4"
                                                  placeholder="e.g. What did you find most challenging about this section? Share your thoughts..."
                                                  class="w-full px-5 py-4 bg-white/5 border border-white/10 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20 transition-all duration-300 resize-none"></textarea>
                                        @error('feedbackPrompt') <span class="text-pink-400 text-sm mt-1 block">{{ $message }}</span> @enderror
                                        <p class="text-slate-500 text-xs mt-1">Course takers will see this prompt and fill in a textarea response.</p>
                                    </div>
                                @endif

                                {{-- Quiz Builder --}}
                                @if($newContentType === 'quiz')
                                    <div class="space-y-5">
                                        {{-- Quiz Source --}}
                                        <div>
                                            <label class="block text-sm font-semibold text-slate-300 mb-3">Question Source</label>
                                            <div class="grid grid-cols-3 gap-3">
                                                @foreach(['manual' => ['label' => 'Manual', 'icon' => 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z'], 'ai' => ['label' => 'AI Generate', 'icon' => 'M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z'], 'both' => ['label' => 'Both', 'icon' => 'M4 6h16M4 10h16M4 14h16M4 18h16']] as $src => $cfg)
                                                    <button type="button" wire:click="$set('quizSource', '{{ $src }}')"
                                                            class="p-3 rounded-xl border-2 text-center transition-all duration-200
                                                            {{ $quizSource === $src ? 'border-emerald-500 bg-emerald-500/10 text-emerald-300' : 'border-white/10 bg-white/5 text-slate-400 hover:border-white/30' }}">
                                                        <svg class="w-5 h-5 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $cfg['icon'] }}"/></svg>
                                                        <span class="text-xs font-medium">{{ $cfg['label'] }}</span>
                                                    </button>
                                                @endforeach
                                            </div>
                                        </div>

                                        {{-- Question Type --}}
                                        <div>
                                            <label class="block text-sm font-semibold text-slate-300 mb-2">Question Type</label>
                                            <select wire:model="quizQuestionType"
                                                    class="w-full px-5 py-3 bg-white/5 border border-white/10 rounded-xl text-white focus:outline-none focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20 transition-all duration-300">
                                                <option value="multiple_choice" class="bg-slate-800">Multiple Choice</option>
                                                <option value="true_false" class="bg-slate-800">True / False</option>
                                                <option value="essay" class="bg-slate-800">Essay</option>
                                            </select>
                                        </div>

                                        {{-- AI Generation Panel --}}
                                        @if(in_array($quizSource, ['ai', 'both']))
                                            <div class="p-4 bg-emerald-500/5 border border-emerald-500/20 rounded-xl space-y-3">
                                                <h4 class="text-sm font-semibold text-emerald-300 flex items-center gap-2">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                                                    AI Question Generation
                                                </h4>
                                                <div>
                                                    <label class="block text-xs text-slate-400 mb-1">Number of Questions</label>
                                                    <input type="number" wire:model="quizQuestionCount" min="1" max="50"
                                                           class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white text-sm focus:outline-none focus:border-emerald-500 transition-all">
                                                </div>
                                                <div>
                                                    <label class="block text-xs text-slate-400 mb-1">Section Context <span class="text-slate-500">(optional — helps AI generate relevant questions)</span></label>
                                                    <textarea wire:model="quizSectionContext" rows="3"
                                                              placeholder="Paste or summarise the section content here..."
                                                              class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white text-sm placeholder-slate-600 focus:outline-none focus:border-emerald-500 transition-all resize-none"></textarea>
                                                </div>
                                                <button type="button" wire:click="generateQuizQuestions" wire:loading.attr="disabled"
                                                        class="w-full px-4 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 rounded-lg text-white text-sm font-semibold hover:opacity-90 transition-all flex items-center justify-center gap-2 disabled:opacity-60">
                                                    <span wire:loading.remove wire:target="generateQuizQuestions">
                                                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                                        Generate Questions
                                                    </span>
                                                    <span wire:loading wire:target="generateQuizQuestions" class="flex items-center gap-2">
                                                        <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                                        Generating...
                                                    </span>
                                                </button>
                                                @if($quizGenerationError)
                                                    <p class="text-pink-400 text-xs flex items-center gap-1">
                                                        <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                        {{ $quizGenerationError }}
                                                    </p>
                                                @endif
                                            </div>
                                        @endif

                                        {{-- Manual Question Entry --}}
                                        @if(in_array($quizSource, ['manual', 'both']))
                                            <div>
                                                <div class="flex items-center justify-between mb-3">
                                                    <label class="text-sm font-semibold text-slate-300">Questions</label>
                                                    <button type="button" wire:click="addQuizQuestion"
                                                            class="flex items-center gap-1 px-3 py-1.5 bg-white/10 hover:bg-white/20 rounded-lg text-xs text-slate-300 transition-all">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                                        Add Question
                                                    </button>
                                                </div>
                                            </div>
                                        @endif

                                        {{-- Questions List (shared for both manual and AI-generated) --}}
                                        @if(!empty($quizQuestions))
                                            <div class="space-y-4 max-h-96 overflow-y-auto pr-1 custom-scrollbar">
                                                @foreach($quizQuestions as $qi => $q)
                                                    <div class="p-4 bg-white/5 border border-white/10 rounded-xl space-y-3" wire:key="quiz-q-{{ $qi }}">
                                                        <div class="flex items-start justify-between gap-2">
                                                            <span class="text-xs font-bold text-slate-400 mt-1">Q{{ $qi + 1 }}</span>
                                                            <button type="button" wire:click="removeQuizQuestion({{ $qi }})" class="text-slate-500 hover:text-pink-400 transition-colors flex-shrink-0">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                            </button>
                                                        </div>
                                                        <textarea wire:model="quizQuestions.{{ $qi }}.question" rows="2"
                                                                  placeholder="Question text..."
                                                                  class="w-full px-3 py-2 bg-white/5 border border-white/10 rounded-lg text-white text-sm placeholder-slate-600 focus:outline-none focus:border-pink-500 transition-all resize-none"></textarea>

                                                        @if(($q['type'] ?? '') === 'multiple_choice')
                                                            <div class="grid grid-cols-2 gap-2">
                                                                @foreach($q['options'] ?? ['','','',''] as $oi => $opt)
                                                                    <input type="text" wire:model="quizQuestions.{{ $qi }}.options.{{ $oi }}"
                                                                           placeholder="Option {{ chr(65 + $oi) }}"
                                                                           class="px-3 py-2 bg-white/5 border border-white/10 rounded-lg text-white text-sm placeholder-slate-600 focus:outline-none focus:border-pink-500 transition-all">
                                                                @endforeach
                                                            </div>
                                                            <div>
                                                                <label class="text-xs text-slate-400 mb-1 block">Correct Answer</label>
                                                                <select wire:model="quizQuestions.{{ $qi }}.correct_answer"
                                                                        class="w-full px-3 py-2 bg-white/5 border border-white/10 rounded-lg text-white text-sm focus:outline-none focus:border-pink-500 transition-all">
                                                                    <option value="" class="bg-slate-800">-- Select --</option>
                                                                    @foreach($q['options'] ?? [] as $opt)
                                                                        @if($opt)
                                                                            <option value="{{ $opt }}" class="bg-slate-800">{{ $opt }}</option>
                                                                        @endif
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        @elseif(($q['type'] ?? '') === 'true_false')
                                                            <div>
                                                                <label class="text-xs text-slate-400 mb-1 block">Correct Answer</label>
                                                                <select wire:model="quizQuestions.{{ $qi }}.correct_answer"
                                                                        class="w-full px-3 py-2 bg-white/5 border border-white/10 rounded-lg text-white text-sm focus:outline-none focus:border-pink-500 transition-all">
                                                                    <option value="" class="bg-slate-800">-- Select --</option>
                                                                    <option value="True" class="bg-slate-800">True</option>
                                                                    <option value="False" class="bg-slate-800">False</option>
                                                                </select>
                                                            </div>
                                                        @else
                                                            <div>
                                                                <label class="text-xs text-slate-400 mb-1 block">Model Answer <span class="text-slate-500">(for grading reference)</span></label>
                                                                <textarea wire:model="quizQuestions.{{ $qi }}.correct_answer" rows="2"
                                                                          placeholder="Expected answer..."
                                                                          class="w-full px-3 py-2 bg-white/5 border border-white/10 rounded-lg text-white text-sm placeholder-slate-600 focus:outline-none focus:border-pink-500 transition-all resize-none"></textarea>
                                                            </div>
                                                        @endif

                                                        <div class="flex gap-2">
                                                            <div class="flex-1">
                                                                <label class="text-xs text-slate-400 mb-1 block">Explanation <span class="text-slate-500">(optional)</span></label>
                                                                <input type="text" wire:model="quizQuestions.{{ $qi }}.explanation"
                                                                       placeholder="Why is this the correct answer?"
                                                                       class="w-full px-3 py-2 bg-white/5 border border-white/10 rounded-lg text-white text-sm placeholder-slate-600 focus:outline-none focus:border-pink-500 transition-all">
                                                            </div>
                                                            <div class="w-20">
                                                                <label class="text-xs text-slate-400 mb-1 block">Points</label>
                                                                <input type="number" wire:model="quizQuestions.{{ $qi }}.points" min="1"
                                                                       class="w-full px-3 py-2 bg-white/5 border border-white/10 rounded-lg text-white text-sm focus:outline-none focus:border-pink-500 transition-all">
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                            <p class="text-xs text-slate-500">{{ count($quizQuestions) }} question(s) added</p>
                                        @elseif($quizSource !== 'ai')
                                            <p class="text-xs text-slate-500 text-center py-4">No questions yet. Click "Add Question" or generate with AI.</p>
                                        @endif

                                        @error('quizQuestions') <span class="text-pink-400 text-sm">{{ $message }}</span> @enderror
                                    </div>
                                @endif

                                {{-- Required Checkbox --}}
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <div class="relative">
                                        <input type="checkbox" wire:model="newContentIsRequired" class="peer sr-only">
                                        <div class="w-6 h-6 bg-white/10 border border-white/20 rounded-lg peer-checked:bg-pink-500 peer-checked:border-pink-500 transition-all"></div>
                                        <svg class="absolute top-1 left-1 w-4 h-4 text-white opacity-0 peer-checked:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                    </div>
                                    <span class="text-slate-300 group-hover:text-white transition-colors">Required for course completion</span>
                                </label>

                                {{-- Add Button --}}
                                <div class="pt-2">
                                    <button wire:click="addContent" class="w-full px-6 py-3 bg-gradient-to-r from-pink-600 to-rose-600 rounded-xl text-white font-semibold shadow-lg shadow-pink-500/30 hover:shadow-pink-500/50 hover:scale-[1.02] transition-all duration-300 flex items-center justify-center gap-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                        Add Content
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Existing Content List --}}
                    @if($contents->isNotEmpty())
                        <div>
                            <h3 class="text-sm font-semibold text-slate-400 uppercase tracking-wider mb-4">Section Contents</h3>
                            <div class="space-y-3">
                                @foreach($contents as $content)
                                    <div class="flex items-center gap-4 p-4 bg-white/5 border border-white/10 rounded-xl hover:border-pink-500/30 transition-all group" wire:key="content-{{ $content->id }}">
                                        {{-- Type Icon --}}
                                        <div class="w-10 h-10 rounded-xl flex items-center justify-center
                                            @switch($content->type)
                                                @case('video') bg-red-500/20 text-red-400 @break
                                                @case('audio') bg-purple-500/20 text-purple-400 @break
                                                @case('text') bg-cyan-500/20 text-cyan-400 @break
                                                @case('quiz') bg-emerald-500/20 text-emerald-400 @break
                                                @case('feedback') bg-amber-500/20 text-amber-400 @break
                                            @endswitch">
                                            @switch($content->type)
                                                @case('video')
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                                    @break
                                                @case('audio')
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/></svg>
                                                    @break
                                                @case('text')
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                    @break
                                                @case('quiz')
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                    @break
                                                @case('feedback')
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                                                    @break
                                            @endswitch
                                        </div>
                                        {{-- Content Info --}}
                                        <div class="flex-1">
                                            <h4 class="text-white font-medium">{{ $content->title }}</h4>
                                            <div class="flex items-center gap-3 mt-1">
                                                <span class="text-xs text-slate-500 uppercase">{{ $content->type }}</span>
                                                @if($content->is_required)
                                                    <span class="px-2 py-0.5 bg-pink-500/20 text-pink-400 rounded-full text-xs">Required</span>
                                                @endif
                                            </div>
                                        </div>
                                        {{-- Actions --}}
                                        <button wire:click="deleteContent({{ $content->id }})" wire:confirm="Delete this content?" class="p-2 text-slate-400 hover:text-pink-400 hover:bg-pink-500/10 rounded-lg transition-all opacity-0 group-hover:opacity-100">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @else
                    <div class="text-center py-16 bg-white/5 rounded-2xl border border-dashed border-white/20">
                        <div class="w-16 h-16 mx-auto bg-gradient-to-br from-pink-500/20 to-rose-500/20 rounded-2xl flex items-center justify-center mb-4">
                            <svg class="w-8 h-8 text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"/></svg>
                        </div>
                        <h3 class="text-lg font-semibold text-white mb-2">Select a section</h3>
                        <p class="text-slate-400">Choose a section from the left to add content</p>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>
@endif

<style>
.custom-scrollbar::-webkit-scrollbar { width: 6px; }
.custom-scrollbar::-webkit-scrollbar-track { background: rgba(255,255,255,0.05); border-radius: 3px; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.2); border-radius: 3px; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.3); }
</style>
