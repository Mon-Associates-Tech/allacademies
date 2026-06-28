{{-- Participant Group Selector Component --}}
{{-- Include this in your exam creation form when participant_mode is 'configured' or 'both' --}}

<div class="bg-white rounded-lg shadow p-6 mb-6" x-data="{ showGroupSelector: {{ in_array($formData['participant_mode'] ?? 'general', ['configured', 'both']) ? 'true' : 'false' }} }">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Participant Configuration</h3>
    
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-2">Participant Mode</label>
        <select name="participant_mode" 
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                x-model="showGroupSelector"
                @change="showGroupSelector = $event.target.value === 'configured' || $event.target.value === 'both'">
            <option value="general" {{ ($formData['participant_mode'] ?? 'general') === 'general' ? 'selected' : '' }}>
                General (Anyone with access code)
            </option>
            <option value="configured" {{ ($formData['participant_mode'] ?? '') === 'configured' ? 'selected' : '' }}>
                Configured (Pre-defined participants only)
            </option>
            <option value="both" {{ ($formData['participant_mode'] ?? '') === 'both' ? 'selected' : '' }}>
                Both (General + Configured)
            </option>
        </select>
    </div>

    <div x-show="showGroupSelector" x-transition class="space-y-4">
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div class="flex-1">
                    <h4 class="font-semibold text-blue-900 mb-1">Select a Participant Group</h4>
                    <p class="text-sm text-blue-800">
                        Choose a pre-defined group to automatically add all its members as configured participants for this exam.
                        You can also add individual participants later.
                    </p>
                </div>
            </div>
        </div>

        <div>
            <label for="participant_group_id" class="block text-sm font-medium text-gray-700 mb-2">
                Select Participant Group (Optional)
            </label>
            <select name="participant_group_id" 
                    id="participant_group_id"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                    onchange="updateGroupInfo(this)">
                <option value="">-- No Group (Add participants manually) --</option>
                @foreach($participantGroups as $group)
                    <option value="{{ $group->id }}" 
                            data-members="{{ $group->members_count ?? 0 }}"
                            {{ ($formData['participant_group_id'] ?? '') == $group->id ? 'selected' : '' }}>
                        {{ $group->name }} ({{ $group->members_count ?? 0 }} participants)
                    </option>
                @endforeach
            </select>
            
            <div id="groupInfo" class="hidden mt-2 p-3 bg-green-50 border border-green-200 rounded-lg">
                <p class="text-sm text-green-800">
                    <span class="font-semibold" id="memberCount">0</span> participants will be added to this exam from the selected group.
                </p>
            </div>
        </div>

        @if($participantGroups->isEmpty())
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-yellow-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <div class="flex-1">
                        <h4 class="font-semibold text-yellow-900 mb-1">No Participant Groups</h4>
                        <p class="text-sm text-yellow-800 mb-2">
                            You haven't created any participant groups yet. Create groups to quickly add multiple participants to exams.
                        </p>
                        <a href="{{ route('examination-hub.participant-groups.index') }}" 
                           target="_blank"
                           class="inline-flex items-center gap-2 text-sm text-yellow-900 hover:text-yellow-700 font-medium">
                            <span>Manage Participant Groups</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        @else
            <div class="text-sm text-gray-600">
                <a href="{{ route('examination-hub.participant-groups.index') }}" 
                   target="_blank"
                   class="text-blue-600 hover:text-blue-800 inline-flex items-center gap-1">
                    <span>Manage participant groups</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                </a>
            </div>
        @endif

        <div class="mt-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Required Fields from Participants</label>
            <div class="space-y-2">
                <label class="flex items-center gap-2">
                    <input type="checkbox" 
                           name="participant_required_fields[]" 
                           value="name"
                           checked
                           disabled
                           class="rounded border-gray-300">
                    <span class="text-sm text-gray-700">Name (Always Required)</span>
                </label>
                <label class="flex items-center gap-2">
                    <input type="checkbox" 
                           name="participant_required_fields[]" 
                           value="email"
                           {{ in_array('email', $formData['participant_required_fields'] ?? ['name', 'email']) ? 'checked' : '' }}
                           class="rounded border-gray-300">
                    <span class="text-sm text-gray-700">Email Address</span>
                </label>
                <label class="flex items-center gap-2" x-show="showGroupSelector">
                    <input type="checkbox" 
                           name="participant_required_fields[]" 
                           value="code"
                           {{ in_array('code', $formData['participant_required_fields'] ?? []) ? 'checked' : '' }}
                           class="rounded border-gray-300">
                    <span class="text-sm text-gray-700">Unique Code</span>
                </label>
            </div>
            <input type="hidden" name="participant_required_fields[]" value="name">
        </div>

        <div x-show="showGroupSelector">
            <label class="block text-sm font-medium text-gray-700 mb-2">Match Mode for Configured Participants</label>
            <select name="configured_match_mode" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                <option value="any" {{ ($formData['configured_match_mode'] ?? 'any') === 'any' ? 'selected' : '' }}>
                    Match by Email OR Unique Code (Any)
                </option>
                <option value="both" {{ ($formData['configured_match_mode'] ?? '') === 'both' ? 'selected' : '' }}>
                    Match by Email AND Unique Code (Both Required)
                </option>
            </select>
            <p class="text-xs text-gray-500 mt-1">
                Determines how participants are matched when joining the exam
            </p>
        </div>
    </div>
</div>

<script>
function updateGroupInfo(select) {
    const groupInfo = document.getElementById('groupInfo');
    const memberCount = document.getElementById('memberCount');
    const selectedOption = select.options[select.selectedIndex];
    
    if (select.value) {
        const members = selectedOption.getAttribute('data-members');
        memberCount.textContent = members;
        groupInfo.classList.remove('hidden');
    } else {
        groupInfo.classList.add('hidden');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const select = document.getElementById('participant_group_id');
    if (select && select.value) {
        updateGroupInfo(select);
    }
});
</script>
