{{-- <div class="sm:col-span-2 relative overflow-hidden rounded-md border border-gray-300 focus-within:ring bg-white focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-500 dark:focus:border-blue-500"> 
  @error('tags') <span class="text-xs font-medium text-red-600 ml-2">{{ $message }}</span> @enderror
  <div class="relative">
    <input class="block w-full border-0 focus:border-0 focus:ring-0 pt-2 text-lg" placeholder="Add Tags" wire:model="tag" wire:keydown.enter="addTag(@js($tag))">
    @if ($showDiv)
    @if (!empty($tags_suggest))
      <div class="absolute z-10 w-full border divide-y shadow max-h-72 overflow-y-auto bg-white">
        @foreach($tags_suggest as $suggestion)
          <a class="block p-2 hover:bg-indigo-50" href="#" wire:click="addTag(@js($suggestion) )">{{$suggestion}}</a>
        @endforeach
      </div>
    @endif
    @endif
    @foreach($tags as $item)
      <div class="bg-blue-100 inline-flex items-center text-sm rounded mt-2 mr-2 overflow-hidden">
        <span class="ml-2 mr-1 leading-relaxed truncate max-w-xs px-1">{{$item}}</span>
        <button class="w-6 h-8 inline-block align-middle text-gray-500 bg-blue-200 focus:outline-none" wire:click="removeTag({{$loop->index}})">
          <svg class="w-6 h-6 fill-current mx-auto" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M15.78 14.36a1 1 0 0 1-1.42 1.42l-2.82-2.83-2.83 2.83a1 1 0 1 1-1.42-1.42l2.83-2.82L7.3 8.7a1 1 0 0 1 1.42-1.42l2.83 2.83 2.82-2.83a1 1 0 0 1 1.42 1.42l-2.83 2.83 2.83 2.82z"/></svg>
        </button>
      </div>
    @endforeach
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
    <x-button.primary class="ml-2 mr-2 p-2" wire:click="upload">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 mr-2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
      </svg>
      Upload 
    </x-button.primary>
  </div>
</div> --}}



<div>
  <div class="space-y-6">
    <div class="sm:col-span-2">
      <x-form.file name="image" name="image" wire:model="image"/>
    </div>
    <div class="sm:col-span-2">
      <x-form.textarea name="description" type="text" wire:model="description"/>
    </div>
    <div class="relative">
      <div class="sm:col-span-2">
        <div class="space-y-1">
          <label for="tag" class="block text-sm tracking-wide font-medium text-gray-700">Tags</label>
          <input name="tag" id="tag" type="text" class = "border-gray-300 rounded-lg shadow-sm w-full leading-tight" wire:model="tag" wire:keydown.enter="addTag(@js($tag))">
          @error('tags') <span class="text-xs font-medium text-red-600">{{ $message }}</span> @enderror
      </div>
      </div>
      @if ($showDiv)
        @if (!empty($tags_suggest))
          <div class="absolute z-10 w-full border divide-y shadow max-h-72 overflow-y-auto bg-white">
            @foreach($tags_suggest as $suggestion)
              <a class="block p-2 hover:bg-indigo-50" href="#" wire:click="addTag(@js($suggestion) )">{{$suggestion}}</a>
            @endforeach
          </div>
        @endif
      @endif
      @foreach($tags as $item)
        <div class="bg-blue-100 inline-flex items-center text-sm rounded mt-2 mr-2 overflow-hidden">
          <span class="ml-2 mr-1 leading-relaxed truncate max-w-xs px-1">{{$item}}</span>
          <button class="w-6 h-8 inline-block align-middle text-gray-500 bg-blue-200 focus:outline-none" wire:click="removeTag({{$loop->index}})">
            <svg class="w-6 h-6 fill-current mx-auto" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M15.78 14.36a1 1 0 0 1-1.42 1.42l-2.82-2.83-2.83 2.83a1 1 0 1 1-1.42-1.42l2.83-2.82L7.3 8.7a1 1 0 0 1 1.42-1.42l2.83 2.83 2.82-2.83a1 1 0 0 1 1.42 1.42l-2.83 2.83 2.83 2.82z"/></svg>
          </button>
        </div>
      @endforeach
    </div>
  </div>
  <div class="flex justify-end mt-5">
    <x-button.primary class="ml-2" wire:click="upload">Upload Image</x-button.primary>
  </div>
</div>


{{-- <div class="editor flex flex-col text-gray-800 border border-gray-300 p-4 shadow-lg space-y-1">
  @error('tags') <span class="text-xs font-medium text-red-600">{{ $message }}</span> @enderror
  <div class="relative">
    <input class="bg-gray-100 border border-gray-300 mb-4 rounded-lg shadow-sm w-full leading-tight" placeholder="Enter Tag" type="text" wire:model="tag" wire:keydown.enter="addTag(@js($tag))"/>
    @if ($showDiv)
    @if (!empty($tags_suggest))
      <div class="absolute z-10 w-full border divide-y shadow max-h-72 overflow-y-auto bg-white">
        @foreach($tags_suggest as $suggestion)
          <a class="block p-2 hover:bg-indigo-50" href="#" wire:click="addTag(@js($suggestion) )">{{$suggestion}}</a>
        @endforeach
      </div>
    @endif
    @endif
    @foreach($tags as $item)
      <div class="bg-blue-100 inline-flex items-center text-sm rounded mt-2 mr-2 overflow-hidden">
        <span class="ml-2 mr-1 leading-relaxed truncate max-w-xs px-1">{{$item}}</span>
        <button class="w-6 h-8 inline-block align-middle text-gray-500 bg-blue-200 focus:outline-none" wire:click="removeTag({{$loop->index}})">
          <svg class="w-6 h-6 fill-current mx-auto" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M15.78 14.36a1 1 0 0 1-1.42 1.42l-2.82-2.83-2.83 2.83a1 1 0 1 1-1.42-1.42l2.83-2.82L7.3 8.7a1 1 0 0 1 1.42-1.42l2.83 2.83 2.82-2.83a1 1 0 0 1 1.42 1.42l-2.83 2.83 2.83 2.82z"/></svg>
        </button>
      </div>
    @endforeach
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
    <x-button.primary class="ml-2" wire:click="upload">Upload Image</x-button.primary>
  </div>
</div> --}}


{{-- <div class="editor flex flex-col text-gray-800 border border-gray-300 p-4 shadow-lg">
  <div class="space-y-6">
    <div class="relative">
      <div class="sm:col-span-2">
        <div class="space-y-1">
          <label for="tag" class="block text-sm tracking-wide font-medium text-gray-700">Tags</label>
          <input name="tag" id="tag" type="text" class = "border-gray-300 rounded-lg shadow-sm w-full leading-tight" wire:model="tag" wire:keydown.enter="addTag(@js($tag))">
          @error('tags') <span class="text-xs font-medium text-red-600">{{ $message }}</span> @enderror
      </div>
      </div>
      @if ($showDiv)
      @if (!empty($tags_suggest))
        <div class="absolute z-10 w-full border divide-y shadow max-h-72 overflow-y-auto bg-white">
          @foreach($tags_suggest as $suggestion)
            <a class="block p-2 hover:bg-indigo-50" href="#" wire:click="addTag(@js($suggestion) )">{{$suggestion}}</a>
          @endforeach
        </div>
      @endif
      @endif
      @foreach($tags as $item)
        <div class="bg-blue-100 inline-flex items-center text-sm rounded mt-2 mr-2 overflow-hidden">
          <span class="ml-2 mr-1 leading-relaxed truncate max-w-xs px-1">{{$item}}</span>
          <button class="w-6 h-8 inline-block align-middle text-gray-500 bg-blue-200 focus:outline-none" wire:click="removeTag({{$loop->index}})">
            <svg class="w-6 h-6 fill-current mx-auto" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M15.78 14.36a1 1 0 0 1-1.42 1.42l-2.82-2.83-2.83 2.83a1 1 0 1 1-1.42-1.42l2.83-2.82L7.3 8.7a1 1 0 0 1 1.42-1.42l2.83 2.83 2.82-2.83a1 1 0 0 1 1.42 1.42l-2.83 2.83 2.83 2.82z"/></svg>
          </button>
        </div>
      @endforeach
    </div>
    <div class="sm:col-span-2">
      <x-form.textarea name="description" type="text" rows=5 wire:model="description"/>
    </div>
    <label class="icons flex text-gray-500 m-2 pt-2" for="image">
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
      <x-button.primary class="ml-2" wire:click="upload">Upload Image</x-button.primary>
    </div>
  </div>
</div> --}}
