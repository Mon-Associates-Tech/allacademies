<div>
    <h1 class="text-2xl font-bold mb-6">Teacher Management</h1>

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

    <!-- Teacher Form -->
    <div class="mb-8 bg-white p-4 rounded shadow">
        <h2 class="text-lg font-semibold mb-4">{{ $isEditing ? 'Edit Teacher' : 'Create New Teacher' }}</h2>

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
                    <label class="block text-sm font-medium text-gray-700 mb-1">Specialization</label>
                    <input type="text" wire:model="specialization" class="w-full p-2 border rounded">
                    @error('specialization') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Biography</label>
                    <textarea wire:model="biography" rows="3" class="w-full p-2 border rounded"></textarea>
                    @error('biography') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Subjects</label>
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2 border p-2 rounded max-h-40 overflow-y-auto">
                        @foreach($subjects as $subject)
                            <label class="inline-flex items-center">
                                <input type="checkbox" wire:model="subjectIds" value="{{ $subject->id }}" class="mr-2">
                                {{ $subject->name }}
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="mt-4 flex space-x-2">
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                    {{ $isEditing ? 'Update Teacher' : 'Create Teacher' }}
                </button>

                @if($isEditing)
                    <button type="button" wire:click="resetForm" class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">
                        Cancel
                    </button>
                @endif
            </div>
        </form>
    </div>

    <!-- Teachers List -->
    <div>
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold">Teachers List</h2>

            <div>
                <input type="text" wire:model.debounce.300ms="searchTerm" placeholder="Search teachers..."
                    class="p-2 border rounded">
            </div>
        </div>

        <div class="bg-white shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Specialization</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subjects</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Groups</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($teachers as $teacher)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $teacher->user->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $teacher->user->email }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $teacher->specialization ?? 'N/A' }}</td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1">
                                    @foreach($teacher->subjects as $subject)
                                        <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                                            {{ $subject->name }}
                                        </span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $teacher->studentGroups->count() }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button wire:click="edit({{ $teacher->id }})" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</button>
                                <button wire:click="delete({{ $teacher->id }})" class="text-red-600 hover:text-red-900"
                                        onclick="return confirm('Are you sure you want to delete this teacher?')">Delete</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $teachers->links() }}
        </div>
    </div>
</div>
