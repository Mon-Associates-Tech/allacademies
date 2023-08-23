<style>
  .customSuggestionsList{border:0px!important; outline:0px !important;}
</style>
<form method="post" enctype="multipart/form-data" wire:submit.prevent="upload">
  @csrf
  <div class="sm:col-span-2 relative overflow-hidden rounded-md border border-gray-300 focus-within:ring bg-white focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-500 dark:focus:border-blue-500"> 
    @error('tags') <span class="text-xs font-medium text-red-600 ml-2">{{ $message }}</span> @enderror
    <div wire:ignore>
      <input class="block w-full border-0 focus:border-0 focus:ring-0 pt-2 text-lg" placeholder="Enter Tags" data-pharaonic="tagify" data-component-id="{{ $this->id }}" wire:model="tags" data-suggest data-suggest-list="{{ json_encode($tags_suggest) }}" data-classname="customSuggestionsList" data-direct>
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


{{-- <form method="post" enctype="multipart/form-data" wire:submit.prevent="upload">
  @csrf
  <div class="space-y-6">
    <div class="sm:col-span-2">
      <x-form.file name="image" name="image" wire:model="image"/>
    </div>
    <div class="sm:col-span-2">
      <x-form.textarea name="description" type="text" wire:model="description"/>
    </div>
    <div class="space-y-1">
      <div class="sm:col-span-2 space-y-1">
        <label class="block text-sm tracking-wide font-medium text-gray-700">Tags</label>
        <div wire:ignore>
          <input name="tags" id="tags" type="text" class="bg-white border-gray-300 rounded-lg shadow-sm w-full leading-tight" data-pharaonic="tagify" data-component-id="{{ $this->id }}" wire:model="tags" data-suggest data-suggest-list="{{ json_encode($tags_suggest) }}" data-direct />
        </div>
      </div>
      @error('tags') <span class="text-xs font-medium text-red-600 ml-2">{{ $message }}</span> @enderror
    </div>
  </div>
  <div class="flex justify-end mt-5">
    <x-button.primary class="ml-2">Upload Image</x-button.primary>
  </div>
</form> --}}

{{-- <form method="post" enctype="multipart/form-data" wire:submit.prevent="upload" class="bg-white">
  @csrf
  <div class="editor flex flex-col text-gray-800 border border-gray-300 p-4 shadow-lg space-y-1">
    @error('tags') <span class="text-xs font-medium text-red-600">{{ $message }}</span> @enderror
    <div wire:ignore>
      <input class="bg-gray-100 border border-gray-300 p-2 mb-4 rounded-lg shadow-sm w-full leading-tight" placeholder="Enter Tags" type="text" data-pharaonic="tagify" data-component-id="{{ $this->id }}" wire:model="tags" data-suggest data-suggest-list="{{ json_encode($tags_suggest) }}" data-direct />
    </div>
    @error('description') <span class="text-xs font-medium text-red-600">{{ $message }}</span> @enderror
    <textarea class="bg-gray-100 sec p-3 h-40 border border-gray-300 outline-none rounded-lg shadow-sm w-full leading-tight" placeholder="Describe image here" wire:model="description"></textarea>
    <label class="icons flex text-gray-500 m-2 pt-4" for="image">
      <svg class="mr-2 cursor-pointer hover:text-gray-700 border rounded-full p-1 h-7" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 01-6.364-6.364l10.94-10.94A3 3 0 1119.5 7.372L8.552 18.32m.009-.01l-.01.01m5.699-9.941l-7.81 7.81a1.5 1.5 0 002.112 2.13"/>   
      </svg>
      <div class="relative">
        <span class="flex space-x-1 cursor-pointer">Attach Image</span>
      </div>
    </label>
    @if ($image)
      <label class="px-6">{{$image->getClientOriginalName()}} </label>
    @endif
    <input hidden type="file" id="image" name="image" wire:model="image">
    @error('image') <span class="text-xs font-medium text-red-600">{{ $message }}</span> @enderror
    <div class="flex justify-end mt-3">
      <x-button.primary class="ml-2">Upload Image</x-button.primary>
    </div>
  </div>
</form> --}}

{{-- <form method="post" enctype="multipart/form-data" wire:submit.prevent="upload">
  @csrf
  <div class="editor flex flex-col text-gray-800 border border-gray-300 p-4 shadow-lg space-y-1">
    <div class="space-y-6">
      <div class="sm:col-span-2">
        <x-form.textarea name="description" type="text" rows=5 wire:model="description"/>
      </div>
      <div class="space-y-1">
        <div class="sm:col-span-2 space-y-1">
          <label class="block text-sm tracking-wide font-medium text-gray-700">Tags</label>
          <div wire:ignore>
            <input name="tags" id="tags" type="text" class="bg-white border-gray-300 rounded-lg shadow-sm w-full leading-tight" data-pharaonic="tagify" data-component-id="{{ $this->id }}" wire:model="tags" data-suggest data-suggest-list="{{ json_encode($tags_suggest) }}" data-direct />
          </div>
        </div>
        @error('tags') <span class="text-xs font-medium text-red-600 ml-2">{{ $message }}</span> @enderror
      </div>
    </div>
    <label class="icons flex text-gray-500 m-2 pt-4" for="image">
      <svg class="mr-2 cursor-pointer hover:text-gray-700 border rounded-full p-1 h-7" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 01-6.364-6.364l10.94-10.94A3 3 0 1119.5 7.372L8.552 18.32m.009-.01l-.01.01m5.699-9.941l-7.81 7.81a1.5 1.5 0 002.112 2.13"/>   
      </svg>
      <div class="relative">
        <span class="flex space-x-1 cursor-pointer">Attach Image</span>
      </div>
    </label>
    @if ($image)
      <label class="px-6">{{$image->getClientOriginalName()}} </label>
    @endif
    <input hidden type="file" id="image" name="image" wire:model="image">
    @error('image') <span class="text-xs font-medium text-red-600">{{ $message }}</span> @enderror
    <div class="flex justify-end mt-3">
      <x-button.primary class="ml-2">Upload Image</x-button.primary>
    </div>
  </div>
</form> --}}