<div class="max-w-4xl mx-auto">
    <div class="bg-white shadow-lg rounded-lg">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $message->subject }}</h1>
                    @if($message->is_urgent)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 mt-2">
                            Urgent
                        </span>
                    @endif
                </div>
                <a href="{{ route('students.messages.index') }}"
                   class="text-sm text-gray-500 hover:text-gray-700">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </a>
            </div>
        </div>

        <!-- Message Details -->
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                            <span class="text-sm font-medium text-gray-700">
                                {{ substr($message->sender->name, 0, 1) }}
                            </span>
                        </div>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900">{{ $message->sender->name }}</p>
                        <p class="text-sm text-gray-500">{{ $message->sender->email }}</p>
                    </div>
                </div>
                <div class="text-sm text-gray-500">
                    {{ $message->created_at->format('M d, Y g:i A') }}
                </div>
            </div>
        </div>

        <!-- Message Body -->
        <div class="px-6 py-6">
            <div class="prose max-w-none">
                {!! nl2br(e($message->body)) !!}
            </div>
        </div>

        <!-- Attachments -->
        @if($message->attachments->count() > 0)
            <div class="px-6 py-4 border-t border-gray-200">
                <h3 class="text-sm font-medium text-gray-900 mb-3">Attachments</h3>
                <div class="space-y-2">
                    @foreach($message->attachments as $attachment)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-md">
                            <div class="flex items-center">
                                <svg class="h-5 w-5 text-gray-400 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $attachment->original_filename }}</div>
                                    <div class="text-xs text-gray-500">{{ $attachment->human_size }} • {{ $attachment->mime_type }}</div>
                                </div>
                            </div>
                            <a href="{{ Storage::url($attachment->path) }}"
                               download="{{ $attachment->original_filename }}"
                               class="text-blue-600 hover:text-blue-800">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Recipients -->
        <div class="px-6 py-4 border-t border-gray-200">
            <h3 class="text-sm font-medium text-gray-900 mb-2">Recipients</h3>
            <div class="flex flex-wrap gap-2">
                @foreach($message->recipients as $recipient)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                        {{ $recipient->name }}
                    </span>
                @endforeach
            </div>
        </div>

        <!-- Actions -->
        <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3">
            <a href="{{ route('students.messages.index') }}"
               class="px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-50">
                Back to Messages
            </a>
        </div>
    </div>
</div>
