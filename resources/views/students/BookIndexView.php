<!-- resources/views/books/index.blade.php -->
@extends('layouts.app')

@section('header')
    {{ __('Books') }}
@endsection

@section('content')
    <div class="flex justify-between mb-6">
        <h3 class="text-lg font-medium text-gray-900">All Books</h3>
        <div class="flex space-x-2">
            @can('create', App\Models\Book::class)
            <a href="{{ route('books.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                Add New Book
            </a>
            @endcan
            <a href="{{ route('book-categories.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:ring ring-blue-200 active:text-gray-800 active:bg-gray-50 disabled:opacity-25 transition ease-in-out duration-150">
                View Categories
            </a>
        </div>
    </div>

    <div class="mb-6">
        <form action="{{ route('books.index') }}" method="GET" class="flex flex-wrap gap-4">
            <div class="flex-grow">
                <label for="search" class="sr-only">Search</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Search by title..." class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
            </div>
            <div>
                <label for="category_id" class="sr-only">Category</label>
                <select id="category_id" name="category_id" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                    <option value="">All Categories</option>
                    @foreach(\App\Models\BookCategory::all() as $category)
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="format" class="sr-only">Format</label>
                <select id="format" name="format" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                    <option value="">All Formats</option>
                    <option value="hardcopy" {{ request('format') == 'hardcopy' ? 'selected' : '' }}>Hardcopy</option>
                    <option value="softcopy" {{ request('format') == 'softcopy' ? 'selected' : '' }}>Softcopy</option>
                    <option value="both" {{ request('format') == 'both' ? 'selected' : '' }}>Both</option>
                </select>
            </div>
            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Filter
            </button>
            @if(request()->hasAny(['search', 'category_id', 'format']))
            <a href="{{ route('books.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Clear
            </a>
            @endif
        </form>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @forelse($books as $book)
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h4 class="text-lg font-semibold text-gray-900 truncate">{{ $book->title }}</h4>
                    <p class="mt-1 text-sm text-gray-600">
                        By {{ $book->author->user->name }}
                    </p>
                    <p class="mt-1 text-sm text-gray-500">
                        {{ $book->edition ? 'Edition: ' . $book->edition : '' }}
                        {{ $book->publisher ? ($book->edition ? ' • ' : '') . 'Publisher: ' . $book->publisher : '' }}
                    </p>
                    <div class="mt-2 flex items-center">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                            {{ $book->category->name }}
                        </span>
                        <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $book->has_hardcopy ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            Hardcopy {{ $book->has_hardcopy ? '✓' : '✗' }}
                        </span>
                        <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $book->has_softcopy ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            Softcopy {{ $book->has_softcopy ? '✓' : '✗' }}
                        </span>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('books.show', $book) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                            View Details <span aria-hidden="true">&rarr;</span>
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <p class="text-sm text-gray-500">No books found matching your criteria.</p>
                </div>
            </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $books->links() }}
    </div>
@endsection