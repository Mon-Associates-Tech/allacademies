<div class="min-h-screen bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900 py-8 px-4 sm:px-6 lg:px-8" x-data="{ showSuccess: false, showError: false }">
    {{-- Animated Background Elements --}}
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-40 -right-40 w-80 h-80 bg-purple-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-pulse"></div>
        <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-cyan-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-pulse" style="animation-delay: 2s;"></div>
        <div class="absolute top-1/2 left-1/2 w-80 h-80 bg-pink-500 rounded-full mix-blend-multiply filter blur-3xl opacity-10 animate-pulse" style="animation-delay: 4s;"></div>
    </div>

    <div class="relative max-w-6xl mx-auto">
        {{-- Flash Messages --}}
        @if (session()->has('message'))
            <div x-show="showSuccess" x-init="showSuccess = true; setTimeout(() => showSuccess = false, 5000)"
                 x-transition:enter="transform ease-out duration-500" x-transition:enter-start="translate-y-[-100%] opacity-0" x-transition:enter-end="translate-y-0 opacity-100"
                 class="mb-6 backdrop-blur-xl bg-emerald-500/20 border border-emerald-400/30 text-emerald-300 px-6 py-4 rounded-2xl shadow-lg shadow-emerald-500/10">
                <div class="flex items-center gap-3">
                    <div class="flex-shrink-0 w-10 h-10 bg-emerald-500/30 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <span class="font-medium">{{ session('message') }}</span>
                </div>
            </div>
        @endif

        {{-- Header --}}
        <div class="text-center mb-10">
            <h1 class="text-4xl md:text-5xl font-bold bg-gradient-to-r from-white via-purple-200 to-cyan-200 bg-clip-text text-transparent mb-3">
                {{ $isEditing ? 'Edit Course' : 'Create New Course' }}
            </h1>
            <p class="text-slate-400 text-lg">{{ $isEditing ? 'Update your course details and content' : 'Build your course step by step' }}</p>
        </div>

        {{-- Futuristic Step Navigation --}}
        <div class="mb-10">
            <div class="flex items-center justify-center">
                <div class="flex items-center gap-2 md:gap-4 p-2 backdrop-blur-xl bg-white/5 rounded-2xl border border-white/10">
                    @foreach(['Details', 'Structure', 'Content', 'Review'] as $index => $stepName)
                        @php $stepNum = $index + 1; @endphp
                        <button wire:click="goToStep({{ $stepNum }})"
                                class="relative group flex items-center gap-2 px-4 py-3 rounded-xl transition-all duration-300
                                {{ $currentStep === $stepNum
                                    ? 'bg-gradient-to-r from-purple-600 to-cyan-600 text-white shadow-lg shadow-purple-500/30'
                                    : ($currentStep > $stepNum
                                        ? 'bg-emerald-500/20 text-emerald-400 hover:bg-emerald-500/30'
                                        : 'text-slate-400 hover:bg-white/10') }}">
                            <span class="w-8 h-8 flex items-center justify-center rounded-lg text-sm font-bold
                                {{ $currentStep === $stepNum ? 'bg-white/20' : ($currentStep > $stepNum ? 'bg-emerald-500/30' : 'bg-white/10') }}">
                                @if($currentStep > $stepNum)
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                @else
                                    {{ $stepNum }}
                                @endif
                            </span>
                            <span class="hidden md:inline font-medium">{{ $stepName }}</span>
                        </button>
                        @if($index < 3)
                            <div class="w-8 h-0.5 {{ $currentStep > $stepNum ? 'bg-emerald-500' : 'bg-white/20' }} hidden md:block"></div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Step 1: Course Details --}}
        @if($currentStep === 1)
        <div class="backdrop-blur-xl bg-white/5 border border-white/10 rounded-3xl p-8 shadow-2xl">
            <div class="flex items-center gap-4 mb-8">
                <div class="w-14 h-14 bg-gradient-to-br from-purple-500 to-cyan-500 rounded-2xl flex items-center justify-center shadow-lg shadow-purple-500/30">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-white">Course Information</h2>
                    <p class="text-slate-400">Define the basics of your course</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Title --}}
                <div class="lg:col-span-2 group">
                    <label class="block text-sm font-semibold text-slate-300 mb-2">Course Title <span class="text-pink-400">*</span></label>
                    <input type="text" wire:model="title" placeholder="Enter an engaging course title..."
                           class="w-full px-5 py-4 bg-white/5 border border-white/10 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 transition-all duration-300">
                    @error('title') <span class="text-pink-400 text-sm mt-1 block">{{ $message }}</span> @enderror
                </div>

                {{-- Description --}}
                <div class="lg:col-span-2">
                    <label class="block text-sm font-semibold text-slate-300 mb-2">Description</label>
                    <textarea wire:model="description" rows="4" placeholder="Describe what students will learn..."
                              class="w-full px-5 py-4 bg-white/5 border border-white/10 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 transition-all duration-300 resize-none"></textarea>
                </div>

                {{-- Objectives --}}
                <div class="lg:col-span-2">
                    <label class="block text-sm font-semibold text-slate-300 mb-2">Learning Objectives</label>
                    <textarea wire:model="objectives" rows="3" placeholder="What will students achieve?"
                              class="w-full px-5 py-4 bg-white/5 border border-white/10 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 transition-all duration-300 resize-none"></textarea>
                </div>

                {{-- Difficulty --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-300 mb-2">Difficulty Level</label>
                    <select wire:model="difficulty_level" class="w-full px-5 py-4 bg-white/5 border border-white/10 rounded-xl text-white focus:outline-none focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 transition-all duration-300">
                        <option value="beginner" class="bg-slate-800">🌱 Beginner</option>
                        <option value="intermediate" class="bg-slate-800">🌿 Intermediate</option>
                        <option value="advanced" class="bg-slate-800">🌳 Advanced</option>
                    </select>
                </div>

                {{-- Audience --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-300 mb-2">Target Audience</label>
                    <select wire:model="audience" class="w-full px-5 py-4 bg-white/5 border border-white/10 rounded-xl text-white focus:outline-none focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 transition-all duration-300">
                        <option value="public" class="bg-slate-800">🌍 Public (Everyone)</option>
                        <option value="school_only" class="bg-slate-800">🏫 School Only</option>
                    </select>
                </div>

                {{-- Pricing --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-300 mb-3">Pricing</label>
                    <div class="flex gap-4">
                        <label class="flex-1 cursor-pointer">
                            <input type="radio" wire:model="is_free" value="1" class="peer hidden">
                            <div class="p-4 bg-white/5 border-2 border-white/10 rounded-xl text-center transition-all duration-300 peer-checked:border-emerald-500 peer-checked:bg-emerald-500/10">
                                <span class="text-2xl">🆓</span>
                                <p class="text-white font-semibold mt-1">Free</p>
                            </div>
                        </label>
                        <label class="flex-1 cursor-pointer">
                            <input type="radio" wire:model="is_free" value="0" class="peer hidden">
                            <div class="p-4 bg-white/5 border-2 border-white/10 rounded-xl text-center transition-all duration-300 peer-checked:border-amber-500 peer-checked:bg-amber-500/10">
                                <span class="text-2xl">💰</span>
                                <p class="text-white font-semibold mt-1">Paid</p>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Price --}}
                @if(!$is_free)
                <div>
                    <label class="block text-sm font-semibold text-slate-300 mb-2">Price</label>
                    <div class="relative">
                        <span class="absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 font-bold">₦</span>
                        <input type="number" wire:model="price" step="0.01" min="0" placeholder="0.00"
                               class="w-full pl-10 pr-5 py-4 bg-white/5 border border-white/10 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 transition-all duration-300">
                    </div>
                </div>
                @endif

                {{-- Thumbnail --}}
                <div class="lg:col-span-2">
                    <label class="block text-sm font-semibold text-slate-300 mb-2">Course Thumbnail</label>
                    <div class="flex items-center gap-6">
                        {{-- Loading state while uploading --}}
                        <div wire:loading wire:target="thumbnail" class="h-24 w-40 bg-white/5 border-2 border-purple-500/50 rounded-xl flex items-center justify-center">
                            <div class="flex flex-col items-center">
                                <svg class="w-8 h-8 text-purple-400 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span class="text-xs text-purple-300 mt-1">Uploading...</span>
                            </div>
                        </div>
                        {{-- Preview after upload complete --}}
                        <div wire:loading.remove wire:target="thumbnail">
                            @if($thumbnail)
                                @php $previewUrl = $this->getThumbnailPreviewUrl(); @endphp
                                @if($previewUrl)
                                    <img src="{{ $previewUrl }}" class="h-24 w-40 object-cover rounded-xl border-2 border-purple-500/50">
                                @else
                                    <div class="h-24 w-40 bg-emerald-500/20 border-2 border-emerald-500/50 rounded-xl flex items-center justify-center">
                                        <div class="text-center">
                                            <svg class="w-8 h-8 text-emerald-400 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            <span class="text-xs text-emerald-300">Selected</span>
                                        </div>
                                    </div>
                                @endif
                            @elseif($existingThumbnail)
                                <img src="{{ asset('storage/' . $existingThumbnail) }}" class="h-24 w-40 object-cover rounded-xl border-2 border-white/20">
                            @else
                                <div class="h-24 w-40 bg-white/5 border-2 border-dashed border-white/20 rounded-xl flex items-center justify-center">
                                    <svg class="w-10 h-10 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                            @endif
                        </div>
                        <label class="flex-1 cursor-pointer">
                            <input type="file" wire:model="thumbnail" accept="image/*" class="hidden">
                            <div class="px-6 py-4 bg-gradient-to-r from-purple-600/20 to-cyan-600/20 border border-purple-500/30 rounded-xl text-center hover:from-purple-600/30 hover:to-cyan-600/30 transition-all duration-300">
                                <div wire:loading wire:target="thumbnail">
                                    <svg class="w-8 h-8 mx-auto text-purple-400 animate-spin mb-2" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span class="text-purple-300 font-medium">Uploading...</span>
                                </div>
                                <div wire:loading.remove wire:target="thumbnail">
                                    <svg class="w-8 h-8 mx-auto text-purple-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                    <span class="text-purple-300 font-medium">{{ $thumbnail ? 'Change Image' : 'Upload Image' }}</span>
                                </div>
                            </div>
                        </label>
                    </div>
                    @error('thumbnail') <span class="text-pink-400 text-sm mt-2 block">{{ $message }}</span> @enderror
                    @if($thumbnail)
                        <p class="text-emerald-400 text-sm mt-2">✓ Image selected. Click "Save Details" to save the thumbnail.</p>
                    @endif
                </div>
            </div>
        </div>
        @endif

        {{-- Include other steps --}}
        @include('livewire.courses.partials.step2-structure')
        @include('livewire.courses.partials.step3-content')
        @include('livewire.courses.partials.step4-review')

        {{-- Navigation --}}
        <div class="mt-8 flex justify-between items-center">
            <div>
                @if($currentStep > 1)
                <button wire:click="previousStep" class="group flex items-center gap-2 px-6 py-3 bg-white/5 border border-white/10 rounded-xl text-slate-300 hover:bg-white/10 hover:text-white transition-all duration-300">
                    <svg class="w-5 h-5 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    Previous
                </button>
                @endif
            </div>
            <div class="flex gap-4">
                @if($currentStep === 1)
                {{-- Save Details button on Step 1 to save course info including thumbnail --}}
                <button wire:click="saveCourse" class="group flex items-center gap-2 px-6 py-3 bg-white/5 border border-white/10 rounded-xl text-slate-300 hover:bg-white/10 hover:text-white transition-all duration-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                    Save Details
                </button>
                @endif
                @if($currentStep < 4)
                <button wire:click="nextStep" class="group flex items-center gap-2 px-8 py-3 bg-gradient-to-r from-purple-600 to-cyan-600 rounded-xl text-white font-semibold shadow-lg shadow-purple-500/30 hover:shadow-purple-500/50 hover:scale-105 transition-all duration-300">
                    Next Step
                    <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </button>
                @else
                <button wire:click="saveCourse" class="group flex items-center gap-2 px-8 py-3 bg-gradient-to-r from-emerald-600 to-cyan-600 rounded-xl text-white font-semibold shadow-lg shadow-emerald-500/30 hover:shadow-emerald-500/50 hover:scale-105 transition-all duration-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    {{ $isEditing ? 'Update Course' : 'Create Course' }}
                </button>
                @endif
            </div>
        </div>
    </div>
</div>
