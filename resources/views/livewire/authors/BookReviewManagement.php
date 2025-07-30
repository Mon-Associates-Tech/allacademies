<div class="space-y-6">
    <!-- Header with Statistics -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Book Reviews</h1>
                <p class="text-gray-600 mt-1">Manage and respond to reviews for {{ $author->name }}'s books</p>
            </div>
            
            <!-- Quick Stats -->
            <div class="mt-4 lg:mt-0 grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="text-center">
                    <div class="text-2xl font-bold text-blue-600">{{ $reviewStats['total_reviews'] }}</div>
                    <div class="text-sm text-gray-500">Total Reviews</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-yellow-600">{{ $reviewStats['average_rating'] }}</div>
                    <div class="text-sm text-gray-500">Avg Rating</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-green-600">{{ $reviewStats['positive_reviews'] }}</div>
                    <div class="text-sm text-gray-500">Positive</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-purple-600">{{ $reviewStats['verified_reviews'] }}</div>
                    <div class="text-sm text-gray-500">Verified</div>
                </div>
            </div>
        </div>

        <!-- Rating Breakdown -->
        <div class="bg-gray-50 rounded-lg p-4">
            <h3 class="text-sm font-medium text-gray-900 mb-3">Rating Distribution</h3>
            <div class="space-y-2">
                @foreach($reviewStats['rating_breakdown'] as $rating => $count)
                    <div class="flex items-center">
                        <div class="flex items-center w-20">
                            <span class="text-sm font-medium w-2">{{ $rating }}</span>
                            <svg class="w-4 h-4 text-yellow-400 ml-1" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        </div>
                        <div class="flex-1 mx-3">
                            <div class="bg-gray-200 rounded-full h-2">
                                <div class="bg-yellow-400 h-2 rounded-full" 
                                     style="width: {{ $reviewStats['total_reviews'] > 0 ? ($count / $reviewStats['total_reviews']) * 100 : 0 }}%"></div>
                            </div>
                        </div>
                        <div class="text-sm text-gray-600 w-10">{{ $count }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
            <!-- Search -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Search Reviews</label>
                <input type="text" 
                       wire:model.live.debounce.300ms="search"
                       placeholder="Search by content, reviewer..."
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>

            <!-- Rating Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Rating</label>
                <select wire:model.live="ratingFilter" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="all">All Ratings</option>
                    <option value="5">5 Stars</option>
                    <option value="4">4 Stars</option>
                    <option value="3">3 Stars</option>
                    <option value="2">2 Stars</option>
                    <option value="1">1 Star</option>
                </select>
            </div>

            <!-- Book Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Book</label>
                <select wire:model.live="bookFilter" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="all">All Books</option>
                    @foreach($books as $book)
                        <option value="{{ $book->id }}">{{ $book->title }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Sort -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Sort By</label>
                <select wire:model.live="sortBy" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="latest">Latest First</option>
                    <option value="oldest">Oldest First</option>
                    <option value="rating_high">Highest Rating</option>
                    <option value="rating_low">Lowest Rating</option>
                    <option value="helpful">Most Helpful</option>
                </select>
            </div>
        </div>

        <div class="flex justify-between items-center">
            <button wire:click="resetFilters" 
                    class="text-sm text-gray-600 hover:text-gray-900">
                Reset Filters
            </button>
            <div class="text-sm text-gray-600">
                {{ $reviews->total() }} review{{ $reviews->total() !== 1 ? 's' : '' }} found
            </div>
        </div>
    </div>

    <!-- Reviews List -->
    @if($hasReviews)
        <div class="space-y-4">
            @foreach($reviews as $review)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-start justify-between">
                        <!-- Review Header -->
                        <div class="flex items-start space-x-4 flex-1">
                            <!-- Avatar -->
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-medium">
                                    {{ $review->reviewer_initials }}
                                </div>
                            </div>

                            <!-- Review Content -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h4 class="font-medium text-gray-900">{{ $review->reviewer_name }}</h4>
                                        <div class="flex items-center space-x-2 mt-1">
                                            <!-- Stars -->
                                            <div class="flex items-center">
                                                @foreach($review->stars as $star)
                                                    <svg class="w-4 h-4 {{ $star['filled'] ? 'text-yellow-400' : 'text-gray-300' }}" 
                                                         fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                    </svg>
                                                @endforeach
                                            </div>
                                            
                                            <!-- Badges -->
                                            @if($review->is_verified_purchase)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                    </svg>
                                                    Verified Purchase
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <div class="text-sm text-gray-500">
                                        {{ $review->time_ago }}
                                    </div>
                                </div>

                                <!-- Book Title -->
                                <div class="mt-2">
                                    <span class="text-sm text-gray-600">Review for:</span>
                                    <span class="text-sm font-medium text-blue-600 ml-1">{{ $review->book->title }}</span>
                                </div>

                                <!-- Review Title -->
                                @if($review->title)
                                    <h5 class="font-medium text-gray-900 mt-3">{{ $review->title }}</h5>
                                @endif

                                <!-- Review Content -->
                                <div class="mt-2 text-gray-700">
                                    <p>{{ $review->review }}</p>
                                </div>

                                <!-- Author Reply -->
                                @if($review->hasAuthorReply())
                                    <div class="mt-4 bg-blue-50 rounded-lg p-4 border-l-4 border-blue-200">
                                        <div class="flex items-center mb-2">
                                            <div class="w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center text-white text-xs font-medium">
                                                A
                                            </div>
                                            <span class="ml-2 text-sm font-medium text-blue-900">Author Response</span>
                                            <span class="ml-auto text-xs text-blue-600">{{ $review->author_reply_time_ago }}</span>
                                        </div>
                                        <p class="text-sm text-blue-800">{{ $review->author_reply }}</p>
                                    </div>
                                @endif

                                <!-- Actions -->
                                <div class="flex items-center justify-between mt-4 pt-4 border-t border-gray-200">
                                    <div class="flex items-center space-x-4">
                                        <!-- Helpful Button -->
                                        <button wire:click="toggleHelpful({{ $review->id }})"
                                                class="flex items-center space-x-1 text-sm {{ auth()->check() && $review->isHelpfulToUser(auth()->id()) ? 'text-blue-600' : 'text-gray-500 hover:text-blue-600' }}">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L9 7m-3 13H4a2 2 0 01-2-2v-6a2 2 0 012-2h6m3 0a6 6 0 11-6 6 2 2 0 002 2h3z"/>
                                            </svg>
                                            <span>Helpful ({{ $review->helpful_count }})</span>
                                        </button>

                                        <!-- Report Button -->
                                        <button wire:click="reportReview({{ $review->id }})"
                                                class="text-sm text-gray-500 hover:text-red-600">
                                            Report
                                        </button>
                                    </div>

                                    <div class="flex items-center space-x-2">
                                        <!-- Reply Button -->
                                        @if(!$review->hasAuthorReply())
                                            <button wire:click="openReplyModal({{ $review->id }})"
                                                    class="inline-flex items-center px-3 py-1 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                Reply
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $reviews->links() }}
        </div>
    @else
        <!-- Empty State -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No reviews found</h3>
            <p class="mt-1 text-sm text-gray-500">
                @if($search || $ratingFilter !== 'all' || $bookFilter !== 'all')
                    Try adjusting your filters to see more reviews.
                @else
                    Your books haven't received any reviews yet.
                @endif
            </p>
            @if($search || $ratingFilter !== 'all' || $bookFilter !== 'all')
                <button wire:click="resetFilters" 
                        class="mt-3 inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    Clear Filters
                </button>
            @endif
        </div>
    @endif

    <!-- Reply Modal -->
    @if($showReplyModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" 
             wire:click="closeReplyModal">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white"
                 wire:click.stop>
                <div class="mt-3">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Reply to Review</h3>
                    
                    <form wire:submit="submitReply">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Your Response</label>
                            <textarea wire:model="replyContent"
                                    rows="4"
                                    placeholder="Write your response to this review..."
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    required></textarea>
                            @error('replyContent')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="flex justify-end space-x-3">
                            <button type="button" 
                                    wire:click="closeReplyModal"
                                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200">
                                Cancel
                            </button>
                            <button type="submit"
                                    class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700">
                                Send Reply
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
