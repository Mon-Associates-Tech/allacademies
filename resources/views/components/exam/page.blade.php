@props(['maxWidth' => 'max-w-6xl'])

<div {{ $attributes->merge(['class' => 'bg-exam-sheet min-h-screen font-sans text-ink']) }}>
    @once
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&family=Zilla+Slab:wght@500;600;700&display=swap" rel="stylesheet">
    @endonce
    <div class="mx-auto {{ $maxWidth }} px-6 py-12 md:pl-28 lg:py-16">
        {{ $slot }}
    </div>
</div>