<div class="space-y-5">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Message Templates</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">System templates are read-only. Duplicate them to create custom versions.</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('accountant.notifications.index') }}"
                class="px-4 py-2 text-sm font-medium border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                ← Back
            </a>
            @if(!$showForm)
                <button wire:click="create"
                    class="px-4 py-2 text-sm font-semibold bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow transition">
                    + New Template
                </button>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg text-sm text-green-700 dark:text-green-400">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg text-sm text-red-700 dark:text-red-400">
            {{ session('error') }}
        </div>
    @endif

    {{-- Form --}}
    @if($showForm)
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-5">
                {{ $editingId ? 'Edit Template' : 'New Template' }}
            </h2>

            <div class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Template Name <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="name"
                            class="w-full text-sm border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white px-3 py-2 focus:ring-2 focus:ring-blue-500"
                            placeholder="e.g. Term 2 Fee Reminder">
                        @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Category <span class="text-red-500">*</span></label>
                        <select wire:model="category"
                            class="w-full text-sm border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white px-3 py-2 focus:ring-2 focus:ring-blue-500">
                            <option value="fee">Fee</option>
                            <option value="event">Event</option>
                            <option value="reminder">Reminder</option>
                            <option value="general">General</option>
                        </select>
                        @error('category') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Subject <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="subject"
                        class="w-full text-sm border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white px-3 py-2 focus:ring-2 focus:ring-blue-500"
                        placeholder="Use @{{variable}} for dynamic content">
                    @error('subject') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Email / In-App Body <span class="text-red-500">*</span>
                    </label>
                    <textarea wire:model="body" rows="10"
                        class="w-full text-sm border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white px-3 py-2 focus:ring-2 focus:ring-blue-500 font-mono"
                        placeholder="Full message body. Use @{{variable}} placeholders."></textarea>
                    @error('body') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        SMS Body
                        <span class="text-xs font-normal text-gray-400 ml-1">(max 160 chars — short, precise, meaningful)</span>
                    </label>
                    <textarea wire:model="smsBody" rows="3" maxlength="160"
                        class="w-full text-sm border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white px-3 py-2 focus:ring-2 focus:ring-blue-500"
                        placeholder="e.g. @{{school_name}}: Fee reminder for @{{student_name}}. Balance: @{{currency}} @{{balance}} due @{{due_date}}."></textarea>
                    <p class="text-xs text-gray-400 mt-1 text-right">{{ strlen($smsBody) }}/160</p>
                    @error('smsBody') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex gap-3 pt-2">
                    <button wire:click="save"
                        class="px-5 py-2 text-sm font-semibold bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                        {{ $editingId ? 'Update Template' : 'Save Template' }}
                    </button>
                    <button wire:click="cancel"
                        class="px-5 py-2 text-sm font-medium border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Template List --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white">All Templates</h2>
            <input type="text" wire:model.live.debounce.300ms="search"
                placeholder="Search templates…"
                class="text-sm border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white px-3 py-1.5 focus:ring-2 focus:ring-blue-500 w-48">
        </div>

        <div class="divide-y divide-gray-200 dark:divide-gray-700">
            @forelse($templates as $tpl)
                <div class="px-6 py-4 flex items-start justify-between gap-4">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="font-medium text-gray-900 dark:text-white">{{ $tpl->name }}</span>
                            @if($tpl->is_system)
                                <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400">System</span>
                            @else
                                <span class="text-xs px-2 py-0.5 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400">Custom</span>
                            @endif
                            <span class="text-xs px-2 py-0.5 rounded-full bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 capitalize">{{ $tpl->category }}</span>
                        </div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5 truncate">{{ $tpl->subject }}</p>
                        @if($tpl->sms_body)
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5 truncate">SMS: {{ $tpl->sms_body }}</p>
                        @endif
                    </div>
                    <div class="flex items-center gap-3 shrink-0">
                        <button wire:click="duplicate({{ $tpl->id }})"
                            class="text-xs text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 font-medium">
                            Duplicate
                        </button>
                        @if(!$tpl->is_system)
                            <button wire:click="edit({{ $tpl->id }})"
                                class="text-xs text-blue-600 hover:text-blue-800 dark:text-blue-400 font-medium">
                                Edit
                            </button>
                            <button wire:click="delete({{ $tpl->id }})"
                                wire:confirm="Delete this template?"
                                class="text-xs text-red-500 hover:text-red-700 font-medium">
                                Delete
                            </button>
                        @endif
                    </div>
                </div>
            @empty
                <div class="px-6 py-12 text-center text-gray-400 dark:text-gray-500">No templates found.</div>
            @endforelse
        </div>
    </div>
</div>
