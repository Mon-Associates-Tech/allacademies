@props(['maxWidth' => 'max-w-6xl', 'variant' => 'full'])

@php
    $isCompact = $variant === 'compact';
@endphp

<div {{ $attributes->merge(['class' => 'bg-exam-sheet min-h-screen font-sans text-ink']) }}>
    <div class="mx-auto {{ $maxWidth }} px-6 {{ $isCompact ? 'py-6 md:pl-28 lg:py-8' : 'py-12 md:pl-28 lg:py-16' }}">
        {{ $slot }}
    </div>
</div>
