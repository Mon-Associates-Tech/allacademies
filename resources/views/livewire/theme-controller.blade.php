<!-- resources/views/livewire/theme-controller.blade.php -->
<div id="theme-controller">
    <!-- Dark Mode Toggle Button -->
    <button
        class="w-8 h-8 flex items-center justify-center bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600/80 rounded-full"
        wire:click="toggleDarkMode"
        aria-controls="dark-mode"
    >
        <span class="sr-only">Toggle dark mode</span>
        <svg class="w-4 h-4 fill-current text-gray-500 dark:text-gray-400 dark:hidden" xmlns="http://www.w3.org/2000/svg" width="16" height="16">
            <path d="M7 0h2v2H7V0ZM12.88 1.637l1.414 1.415-1.415 1.413-1.414-1.414 1.415-1.414ZM14 7h2v2h-2V7ZM12.95 14.433l-1.414-1.413 1.413-1.415 1.415 1.414-1.414 1.414ZM7 14h2v2H7v-2ZM2.98 14.363L1.566 12.95l1.415-1.414 1.414 1.415-1.414 1.413ZM0 7h2v2H0V7ZM3.05 1.707 4.465 3.12 3.05 4.535 1.636 3.12 3.05 1.707Z" />
            <path d="M8 4C5.8 4 4 5.8 4 8s1.8 4 4 4 4-1.8 4-4-1.8-4-4-4Z" />
        </svg>
        <svg class="w-4 h-4 fill-current text-gray-400 dark:text-gray-500 hidden dark:block" xmlns="http://www.w3.org/2000/svg" width="16" height="16">
            <path d="M6.2 1C3.2 1.8 1 4.6 1 7.9 1 11.8 4.2 15 8.1 15c3.3 0 6-2.2 6.9-5.2C9.7 11.2 4.8 6.3 6.2 1Z" />
            <path d="M12.5 5a.625.625 0 0 1-.625-.625.625.625 0 0 0-.625-.625.625.625 0 0 1 0-1.25.625.625 0 0 0 .625-.625.625.625 0 0 1 1.25 0 .625.625 0 0 0 .625.625.625.625 0 0 1 0 1.25.625.625 0 0 0-.625.625A.625.625 0 0 1 12.5 5Z" />
        </svg>
    </button>
</div>
