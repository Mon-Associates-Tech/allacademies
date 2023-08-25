
<div class="space-y-2">
  <x-form.file-upload name="image" name="image" wire:model="image"/>
  <x-form.textarea name="description" type="text" wire:model="description"/>
  <div class="relative">
    <div>
      <label for="tag" class="block text-sm tracking-wide font-medium text-gray-700">Tags</label>
      <input name="tag" id="tag" type="text" class = "border-gray-300 rounded-lg shadow-sm w-full leading-tight" wire:model="tag" wire:keydown.enter="addTag(@js($tag))">
      @error('tags') <span class="text-xs font-medium text-red-600">{{ $message }}</span> @enderror
    </div>
    @if($showTagsSuggestions)
      @if(!empty($tags_suggest))
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
  <div class="flex justify-end mt-5">
    <x-button.primary class="ml-2" wire:click="upload">Upload Image</x-button.primary>
  </div>
</div>

