<div>
    <h1 class="text-2xl font-bold mb-6">Author Management</h1>

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

    <!-- Author Form -->
    <div class="mb-8 bg-white p-4 rounded shadow">
        <h2 class="text-lg font-semibold mb-4">{{ $isEditing ? 'Edit Author' : 'Create New Author' }}</h2>

        <form wire:submit.prevent="{{ $isEditing ? 'update' : 'create' }}">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                    <input type="text" wire:model="name" class="w-full p-2 border rounded">
                    @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" wire:model="email" class="w-full p-2 border rounded">
                    @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password {{ $isEditing ? '(leave blank to keep current)' : '' }}</label>
                    <input type="password" wire:model="password" class="w-full p-2 border rounded">
                    @error('password') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Profile Photo</label>
                    <input type="file" wire:model="profilePhoto" class="w-full p-2 border rounded">
                    @error('profilePhoto') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

                    @if ($isEditing && $existingPhoto)
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">Current photo:</p>
                            <img src="{{ Storage::url($existingPhoto) }}" alt="Profile Photo" class="h-16 w-16 object-cover rounded-full mt-1">
                        </div>
                    @endif

                    @if ($profilePhoto)
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">New photo preview:</p>
                            <img src="{{ $profilePhoto->temporaryUrl() }}" alt="Profile Photo Preview" class="h-16 w-16 object-cover rounded-full mt-1">
                        </div>
                    @endif
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Biography</label>
                    <textarea wire:model="biography" rows="4" class="w-full p-2 border rounded"></textarea>
                    @error('biography') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="mt-4 flex space-x-2">
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                    {{ $isEditing ? 'Update Author' : 'Create Author' }}
                </button>

                @if($isEditing)
                    <button type="button" wire:click="resetForm" class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">
                        Cancel
                    </button>
                @endif
            </div>
        </form>
    </div>

    <!-- Authors List -->
    <div>
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold">Authors List</h2>

            <div>
                <input type="text" wire:model.debounce.300ms="searchTerm" placeholder="Search authors..."
                    class="p-2 border rounded">
            </div>
        </div>

        <div class="bg-white shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Author</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Biography</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Books</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($authors as $author)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    @if($author->profile_photo_path)
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <img class="h-10 w-10 rounded-full object-cover" src="{{ Storage::url($author->profile_photo_path) }}" alt="{{ $author->user->name }}">
                                        </div>
                                    @else
                                        <div class="flex-shrink-0 h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                            <span class="text-gray-500">{{ substr($author->user->name, 0, 1) }}</span>
                                        </div>
                                    @endif
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $author->user->name }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $author->user->email }}</td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 max-w-xs truncate">
                                    {{ $author->biography ?? 'No biography available' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $author->books->count() }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button wire:click="edit({{ $author->id }})" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</button>
                                <button wire:click="delete({{ $author->id }})" class="text-red-600 hover:text-red-900"
                                        onclick="return confirm('Are you sure you want to delete this author?')">Delete</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $authors->links() }}
        </div>
    </div>
</div>
