@props(['lines' => [], 'fresh' => false])

<div {{ $attributes->merge(['class' => 'relative grid h-24 w-24 shrink-0 rotate-[8deg] place-items-center rounded-full border-2 border-accent text-accent' . ($fresh ? ' animate-stamp' : '')]) }} aria-hidden="true">
    <span class="absolute inset-[5px] rounded-full border border-dashed border-accent opacity-70"></span>
    <span class="flex flex-col items-center gap-0.5 font-mono text-[10px] font-semibold tracking-[0.18em]">
        @foreach($lines as $line)<span>{{ $line }}</span>@endforeach
    </span>
</div>