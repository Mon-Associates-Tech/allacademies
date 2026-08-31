<div class="space-y-5">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Notifications</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Manage messages and reminders sent to students, parents and staff.</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('accountant.notifications.templates') }}"
                class="px-4 py-2 text-sm font-medium border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                Manage Templates
            </a>
            <a href="{{ route('accountant.notifications.compose') }}"
                class="px-4 py-2 text-sm font-semibold bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow transition">
                + Compose
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg text-sm text-green-700 dark:text-green-400">
            {{ session('success') }}
        </div>
    @endif

    {{-- Tabs + Search --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between px-6 pt-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex gap-1">
                @foreach(['sent' => 'Sent', 'scheduled' => 'Scheduled', 'draft' => 'Drafts'] as $key => $label)
                    <button wire:click="$set('tab', '{{ $key }}')"
                        class="px-4 py-2.5 text-sm font-medium border-b-2 transition {{ $tab === $key ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>
            <div class="pb-3">
                <input type="text" wire:model.live.debounce.300ms="search"
                    placeholder="Search by subject…"
                    class="text-sm border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white px-3 py-1.5 focus:ring-2 focus:ring-blue-500 w-56">
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Subject</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Template</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Recipients</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            {{ $tab === 'scheduled' ? 'Scheduled For' : ($tab === 'draft' ? 'Last Updated' : 'Sent At') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($messages as $msg)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900 dark:text-white flex items-center gap-2">
                                    @if($msg->is_urgent)
                                        <span class="text-red-500 text-xs font-bold">URGENT</span>
                                    @endif
                                    {{ Str::limit($msg->template ? $msg->template->renderSubject($baseVars) : $msg->subject, 60) }}
                                </div>
                                <div class="text-xs text-gray-400 mt-0.5 capitalize">{{ $msg->target_type }}</div>
                            </td>
                            <td class="px-6 py-4 text-gray-500 dark:text-gray-400">
                                {{ $msg->template?->name ?? '—' }}
                            </td>
                            <td class="px-6 py-4 text-gray-700 dark:text-gray-300">
                                {{ $msg->recipients->count() }}
                            </td>
                            <td class="px-6 py-4 text-gray-500 dark:text-gray-400 whitespace-nowrap">
                                @if($tab === 'scheduled')
                                    {{ $msg->scheduled_at?->format('d M Y, H:i') ?? '—' }}
                                @elseif($tab === 'draft')
                                    {{ $msg->updated_at->format('d M Y, H:i') }}
                                @else
                                    {{ $msg->sent_at?->format('d M Y, H:i') ?? '—' }}
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $badge = match($msg->status) {
                                        'sent'      => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
                                        'scheduled' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
                                        'draft'     => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
                                        'failed'    => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
                                        default     => 'bg-yellow-100 text-yellow-800',
                                    };
                                @endphp
                                <span class="px-2 py-0.5 text-xs font-medium rounded-full {{ $badge }} capitalize">{{ $msg->status }}</span>
                            </td>
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <a href="{{ route('accountant.notifications.show', $msg) }}"
                                    class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 text-xs font-medium mr-3">View</a>
                                @if($tab === 'draft')
                                    <a href="{{ route('accountant.notifications.compose', ['draft' => $msg->id]) }}"
                                        class="text-blue-600 hover:text-blue-800 text-xs font-medium mr-3">Edit</a>
                                    <button wire:click="deleteDraft({{ $msg->id }})"
                                        wire:confirm="Delete this draft?"
                                        class="text-red-500 hover:text-red-700 text-xs font-medium">Delete</button>
                                @elseif($tab === 'scheduled')
                                    <button wire:click="cancelScheduled({{ $msg->id }})"
                                        wire:confirm="Move this back to drafts?"
                                        class="text-yellow-600 hover:text-yellow-800 text-xs font-medium">Cancel</button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-400 dark:text-gray-500">
                                No {{ $tab }} notifications found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($messages->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $messages->links() }}
            </div>
        @endif
    </div>
</div>
