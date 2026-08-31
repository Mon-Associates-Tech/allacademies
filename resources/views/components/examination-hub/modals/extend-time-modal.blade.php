{{-- Extend Time Modal Component --}}
<template x-teleport="body">
    <div x-show="showExtendTimeModal"
         x-transition:enter="ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
         @click.self="showExtendTimeModal = false">
        <div class="bg-white dark:bg-slate-900 w-full max-w-md overflow-hidden"
             style="border-radius: 2px; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);">
            <div class="h-1 w-full" style="background: linear-gradient(90deg, #10b981, #34d399);"></div>
            <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800">
                <h3 class="font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <x-heroicon-o-clock class="w-5 h-5 text-emerald-500" />
                    Extend Time
                </h3>
                <p class="text-sm text-slate-500 mt-1" x-text="'Participant: ' + (selectedParticipant?.participant_name || '')"></p>
            </div>
            <div class="px-6 py-4">
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Minutes to add</label>
                <input x-model="extendMinutes" type="number" min="1" max="480"
                       class="w-full px-3 py-2 text-sm border border-slate-200 dark:border-slate-700 dark:bg-slate-800 focus:ring-2 focus:ring-emerald-500"
                       style="border-radius: 2px;">
            </div>
            <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/50 flex justify-end gap-3">
                <button @click="showExtendTimeModal = false"
                        class="px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors"
                        style="border-radius: 2px;">
                    Cancel
                </button>
                <button @click="extendTime()"
                        :disabled="!extendMinutes || extendMinutes < 1 || actionLoading"
                        class="px-4 py-2 text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 transition-colors disabled:opacity-50"
                        style="border-radius: 2px;">
                    Extend
                </button>
            </div>
        </div>
    </div>
</template>
