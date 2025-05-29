<div>
    <h1 class="text-2xl font-bold mb-6">Book Management</h1>

    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
            {{ session('error') }}
        </div>
    @endif

    <!-- Book Form -->
    <div class="mb-8 bg-white p-4 rounded shadow">
        <h2 class="text-lg font-semibold mb-4">{{ $isEditing ? 'Edit Book' : 'Create New Book' }}</h2>

        <form wire:submit.prevent="{{ $isEditing ? 'update' : 'create' }}">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                    <input type="text" wire:model="title" class="w-full p-2 border rounded">
                    @error('title') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Slug</label>
                    <input type="text" wire:model="slug" class="w-full p-2 border rounded bg-gray-50" readonly>
                    @error('slug') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Author</label>
                    <select wire:model="authorId" class="w-full p-2 border rounded">
                        <option value="">-- Select Author --</option>
                        @foreach($authors as $author)
                            <option value="{{ $author->id }}">{{ $author->user->name }}</option>
                        @endforeach
                    </select>
                    @error('authorId') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                    <select wire:model="bookCategoryId" class="w-full p-2 border rounded">
                        <option value="">-- Select Category --</option>
                        @foreach($bookCategories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('bookCategoryId') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Edition</label>
                    <input type="text" wire:model="edition" class="w-full p-2 border rounded">
                    @error('edition') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Publisher</label>
                    <input type="text" wire:model="publisher" class="w-full p-2 border rounded">
                    @error('publisher') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pages</label>
                    <input type="number" wire:model="pages" class="w-full p-2 border rounded">
                    @error('pages') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="flex space-x-6">
                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" wire:model="hasHardcopy" class="mr-2">
                            <span class="text-sm font-medium text-gray-700">Has Hardcopy</span>
                        </label>
                        @error('hasHardcopy') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" wire:model="hasSoftcopy" class="mr-2">
                            <span class="text-sm font-medium text-gray-700">Has Softcopy</span>
                        </label>
                        @error('hasSoftcopy') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Additional Information</label>
                    <textarea wire:model="additionalInfo" rows="3" class="w-full p-2 border rounded"></textarea>
                    @error('additionalInfo') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cover Image</label>
                    <input type="file" wire:model="coverImage" class="w-full p-2 border rounded">
                    @error('coverImage') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

                    @if ($isEditing && $existingCover)
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">Current cover:</p>
                            <img src="{{ Storage::url($existingCover) }}" alt="Book Cover" class="h-24 object-cover mt-1">
                        </div>
                    @endif

                    @if ($coverImage)
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">New cover preview:</p>
                            <img src="{{ $coverImage->temporaryUrl() }}" alt="Cover Preview" class="h-24 object-cover mt-1">
                        </div>
                    @endif
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">PDF File (for softcopy)</label>
                    <input type="file" wire:model="pdfFile" class="w-full p-2 border rounded">
                    @error('pdfFile') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

                    @if ($isEditing && $existingPdf)
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">Current PDF:
                                <a href="{{ Storage::url($existingPdf) }}" target="_blank" class="text-blue-500 hover:underline">View PDF</a>
                            </p>
                        </div>
                    @endif

                    @if ($pdfFile)
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">New PDF file selected</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="mt-4 flex space-x-2">
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                    {{ $isEditing ? 'Update Book' : 'Create Book' }}
                </button>

                @if($isEditing)
                    <button type="button" wire:click="resetForm" class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">
                        Cancel
                    </button>
                @endif
            </div>
        </form>
    </div>

    <!-- Books List -->
    <div>
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold">Books List</h2>

            <div>
                <input type="text" wire:model.debounce.300ms="searchTerm" placeholder="Search books..."
                    class="p-2 border rounded">
            </div>
        </div>

        <div class="bg-white shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Book</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Author</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Formats</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usage</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($books as $book)
                        <tr>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    @if($book->cover_image_path)
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <img class="h-10 w-10 object-cover" src="{{ Storage::url($book->cover_image_path) }}" alt="{{ $book->title }}">
                                        </div>
                                    @else
                                        <div class="flex-shrink-0 h-10 w-10 bg-gray-200 flex items-center justify-center">
                                            <span class="text-gray-500">Book</span>
                                        </div>
                                    @endif
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $book->title }}</div>
                                        <div class="text-xs text-gray-500">{{ $book->edition ?? 'No Edition' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $book->author->user->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $book->bookCategory->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="space-y-1">
                                    @if($book->has_hardcopy)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Hardcopy</span>
                                    @endif

                                    @if($book->has_softcopy)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Softcopy</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-xs text-gray-500">
                                    <div>Borrowings: {{ $book->borrowings->count() }}</div>
                                    <div>Subscriptions: {{ $book->subscriptions->count() }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button wire:click="edit({{ $book->id }})" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</button>
                                <button wire:click="delete({{ $book->id }})" class="text-red-600 hover:text-red-900"
                                        onclick="return confirm('Are you sure you want to delete this book?')">Delete</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $books->links() }}
        </div>
    </div>
</div>
