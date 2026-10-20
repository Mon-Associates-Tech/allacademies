@props(['headers' => [], 'tableAttributes' => null, 'scrollable' => true])

<section {{ $attributes->merge(['class' => 'overflow-hidden rounded-md border border-line bg-surface shadow-[0_1px_2px_rgba(20,24,34,0.04),0_18px_40px_-30px_rgba(20,24,34,0.25)]']) }}>
    <div class="{{ $scrollable ? 'overflow-x-auto' : 'overflow-hidden' }}">
        <table class="w-full text-sm" {{ $tableAttributes }}>
            @isset($head)
                <thead>{{ $head }}</thead>
            @elseif(count($headers))
                <thead>
                <tr class="border-b border-line bg-surface-2">
                    @foreach($headers as $header)
                        <th class="whitespace-nowrap px-4 py-3 text-left font-mono text-[10px] font-semibold uppercase tracking-[0.14em] text-muted {{ $loop->last ? 'text-right' : '' }}">{{ $header }}</th>
                    @endforeach
                </tr>
                </thead>
            @endisset
            <tbody class="divide-y divide-line">{{ $slot }}</tbody>
        </table>
    </div>

    @isset($footer)
        <footer class="border-t border-line bg-surface-2 px-4 py-3">{{ $footer }}</footer>
    @endisset
</section>
