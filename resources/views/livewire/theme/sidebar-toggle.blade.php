<!-- resources/views/livewire/sidebar-toggle.blade.php -->
<button
    class="text-gray-500 hover:text-gray-600 dark:hover:text-gray-400 lg:hidden"
    wire:click="toggle"
    aria-controls="sidebar"
    aria-expanded="false"
>
    <span class="sr-only">Toggle sidebar</span>
    <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
        <rect x="4" y="5" width="16" height="2"/>
        <rect x="4" y="11" width="16" height="2"/>
        <rect x="4" y="17" width="16" height="2"/>
    </svg>
</button>
