<div class="space-y-2">
  <div class="relative">
    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20" stroke-width="2" stroke="currentColor" class="w-3 h-3 text-gray-500 dark:text-gray-400">
        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
      </svg>
    </div>
    <x-form.input class="p-2 pl-10" name="" type="search" placeholder="Search Images..." wire:model="searchTerm" />
  </div>
  <div class="overflow-hidden rounded-lg bg-white shadow">
    @if ($images->count())
      <ul class="divide-y divide-gray-100 py-1 px-2">
        @foreach($images as $img)
          <li class="flex">
            <div class="mr-4 flex-1 w-2/3">
              <h6 class="text-sm font-medium text-gray-900 text-clip overflow-hidden">{{$img->description}}</h6>
              <div class="text-xs text-gray-400 bottom-0">
                <span class="inline-flex">
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5">
                    <path fill-rule="evenodd" d="M5.5 3A2.5 2.5 0 003 5.5v2.879a2.5 2.5 0 00.732 1.767l6.5 6.5a2.5 2.5 0 003.536 0l2.878-2.878a2.5 2.5 0 000-3.536l-6.5-6.5A2.5 2.5 0 008.38 3H5.5zM6 7a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                  </svg>
                </span>
                @foreach($img->tags as $tag) 
                    <span class="mr-2">#{{$tag}}</span>
                  @endforeach
              </div>
            </div>
            <div class="relative m-1">
              <img src="{{ asset('storage/' . $img->path) }}" class="h-20 w-20 rounded-lg object-cover" alt="" />
              <div x-data="{ input: '{{ asset('storage/' . $img->path) }}', showMsg: false }"  class="group absolute bottom-0 left-0 right-0 top-0 w-full h-20 rounded-lg overflow-hidden bg-gray-200 bg-fixed opacity-0 transition duration-300 ease-in-out hover:opacity-90">  
                <a class="flex items-center justify-center px-5 text-sm font-normal text-center text-black border-t border-gray-50 bg-gray-300 hover:bg-gray-400 truncate rounded-b" type="button" @click="navigator.clipboard.writeText(input), showMsg = true, setTimeout(() => showMsg = false, 1000)"> 
                  <button id="clipboard" class="p-2 relative text-sm font-medium">Copy Url</button>
                </a>
                <div x-show="showMsg" @click.away="showMsg = false" class="overflow-hidden mt-2">
                  <p class="flex items-center justify-center text-black-700">Copied!</p>
                </div>
              </div>
            </div>
          </li>
        @endforeach
      </ul>
    @else
      <x-blank/>
    @endif
  </div>
</div>

