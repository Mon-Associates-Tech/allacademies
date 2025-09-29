<div class="max-w-6xl mx-auto">
    <div class="bg-white shadow-lg rounded-lg">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Messages</h1>
                <p class="text-sm text-gray-600 mt-1">Your sent and received messages</p>
            </div>
            <a href="{{ route('students.messages.compose') }}"
               class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700">
                Compose Message
            </a>
        </div>

        <!-- Search -->
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center">
                <div class="relative flex-grow max-w-md">
                    <input type="text"
                           wire:model.live.debounce.300ms="search"
                           placeholder="Search messages..."
                           class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Messages List -->
        <div class="divide-y divide-gray-200">
            @forelse($messages as $message)
                <div class="px-6 py-4 hover:bg-gray-50">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center min-w-0">
                            <div class="flex-shrink-0">
                                @if($message->is_urgent)
                                    <span
                                        class="inline-flex items-center justify-center h-6 w-6 rounded-full bg-red-100">
                                        <svg class="h-4 w-4 text-red-600" fill="none" viewBox="0 0 24 24"
                                             stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                        </svg>
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center justify-center h-6 w-6 rounded-full bg-gray-100">
                                        <svg class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24"
                                             stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                        </svg>
                                    </span>
                                @endif
                            </div>
                            <div class="ml-4 min-w-0">
                                <div class="flex items-baseline">
                                    <a href="{{ route('students.messages.show', $message) }}"
                                       class="text-sm font-medium text-gray-900 truncate">
                                        {{ $message->subject }}
                                    </a>
                                    @if($message->sender_id !== auth()->id() && !$message->readByUser(auth()->user()))
                                        <span
                                            class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                            New
                                        </span>
                                    @endif

                                </div>
                                <div class="mt-1 flex items-center text-sm text-gray-500">
                                    <span>
                                        @if($message->sender_id === auth()->id())
                                            To: {{ $message->recipients->take(3)->pluck('name')->join(', ') }}
                                            @if($message->recipients->count() > 3)
                                                and {{ $message->recipients->count() - 3 }} more
                                            @endif
                                        @else
                                            From: {{ $message->sender->name }}
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="ml-4 flex-shrink-0 flex items-center">
                            <div class="text-sm text-gray-500">
                                {{ $message->created_at->diffForHumans() }}
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="px-6 py-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No messages</h3>
                    <p class="mt-1 text-sm text-gray-500">Get started by composing a new message.</p>
                    <div class="mt-6">
                        <a href="{{ route('students.messages.compose') }}"
                           class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                            Compose Message
                        </a>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $messages->links() }}
        </div>
    </div>
</div>
