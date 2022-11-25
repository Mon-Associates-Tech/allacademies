<x-layout>
    <x-dashboard title="Dashboard" summary="Quick overview of everything">
        <div class="flex items-center justify-between">
            <p class="text-gray-800 text-lg">👋 Welcome, {{ auth()->user()->name }}</p>
            <x-button>New Subscription</x-button>
        </div>

    </x-dashboard>
</x-layout>