<x-layouts.exam>
    <div class="container mx-auto px-4 py-8">
        @if(session('success'))
            <div class="flex items-start gap-3 px-5 py-4 mb-6 border-l-4 border-emerald-500 bg-emerald-50 dark:bg-emerald-950/30" style="border-radius: 2px;">
                <svg class="w-5 h-5 text-emerald-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-sm text-emerald-800 dark:text-emerald-300">{{ session('success') }} {{ session('participant_name') ? 'for ' . session('participant_name') : '' }}</p>
            </div>
        @endif
        <div class="bg-white dark:bg-slate-900 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-1 h-5" style="background: linear-gradient(180deg, #b45309, #fbbf24); border-radius: 1px;"></div>
                    <h2 class="font-bold text-slate-900 dark:text-white text-sm uppercase tracking-wider" style="letter-spacing: 0.08em;">
                        Edit Participant
                    </h2>
                </div>
                <a href="{{ route('examination-hub.exams.show', $exam) }}"
                   class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium text-amber-700 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800 hover:bg-amber-100 dark:hover:bg-amber-900/30 transition-colors"
                   style="border-radius: 2px;">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Exam
                </a>
            </div>

            <div class="p-6">
                <form action="{{ route('examination-hub.participants.configured.update', [$exam, $participant]) }}" method="POST">
                    @csrf
                    @method('PATCH')

                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div>
                            <label class="block text-xs font-medium text-slate-500 uppercase tracking-wider mb-1" style="font-size: 10px; letter-spacing: 0.1em;">Full Name</label>
                            <input name="name" value="{{ old('name', $participant->name) }}" required
                                   class="w-full px-3 py-2.5 text-sm bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all"
                                   style="border-radius: 2px;">
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-slate-500 uppercase tracking-wider mb-1" style="font-size: 10px; letter-spacing: 0.1em;">Email Address</label>
                            <input name="email" value="{{ old('email', $participant->email) }}" required
                                   class="w-full px-3 py-2.5 text-sm bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all"
                                   style="border-radius: 2px;">
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-slate-500 uppercase tracking-wider mb-1" style="font-size: 10px; letter-spacing: 0.1em;">Unique Code <span class="normal-case text-slate-400">(optional)</span></label>
                            <input name="unique_code" value="{{ old('unique_code', $participant->unique_code) }}"
                                   class="w-full px-3 py-2.5 text-sm bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all"
                                   style="border-radius: 2px;">
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2 mt-6 pt-6 border-t border-slate-100 dark:border-slate-800">
                        <a href="{{ route('examination-hub.exams.show', $exam) }}"
                           class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium text-slate-600 dark:text-slate-400 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700/50 transition-colors"
                           style="border-radius: 2px;">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Cancel
                        </a>

                        <button type="submit"
                                class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium text-white bg-amber-600 hover:bg-amber-700 dark:bg-amber-700 dark:hover:bg-amber-800 transition-colors"
                                style="border-radius: 2px;">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</x-layouts.exam>
