{{-- resources/views/livewire/subscribers/library.blade.php --}}
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">My Library</h1>
    </div>

    <!-- Filters -->
    <div class="mb-6 bg-white dark:bg-gray-800 rounded-lg shadow p-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <input wire:model.live="search" type="text" placeholder="Search books..." 
                       class="w-full px-3 py-2 border rounded-md">
            </div>
            <div>
                <select wire:model.live="category" class="w-full px-3 py-2 border rounded-md">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <select wire:model.live="status" class="w-full px-3 py-2 border rounded-md">
                    <option value="all">All Books</option>
                    <option value="subscribed">My Subscriptions</option>
                    <option value="free">Free Books</option>
                </select>
            </div>
            <div>
                <select wire:model.live="sortBy" class="w-full px-3 py-2 border rounded-md">
                    <option value="title">Sort by Title</option>
                    <option value="created_at">Sort by Date</option>
                    <option value="author_id">Sort by Author</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Books Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @foreach($books as $book)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
                <div class="aspect-w-3 aspect-h-4">
                    <img src="{{ $book->cover_image }}" alt="{{ $book->title }}" 
                         class="w-full h-48 object-cover">
                </div>
                <div class="p-4">
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-2">{{ $book->title }}</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                        {{ $book->author->user->name ?? 'Unknown Author' }}
                    </p>
                    <div class="flex justify-between items-center">
                        @if(in_array($book->id, $subscribedBookIds))
                            <span class="text-green-600 text-sm font-medium">Subscribed</span>
                            <a href="{{ route('books.read', $book) }}" 
                               class="bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700">
                                Read
                            </a>
                        @elseif($book->is_free)
                            <span class="text-blue-600 text-sm font-medium">Free</span>
                            <a href="{{ route('books.read', $book) }}" 
                               class="bg-green-600 text-white px-3 py-1 rounded text-sm hover:bg-green-700">
                                Read Free
                            </a>
                        @else
                            <span class="text-gray-600 text-sm">${{ $book->annual_subscription_fee }}</span>
                            <a href="{{ route('books.show', $book) }}" 
                               class="bg-violet-600 text-white px-3 py-1 rounded text-sm hover:bg-violet-700">
                                Subscribe
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="mt-8">
        {{ $books->links() }}
    </div>
</div>
