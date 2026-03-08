<div class="space-y-6">
    <!-- Reviews Header with Stats -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                    User Reviews
                </h2>
                <div class="flex items-center space-x-4">
                    <div class="flex items-center">
                        <div class="flex items-center">
                            @for($i = 1; $i <= 5; $i++)
                                <svg
                                    class="w-5 h-5 {{ $i <= $book->average_rating ? 'text-yellow-400' : 'text-gray-300' }}"
                                    fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            @endfor
                        </div>
                        <span class="ml-2 text-lg font-semibold text-gray-900 dark:text-white">
                            {{ number_format($book->average_rating, 1) }}
                        </span>
                    </div>
                    <span class="text-gray-600 dark:text-gray-400">
                        Based on {{ $book->reviews->count() }} review{{ $book->reviews !== 1 ? 's' : '' }}
                    </span>
                </div>
            </div>

            <!-- Write Review Button -->
            @auth
                @if($canUserReview)
                    <button wire:click="toggleReviewForm"
                            class="mt-4 lg:mt-0 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Write a Review
                    </button>
                @elseif($userReview)
                    <button wire:click="toggleReviewForm"
                            class="mt-4 lg:mt-0 inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit Your Review
                    </button>
                @endif
            @else
                <a href="{{ route('login') }}"
                   class="mt-4 lg:mt-0 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    Sign in to Write Review
                </a>
            @endauth
        </div>

        <!-- Rating Distribution -->
        @if($book->reviews()->count() > 0)
            <div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="space-y-3">
                    @foreach($ratingDistribution as $dist)
                        <div class="flex items-center">
                            <div class="flex items-center w-16">
                                <span class="text-sm font-medium w-4">{{ $dist['rating'] }}</span>
                                <svg class="w-4 h-4 text-yellow-400 ml-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            </div>
                            <div class="flex-1 mx-3">
                                <div class="bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                    <div class="bg-yellow-400 h-2 rounded-full transition-all duration-300"
                                         style="width: {{ $dist['percentage'] }}%"></div>
                                </div>
                            </div>
                            <div class="text-sm text-gray-600 dark:text-gray-400 w-12 text-right">
                                {{ $dist['count'] }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <!-- Review Form Modal -->
    @if($showReviewForm)
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    {{ $editingReview ? 'Edit Your Review' : 'Write a Review' }}
                </h3>
                <button wire:click="toggleReviewForm"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form wire:submit="submitReview" class="space-y-4">
                <!-- Rating -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Rating <span class="text-red-500">*</span>
                    </label>
                    <div class="flex items-center space-x-1">
                        @for($i = 1; $i <= 5; $i++)
                            <button type="button"
                                    wire:click="setRating({{ $i }})"
                                    class="focus:outline-none">
                                <svg
                                    class="w-8 h-8 {{ $i <= $rating ? 'text-yellow-400' : 'text-gray-300' }} hover:text-yellow-400 transition-colors"
                                    fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            </button>
                        @endfor
                        <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">
                            {{ $rating > 0 ? $rating . ' star' . ($rating > 1 ? 's' : '') : 'Select rating' }}
                        </span>
                    </div>
                    @error('rating')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Review Title -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Review Title (Optional)
                    </label>
                    <input type="text"
                           wire:model="reviewTitle"
                           placeholder="Sum up your review in a few words"
                           class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    @error('reviewTitle')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Review Content -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Your Review <span class="text-red-500">*</span>
                    </label>
                    <textarea wire:model="reviewContent"
                              rows="6"
                              placeholder="Share your thoughts about this book..."
                              class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"></textarea>
                    <div class="mt-1 flex justify-between">
                        @error('reviewContent')
                        <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @else
                            <p class="text-sm text-gray-500">Minimum 10 characters</p>
                            @enderror
                            <p class="text-sm text-gray-500">{{ strlen($reviewContent) }}/2000</p>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-between pt-4">
                    <div>
                        @if($editingReview)
                            <button type="button"
                                    wire:click="deleteReview"
                                    wire:confirm="Are you sure you want to delete your review?"
                                    class="inline-flex items-center px-4 py-2 border border-red-300 text-sm font-medium rounded-md text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Delete Review
                            </button>
                        @endif
                    </div>
                    <div class="flex space-x-3">
                        <button type="button"
                                wire:click="toggleReviewForm"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600">
                            Cancel
                        </button>
                        <button type="submit"
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            {{ $editingReview ? 'Update Review' : 'Submit Review' }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    @endif

    <!-- Filters and Sorting -->
    @if($book->total_reviews > 0)
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-3 sm:space-y-0">
                <div class="flex items-center space-x-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Filter by
                            Rating</label>
                        <select wire:model.live="filterByRating"
                                class="rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option value="all">All Ratings</option>
                            <option value="5">5 Stars</option>
                            <option value="4">4 Stars</option>
                            <option value="3">3 Stars</option>
                            <option value="2">2 Stars</option>
                            <option value="1">1 Star</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Sort By</label>
                    <select wire:model.live="sortBy"
                            class="rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="newest">Newest First</option>
                        <option value="oldest">Oldest First</option>
                        <option value="rating_high">Highest Rating</option>
                        <option value="rating_low">Lowest Rating</option>
                        <option value="helpful">Most Helpful</option>
                    </select>
                </div>
            </div>
        </div>
    @endif

    <!-- Reviews List -->
    @if($reviews->count() > 0)
        <div class="space-y-4">
            @foreach($reviews as $review)
                <div
                    class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-start space-x-4">
                        <!-- Avatar -->
                        <div class="flex-shrink-0">
                            <div
                                class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center text-white font-semibold text-lg">
                                {{ $review->reviewer_initials }}
                            </div>
                        </div>

                        <!-- Review Content -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="font-semibold text-gray-900 dark:text-white">{{ $review->reviewer_name }}</h4>
                                    <div class="flex items-center mt-1 space-x-2">
                                        <!-- Stars -->
                                        <div class="flex items-center">
                                            @foreach($review->stars as $star)
                                                <svg
                                                    class="w-4 h-4 {{ $star['filled'] ? 'text-yellow-400' : 'text-gray-300' }}"
                                                    fill="currentColor" viewBox="0 0 20 20">
                                                    <path
                                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                </svg>
                                            @endforeach
                                        </div>

                                        <!-- Verified Purchase Badge -->
                                        @if($review->is_verified_purchase)
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                          d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                          clip-rule="evenodd"/>
                                                </svg>
                                                Verified Purchase
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $review->time_ago }}
                                </div>
                            </div>

                            <!-- Review Title -->
                            @if($review->title)
                                <h5 class="font-medium text-gray-900 dark:text-white mt-3">{{ $review->title }}</h5>
                            @endif

                            <!-- Review Content -->
                            <div class="mt-2 text-gray-700 dark:text-gray-300">
                                <p class="leading-relaxed">{{ $review->review }}</p>
                            </div>

                            <!-- Author Reply -->
                            @if($review->hasAuthorReply())
                                <div class="mt-4 bg-blue-50 dark:bg-blue-900 rounded-lg p-4 border-l-4 border-blue-200">
                                    <div class="flex items-center mb-2">
                                        <div
                                            class="w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center text-white text-xs font-medium">
                                            A
                                        </div>
                                        <span class="ml-2 text-sm font-medium text-blue-900 dark:text-blue-200">Author Response</span>
                                        <span
                                            class="ml-auto text-xs text-blue-600 dark:text-blue-400">{{ $review->author_reply_time_ago }}</span>
                                    </div>
                                    <p class="text-sm text-blue-800 dark:text-blue-300">{{ $review->author_reply }}</p>
                                </div>
                            @endif

                            <!-- Review Actions -->
                            <div
                                class="flex items-center justify-between mt-4 pt-4 border-t border-gray-200 dark:border-gray-600">
                                <div class="flex items-center space-x-4">
                                    <!-- Helpful Button -->
                                    <button wire:click="toggleHelpful({{ $review->id }})"
                                            class="flex items-center space-x-1 text-sm {{ auth()->check() && $review->isHelpfulToUser(auth()->id()) ? 'text-blue-600' : 'text-gray-500 hover:text-blue-600' }} transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L9 7m-3 13H4a2 2 0 01-2-2v-6a2 2 0 012-2h6m3 0a6 6 0 11-6 6 2 2 0 002 2h3z"/>
                                        </svg>
                                        <span>Helpful ({{ $review->helpful_count }})</span>
                                    </button>
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

        <!-- Load More Button (Alternative to pagination) -->
        @if($reviews->hasMorePages())
            <div class="text-center">
                <button wire:click="loadMoreReviews"
                        class="inline-flex items-center px-6 py-3 border border-gray-300 shadow-sm text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600">
                    Load More Reviews
                </button>
            </div>
        @endif
    @else
        <!-- No Reviews State -->
        <div
            class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No reviews yet</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                @if($filterByRating !== 'all')
                    No reviews found with the selected rating filter.
                @else
                    Be the first to share your thoughts about this book.
                @endif
            </p>
            @if($filterByRating !== 'all')
                <button wire:click="$set('filterByRating', 'all')"
                        class="mt-3 inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    Show All Reviews
                </button>
            @endif
        </div>
    @endif

    <!-- Success Message -->
    @if(session()->has('message'))
        <div x-data="{ show: true }"
             x-show="show"
             x-transition
             x-init="setTimeout(() => show = false, 5000)"
             class="fixed bottom-4 right-4 z-50 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded shadow-lg">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                          d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                          clip-rule="evenodd"/>
                </svg>
                {{ session('message') }}
                <button @click="show = false" class="ml-2 text-green-500 hover:text-green-700">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                              d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                              clip-rule="evenodd"/>
                    </svg>
                </button>
            </div>
        </div>
    @endif
</div>
