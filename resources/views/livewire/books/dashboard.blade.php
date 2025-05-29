<!-- resources/views/livewire/books/dashboard.blade.php -->
<div class="container mx-auto py-6">
    <div class="mb-6">
        <h1 class="text-3xl font-bold">Books Dashboard</h1>
        <p class="text-gray-600">Browse and subscribe to available books</p>
    </div>

    <!-- Search and Filters -->
    <div class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="col-span-1 md:col-span-2">
            <input
                type="text"
                placeholder="Search by title or author..."
                wire:model.live.debounce.300ms="search"
                class="w-full px-4 py-2 border rounded-md"
            >
        </div>

        <div>
            <select
                wire:model.live="category"
                class="w-full px-4 py-2 border rounded-md"
            >
                <option value="">All Categories</option>
                @foreach(\App\Models\BookCategory::all() as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <select
                wire:model.live="filterType"
                class="w-full px-4 py-2 border rounded-md"
            >
                <option value="all">All Books</option>
                <option value="free">Free Books</option>
                <option value="paid">Paid Books</option>
            </select>
        </div>
    </div>

    <!-- Books Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 xl:grid-cols-3 gap-6">
        @forelse($books as $book)
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="h-56 bg-gray-200 flex items-center justify-center">
                    @if($book->cover_image)
                        <img src="{{ asset('storage/' . $book->cover_image) }}" alt="{{ $book->title }}" class="h-full w-full object-cover">
                    @else
                        <div class="text-gray-400">
                            <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                    @endif
                </div>

                <div class="p-4">
                    <h3 class="text-lg font-semibold">{{ $book->title }}</h3>
                    <p class="text-gray-600">By {{ $book->author->user->name }}</p>
                    <p class="text-gray-500 text-sm">{{ $book->bookCategory?->name }}</p>

                    <div class="mt-3 flex items-center">
                        @if($book->is_free)
                            <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">Free</span>
                        @else
                            <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">${{ number_format($book->price, 2) }}</span>
                        @endif
                    </div>

                    <div class="mt-4">
                        @if(in_array($book->id, $subscribedBookIds))
                            <button class="w-full bg-green-500 text-white py-2 rounded-md" disabled>
                                Subscribed
                            </button>
                        @else
                            @if($book->is_free)
                                <button
                                    wire:click="subscribeToBook({{ $book->id }})"
                                    wire:loading.attr="disabled"
                                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-2 rounded-md"
                                >
                                    Subscribe
                                </button>
                            @else
                                <button
                                    wire:click="subscribeToBook({{ $book->id }})"
                                    wire:loading.attr="disabled"
                                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-2 rounded-md"
                                >
                                    Purchase
                                </button>
                            @endif

                            @if(count($userGroups) > 0)
                                <div class="mt-2 relative">
                                    <select
                                        class="w-full px-3 py-2 border rounded-md"
                                        id="group-select-{{ $book->id }}"
                                    >
                                        <option value="">Select a group</option>
                                        @foreach($userGroups as $group)
                                            <option value="{{ $group->id }}">{{ $group->name }}</option>
                                        @endforeach
                                    </select>

                                    <button
                                        wire:click="subscribeGroupToBook({{ $book->id }}, document.getElementById('group-select-{{ $book->id }}').value)"
                                        wire:loading.attr="disabled"
                                        class="mt-2 w-full bg-purple-600 hover:bg-purple-700 text-white py-2 rounded-md"
                                    >
                                        Subscribe for Group
                                    </button>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12">
                <p class="text-gray-500">No books found matching your criteria.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-6">
{{--        {{ $books->links() }}--}}
    </div>

    <!-- Notifications -->
    <div x-data="{ show: false, message: '' }" @book-subscribed.window="show = true; message = 'Successfully subscribed to book!'; setTimeout(() => show = false, 3000)" @group-subscribed.window="show = true; message = 'Group successfully subscribed to book!'; setTimeout(() => show = false, 3000)" x-show="show" x-transition class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-md shadow-lg">
        <p x-text="message"></p>
    </div>
</div>
