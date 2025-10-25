<div>
    <!-- Header with Search and Add Button -->
    <div class="mb-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <!-- Search and Filter -->
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
                <div class="relative">
                    <input wire:model.live.debounce.300ms="search"
                           type="text"
                           placeholder="Search participants..."
                           class="w-full px-4 py-2 pr-10 text-sm border border-gray-300 rounded-lg focus:ring-violet-500 focus:border-violet-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:w-64">
                    <svg class="absolute w-5 h-5 text-gray-400 transform -translate-y-1/2 right-3 top-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>

                <select wire:model.live="statusFilter"
                        class="px-4 py-2 text-sm border border-gray-300 rounded-lg focus:ring-violet-500 focus:border-violet-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option value="all">All Status</option>
                    <option value="invited">Invited</option>
                    <option value="joined">Joined</option>
                    <option value="left">Left</option>
                    <option value="declined">Declined</option>
                </select>
            </div>

            <!-- Add Participants Button -->
            @if($session->status === 'scheduled')
                <button wire:click="toggleAddModal"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-violet-600 rounded-lg hover:bg-violet-700">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add Participants
                </button>
            @endif
        </div>
    </div>

    <!-- Participants List -->
    <div class="overflow-hidden bg-white rounded-lg shadow dark:bg-gray-800">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300">
                        Participant
                    </th>
                    <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300">
                        Status
                    </th>
                    <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300">
                        Role
                    </th>
                    <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300">
                        Invited At
                    </th>
                    <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300">
                        Join Count
                    </th>
                    <th scope="col" class="relative px-6 py-3">
                        <span class="sr-only">Actions</span>
                    </th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                @forelse($participants as $participant)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 w-10 h-10">
                                    @if($participant->user->avatar)
                                        <img class="w-10 h-10 rounded-full" src="{{ $participant->user->avatar }}" alt="">
                                    @else
                                        <div class="flex items-center justify-center w-10 h-10 text-white rounded-full bg-violet-600">
                                            {{ substr($participant->full_name, 0, 1) }}
                                        </div>
                                    @endif
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $participant->full_name }}
                                    </div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $participant->user->email }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 text-xs font-semibold leading-5 rounded-full
                                    {{ $participant->status === 'joined' ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400' : '' }}
                                    {{ $participant->status === 'invited' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400' : '' }}
                                    {{ $participant->status === 'left' ? 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400' : '' }}
                                    {{ $participant->status === 'declined' ? 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400' : '' }}">
                                    {{ ucfirst($participant->status) }}
                                </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-400">
                            {{ ucfirst($participant->role) }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-400">
                            {{ $participant->invited_at?->format('M d, Y g:i A') }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-400">
                            {{ $participant->join_count }}
                        </td>
                        <td class="px-6 py-4 text-sm font-medium text-right whitespace-nowrap">
                            <div class="flex items-center justify-end gap-2">
                                @if($participant->status === 'invited')
                                    <button wire:click="resendInvitation({{ $participant->id }})"
                                            class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                        Resend
                                    </button>
                                @endif

                                @if($session->status === 'scheduled')
                                    <button wire:click="removeParticipant({{ $participant->id }})"
                                            wire:confirm="Are you sure you want to remove this participant?"
                                            class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                        Remove
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <svg class="w-12 h-12 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            <h3 class="mt-4 text-sm font-medium text-gray-900 dark:text-white">No participants found</h3>
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                {{ $search ? 'Try adjusting your search or filter criteria.' : 'Add participants to get started.' }}
                            </p>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($participants->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $participants->links() }}
            </div>
        @endif
    </div>

    <!-- Add Participants Modal -->
    @if($showAddModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <!-- Background overlay -->
                <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" wire:click="toggleAddModal"></div>

                <!-- Modal panel -->
                <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl dark:bg-gray-800 sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="px-4 pt-5 pb-4 bg-white dark:bg-gray-800 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="w-full mt-3 text-center sm:mt-0 sm:text-left">
                                <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white" id="modal-title">
                                    Add Participants
                                </h3>
                                <div class="mt-4">
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                                        Select students to add to this session
                                    </p>

                                    @if($availableStudents->isEmpty())
                                        <div class="p-4 text-center text-gray-500 bg-gray-50 rounded-lg dark:bg-gray-700 dark:text-gray-400">
                                            <p>No more students available to add.</p>
                                        </div>
                                    @else
                                        <div class="max-h-96 overflow-y-auto border border-gray-300 rounded-lg dark:border-gray-600">
                                            @foreach($availableStudents as $student)
                                                <label class="flex items-center p-3 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer">
                                                    <input wire:model="selectedStudents"
                                                           type="checkbox"
                                                           value="{{ $student->id }}"
                                                           class="w-4 h-4 text-violet-600 border-gray-300 rounded focus:ring-violet-500">
                                                    <div class="ml-3 flex items-center">
                                                        @if($student->user->avatar)
                                                            <img class="w-8 h-8 rounded-full mr-3" src="{{ $student->user->avatar }}" alt="">
                                                        @else
                                                            <div class="flex items-center justify-center w-8 h-8 mr-3 text-sm text-white rounded-full bg-violet-600">
                                                                {{ substr($student->user->name, 0, 1) }}
                                                            </div>
                                                        @endif
                                                        <div>
                                                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                                {{ $student->user->name }}
                                                            </p>
                                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                                {{ $student->user->email }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </label>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="px-4 py-3 bg-gray-50 dark:bg-gray-700 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button wire:click="addParticipants"
                                type="button"
                                class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-white bg-violet-600 border border-transparent rounded-md shadow-sm hover:bg-violet-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-violet-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Add Selected
                        </button>
                        <button wire:click="toggleAddModal"
                                type="button"
                                class="inline-flex justify-center w-full px-4 py-2 mt-3 text-base font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-violet-500 sm:mt-0 sm:w-auto sm:text-sm dark:bg-gray-600 dark:text-gray-300 dark:border-gray-500 dark:hover:bg-gray-500">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
