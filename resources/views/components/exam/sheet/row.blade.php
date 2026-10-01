@props(['label', 'value' => null])

<div class="flex items-baseline gap-2 px-[18px] pt-2 text-[11.5px]">
    <span class="whitespace-nowrap text-[#6d6753]">{{ $label }}</span>
    <span class="flex-1 -translate-y-[3px] border-b-[1.5px] border-dotted border-[#b6ae97]"></span>
    <span class="max-w-[55%] truncate font-semibold">{{ $value ?: '···········' }}</span>
</div>