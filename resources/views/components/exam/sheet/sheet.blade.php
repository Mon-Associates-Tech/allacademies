@props([
    'band' => 'CONFIDENTIAL — DO NOT TURN OVER UNTIL INSTRUCTED',
    'logo' => null,
    'title' => 'Your institution',
    'kind' => 'EXAMINATION PAPER',
    'meta' => 'EI-01 · AUTO-FILLED AT GENERATION',
])

<div {{ $attributes->merge(['class' => 'rotate-[0.4deg] rounded border border-[#e3dfd0] bg-[#fffdf7] pb-4 text-[#232733] shadow-[0_1px_0_rgba(31,36,52,0.05),0_24px_48px_-28px_rgba(31,36,52,0.35)] transition duration-300 hover:-translate-y-0.5 hover:rotate-0 motion-reduce:transition-none']) }} aria-hidden="true">
    <div class="border-b border-dashed border-[#d6cfb8] bg-[#f1ecdd] px-2.5 py-[7px] text-center font-mono text-[8.5px] tracking-[0.22em] text-[#6c6653]">{{ $band }}</div>

    <div class="flex justify-end gap-1.5 px-3.5 pt-2">
        @foreach([1, 2, 3, 4, 5, 6] as $b)
            <span class="h-2 w-2 rounded-full border border-[#b9b19a] @if($b === 4) border-accent bg-accent @endif"></span>
        @endforeach
    </div>

    <div class="flex items-center gap-3 px-[18px] pb-2.5 pt-3">
        @if($logo)
            <img src="{{ $logo }}" alt="" class="h-11 w-auto object-contain">
        @endif
        <div>
            <p class="font-display text-[17px] font-bold leading-tight">{{ $title }}</p>
            <p class="mt-0.5 font-mono text-[8.5px] tracking-[0.3em] text-[#8a8264]">{{ $kind }}</p>
        </div>
    </div>

    <div class="mx-[18px] space-y-[3px]">
        <div class="h-[1.5px] bg-[#232733]"></div>
        <div class="h-[1.5px] bg-[#232733]"></div>
    </div>

    {{ $slot }}

    <div class="mx-[18px] mt-4 flex items-center justify-between gap-2.5 border-t border-[#e6e0cd] pt-3">
        <span class="h-[26px] w-[120px] bg-[repeating-linear-gradient(90deg,#232733_0_2px,transparent_2px_4px,#232733_4px_5px,transparent_5px_9px,#232733_9px_12px,transparent_12px_14px)]"></span>
        <span class="font-mono text-[8.5px] tracking-[0.14em] text-[#8a8264]">{{ $meta }}</span>
    </div>
</div>