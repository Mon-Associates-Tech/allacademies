<div>
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-900">Create New Chat Group</h3>
        <button
            wire:click="$dispatch('closeModal')"
            class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>

    <form wire:submit.prevent="createGroup" class="space-y-4">
        <!-- Group Type -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Group Type</label>
            <select wire:model.live="type" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="custom">Custom Group</option>
                <option value="academic_level">Academic Level Group</option>
                <option value="academic_group">Academic Group</option>
            </select>
            @error('type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <!-- Academic Level Selection -->
        @if($this->type === 'academic_level')
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Academic Level</label>
                <select wire:model="academic_level_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Select Academic Level</option>
                    @foreach($academicLevels as $level)
                        <option value="{{ $level->id }}">
                            {{ $level->name }}
                            @if($level->academicGroup)
                                ({{ $level->academicGroup->name }})
                            @endif
                        </option>
                    @endforeach
                </select>
                @error('academic_level_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        @endif

        <!-- Academic Group Selection -->
        @if($type === 'academic_group')
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Academic Group</label>
                <select wire:model="academic_group_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Select Academic Group</option>
                    @foreach($academicGroups as $group)
                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                    @endforeach
                </select>
                @error('academic_group_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        @endif

        <!-- Group Name -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Group Name</label>
            <input
                type="text"
                wire:model="name"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                placeholder="Enter group name">
            @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <!-- Description -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Description (optional)</label>
            <textarea
                wire:model="description"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                rows="3"
                placeholder="Describe the purpose of this group"></textarea>
            @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <!-- Add Users (for custom groups) -->
        @if($this->type === 'custom')
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Add Members</label>
                <div class="relative">
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="userSearch"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Search by name or email">

                    <!-- Search Results -->
                    @if(!empty($this->searchResults) && count($this->searchResults) > 0)
                        <div class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-40 overflow-y-auto">
                            @foreach($this->searchResults as $user)
                                <button
                                    type="button"
                                    wire:click="addUser({{ $user->id }})"
                                    class="w-full px-3 py-2 text-left hover:bg-gray-50 flex items-center space-x-2">
                                    <div class="w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center flex-shrink-0">
                                        <span class="text-white text-xs">{{ substr($user->name, 0, 1) }}</span>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="font-medium text-sm truncate">{{ $user->name }}</p>
                                        <p class="text-xs text-gray-600 truncate">{{ $user->email }}</p>
                                    </div>
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Selected Users -->
                @if(!empty($this->selectedUsers))
                    <div class="mt-2 flex flex-wrap gap-2">
                        @foreach($this->selectedUsers as $userId)
                            @php $user = \App\Models\User::find($userId) @endphp
                            @if($user)
                                <div class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm flex items-center space-x-2">
                                    <span>{{ $user->name }}</span>
                                    <button
                                        type="button"
                                        wire:click="removeUser({{ $userId }})"
                                        class="text-blue-600 hover:text-blue-800">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @endif
            </div>
        @endif

        <!-- Privacy Setting -->
        <div class="flex items-center">
            <input
                type="checkbox"
                id="is_private"
                wire:model="is_private"
                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
            <label for="is_private" class="ml-2 block text-sm text-gray-700">
                Make this group private (invite only)
            </label>
        </div>

        <!-- Submit Buttons -->
        <div class="flex justify-end space-x-3 pt-4">
            <button
                type="button"
                wire:click="$dispatch('closeModal')"
                class="px-4 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                Cancel
            </button>
            <button
                type="submit"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                Create Group
            </button>
        </div>

        @if($errors->has('general'))
            <div class="text-red-500 text-sm mt-2">{{ $errors->first('general') }}</div>
        @endif
    </form>
</div>
