<x-layouts.app>
    <x-examination-hub.navigation active="participant-groups"/>
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-7"
         style="font-family: 'system-ui', -apple-system, sans-serif;">

        {{-- ── BACK LINK ── --}}
        <a href="{{ route('examination-hub.participant-groups.index') }}"
           class="inline-flex items-center gap-1.5 text-xs font-semibold text-amber-700 dark:text-amber-400 hover:text-amber-900 dark:hover:text-amber-300 transition-colors uppercase tracking-wider"
           style="letter-spacing: 0.08em;">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Back to Groups
        </a>

        {{-- ── PAGE HEADER ── --}}
        <div class="overflow-hidden"
             style="border-radius: 2px; background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); box-shadow: 0 4px 24px rgba(0,0,0,0.15);">
            <div class="h-1 w-full" style="background: linear-gradient(90deg, #4c1d95, #7c3aed, #a78bfa);"></div>
            <div class="px-7 py-6">
                <h1 class="text-2xl font-bold text-white leading-snug"
                    style="letter-spacing: -0.02em; font-family: 'Georgia', serif;">
                    Edit Group
                </h1>
                <p class="text-sm text-slate-400 mt-1">Update group information</p>
            </div>
        </div>

        {{-- ── VALIDATION ERRORS ── --}}
        @if($errors->any())
            <div class="overflow-hidden" style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                <div class="h-1 w-full" style="background: linear-gradient(90deg, #991b1b, #dc2626, #f87171);"></div>
                <div class="bg-white dark:bg-slate-900 px-5 py-4 text-sm text-red-700 dark:text-red-400 font-medium flex items-start gap-3">
                    <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        {{-- ── FORM CARD ── --}}
        <div class="bg-white dark:bg-slate-900 overflow-hidden" style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                <div class="w-1 h-5" style="background: linear-gradient(180deg, #7c3aed, #a78bfa); border-radius: 1px;"></div>
                <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider" style="letter-spacing: 0.1em;">Group Details</h2>
            </div>
            <form action="{{ route('examination-hub.participant-groups.update', $group) }}" method="POST" class="p-6 space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <label for="name" class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">
                        Group Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" id="name" value="{{ old('name', $group->name) }}"
                           class="w-full px-4 py-2.5 text-sm text-slate-900 dark:text-white bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 dark:focus:border-amber-500 outline-none transition-colors"
                           style="border-radius: 2px;"
                           required>
                    @error('name')
                        <p class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="description" class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">
                        Description
                    </label>
                    <textarea name="description" id="description" rows="4"
                              class="w-full px-4 py-2.5 text-sm text-slate-900 dark:text-white bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 dark:focus:border-amber-500 outline-none transition-colors resize-none"
                              style="border-radius: 2px;">{{ old('description', $group->description) }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center gap-3 pt-4 border-t border-slate-100 dark:border-slate-800">
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2.5 text-xs font-bold text-white uppercase tracking-wider transition-opacity hover:opacity-90"
                            style="background: linear-gradient(135deg, #1d4ed8, #3b82f6); border-radius: 2px; letter-spacing: 0.08em;">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Update Group
                    </button>
                    <a href="{{ route('examination-hub.participant-groups.show', $group) }}"
                       class="inline-flex items-center gap-2 px-5 py-2.5 text-xs font-bold text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600 transition-colors hover:bg-slate-50 dark:hover:bg-slate-700 uppercase tracking-wider"
                       style="border-radius: 2px; letter-spacing: 0.08em;">
                        Cancel
                    </a>
                </div>
            </form>
        </div>

    </div>
</x-layouts.app>