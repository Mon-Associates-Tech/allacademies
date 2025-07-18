@if(session()->has('impersonated_by'))
    <div class="bg-red-600 text-white px-4 py-2  z-50 shadow-lg">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.314 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
                <span class="font-semibold">You are impersonating: {{ auth()->user()->name }} ({{ auth()->user()->email }})</span>
            </div>
            <a href="{{ route('impersonate.leave') }}"
               class="inline-flex items-center px-3 py-1 border border-red-300 text-sm font-medium rounded-md text-red-100 hover:bg-red-700 transition-colors">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                Stop Impersonating
            </a>
        </div>
    </div>
@endif
