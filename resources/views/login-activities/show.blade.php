<x-layouts.app>

    <div class="flex justify-between items-center">
        <div class="flex items-center space-x-4">
            <a href="{{ route('users.index') }}" class="text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Login Activity - {{ $user->name }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Complete session history and statistics
                </p>
            </div>
        </div>
    </div>


    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @livewire('users.user-activity-details', ['user' => $user])
        </div>
    </div>
</x-layouts.app>
