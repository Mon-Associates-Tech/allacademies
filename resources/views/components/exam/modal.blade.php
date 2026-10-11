{{-- resources/views/components/exam/modal.blade.php --}}
@props([
    'title' => null,
    'maxWidth' => 'max-w-md',
    'closeEvent' => 'close-modal',
])

<div
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4"
    @click.self="$dispatch('{{ $closeEvent }}')"
    @keydown.escape.window="$dispatch('{{ $closeEvent }}')"
>
    <div {{ $attributes->merge(['class' => "relative flex max-h-[90vh] w-full {$maxWidth} animate-rise flex-col rounded-md border border-line bg-surface shadow-2xl"]) }}>
        <div class="flex items-center justify-between gap-4 border-b border-line bg-surface-2 px-5 py-3.5">
            <h3 class="font-display text-base font-bold text-ink">
                {{ $title }}
            </h3>

            <button
                type="button"
                class="rounded-md p-1 text-muted transition hover:bg-surface hover:text-ink focus:outline-none focus:ring-2 focus:ring-primary/20"
                @click="$dispatch('{{ $closeEvent }}')"
                aria-label="Close modal"
            >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div class="flex-1 overflow-y-auto p-5">
            {{ $slot }}
        </div>

        @isset($footer)
            <div class="flex justify-end gap-2 border-t border-line bg-surface-2 px-5 py-4">
                {{ $footer }}
            </div>
        @endisset
    </div>
</div>