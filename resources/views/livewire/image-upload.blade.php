<form method="post" enctype="multipart/form-data" wire:submit.prevent="upload">
  @csrf
  <div class="sm:col-span-2 relative overflow-hidden rounded-md border border-gray-300 focus-within:ring bg-white focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-500 dark:focus:border-blue-500"> 
    @error('tags') <span class="text-xs font-medium text-red-600 ml-2">{{ $message }}</span> @enderror
    <div wire:ignore>
      <input class="block w-full border-0 focus:border-0 focus:ring-0 pt-2 text-lg" placeholder="Enter Tags" data-pharaonic="tagify" data-component-id="{{ $this->id }}" wire:model="tags" data-suggest data-suggest-list="{{json_encode($tags_suggest)}}" data-classname="customSuggestionsList" data-direct>
    </div>
    @error('description') <span class="text-xs font-medium text-red-600 ml-2">{{ $message }}</span> @enderror
    <textarea id="description" class="block w-full border-0 focus:border-0 focus:ring-0" rows="4" placeholder="Description" wire:model="description"></textarea>
    <hr class="h-px border-0 bg-gray-300" />
    <div class="flex w-full items-center justify-between">
      <div class="relative">
        @error('image') <span class="text-xs font-medium text-red-600 items-center justify-between ml-2">{{ $message }}</span> @enderror
        <label title="Click to upload" for="image" class="cursor-pointer flex items-center gap-4 px-6 py-4 group before:absolute">
        <div class="w-max relative">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 01-6.364-6.364l10.94-10.94A3 3 0 1119.5 7.372L8.552 18.32m.009-.01l-.01.01m5.699-9.941l-7.81 7.81a1.5 1.5 0 002.112 2.13" />
          </svg>            
          </div>
          <div class="relative">
            <span class="flex space-x-1">Attach Image</span>
          </div>
        </label>
        @if ($image)
          <label class="px-6">{{$image->getClientOriginalName()}} </label>
        @endif
        <input class="opacity-0" type="file" name="image" id="image" wire:model="image" hidden>
      </div>
      <x-button.primary class="ml-2 mr-2 p-2">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 mr-2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
        </svg>
        Upload 
      </x-button.primary>
    </div>
  </div>
</form>

<div class="relative">
  <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
      <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
      </svg>
  </div>
  <x-form.input class="p-4 pl-10" name="" type="search" placeholder="Search Images..." wire:model="term"/>
</div>

<div class="overflow-hidden rounded-lg bg-white shadow">
  @if ($images->count())
  <ul class="divide-y divide-gray-100 px-4">
    @foreach($images as $img)
      <li class="flex py-2 relative justify-center items-center">
        <div class="mr-4 flex-1">
          <h4 class="text-lg font-medium text-gray-900">{{$img->description}}</h4>
          <div class="mt-1 text-sm text-gray-400 align-baseline mb-4"><span>Uploaded On</span> • <time>{{ $img->created_at->format('F d, Y') }}</time></div>
          <div class="mt-1 text-sm text-gray-400 inline-flex">
            <span>
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5 mr-2">
                <path fill-rule="evenodd" d="M5.5 3A2.5 2.5 0 003 5.5v2.879a2.5 2.5 0 00.732 1.767l6.5 6.5a2.5 2.5 0 003.536 0l2.878-2.878a2.5 2.5 0 000-3.536l-6.5-6.5A2.5 2.5 0 008.38 3H5.5zM6 7a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
              </svg>
            </span>

            @foreach($img->tags as $tag) 
                <span class="mr-2">#{{$tag}}</span>
              @endforeach
          </div>
        </div> 
        <div class="mb-4 md:mb-0">
          <div class="relative max-w-xs overflow-hidden bg-cover bg-no-repeat">
            <img
              src="{{ asset('storage/' . $img->path) }}"
              class="max-w-xs"
              alt="Image" />
            <div class="group absolute bottom-0 left-0 right-0 top-0 h-full w-full overflow-hidden bg-gray-200 bg-fixed opacity-0 transition duration-300 ease-in-out hover:opacity-90">
              <button class="flex group-hover:visible bg-gray-400 hover:bg-gray-500 text-gray-800 font-bold py-4 px-4 rounded inline-flex items-center justify-center w-full" data-path="{{ asset('storage/' . $img->path) }}" onclick="copyText(this)">
                Copy Image Url
              </button>
            </div>
            
          </div>
        </div>
      </li>
    @endforeach
  </ul>
  @else
  <x-blank />
  @endif
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

