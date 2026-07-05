{{-- Readmission Modal --}}
<template x-teleport="body">
    <div x-show="{{ $show ?? 'showReadmitModal' }}"
         x-transition:enter="ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
         @click.self="{{ $close ?? 'showReadmitModal = false' }}">
        <div class="bg-white dark:bg-slate-900 w-full max-w-md overflow-hidden"
             style="border-radius: 2px; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);">
            <div class="h-1 w-full" style="background: linear-gradient(90deg, #8b5cf6, #a78bfa);"></div>
            <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800">
                <h3 class="font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <x-heroicon-o-arrow-path-rounded-square class="w-5 h-5 text-violet-500" />
                    {{ $title ?? 'Grant Readmission' }}
                </h3>
                <p class="text-sm text-slate-500 mt-1" x-text="{{ $subtitle ?? \"'Participant: ' + (selectedParticipant?.participant_name || '')\" }}"></p>
            </div>
            <div class="px-6 py-4 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">{{ $modeLabel ?? 'Mode' }}</label>
                    <select x-model="{{ $modeModel ?? 'readmitMode' }}"
                            class="w-full px-3 py-2 text-sm border border-slate-200 dark:border-slate-700 dark:bg-slate-800 focus:ring-2 focus:ring-violet-500"
                            style="border-radius: 2px;">
                        <option value="continue">{{ $continueLabel ?? 'Continue (Resume answers)' }}</option>
                        <option value="fresh">{{ $freshLabel ?? 'Fresh (Start over)' }}</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">{{ $timeLabel ?? 'Extra Time (minutes)' }}</label>
                    <input x-model="{{ $timeModel ?? 'readmitExtraMinutes' }}" type="number" min="0" max="480"
                           class="w-full px-3 py-2 text-sm border border-slate-200 dark:border-slate-700 dark:bg-slate-800 focus:ring-2 focus:ring-violet-500"
                           style="border-radius: 2px;">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">{{ $reasonLabel ?? 'Reason (optional)' }}</label>
                    <textarea x-model="{{ $reasonModel ?? 'readmitReason' }}" rows="2" placeholder="{{ $reasonPlaceholder ?? 'Admin notes...' }}"
                              class="w-full px-3 py-2 text-sm border border-slate-200 dark:border-slate-700 dark:bg-slate-800 focus:ring-2 focus:ring-violet-500"
                              style="border-radius: 2px;"></textarea>
                </div>
            </div>
            <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/50 flex justify-end gap-3">
                <button @click="{{ $close ?? 'showReadmitModal = false' }}"
                        class="px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors"
                        style="border-radius: 2px;">
                    Cancel
                </button>
                <button @click="{{ $action ?? 'grantReadmission()' }}"
                        :disabled="{{ $disabled ?? 'actionLoading' }}"
                        class="px-4 py-2 text-sm font-semibold text-white bg-violet-600 hover:bg-violet-700 transition-colors disabled:opacity-50"
                        style="border-radius: 2px;">
                    {{ $button ?? 'Grant Readmission' }}
                </button>
            </div>
        </div>
    </div>
</template>
