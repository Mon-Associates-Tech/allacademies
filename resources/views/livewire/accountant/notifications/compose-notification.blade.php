<div class="max-w-5xl mx-auto space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Compose Notification</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Send messages, reminders and announcements to students, parents and staff.</p>
        </div>
        <a href="{{ route('accountant.notifications.index') }}" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
            ← Back to Notifications
        </a>
    </div>

    <form wire:submit.prevent="send" class="space-y-6">

        {{-- ── Template Picker ── --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-4">1. Choose a Template</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                {{-- Blank --}}
                <label class="relative cursor-pointer">
                    <input type="radio" wire:model.live="templateId" value="" class="sr-only peer">
                    <div class="p-4 border-2 rounded-lg border-gray-200 dark:border-gray-600 peer-checked:border-blue-500 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-900/20 transition">
                        <div class="font-medium text-sm text-gray-900 dark:text-white">Blank Message</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Write from scratch</div>
                    </div>
                </label>

                @foreach($templates as $tpl)
                    <label class="relative cursor-pointer">
                        <input type="radio" wire:model.live="templateId" value="{{ $tpl->id }}" class="sr-only peer">
                        <div class="p-4 border-2 rounded-lg border-gray-200 dark:border-gray-600 peer-checked:border-blue-500 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-900/20 transition">
                            <div class="flex items-center gap-2">
                                <span class="font-medium text-sm text-gray-900 dark:text-white">{{ $tpl->name }}</span>
                                @if($tpl->is_system)
                                    <span class="text-xs px-1.5 py-0.5 rounded bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400">System</span>
                                @endif
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 capitalize">{{ $tpl->category }}</div>
                        </div>
                    </label>
                @endforeach
            </div>

            @if($templateId)
                <p class="mt-3 text-xs text-blue-600 dark:text-blue-400">
                    Template loaded. You can edit the subject and body below before sending.
                </p>
            @endif
        </div>

        {{-- ── Delivery Channels ── --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-4">2. Delivery Channels</h2>

            <div class="flex flex-wrap gap-4">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" wire:model.live="channelEmail" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">📧 Email</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" wire:model.live="channelSms" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">📱 SMS</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" wire:model.live="channelInApp" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">🔔 In-App</span>
                </label>
            </div>

            @if(!$channelEmail && !$channelSms && !$channelInApp)
                <p class="mt-2 text-sm text-red-500">Please select at least one delivery channel.</p>
            @endif
        </div>

        {{-- ── Recipients ── --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-4">3. Recipients</h2>

            {{-- Target Type --}}
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 mb-5">
                @foreach([
                    'all_unpaid'     => ['label' => 'All Unpaid Fees',   'icon' => '💳'],
                    'all_overdue'    => ['label' => 'Overdue Fees',       'icon' => '⚠️'],
                    'all_partial'    => ['label' => 'Partial Payments',   'icon' => '🔄'],
                    'academic_group' => ['label' => 'By Class/Group',     'icon' => '🏫'],
                    'academic_level' => ['label' => 'By Level',           'icon' => '📚'],
                    'role'           => ['label' => 'By Role',            'icon' => '👥'],
                    'individual'     => ['label' => 'Specific Users',     'icon' => '🔍'],
                ] as $value => $meta)
                    <label class="relative cursor-pointer">
                        <input type="radio" wire:model.live="targetType" value="{{ $value }}" class="sr-only peer">
                        <div class="p-3 border-2 rounded-lg border-gray-200 dark:border-gray-600 peer-checked:border-blue-500 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-900/20 text-center transition">
                            <div class="text-lg">{{ $meta['icon'] }}</div>
                            <div class="text-xs font-medium text-gray-700 dark:text-gray-300 mt-1">{{ $meta['label'] }}</div>
                        </div>
                    </label>
                @endforeach
            </div>

            {{-- Fee-based filters (period + group/level) --}}
            @if(in_array($targetType, ['all_unpaid', 'all_overdue', 'all_partial']))
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4 p-4 bg-amber-50 dark:bg-amber-900/10 rounded-lg border border-amber-200 dark:border-amber-800">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Academic Period (optional)</label>
                        <select wire:model.live="academicPeriodId" class="w-full text-sm border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white px-3 py-2 focus:ring-2 focus:ring-blue-500">
                            <option value="">All periods</option>
                            @foreach($academicPeriods as $period)
                                <option value="{{ $period->id }}">{{ $period->name }} {{ $period->is_current ? '(Current)' : '' }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Filter by Group (optional)</label>
                        <select wire:model.live="selectedAcademicGroups.0" class="w-full text-sm border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white px-3 py-2 focus:ring-2 focus:ring-blue-500">
                            <option value="">All groups</option>
                            @foreach($academicGroups as $group)
                                <option value="{{ $group->id }}">{{ $group->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Filter by Level (optional)</label>
                        <select wire:model.live="selectedAcademicLevels.0" class="w-full text-sm border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white px-3 py-2 focus:ring-2 focus:ring-blue-500">
                            <option value="">All levels</option>
                            @foreach($academicLevels as $level)
                                <option value="{{ $level->id }}">{{ $level->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            @endif

            {{-- Academic Group multi-select --}}
            @if($targetType === 'academic_group')
                <div class="p-4 bg-gray-50 dark:bg-gray-700/30 rounded-lg mb-4">
                    <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Select Groups</h3>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                        @foreach($academicGroups as $group)
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" wire:click="toggleAcademicGroup({{ $group->id }})"
                                    {{ in_array($group->id, $selectedAcademicGroups) ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="text-sm text-gray-700 dark:text-gray-300">{{ $group->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Academic Level multi-select --}}
            @if($targetType === 'academic_level')
                <div class="p-4 bg-gray-50 dark:bg-gray-700/30 rounded-lg mb-4">
                    <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Select Levels</h3>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                        @foreach($academicLevels as $level)
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" wire:click="toggleAcademicLevel({{ $level->id }})"
                                    {{ in_array($level->id, $selectedAcademicLevels) ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="text-sm text-gray-700 dark:text-gray-300">{{ $level->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Role select --}}
            @if($targetType === 'role')
                <div class="p-4 bg-gray-50 dark:bg-gray-700/30 rounded-lg mb-4">
                    <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Select Roles</h3>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                        @foreach($availableRoles as $roleValue => $roleLabel)
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" wire:click="toggleRole('{{ $roleValue }}')"
                                    {{ in_array($roleValue, $selectedRoles) ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="text-sm text-gray-700 dark:text-gray-300">{{ $roleLabel }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Individual user search --}}
            @if($targetType === 'individual')
                <div class="p-4 bg-gray-50 dark:bg-gray-700/30 rounded-lg mb-4">
                    <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Search & Select Users</h3>
                    <input type="text" wire:model.live.debounce.300ms="userSearch"
                        placeholder="Search by name or email…"
                        class="w-full text-sm border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white px-3 py-2 focus:ring-2 focus:ring-blue-500 mb-3">

                    @if(!empty($searchedUsers))
                        <div class="max-h-40 overflow-y-auto border border-gray-200 dark:border-gray-600 rounded-md divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach($searchedUsers as $user)
                                <div class="flex items-center justify-between px-3 py-2 hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <div>
                                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $user['name'] }}</span>
                                        <span class="text-xs text-gray-500 dark:text-gray-400 ml-2">{{ $user['email'] }}</span>
                                        <span class="text-xs text-gray-400 ml-1 capitalize">({{ $user['role'] }})</span>
                                    </div>
                                    <button type="button" wire:click="toggleUser({{ $user['id'] }})"
                                        class="text-xs font-medium {{ in_array($user['id'], $selectedUsers) ? 'text-red-600 hover:text-red-800' : 'text-blue-600 hover:text-blue-800' }}">
                                        {{ in_array($user['id'], $selectedUsers) ? 'Remove' : 'Add' }}
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @if(!empty($selectedUsersList))
                        <div class="mt-3 flex flex-wrap gap-2">
                            @foreach($selectedUsersList as $user)
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300">
                                    {{ $user['name'] }}
                                    <button type="button" wire:click="removeUser({{ $user['id'] }})" class="text-blue-600 hover:text-blue-800">×</button>
                                </span>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endif

            {{-- Include parents toggle --}}
            @if(in_array($targetType, ['all_unpaid', 'all_overdue', 'all_partial', 'academic_group', 'academic_level', 'individual']))
                <label class="flex items-center gap-2 cursor-pointer mt-2">
                    <input type="checkbox" wire:model.live="includeParents" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <span class="text-sm text-gray-700 dark:text-gray-300">Also notify parents (if email on record)</span>
                </label>
            @endif

            {{-- Preview button --}}
            <div class="mt-4 flex items-center gap-3">
                <button type="button" wire:click="previewRecipients"
                    class="px-4 py-2 text-sm font-medium bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                    Preview Recipients
                </button>
                @if($showPreview)
                    <span class="text-sm text-gray-600 dark:text-gray-400">{{ $recipientCount }} recipient(s) found</span>
                @endif
            </div>

            @if($showPreview)
                <div class="mt-3 p-4 bg-blue-50 dark:bg-blue-900/10 border border-blue-200 dark:border-blue-800 rounded-lg">
                    <div class="max-h-36 overflow-y-auto space-y-1">
                        @forelse($previewRecipients as $r)
                            <div class="text-sm text-blue-800 dark:text-blue-300">
                                {{ $r['name'] }} <span class="text-blue-500">({{ $r['email'] }})</span>
                                <span class="text-xs text-blue-400 capitalize ml-1">{{ $r['role'] }}</span>
                            </div>
                        @empty
                            <p class="text-sm text-blue-600 dark:text-blue-400">No recipients found for the selected criteria.</p>
                        @endforelse
                        @if($recipientCount > 30)
                            <p class="text-xs text-blue-500 dark:text-blue-400 mt-1">… and {{ $recipientCount - 30 }} more</p>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        {{-- ── Message Content ── --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 space-y-5">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white">4. Message Content</h2>

            {{-- Subject --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Subject <span class="text-red-500">*</span>
                </label>
                <input type="text" wire:model="subject"
                    class="w-full text-sm border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white px-3 py-2 focus:ring-2 focus:ring-blue-500"
                    placeholder="Message subject">
                @error('subject') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Email / In-App Body --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    {{ $templateId ? 'Message Content' : 'Email / In-App Body' }} <span class="text-red-500">*</span>
                    @if($templateId)
                        <span class="text-xs font-normal text-gray-400 ml-1">(replaces <code>@{{message_body}}</code> in the template)</span>
                    @endif
                </label>
                <textarea wire:model="body" rows="10"
                    class="w-full text-sm border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white px-3 py-2 focus:ring-2 focus:ring-blue-500 font-mono"
                    placeholder="Message body. Use @{{variable}} placeholders e.g. @{{student_name}}, @{{balance}}, @{{due_date}}"></textarea>
                @error('body') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- SMS Body --}}
            @if($channelSms)
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        SMS Body
                        <span class="text-xs text-gray-400 font-normal ml-1">(max 160 chars — keep it short and precise)</span>
                    </label>
                    <textarea wire:model="smsBody" rows="3" maxlength="160"
                        class="w-full text-sm border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white px-3 py-2 focus:ring-2 focus:ring-blue-500"
                        placeholder="e.g. @{{school_name}}: Fee reminder for @{{student_name}}. Balance: @{{currency}} @{{balance}} due @{{due_date}}."></textarea>
                    <p class="text-xs text-gray-400 mt-1 text-right">{{ strlen($smsBody) }}/160</p>
                    @error('smsBody') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            @endif

            {{-- Available variables hint --}}
            @if($templateId)
                @php $tpl = $templates->firstWhere('id', $templateId); @endphp
                @if($tpl && $tpl->available_variables)
                    <div class="p-3 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
                        <p class="text-xs font-medium text-gray-600 dark:text-gray-400 mb-2">Available placeholders:</p>
                        <div class="flex flex-wrap gap-1.5">
                            @foreach($tpl->available_variables as $var)
                                @php $placeholder = '{{' . $var . '}}'; @endphp
                                <code class="text-xs px-2 py-0.5 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded text-blue-600 dark:text-blue-400">{{ $placeholder }}</code>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endif
        </div>

        {{-- ── Attachments ── --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-4">5. Attachments <span class="text-xs font-normal text-gray-400">(optional)</span></h2>

            <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-5 text-center hover:border-gray-400 transition"
                x-data="{ uploading: false }"
                x-on:livewire-upload-start="uploading = true"
                x-on:livewire-upload-finish="uploading = false"
                x-on:livewire-upload-error="uploading = false">
                <input type="file" multiple class="hidden" id="att-input"
                    x-on:change="
                        uploading = true;
                        Array.from($event.target.files).forEach(file => {
                            $wire.upload('attachments', file, () => {}, () => {});
                        });
                        $event.target.value = '';
                    ">
                <label for="att-input" class="cursor-pointer block">
                    <svg class="mx-auto h-10 w-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400" x-show="!uploading">Click to attach files · Max 10MB each</p>
                    <p class="mt-2 text-sm text-blue-500" x-show="uploading" x-cloak>Uploading…</p>
                </label>
            </div>

            @if(!empty($tempAttachments))
                <div class="mt-3 space-y-2">
                    @foreach($tempAttachments as $att)
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/30 border border-gray-200 dark:border-gray-600 rounded-lg">
                            <div class="text-sm">
                                <span class="font-medium text-gray-900 dark:text-white">{{ $att['original_filename'] }}</span>
                                <span class="text-gray-400 ml-2">{{ $att['human_size'] }}</span>
                            </div>
                            <button type="button" wire:click="removeAttachment('{{ $att['id'] }}')" class="text-red-500 hover:text-red-700 text-xs">Remove</button>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- ── Send Options ── --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-4">6. Send Options</h2>

            <div class="space-y-3">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" wire:model.live="isUrgent" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                    <span class="text-sm text-gray-700 dark:text-gray-300">Mark as urgent</span>
                </label>

                <div class="space-y-2">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" wire:model.live="sendNow" value="1" class="border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-sm text-gray-700 dark:text-gray-300">Send immediately</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" wire:model.live="sendNow" value="0" class="border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-sm text-gray-700 dark:text-gray-300">Schedule for later</span>
                    </label>

                    @if(!$sendNow)
                        <div class="ml-6">
                            <input type="datetime-local" wire:model.live="scheduledAt"
                                class="text-sm border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white px-3 py-2 focus:ring-2 focus:ring-blue-500">
                            @error('scheduledAt') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ── Actions ── --}}
        <div class="flex items-center justify-between pb-6">
            <div class="flex gap-3">
                <button type="button" wire:click="saveDraft"
                    class="px-5 py-2.5 text-sm font-medium border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                    Save Draft
                </button>
                <a href="{{ route('accountant.notifications.index') }}"
                    class="px-5 py-2.5 text-sm font-medium border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                    Cancel
                </a>
            </div>

            <button type="submit"
                class="px-6 py-2.5 text-sm font-semibold bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow transition">
                {{ $sendNow ? 'Send Notification' : 'Schedule Notification' }}
            </button>
        </div>

    </form>
</div>
