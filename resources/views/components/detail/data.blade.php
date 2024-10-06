@props(['label', 'expand' => false,])

<div class="{{ $expand ? 'sm:col-span-2' : 'sm:col-span-1' }}">
    <dt class="text-sm font-medium text-gray-300">
      {{ $label }}
    </dt>
    <dd class="mt-1 text-sm text-gray-900">
      {{ $slot }}
    </dd>
</div>