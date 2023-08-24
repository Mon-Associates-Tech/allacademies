<div class="space-y-6">
  <div class="relative">
    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20" stroke-width="2" stroke="currentColor" class="w-4 h-4 text-gray-500 dark:text-gray-400">
        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
      </svg>
    </div>
    <x-form.input class="p-4 pl-10" name="" type="search" placeholder="Search Images..." wire:model="searchTerm" />
  </div>
  <div class="overflow-hidden rounded-lg bg-white shadow">
    @if ($images->count())
      <ul class="divide-y divide-gray-100 px-4">
        @foreach($images as $img)
          <li class="flex py-2 relative justify-center items-center">
            <div class="grid grid-cols-2 gap-4">
              <div class="mr-4">
                <p class="text-md font-medium text-gray-900 text-clip overflow-hidden pt-2">{{$img->description}}</p>
                <div class="mt-1 text-sm text-gray-400 align-baseline mb-4 text-clip overflow-hidden"><span>Uploaded On</span> • <time>{{ $img->created_at->format('F d, Y') }}</time></div>
                <div class="mt-1 text-sm text-gray-400 inline-flex absolute bottom-4">
                  <span>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-4">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z" />
                      <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z" />
                    </svg>
                  </span>
                  @foreach($img->tags as $tag) 
                      <span class="mr-2 text-clip overflow-hidden">#{{$tag}}</span>
                    @endforeach
                </div>
              </div> 
              <div class="mb-4 md:mb-0">
                <div class="relative max-w-xs overflow-hidden bg-cover bg-no-repeat">
                  <img src="{{ asset('storage/' . $img->path) }}" class="max-w-xs rounded-lg" alt="Image" />
                  <div x-data="{ input: '{{ asset('storage/' . $img->path) }}', showMsg: false }"  class="group absolute bottom-0 left-0 right-0 top-0 h-full w-full overflow-hidden bg-gray-200 bg-fixed opacity-0 transition duration-300 ease-in-out hover:opacity-90">  
                    <a class="flex items-center justify-center px-5 text-sm font-normal text-center text-black border-t border-purple-50 bg-purple-50 hover:bg-gray-100 truncate rounded-b" type="button" @click="navigator.clipboard.writeText(input), showMsg = true, setTimeout(() => showMsg = false, 1000)"> 
                      <button id="clipboard" class="relative pl-1 py-4 text-md font-medium">Copy Image Url</button>
                    </a>
                    <div x-show="showMsg" @click.away="showMsg = false" class="overflow-hidden mt-10">
                      <p class="flex items-center justify-center text-gray-700">Copied!</p>
                    </div>
                  </div>
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