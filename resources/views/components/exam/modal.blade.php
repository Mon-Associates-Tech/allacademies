@props(['title' => null, 'maxWidth' => 'max-w-md'])

<div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4">
    <div {{ $attributes->merge(['class' => "flex max-h-[90vh] w-full {$maxWidth} animate-rise flex-col rounded-md border border-line bg-surface shadow-2xl"]) }}>
        @if($title)
            <div class="border-b border-line bg-surface-2 px-5 py-3.5">
                <h3 class="font-display text-base font-bold text-ink">{{ $title }}</h3>
            </div>
        @endif

        <div class="flex-1 overflow-y-auto p-5">{{ $slot }}</div>

        @isset($footer)
            <div class="flex justify-end gap-2 border-t border-line bg-surface-2 px-5 py-4">{{ $footer }}</div>
        @endisset
    </div>
</div>