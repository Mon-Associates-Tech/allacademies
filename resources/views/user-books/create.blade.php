<x-layouts.app class="">
        @if(auth()->check() && !$has_token_subscription ?? false)
        <x-alert.token-subscription-banner />
    @else
    @livewire('user-books.user-book-form')
    @endif
</x-layouts.app>
