<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-start">
        <div>
            <div class="flex items-center space-x-2 mb-2">
                <h1 class="text-2xl font-bold text-gray-900">{{ $message->subject }}</h1>
                @if($message->is_urgent)
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"/>
                        </svg>
                        Urgent
                    </span>
                @endif
            </div>
            <div class="flex items-center space-x-4 text-sm text-gray-500">
                <span>To: {{ $totalCount }} recipients</span>
                <span>•</span>
                <span>{{ $message->created_at->format('M j, Y \a\t H:i') }}</span>
            </div>
        </div>

        <div class="flex items-center space-x-3">
            @if($message->status === 'failed')
                <button wire:click="resendMessage"
                        wire:confirm="Are you sure you want to resend this message?"
                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Resend
                </button>
            @endif

            <a href="{{ route('teacher.messages.index') }}"
               class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Messages
            </a>
        </div>
    </div>

    <!-- Status Banner -->
    <div class="rounded-md p-4
        @if($message->status === 'sent') bg-green-50 border border-green-200
        @elseif($message->status === 'scheduled') bg-blue-50 border border-blue-200
        @elseif($message->status === 'failed') bg-red-50 border border-red-200
        @else bg-gray-50 border border-gray-200 @endif">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                @if($message->status === 'sent')
                    <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                    </svg>
                @elseif($message->status === 'scheduled')
                    <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"/>
                    </svg>
                @elseif($message->status === 'failed')
                    <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"/>
                    </svg>
                @endif
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium
                    @if($message->status === 'sent') text-green-800
                    @elseif($message->status === 'scheduled') text-blue-800
                    @elseif($message->status === 'failed') text-red-800
                    @else text-gray-800 @endif">
                    @if($message->status === 'sent')
                        Message Sent Successfully
                    @elseif($message->status === 'scheduled')
                        Message Scheduled
                    @elseif($message->status === 'failed')
                        Message Failed to Send
                    @endif
                </h3>
                <div class="mt-1 text-sm
                    @if($message->status === 'sent') text-green-700
                    @elseif($message->status === 'scheduled') text-blue-700
                    @elseif($message->status === 'failed') text-red-700
                    @else text-gray-700 @endif">
                    @if($message->status === 'sent')
                        Sent on {{ $message->sent_at->format('M j, Y \a\t H:i') }} • {{ $readCount }} read, {{ $unreadCount }} unread
                    @elseif($message->status === 'scheduled')
                        Scheduled for {{ $message->scheduled_at->format('M j, Y \a\t H:i') }}
                    @elseif($message->status === 'failed')
                        Failed to send. Click "Resend" to try again.
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="border-b border-gray-200">
        <nav class="-mb-px flex space-x-8">
            <button wire:click="setActiveTab('details')"
                    class="py-2 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'details' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-500' }}">
                Message Details
            </button>
            <button wire:click="setActiveTab('recipients')"
                    class="py-2 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'recipients' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-500' }}">
                Recipients ({{ $totalCount }})
            </button>
            <button wire:click="setActiveTab('targeting')"
                    class="py-2 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'targeting' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-500' }}">
                Targeting Criteria
            </button>
        </nav>
    </div>

    <!-- Tab Content -->
    <div class="bg-white shadow rounded-lg">
        @if($activeTab === 'details')
            <div class="p-6">
                <!-- Message Content -->
                <div class="prose max-w-none mb-6">
                    {!! $message->body !!}
                </div>

                <!-- Attachments -->
                @if($message->attachments->count() > 0)
                    <div class="border-t pt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Attachments</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($message->attachments as $attachment)
                                <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                                    <svg class="h-8 w-8 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate">
                                            {{ $attachment->original_filename }}
                                        </p>
                                        <p class="text-sm text-gray-500">
                                            {{ $attachment->human_readable_size }}
                                        </p>
                                    </div>
                                    <a href="{{ $attachment->url }}"
                                       target="_blank"
                                       class="ml-2 inline-flex items-center px-2.5 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50">
                                        Download
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        @endif

        @if($activeTab === 'recipients')
            <div class="p-6">
                <!-- Recipients Filters -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
                    <div class="flex items-center space-x-4">
                        <!-- Search -->
                        <div class="flex-1 min-w-0">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"/>
                                    </svg>
                                </div>
                                <input wire:model.live.debounce.300ms="searchRecipients"
                                       type="text"
                                       placeholder="Search recipients..."
                                       class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            </div>
                        </div>

                        <!-- Read Status Filter -->
                        <div>
                            <select wire:model.live="readStatusFilter"
                                    class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white leading-5 bg-white focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <option value="all">All Recipients</option>
                                <option value="read">Read ({{ $readCount }})</option>
                                <option value="unread">Unread ({{ $unreadCount }})</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Recipients List -->
                <div class="overflow-hidden border border-gray-200 rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Recipient
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Read At
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Email Status
                            </th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($recipients as $recipient)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                                    {{ substr($recipient->user->name, 0, 2) }}
                                                </span>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $recipient->user->name }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ $recipient->user->email }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($recipient->read_at)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Read
                                            </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                Unread
                                            </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @if($recipient->read_at)
                                        {{ $recipient->read_at->format('M j, Y H:i') }}
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($recipient->email_sent)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                Sent
                                            </span>
                                    @elseif($recipient->email_failed_at)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800" title="{{ $recipient->failure_reason }}">
                                                Failed
                                            </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                Pending
                                            </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                    No recipients found matching your criteria.
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($recipients->hasPages())
                    <div class="mt-6">
                        {{ $recipients->links() }}
                    </div>
                @endif
            </div>
        @endif

        @if($activeTab === 'targeting')
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Target Type -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-3">Target Type</h3>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="text-sm font-medium text-gray-900 capitalize">
                                {{ str_replace('_', ' ', $message->target_type) }}
                            </div>
                        </div>
                    </div>

                    <!-- Target Criteria -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-3">Targeting Criteria</h3>
                        <div class="bg-gray-50 p-4 rounded-lg space-y-2">
                            @if($message->target_criteria)
                                @foreach($message->target_criteria as $key => $value)
                                    @if(!empty($value))
                                        <div class="text-sm">
                                            <span class="font-medium text-gray-700">{{ ucfirst(str_replace('_', ' ', $key)) }}:</span>
                                            @if(is_array($value))
                                                <span class="text-gray-600">{{ implode(', ', $value) }}</span>
                                            @elseif(is_bool($value))
                                                <span class="text-gray-600">{{ $value ? 'Yes' : 'No' }}</span>
                                            @else
                                                <span class="text-gray-600">{{ $value }}</span>
                                            @endif
                                        </div>
                                    @endif
                                @endforeach
                            @else
                                <div class="text-sm text-gray-500">No specific criteria set</div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Additional Info -->
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-3">Additional Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <div class="text-sm font-medium text-gray-700 dark:text-gray-300">Priority</div>
                            <div class="mt-1">
                                @if($message->is_urgent)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Urgent
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        Normal
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div>
                            <div class="text-sm font-medium text-gray-700 dark:text-gray-300">Attachments</div>
                            <div class="mt-1 text-sm text-gray-600">
                                {{ $message->attachments->count() }} file(s)
                            </div>
                        </div>

                        <div>
                            <div class="text-sm font-medium text-gray-700 dark:text-gray-300">Created</div>
                            <div class="mt-1 text-sm text-gray-600">
                                {{ $message->created_at->format('M j, Y H:i') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
