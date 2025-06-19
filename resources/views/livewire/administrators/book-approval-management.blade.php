<div>
    <h1 class="text-2xl font-bold mb-6">Book Approval Management</h1>

    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
            {{ session('message') }}
        </div>
    @endif

    <!-- Filters and Search -->
    <div class="mb-6 bg-white p-4 rounded shadow flex flex-col md:flex-row gap-4 justify-between">
        <div class="flex flex-col sm:flex-row gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Filter by Status</label>
                <select wire:model="filterStatus" class="p-2 border rounded">
                    <option value="">All Books</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                    <option value="pending">Pending Approval</option>
                </select>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
            <input type="text" wire:model.debounce.300ms="searchTerm"
                placeholder="Search by title, author, or category..."
                class="p-2 border rounded w-full">
        </div>
    </div>

    <!-- Books List -->
    <div class="bg-white shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th wire:click="sortBy('title')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer">
                        Title
                        @if($sortField === 'title')
                            <span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                        @endif
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Author</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                    <th wire:click="sortBy('created_at')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer">
                        Date Added
                        @if($sortField === 'created_at')
                            <span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                        @endif
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($books as $book)
                    <tr>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $book->title }}</div>
                            <div class="text-sm text-gray-500">{{ $book->edition ?? 'No Edition' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $book->author->user->name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $book->bookCategory->name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $book->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($book->approvals->where('status', 'approved')->count() > 0)
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Approved
                                </span>
                            @elseif($book->approvals->where('status', 'rejected')->count() > 0)
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                    Rejected
                                </span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    Pending
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button wire:click="openApprovalModal({{ $book->id }}, '{{ $book->title }}')"
                                    class="text-indigo-600 hover:text-indigo-900">
                                Review
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $books->links() }}
    </div>

    <!-- Approval Modal -->
    @if($showApprovalModal)
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-md">
            <div class="p-4 border-b">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-semibold">Review Book Approval</h3>
                    <button wire:click="closeApprovalModal" class="text-gray-500 hover:text-gray-700">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="p-4">
                <h4 class="font-medium text-lg mb-2">{{ $selectedBookTitle }}</h4>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Approval Decision</label>
                    <div class="space-y-2">
                        <label class="inline-flex items-center">
                            <input type="radio" wire:model="approvalStatus" value="approved" class="form-radio">
                            <span class="ml-2">Approve</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" wire:model="approvalStatus" value="rejected" class="form-radio">
                            <span class="ml-2">Reject</span>
                        </label>
                    </div>
                    @error('approvalStatus') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Comments</label>
                    <textarea wire:model="approvalComments" rows="3" class="w-full p-2 border rounded"></textarea>
                </div>

                <div class="flex justify-end space-x-2">
                    <button wire:click="closeApprovalModal" class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">
                        Cancel
                    </button>
                    <button wire:click="submitApproval" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                        Submit Decision
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
