<div class="space-y-6">
    @if($borrowedBooks && $borrowedBooks->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($borrowedBooks as $borrow)
                @php $book = $borrow->book; @endphp
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-lg transition-all duration-300 group">
                    <!-- Book Cover -->
                    <div class="relative h-48 bg-gradient-to-br from-orange-50 to-red-50 dark:from-gray-700 dark:to-gray-600 flex items-center justify-center">
                        @if($book->cover_image)
                            <img src="{{ asset('storage/' . $book->cover_image) }}"
                                 alt="{{ $book->title }}"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        @else
                            <div class="text-center p-4">
                                <svg class="w-16 h-16 text-orange-300 dark:text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                                <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">{{ $book->title }}</p>
                            </div>
                        @endif

                        <!-- Status Badge -->
                        <div class="absolute top-3 right-3">
                            @php
                                $statusColors = [
                                    'borrowed' => 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200',
                                    'overdue' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                                    'returned' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                                    'requested' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200'
                                ];
                                $statusIcons = [
                                    'borrowed' => '📚',
                                    'overdue' => '⚠️',
                                    'returned' => '✅',
                                    'requested' => '⏳'
                                ];
                            @endphp
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $statusColors[$borrow->status] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ $statusIcons[$borrow->status] ?? '📖' }} {{ ucfirst($borrow->status) }}
                            </span>
                        </div>

                        <!-- Due Date Warning -->
                        @if($borrow->due_date && $borrow->due_date->isPast() && $borrow->status === 'borrowed')
                            <div class="absolute top-12 right-3">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                    Overdue
                                </span>
                            </div>
                        @elseif($borrow->due_date && $borrow->due_date->diffInDays() <= 3 && $borrow->status === 'borrowed')
                            <div class="absolute top-12 right-3">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                    Due Soon
                                </span>
                            </div>
                        @endif
                    </div>

                    <!-- Book Details -->
                    <div class="p-4 space-y-3">
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-gray-100 text-sm line-clamp-2 group-hover:text-orange-600 dark:group-hover:text-orange-400 transition-colors">
                                {{ $book->title }}
                            </h3>
                            @if($book->author)
                                <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">by {{ $book->author }}</p>
                            @endif
                        </div>

                        <!-- Borrow Info -->
                        <div class="space-y-2 text-xs">
                            <div class="flex items-center justify-between">
                                <span class="text-gray-500 dark:text-gray-400">Borrowed:</span>
                                <span class="font-medium text-gray-900 dark:text-gray-100">
                                    {{ $borrow->borrowed_at ? $borrow->borrowed_at->format('M j, Y') : 'Pending' }}
                                </span>
                            </div>

                            @if($borrow->due_date)
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-500 dark:text-gray-400">Due Date:</span>
                                    <span class="font-medium {{ $borrow->due_date->isPast() && $borrow->status === 'borrowed' ? 'text-red-600 dark:text-red-400' : ($borrow->due_date->diffInDays() <= 3 ? 'text-yellow-600 dark:text-yellow-400' : 'text-gray-900 dark:text-gray-100') }}">
                                        {{ $borrow->due_date->format('M j, Y') }}
                                    </span>
                                </div>
                            @endif

                            @if($borrow->returned_at)
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-500 dark:text-gray-400">Returned:</span>
                                    <span class="font-medium text-green-600 dark:text-green-400">
                                        {{ $borrow->returned_at->format('M j, Y') }}
                                    </span>
                                </div>
                            @endif

                            @if($borrow->due_date && $borrow->status === 'borrowed')
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-500 dark:text-gray-400">Days Left:</span>
                                    <span class="font-medium {{ $borrow->due_date->isPast() ? 'text-red-600 dark:text-red-400' : ($borrow->due_date->diffInDays() <= 3 ? 'text-yellow-600 dark:text-yellow-400' : 'text-gray-900 dark:text-gray-100') }}">
                                        @if($borrow->due_date->isPast())
                                            {{ $borrow->due_date->diffInDays() }} days overdue
                                        @else
                                            {{ $borrow->due_date->diffInDays() }} days
                                        @endif
                                    </span>
                                </div>
                            @endif
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center justify-between pt-2 space-x-2">
                            @if($borrow->status === 'borrowed')
                                <button wire:click="returnBook({{ $borrow->id }})"
                                        onclick="return confirm('Mark this book as returned?')"
                                        class="flex-1 inline-flex items-center justify-center px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-xs font-medium rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-1">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Return
                                </button>

                                @if($borrow->due_date && $borrow->due_date->diffInDays() <= 7)
                                    <button wire:click="requestExtension({{ $borrow->id }})"
                                            class="inline-flex items-center px-3 py-1.5 border border-blue-300 dark:border-blue-600 text-blue-700 dark:text-blue-400 text-xs font-medium rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        Extend
                                    </button>
                                @endif
                            @elseif($borrow->status === 'requested')
                                <span class="flex-1 inline-flex items-center justify-center px-3 py-1.5 bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 text-xs font-medium rounded-lg">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Pending Approval
                                </span>

                                <button wire:click="cancelBorrowRequest({{ $borrow->id }})"
                                        onclick="return confirm('Cancel this borrow request?')"
                                        class="inline-flex items-center px-3 py-1.5 border border-red-300 dark:border-red-600 text-red-700 dark:text-red-400 text-xs font-medium rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-1">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                    Cancel
                                </button>
                            @elseif($borrow->status === 'returned')
                                <span class="flex-1 inline-flex items-center justify-center px-3 py-1.5 bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 text-xs font-medium rounded-lg">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    Returned
                                </span>

                                <button wire:click="borrowAgain({{ $book->id }})"
                                        class="inline-flex items-center px-3 py-1.5 border border-orange-300 dark:border-orange-600 text-orange-700 dark:text-orange-400 text-xs font-medium rounded-lg hover:bg-orange-50 dark:hover:bg-orange-900/20 transition-colors focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-1">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                    </svg>
                                    Borrow Again
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if($borrowedBooks->hasPages())
            <div class="mt-6">
                {{ $borrowedBooks->links() }}
            </div>
        @endif
    @else
        <!-- Empty State -->
        <div class="text-center py-12">
            <div class="mx-auto w-24 h-24 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4">
                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414A1 1 0 0120 8.414V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"/>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">No borrowed books</h3>
            <p class="text-gray-600 dark:text-gray-400 mb-4">
                You haven't borrowed any physical books yet. Subscribe to books first, then request to borrow them.
            </p>
            <button wire:click="changeTab('subscribed')"
                    class="inline-flex items-center px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white text-sm font-medium rounded-lg transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                View Subscriptions
            </button>
        </div>
    @endif
</div>
