@props(['blank_page_link' => url()->current()])
<div class="space-y-2">
  <div class="relative">
    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5 text-gray-500">
        <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
      </svg>
    </div>
    <x-form.input class="pl-10" name="" type="search" placeholder="Search Images..." wire:model.live="search" />
  </div>

  @if ($images->count())
  <div class="overflow-hidden rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800">
    <ul role="list" class="divide-y divide-gray-300">
      @foreach ($images as $image)
      <li x-data="{ url: @js(asset($image->path)), description: @js($image->description) }" class="flex">
        <div class="flex-1 px-6 py-4 space-y-1 overflow-auto">
          <p class="truncate text-sm font-medium text-gray-900">{{ $image->description }}</p>
          <div class="flex items-center space-x-1">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="flex-none w-5 h-5 text-gray-500">
              <path fill-rule="evenodd" d="M5.5 3A2.5 2.5 0 003 5.5v2.879a2.5 2.5 0 00.732 1.767l6.5 6.5a2.5 2.5 0 003.536 0l2.878-2.878a2.5 2.5 0 000-3.536l-6.5-6.5A2.5 2.5 0 008.38 3H5.5zM6 7a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
            </svg>

            <p class="text-xs text-gray-500 space-x-1">
              @foreach ($image->tags as $tag)
                <span class="inline-block truncate max-w-[12rem]">#{{ $tag }}</span>
              @endforeach
            </p>
          </div>
          <div class="space-x-1">
            <div x-data="{ show: false }" class="relative inline-block">
              <button x-on:click.away="show = false" x-on:click="navigator.clipboard && navigator.clipboard.writeText(url).then(() => show = true, setTimeout(() => show = false, 1000)).catch(() => {})" type="button" class="rounded bg-white px-2 py-1 text-xs font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">URL</button>
              <span x-cloak x-show="show" class="absolute -top-6 left-1/2 -translate-x-1/2 inline-flex items-center rounded-md bg-green-50 px-1.5 py-0.5 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">Copied!</span>
            </div>
            <div x-data="{ show: false }" class="relative inline-block">
              <button x-on:click.away="show = false" x-on:click="navigator.clipboard && navigator.clipboard.writeText('![' + description + '](' + url + ')').then(() => show = true, setTimeout(() => show = false, 1000)).catch(() => {})" type="button" class="rounded bg-white px-2 py-1 text-xs font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">MD</button>
              <span x-cloak x-show="show" class="absolute -top-6 left-1/2 -translate-x-1/2 inline-flex items-center rounded-md bg-green-50 px-1.5 py-0.5 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">Copied!</span>
            </div>
            <div x-data="{ show: false }" class="relative inline-block">
              <button x-on:click.away="show = false" x-on:click="navigator.clipboard && navigator.clipboard.writeText('<img alt=\'' + description + '\' src=\'' + url + '\'>').then(() => show = true, setTimeout(() => show = false, 1000)).catch(() => {})" type="button" class="rounded bg-white px-2 py-1 text-xs font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">HTML</button>
              <span x-cloak x-show="show" class="absolute -top-6 left-1/2 -translate-x-1/2 inline-flex items-center rounded-md bg-green-50 px-1.5 py-0.5 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">Copied!</span>
            </div>
          </div>
        </div>
        <div class="flex-none w-28 relative">
          <img :src="url" alt="{{ $image->description }}" class="absolute inset-0 w-full h-full object-cover">
        </div>
      </li>
      @endforeach
    </ul>
  </div>
  @else
    <x-blank :link="$blank_page_link"/>
  @endif
</div>

