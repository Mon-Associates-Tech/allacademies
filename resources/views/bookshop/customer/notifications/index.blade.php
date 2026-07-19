<x-bookshop::layouts.customer :title="'Notifications - BookShop'">
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-bold text-slate-900 dark:text-white" style="font-family: 'Georgia', serif;">Notifications</h1>
        <form method="POST" action="{{ route('bookshop.shop.notifications.mark-all-read') }}">
            @csrf @method('PATCH')
            <button type="submit" class="text-sm text-purple-600 dark:text-purple-400 font-medium">Mark all read</button>
        </form>
    </div>

    <div class="bg-white dark:bg-slate-900 overflow-hidden" style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
        @forelse($notifications as $notification)
            <form method="POST" action="{{ route('bookshop.shop.notifications.read', $notification->id) }}"
                  class="border-b border-slate-100 dark:border-slate-800 last:border-0">
                @csrf @method('PATCH')
                <button type="submit" class="w-full text-left px-5 py-4 hover:bg-slate-50 dark:hover:bg-slate-800/50 flex items-start gap-3 {{ $notification->read_at ? 'opacity-60' : '' }}">
                    @if(! $notification->read_at)
                        <span class="w-2 h-2 mt-1.5 bg-purple-500 flex-shrink-0" style="border-radius: 2px;"></span>
                    @else
                        <span class="w-2 h-2 mt-1.5 flex-shrink-0"></span>
                    @endif
                    <span class="flex-1">
                        <span class="block text-sm font-semibold text-slate-900 dark:text-white">{{ $notification->data['title'] ?? 'Notification' }}</span>
                        <span class="block text-sm text-slate-500 dark:text-slate-400 mt-0.5">{{ $notification->data['body'] ?? '' }}</span>
                        <span class="block text-xs text-slate-400 dark:text-slate-500 mt-1">{{ $notification->created_at->diffForHumans() }}</span>
                    </span>
                </button>
            </form>
        @empty
            <p class="px-5 py-12 text-center text-sm text-slate-500 dark:text-slate-400">No notifications yet.</p>
        @endforelse
    </div>

    @if($notifications->hasPages())
        <div class="bg-white dark:bg-slate-900 px-5 py-4" style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06);">
            {{ $notifications->links() }}
        </div>
    @endif
</x-bookshop::layouts.customer>
