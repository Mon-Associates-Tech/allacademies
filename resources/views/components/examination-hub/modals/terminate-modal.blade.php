{{-- Terminate Modal Component --}}
<template x-teleport="body">
    <div x-show="showTerminateModal"
         x-transition:enter="ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
         @click.self="showTerminateModal = false">
        <div class="bg-white dark:bg-slate-900 w-full max-w-md overflow-hidden"
             style="border-radius: 2px; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);">
            <div class="h-1 w-full bg-gradient-to-r from-red-600 to-red-400"></div>
            <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800">
                <h3 class="font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <x-heroicon-o-x-circle class="w-5 h-5 text-red-500" />
                    Terminate Session
                </h3>
                <p class="text-sm text-slate-500 mt-1" x-text="'Participant: ' + (selectedParticipant?.participant_name || '')"></p>
            </div>
            <div class="px-6 py-4">
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Reason <span class="text-red-500">*</span></label>
                <textarea x-model="terminateReason"
                          rows="3"
                          placeholder="Enter a reason (required)..."
                          class="w-full px-3 py-2 text-sm border border-slate-200 dark:border-slate-700 dark:bg-slate-800 focus:ring-2 focus:ring-red-500"
                          style="border-radius: 2px;"></textarea>
            </div>
            <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/50 flex justify-end gap-3">
                <button @click="showTerminateModal = false"
                        class="px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors"
                        style="border-radius: 2px;">
                    Cancel
                </button>
                <button @click="executeTerminate()"
                        :disabled="!terminateReason.trim() || actionLoading"
                        class="px-4 py-2 text-sm font-semibold text-white bg-red-600 hover:bg-red-700 transition-colors disabled:opacity-50"
                        style="border-radius: 2px;">
                    Terminate
                </button>
            </div>
        </div>
    </div>
</template>
