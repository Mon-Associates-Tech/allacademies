@props([
    'guard',        // 'bookshop_staff' or 'bookshop_customer'
    'routePrefix',  // 'bookshop.staff.' or 'bookshop.shop.'
])

@php
    $user = auth($guard)->user();
    $unreadCount = $user?->unreadNotifications()->count() ?? 0;
    $recent = $user?->notifications()->limit(5)->get() ?? collect();
@endphp

<div class="relative" data-notification-bell="true">
    <button type="button" data-bell-toggle class="relative text-slate-300 hover:text-white transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
        </svg>
        @if($unreadCount > 0)
            <span class="absolute -top-1 -right-1 flex items-center justify-center w-4 h-4 text-[10px] font-bold text-white bg-red-500" style="border-radius: 2px;">
                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
            </span>
        @endif
    </button>

    <div data-bell-dropdown class="hidden absolute right-0 mt-2 w-80 max-w-[calc(100vw-2rem)] bg-white dark:bg-slate-900 z-20"
         style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 4px 16px rgba(0,0,0,0.15);">
        <div class="flex items-center justify-between px-4 py-3 border-b border-slate-100 dark:border-slate-800">
            <span class="text-xs font-semibold text-slate-900 dark:text-white uppercase tracking-wider">Notifications</span>
            @if($unreadCount > 0)
                <form method="POST" action="{{ route($routePrefix.'notifications.mark-all-read') }}">
                    @csrf @method('PATCH')
                    <button type="submit" class="text-xs text-purple-600 dark:text-purple-400">Mark all read</button>
                </form>
            @endif
        </div>

        <div class="max-h-80 overflow-y-auto">
            @forelse($recent as $notification)
                <a href="{{ route($routePrefix.'notifications.open', $notification->id) }}"
                   class="block w-full text-left px-4 py-3 border-b border-slate-100 dark:border-slate-800 last:border-0 hover:bg-slate-50 dark:hover:bg-slate-800/50 {{ $notification->read_at ? 'opacity-60' : '' }}">
                    <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $notification->data['title'] ?? 'Notification' }}</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">{{ $notification->data['body'] ?? '' }}</p>
                    <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                </a>
            @empty
                <p class="px-4 py-6 text-sm text-slate-500 dark:text-slate-400 text-center">No notifications yet.</p>
            @endforelse
        </div>

        <a href="{{ route($routePrefix.'notifications.index') }}" class="block px-4 py-3 text-center text-xs text-purple-600 dark:text-purple-400 border-t border-slate-100 dark:border-slate-800">
            View All
        </a>
    </div>
</div>

@once
    <script>
        (function initNotificationBells() {
            const init = (wrapper) => {
                if (!wrapper || wrapper.dataset.ready) return;
                wrapper.dataset.ready = 'true';

                const toggle = wrapper.querySelector('[data-bell-toggle]');
                const dropdown = wrapper.querySelector('[data-bell-dropdown]');

                toggle.addEventListener('click', (e) => {
                    e.stopPropagation();
                    dropdown.classList.toggle('hidden');
                });

                document.addEventListener('click', (e) => {
                    if (!wrapper.contains(e.target)) dropdown.classList.add('hidden');
                });
            };

            const hydrate = () => document.querySelectorAll('[data-notification-bell="true"]').forEach(init);
            document.addEventListener('DOMContentLoaded', hydrate);
            setTimeout(hydrate, 0);
        })();
    </script>
@endonce
