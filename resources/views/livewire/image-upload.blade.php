<div class="grid sm:grid-cols-2 gap-4">
  <form method="post" enctype="multipart/form-data" wire:submit.prevent="upload">
    @csrf
    <div class="sm:col-span-2 relative overflow-hidden rounded-md border border-gray-300 focus-within:ring bg-white"> 
      @error('tags') <span class="text-xs font-medium text-red-600 ml-2">{{ $message }}</span> @enderror
      <div wire:ignore>
        <input class="block w-full border-0 focus:border-0 focus:ring-0 p-2 text-lg" placeholder="Enter Tags" data-pharaonic="tagify" data-component-id="{{ $this->id }}" wire:model="tags" data-direct>
      </div>
      @error('description') <span class="text-xs font-medium text-red-600 ml-2">{{ $message }}</span> @enderror
      <textarea id="description" class="block w-full border-0 focus:border-0 focus:ring-0" rows="5" placeholder="Description" wire:model="description"></textarea>
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
</div>