@props(['action' => false])

<th scope="col" class="{{ $action ? 'relative px-6 py-3' : 'px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider' }}">
    {{ $slot }}
</th>
