<x-layouts.app pageName="View Notification">
    <x-slot name="title">View Notification</x-slot>

    @php
        $school = auth()->user()->school;
        $baseVars = [
            'school_name'  => $school?->name ?? config('app.name'),
            'currency'     => $school?->currency ?? 'GHS',
            'student_name' => '…', 'recipient_name' => '…', 'term_name' => '…',
            'balance' => '…', 'due_date' => '…', 'total_amount' => '…',
            'amount_paid' => '…', 'event_title' => '…', 'event_date' => '…',
            'event_venue' => '…', 'message_body' => $message->body ?? '…',
        ];
        $subject = $message->template ? $message->template->renderSubject($baseVars) : $message->subject;
        $body    = $message->template ? $message->template->renderBody($baseVars) : $message->body;
    @endphp

    <div class="max-w-3xl mx-auto space-y-5">

        {{-- Back + actions --}}
        <div class="flex items-center justify-between">
            <a href="{{ route('accountant.notifications.index') }}"
                class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                ← Back to Notifications
            </a>
            @if($message->status === 'draft')
                <a href="{{ route('accountant.notifications.compose', ['draft' => $message->id]) }}"
                    class="px-4 py-2 text-sm font-semibold bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                    Edit Draft
                </a>
            @endif
        </div>

        {{-- Main card --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">

            {{-- Header --}}
            <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        @if($message->is_urgent)
                            <span class="inline-block mb-2 text-xs font-bold text-red-600 dark:text-red-400 uppercase tracking-wide">⚠ Urgent</span>
                        @endif
                        <h1 class="text-xl font-bold text-gray-900 dark:text-white">{{ $subject }}</h1>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            Sent by <strong class="text-gray-700 dark:text-gray-300">{{ $message->sender->name }}</strong>
                            @if($message->sent_at)
                                · {{ $message->sent_at->format('M j, Y \a\t g:i A') }}
                            @endif
                        </p>
                    </div>
                    @php
                        $badge = match($message->status) {
                            'sent'      => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
                            'scheduled' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
                            'draft'     => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
                            'failed'    => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
                            default     => 'bg-yellow-100 text-yellow-800',
                        };
                    @endphp
                    <span class="shrink-0 px-3 py-1 text-xs font-semibold rounded-full {{ $badge }} capitalize">{{ $message->status }}</span>
                </div>
            </div>

            {{-- Meta --}}
            <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/30 border-b border-gray-200 dark:border-gray-700 grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Template</p>
                    <p class="font-medium text-gray-700 dark:text-gray-300">{{ $message->template?->name ?? 'None' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Channels</p>
                    <p class="font-medium text-gray-700 dark:text-gray-300">{{ implode(', ', $message->channels ?? ['email']) }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Recipients</p>
                    <p class="font-medium text-gray-700 dark:text-gray-300">{{ $message->recipients()->count() }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Target</p>
                    <p class="font-medium text-gray-700 dark:text-gray-300 capitalize">{{ str_replace('_', ' ', $message->target_type ?? '—') }}</p>
                </div>
            </div>

            {{-- Body --}}
            <div class="px-6 py-6">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">Message Body</p>
                <div class="prose prose-sm dark:prose-invert max-w-none text-gray-700 dark:text-gray-300 whitespace-pre-line leading-relaxed">{{ $body }}</div>
            </div>

            {{-- SMS body --}}
            @if(!empty($message->context_data['sms_body']))
                <div class="px-6 pb-6">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2">SMS Body</p>
                    <div class="p-3 bg-gray-50 dark:bg-gray-700/30 rounded-lg text-sm text-gray-700 dark:text-gray-300 font-mono">
                        {{ $message->context_data['sms_body'] }}
                    </div>
                </div>
            @endif

        </div>

        {{-- Recipients --}}
        @if($message->recipients()->count() > 0)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-sm font-semibold text-gray-900 dark:text-white">
                        Recipients <span class="text-gray-400 font-normal">({{ $message->recipients()->count() }})</span>
                    </h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email Sent</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SMS Sent</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">In-App</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($message->recipients()->with('user')->limit(50)->get() as $r)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                                    <td class="px-6 py-3 font-medium text-gray-900 dark:text-white">{{ $r->user?->name ?? '—' }}</td>
                                    <td class="px-6 py-3 text-gray-500 dark:text-gray-400">{{ $r->user?->email ?? '—' }}</td>
                                    <td class="px-6 py-3">
                                        @if($r->email_sent)
                                            <span class="text-green-600 dark:text-green-400 text-xs font-medium">✓ {{ $r->email_sent_at?->format('g:i A') }}</span>
                                        @elseif($r->failure_reason)
                                            <span class="text-red-500 text-xs" title="{{ $r->failure_reason }}">✗ Failed</span>
                                        @else
                                            <span class="text-gray-300 dark:text-gray-600 text-xs">—</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-3">
                                        @if($r->sms_sent)
                                            <span class="text-green-600 dark:text-green-400 text-xs font-medium">✓</span>
                                        @else
                                            <span class="text-gray-300 dark:text-gray-600 text-xs">—</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-3">
                                        @if($r->in_app_sent)
                                            <span class="text-green-600 dark:text-green-400 text-xs font-medium">✓</span>
                                        @else
                                            <span class="text-gray-300 dark:text-gray-600 text-xs">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @if($message->recipients()->count() > 50)
                        <p class="px-6 py-3 text-xs text-gray-400">Showing first 50 of {{ $message->recipients()->count() }} recipients.</p>
                    @endif
                </div>
            </div>
        @endif

    </div>
</x-layouts.app>
