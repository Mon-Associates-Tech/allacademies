<x-layouts.app>
        @if(auth()->check() && !$has_token_subscription ?? false)
        <x-alert.token-subscription-banner />
    @else
    @livewire('user-books.shared-books')
    @endif
</x-layouts.app>
