<div class="space-y-2">
    <x-form.file name="image" wire:model.live="image"/>
  <x-form.textarea name="description" type="text" wire:model.live="description"/>
  <div class="relative">
    <div>
      <label for="tag" class="block text-sm tracking-wide font-medium text-gray-700">Tags</label>
      <input name="tag" id="tag" type="text" class="border-gray-300 rounded-lg shadow-sm w-full leading-tight" wire:model.live="tag" wire:keydown.enter="addTag(@js($tag))">
      @error('tags') <span class="text-xs font-medium text-red-600">{{ $message }}</span> @enderror
    </div>
      @if ($suggestedTags)
        <div class="absolute z-10 mt-1 w-full origin-top rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none" role="menu" aria-orientation="vertical" aria-labelledby="menu-button" tabindex="-1">
          <div class="py-1" role="none">
            @foreach($suggestedTags as $tag)
            <button type="button" wire:click="addTag(@js($tag))" class="w-full text-left text-gray-700 block px-4 py-2 text-sm hover:bg-gray-100 hover:text-gray-900">{{ $tag }}</button>
            @endforeach
          </div>
        </div>
      @endif
    <div class="mt-1">
    @foreach ($tags as $tag)
      <span class="inline-flex items-center gap-x-0.5 rounded-md bg-primary-100 px-2 py-1 text-xs font-medium text-primary-700">
        <span class="truncate max-w-xs">{{ $tag }}</span>
        <button wire:click="removeTag({{ $loop->index }})" type="button" class="group relative -mr-1 h-3.5 w-3.5 rounded-sm hover:bg-primary-600/20">
          <span class="sr-only">Remove</span>
          <svg viewBox="0 0 14 14" class="h-3.5 w-3.5 stroke-primary-800/50 group-hover:stroke-primary-800/75">
            <path d="M4 4l6 6m0-6l-6 6" />
          </svg>
          <span class="absolute -inset-1"></span>
        </button>
      </span>
    @endforeach
    </div>
  </div>
  <div class="flex justify-end mt-5">
    <x-button.primary class="ml-2" wire:click="uploads">Upload Image</x-button.primary>
  </div>
</div>

