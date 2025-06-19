<!-- resources/views/books/show.blade.php -->
@extends('layouts.app')

@section('header')
    {{ __('Book Details') }}
@endsection

@section('content')
    <div class="flex justify-between mb-6">
        <h3 class="text-lg font-medium text-gray-900">{{ $book->title }}</h3>
        <div class="flex space-x-2">
            @can('update', $book)
            <a href="{{ route('books.edit', $book) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                Edit
            </a>
            @endcan
            
            @can('delete', $book)
            <form action="{{ route('books.destroy', $book) }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" onclick="return confirm('Are you sure you want to delete this book?')" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-900 focus:outline-none focus:border-red-900 focus:ring ring-red-300 disabled:opacity-25 transition ease-in-out duration-150">
                    Delete
                </button>
            </form>
            @endcan
            
            <a href="{{ route('books.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:ring ring-blue-200 active:text-gray-800 active:bg-gray-50 disabled:opacity-25 transition ease-in-out duration-150">
                Back to List
            </a>
        </div>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Book Information</h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">Details and availability.</p>
        </div>
        <div class="border-t border-gray-200">
            <dl>
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Title</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $book->title }}</dd>
                </div>
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Author</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $book->author->user->name }}</dd>
                </div>
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Category</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $book->category->name }}</dd>
                </div>
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Edition</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $book->edition ?? 'Not specified' }}</dd>
                </div>
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Publisher</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $book->publisher ?? 'Not specified' }}</dd>
                </div>
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Number of Pages</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $book->pages ?? 'Not specified' }}</dd>
                </div>
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Format</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        @if($book->has_hardcopy && $book->has_softcopy)
                            Both hardcopy and softcopy available
                        @elseif($book->has_hardcopy)
                            Hardcopy only
                        @elseif($book->has_softcopy)
                            Softcopy only
                        @endif
                    </dd>
                </div>
                @if($book->additional_info)
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Additional Information</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $book->additional_info }}</dd>
                </div>
                @endif
            </dl>
        </div>
    </div>

    <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Borrowing Status</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">Current and past borrowings.</p>
            </div>
            <div class="border-t border-gray-200">
                <ul class="divide-y divide-gray-200">
                    @forelse($book->borrowings->where('status', 'borrowed') as $borrowing)
                        <li class="px-4 py-3">
                            <div class="flex justify-between">
                                <div>
                                    <p class="text-sm font-medium text-indigo-600">{{ $borrowing->student->user->name }}</p>
                                    <p class="text-xs text-gray-500">Borrowed: {{ $borrowing->borrow_date }}</p>
                                    <p class="text-xs text-gray-500">Due: {{ $borrowing->due_date }}</p>
                                </div>
                                <div>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        Active
                                    </span>
                                </div>
                            </div>
                        </li>
                    @empty
                        <li class="px-4 py-3 text-sm text-gray-500">No active borrowings.</li>
                    @endforelse
                </ul>
            </div>
        </div>

        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Subscription Status</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">Individual and group subscriptions.</p>
            </div>
            <div class="border-t border-gray-200">
                <ul class="divide-y divide-gray-200">
                    @forelse($book->subscriptions->where('status', 'active')->take(5) as $subscription)
                        <li class="px-4 py-3">
                            <div class="flex justify-between">
                                <div>
                                    <p class="text-sm font-medium text-indigo-600">{{ $subscription->student->user->name }}</p>
                                    <p class="text-xs text-gray-500">From: {{ $subscription->start_date }}</p>
                                    <p class="text-xs text-gray-500">To: {{ $subscription->end_date }}</p>
                                </div>
                                <div>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Individual
                                    </span>
                                </div>
                            </div>
                        </li>
                    @empty
                        @if($book->groupSubscriptions->where('status', 'active')->isEmpty())
                            <li class="px-4 py-3 text-sm text-gray-500">No active subscriptions.</li>
                        @endif
                    @endforelse

                    @foreach($book->groupSubscriptions->where('status', 'active')->take(5) as $groupSubscription)
                        <li class="px-4 py-3">
                            <div class="flex justify-between">
                                <div>
                                    <p class="text-sm font-medium text-indigo-600">{{ $groupSubscription->studentGroup->name }} (Group)</p>
                                    <p class="text-xs text-gray-500">From: {{ $groupSubscription->start_date }}</p>
                                    <p class="text-xs text-gray-500">To: {{ $groupSubscription->end_date }}</p>
                                </div>
                                <div>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                        Group
                                    </span>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
@endsection