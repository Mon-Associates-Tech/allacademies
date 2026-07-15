{{-- MESSAGE MODAL --}}
<div x-show="showMessageModal"
     x-transition:enter="ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
     @click.self="showMessageModal = false"
     style="display: none;">
    <div class="bg-white dark:bg-slate-900 w-full max-w-md overflow-hidden rounded-lg shadow-xl">
        <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800">
            <h3 class="font-bold text-slate-900 dark:text-white">Send Message</h3>
            <p class="text-sm text-slate-500 mt-1" x-text="'To: ' + (selectedParticipant?.participant_name || '')"></p>
        </div>
        <div class="px-6 py-4">
            <textarea x-model="messageText"
                      rows="3"
                      placeholder="Type your message..."
                      class="w-full px-3 py-2 text-sm border border-slate-200 dark:border-slate-700 dark:bg-slate-800 focus:ring-2 focus:ring-indigo-500 rounded"></textarea>
        </div>
        <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/50 flex justify-end gap-3">
            <button @click="showMessageModal = false"
                    class="px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-200 rounded">
                Cancel
            </button>
            <button @click="sendMessage()"
                    :disabled="!messageText.trim() || actionLoading"
                    class="px-4 py-2 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded disabled:opacity-50">
                <span x-show="!actionLoading">Send Message</span>
                <span x-show="actionLoading">Sending...</span>
            </button>
        </div>
    </div>
</div>

{{-- WARNING MODAL --}}
<div x-show="showWarningModal"
     x-transition
     class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
     @click.self="showWarningModal = false"
     style="display: none;">
    <div class="bg-white dark:bg-slate-900 w-full max-w-md overflow-hidden rounded-lg shadow-xl">
        <div class="h-1 w-full bg-gradient-to-r from-yellow-500 to-yellow-400"></div>
        <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800">
            <h3 class="font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <svg class="w-5 h-5 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                Send Warning
            </h3>
            <p class="text-sm text-slate-500 mt-1" x-text="'To: ' + (selectedParticipant?.participant_name || '')"></p>
        </div>
        <div class="px-6 py-4">
            <textarea x-model="warningText"
                      rows="3"
                      placeholder="Warning message..."
                      class="w-full px-3 py-2 text-sm border border-slate-200 dark:border-slate-700 dark:bg-slate-800 focus:ring-2 focus:ring-yellow-500 rounded"></textarea>
            <p class="text-xs text-slate-500 mt-2">This warning will be displayed prominently to the participant.</p>
        </div>
        <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/50 flex justify-end gap-3">
            <button @click="showWarningModal = false" class="px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-200 rounded">
                Cancel
            </button>
            <button @click="sendWarning()"
                    :disabled="!warningText.trim() || actionLoading"
                    class="px-4 py-2 text-sm font-semibold text-white bg-yellow-500 hover:bg-yellow-600 rounded disabled:opacity-50">
                <span x-show="!actionLoading">Send Warning</span>
                <span x-show="actionLoading">Sending...</span>
            </button>
        </div>
    </div>
</div>

{{-- EXTEND TIME MODAL --}}
<div x-show="showExtendTimeModal"
     x-transition
     class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
     @click.self="showExtendTimeModal = false"
     style="display: none;">
    <div class="bg-white dark:bg-slate-900 w-full max-w-md overflow-hidden rounded-lg shadow-xl">
        <div class="h-1 w-full bg-gradient-to-r from-green-500 to-green-400"></div>
        <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800">
            <h3 class="font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <svg class="w-5 h-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Extend Time
            </h3>
            <p class="text-sm text-slate-500 mt-1" x-text="'For: ' + (selectedParticipant?.participant_name || '')"></p>
        </div>
        <div class="px-6 py-4">
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Additional Minutes</label>
            <input type="number" x-model="extendMinutes" min="1" max="120" 
                   class="w-full px-3 py-2 text-sm border border-slate-200 dark:border-slate-700 dark:bg-slate-800 focus:ring-2 focus:ring-green-500 rounded">
            <p class="text-xs text-slate-500 mt-2">
                Current extra time: <span x-text="selectedParticipant?.extra_time_minutes || 0"></span> minutes
            </p>
        </div>
        <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/50 flex justify-end gap-3">
            <button @click="showExtendTimeModal = false" class="px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-200 rounded">
                Cancel
            </button>
            <button @click="extendTime()"
                    :disabled="!extendMinutes || actionLoading"
                    class="px-4 py-2 text-sm font-semibold text-white bg-green-500 hover:bg-green-600 rounded disabled:opacity-50">
                <span x-show="!actionLoading">Extend Time</span>
                <span x-show="actionLoading">Extending...</span>
            </button>
        </div>
    </div>
</div>

{{-- TOAST NOTIFICATIONS --}}
<div x-show="toasts.length > 0" class="fixed bottom-4 right-4 z-50 space-y-2">
    <template x-for="toast in toasts" :key="toast.id">
        <div x-show="toast.visible"
             x-transition:enter="transform ease-out duration-300"
             x-transition:enter-start="translate-x-full opacity-0"
             x-transition:enter-end="translate-x-0 opacity-100"
             x-transition:leave="transform ease-in duration-200"
             x-transition:leave-start="translate-x-0 opacity-100"
             x-transition:leave-end="translate-x-full opacity-0"
             class="max-w-sm p-4 rounded-lg shadow-lg"
             :class="{
                 'bg-green-600 text-white': toast.type === 'success',
                 'bg-red-600 text-white': toast.type === 'error',
                 'bg-yellow-500 text-white': toast.type === 'warning',
                 'bg-blue-600 text-white': toast.type === 'info'
             }">
            <div class="flex items-start gap-3">
                <p class="text-sm font-medium" x-text="toast.message"></p>
                <button @click="removeToast(toast.id)" class="ml-auto text-white/70 hover:text-white">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </template>
</div>
