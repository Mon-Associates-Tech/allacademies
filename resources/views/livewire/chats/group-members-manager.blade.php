<div>
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-900">Group Members</h3>
        <button
            x-on:click="showMembersModal = false"
            class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>

    <!-- Add Member Section -->
    @if($this->canManageMembers())
        <div class="mb-6">
            <div class="flex items-center justify-between mb-2">
                <h4 class="font-medium text-gray-900">Add New Member</h4>
                <button
                    wire:click="$toggle('showAddMember')"
                    class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                    {{ $showAddMember ? 'Cancel' : 'Add Member' }}
                </button>
            </div>

            @if($showAddMember)
                <div class="relative">
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="userSearch"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Search by name or email">

                    @if(!empty($searchResults) && count($searchResults) > 0)
                        <div class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-40 overflow-y-auto">
                            @foreach($searchResults as $user)
                                <button
                                    type="button"
                                    wire:click="addMember({{ $user->id }})"
                                    class="w-full px-3 py-2 text-left hover:bg-gray-50 flex items-center space-x-2">
                                    <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center flex-shrink-0">
                                        <span class="text-white text-sm">{{ substr($user->name, 0, 1) }}</span>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="font-medium truncate">{{ $user->name }}</p>
                                        <p class="text-sm text-gray-600 truncate">{{ $user->email }}</p>
                                    </div>
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endif

            @error('member')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror

            @if(session()->has('success'))
                <p class="text-green-500 text-sm mt-1">{{ session('success') }}</p>
            @endif
        </div>
    @endif

    <!-- Current Members List -->
    <div class="space-y-2">
        <h4 class="font-medium text-gray-900">Current Members ({{ count($members) }})</h4>

        <div class="max-h-96 overflow-y-auto space-y-2">
            @foreach($members as $member)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div class="flex items-center space-x-3 min-w-0 flex-1">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center flex-shrink-0">
                            <span class="text-white font-medium text-sm">{{ substr($member->name, 0, 1) }}</span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="font-medium text-gray-900 truncate">{{ $member->name }}</p>
                            <p class="text-sm text-gray-600 truncate">{{ $member->email }}</p>
                            <div class="flex items-center space-x-2 mt-1">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                    {{ $member->pivot->role === 'admin' ? 'bg-red-100 text-red-800' :
                                       ($member->pivot->role === 'moderator' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                                    {{ ucfirst($member->pivot->role) }}
                                </span>
                                @if($member->pivot->joined_at)
                                    <span class="text-xs text-gray-500">
                                        Joined {{ \Carbon\Carbon::parse($member->pivot->joined_at)->diffForHumans() }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if($this->canManageMembers() && $member->id !== auth()->id() && $member->id !== $chatGroup->created_by)
                        <button
                            wire:click="removeMember({{ $member->id }})"
                            onclick="return confirm('Are you sure you want to remove this member?')"
                            class="text-red-600 hover:text-red-800 text-sm font-medium ml-2 flex-shrink-0">
                            Remove
                        </button>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</div>
