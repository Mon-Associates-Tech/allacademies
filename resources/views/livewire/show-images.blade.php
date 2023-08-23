<div class="space-y-6">
  <div class="relative">
    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
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
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5 mr-2">
                      <path fill-rule="evenodd" d="M5.5 3A2.5 2.5 0 003 5.5v2.879a2.5 2.5 0 00.732 1.767l6.5 6.5a2.5 2.5 0 003.536 0l2.878-2.878a2.5 2.5 0 000-3.536l-6.5-6.5A2.5 2.5 0 008.38 3H5.5zM6 7a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                    </svg>
                  </span>
                  @foreach($img->tags as $tag) 
                      <span class="mr-2 text-clip overflow-hidden">#{{$tag}}</span>
                    @endforeach
                </div>
              </div> 
              <div class="mb-4 md:mb-0">
                <div class="relative max-w-xs overflow-hidden bg-cover bg-no-repeat">
                  <img
                    src="{{ asset('storage/' . $img->path) }}"
                    class="max-w-xs rounded-lg"
                    alt="Image" />
                  <div class="group absolute bottom-0 left-0 right-0 top-0 h-full w-full overflow-hidden bg-gray-200 bg-fixed opacity-0 transition duration-300 ease-in-out hover:opacity-90">
                    <button class="flex group-hover:visible bg-gray-400 hover:bg-gray-500 text-gray-800 font-bold py-4 px-4 rounded inline-flex items-center justify-center w-full" data-path="{{ asset('storage/' . $img->path) }}" onclick="copyText(this)">
                      Copy Image Url
                    </button>
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
  
<script>
    let copyText = button => {
      path = button.getAttribute('data-path');
      navigator.clipboard.writeText(path)
        .then(() => {
          button.innerHTML = "Copied!";
        })
        .catch(() => {
          button.innerHTML = "Error!";
      });
    }
</script>