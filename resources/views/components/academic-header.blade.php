<div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-gray-800 dark:to-gray-900 px-4 sm:px-6 lg:px-8 py-6 border-b border-gray-200 dark:border-gray-700">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
        <div class="flex items-center">
            @if(isset($headerIcon))
          {!! $headerIcon !!}
            @else
                <div class="flex-shrink-0 w-12 h-12 sm:w-14 sm:h-14 bg-blue-100 dark:bg-gray-700 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 sm:w-7 sm:h-7 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-2M7 21H5m2 0h2m6-11V7h-2v3m0 0V7h2v3m-2 0h2v3m-2-3h-2v3"></path>
                    </svg>
                </div>            @endif
            <div class="ml-4 sm:ml-5">
                <!-- Title and subtitle content goes here -->
                @isset($headerContent)
                    {!! $headerContent !!}
                @else
                    <h1 class="text-xl sm:text-2xl font-bold text-gray-800 dark:text-white mb-1">@yield('header-title', 'Page Title')</h1>
                    <p class="text-gray-600 dark:text-gray-400 text-sm">@yield('header-subtitle', 'Page description')</p>
                @endisset
            </div>
        </div>

        <div class="mt-4 lg:mt-0 flex flex-col sm:flex-row sm:items-center sm:space-x-3">
            <!-- Action buttons go here -->
            @isset($headerActions)
                {!! $headerActions !!}
            @else
                @yield('header-actions')
            @endisset
        </div>
    </div>
</div>

