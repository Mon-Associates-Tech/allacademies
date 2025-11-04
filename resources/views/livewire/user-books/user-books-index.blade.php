<div class="max-w-7xl mx-auto py-6">
    <div class="bg-white shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    Personal Book Collection
                </h3>
                <a href="{{ route('user-books.create') }}"
                   class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Upload New Book
                </a>
            </div>
        </div>

        <div class="">
            <livewire:user-books.shared-books />
        </div>
    </div>
</div>
