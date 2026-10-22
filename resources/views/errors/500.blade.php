@if(Auth::check())
<x-layouts.app>
    <div class="flex min-h-[60vh] items-center justify-center px-4">
        <div class="text-center max-w-md">
            <h1 class="text-6xl font-light text-gray-300 dark:text-gray-700">500</h1>
            <p class="mt-4 text-xl font-medium text-gray-900 dark:text-white">Something went wrong</p>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                We're sorry, but something went wrong on our end. Please try again later.
            </p>
            <div class="mt-8 flex justify-center gap-3">
                <button onclick="history.back()" class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition-colors">
                    ← Go back
                </button>
                <a href="{{ route('dashboard') }}" class="px-4 py-2 text-sm bg-indigo-600 hover:bg-indigo-700 text-white rounded-md transition-colors">
                    Back to dashboard
                </a>
            </div>
        </div>
    </div>
</x-layouts.app>
@else
<x-layouts.guest>
    <div class="flex min-h-[60vh] items-center justify-center px-4">
        <div class="text-center max-w-md">
            <h1 class="text-6xl font-light text-gray-300 dark:text-gray-700">500</h1>
            <p class="mt-4 text-xl font-medium text-gray-900 dark:text-white">Something went wrong</p>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                We're sorry, but something went wrong on our end. Please try again later.
            </p>
            <div class="mt-8 flex justify-center gap-3">
                <button onclick="history.back()" class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition-colors">
                    ← Go back
                </button>
                <a href="{{ route('home') }}" class="px-4 py-2 text-sm bg-indigo-600 hover:bg-indigo-700 text-white rounded-md transition-colors">
                    Back to home
                </a>
            </div>
        </div>
    </div>
</x-layouts.guest>
@endif
