<div x-data="{ tab: 'image' }">
    <div class="text-sm tracking-wide font-medium text-gray-700 mb-2">Plugins</div>

    <div class="flex border border-gray-300 space-x-2 rounded-lg p-1 text-xs font-medium mb-2">
        <button x-on:click="tab = 'image'" type="button" x-bind:class="tab === 'image' && 'bg-white'" class="text-gray-900 whitespace-nowrap px-2.5 py-1.5 rounded hover:bg-gray-50">Image</button>
        {{-- <button x-on:click="tab = 'table'" type="button" x-bind:class="tab === 'table' && 'bg-white'" class="text-gray-900 whitespace-nowrap px-2.5 py-1.5 rounded hover:bg-gray-50">Table</button> --}}
    </div>


    <div x-show="tab === 'image'" class="space-y-5">
        @livewire('image-upload')
        @livewire('show-images')
    </div>
    {{-- <div x-show="tab === 'table'">
    Table
    </div> --}}
</div>