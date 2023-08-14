<div class="grid sm:grid-cols-2 gap-4">
  <form>
    <div class="sm:col-span-2 relative overflow-hidden rounded-md border border-gray-300 shadow-sm focus-within:border-primary-300 focus-within:ring focus-within:ring-primary-200 focus-within:ring-opacity-50">
      <input id="tags" class="block w-full border-0 focus:border-0 focus:ring-0 p-4 text-lg" placeholder="Tag">
      <textarea id="description" class="block w-full border-0 focus:border-0 focus:ring-0" rows="5" placeholder="Description"></textarea>
      <hr class="h-px border-0 bg-gray-300" />
      <div class="flex w-full items-center justify-between">
        <div class="relative">
          <label title="Click to upload" for="image" class="cursor-pointer flex items-center gap-4 px-6 py-4 before:border-gray-400/60 hover:before:border-gray-300 group dark:before:bg-darker dark:hover:before:border-gray-500 before:bg-gray-100 dark:before:border-gray-600 before:absolute before:inset-0 before:rounded-3xl before:border before:border-dashed before:transition-transform before:duration-300 hover:before:scale-105 active:duration-75 active:before:scale-95">
          <div class="w-max relative">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
              <path stroke-linecap="round" stroke-linejoin="round" d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 01-6.364-6.364l10.94-10.94A3 3 0 1119.5 7.372L8.552 18.32m.009-.01l-.01.01m5.699-9.941l-7.81 7.81a1.5 1.5 0 002.112 2.13" />
            </svg>            
            </div>
            <div class="relative">
              <span class="flex space-x-1">Attach Image</span>
            </div>
          </label>
          <input hidden="" type="file" name="image" id="image">
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

<br>
<br>
<br>
   
<form method="post" enctype="multipart/form-data" wire:submit.prevent="upload">
    @csrf
    @method('PATCH')

    @if (session()->has('message'))
        <div class="flex items-center bg-green-500 text-white text-sm font-bold px-4 py-3 mb-6 rounded" role="alert">
            <svg class="fill-current w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M12.432 0c1.34 0 2.01.912 2.01 1.957 0 1.305-1.164 2.512-2.679 2.512-1.269 0-2.009-.75-1.974-1.99C9.789 1.436 10.67 0 12.432 0zM8.309 20c-1.058 0-1.833-.652-1.093-3.524l1.214-5.092c.211-.814.246-1.141 0-1.141-.317 0-1.689.562-2.502 1.117l-.528-.88c2.572-2.186 5.531-3.467 6.801-3.467 1.057 0 1.233 1.273.705 3.23l-1.391 5.352c-.246.945-.141 1.271.106 1.271.317 0 1.357-.392 2.379-1.207l.6.814C12.098 19.02 9.365 20 8.309 20z"/></svg>
            <p>{{ session('message') }}</p>
         </div>
    @endif


    @if ($image)
        Image Preview:
        <div class="row">
            <div class="grid sm:grid-cols-3 gap-4">
                <img src="{{ $image->temporaryUrl() }}">
            </div>
        </div>
    @endif

    <div class="grid sm:grid-cols-3 gap-4">
        <div class="sm:col-span-2">
            <div class="space-y-1">
                <x-form.file name='image' wire:model="image"/>
            </div>
            
        </div>
        <div class="sm:col-span-2">
            <x-form.textarea name="description" type="text" wire:model="description"/>
        </div>
        <div class="sm:col-span-2">
            <x-form.input name="tags" type="text" wire:model="tags"/>
        </div>
    </div>
    <div class="flex justify-end mt-3">
         <x-button.primary class="ml-2">Upload Image</x-button.primary>
    </div>
</form>

<br>
<br>
<br>

<style>
  body {background:white !important;}
</style>
  <div class="editor mx-auto w-10/12 flex flex-col text-gray-800 border border-gray-300 p-4 shadow-lg max-w-2xl">
    <input class="bg-gray-100 border border-gray-300 p-2 mb-4 rounded-lg shadow-sm w-full leading-tight" placeholder="Enter Tag" type="text" name="tags" id="tags">
    <textarea class="bg-gray-100 sec p-3 h-60 border border-gray-300 outline-none rounded-lg shadow-sm w-full leading-tight" placeholder="Describe image here" name="description" id="description"></textarea>
    
    <!-- icons -->
    <div class="icons flex text-gray-500 m-2">
      <svg class="mr-2 cursor-pointer hover:text-gray-700 border rounded-full p-1 h-7" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 01-6.364-6.364l10.94-10.94A3 3 0 1119.5 7.372L8.552 18.32m.009-.01l-.01.01m5.699-9.941l-7.81 7.81a1.5 1.5 0 002.112 2.13"/>   
      </svg>
      <div class="relative">
        <span class="flex space-x-1">Attach Image</span>
      </div>
      <input hidden="" type="file" name="image" id="image">
      <div class="count ml-auto text-gray-400 text-xs font-semibold">0/250</div>
    </div>
    <!-- buttons -->
    <div class="flex justify-end mt-3">
      <x-button.primary class="ml-2">Upload Image</x-button.primary>
    </div>
  </div>

<br>
<br>
<br>

<div class="mx-auto max-w-md overflow-hidden rounded-lg bg-white shadow">
    <ul class="divide-y divide-gray-100 py-2 px-4">
      <li class="flex py-4">
        <div class="mr-4 flex-1">
          <h4 class="text-lg font-medium text-gray-900">The Bank of England Risks Hiking Too Far Ahead</h4>
          <div class="mt-1 text-sm text-gray-400"><span>Business</span> • <time>18 Nov 2022</time></div>
        </div>
        <div>
          <img src="https://images.unsplash.com/photo-1631016800696-5ea8801b3c2a?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=927&q=80" class="h-20 w-20 rounded-lg object-cover" alt="" />
        </div>
      </li>
      <li class="flex py-4">
        <div class="mr-4 flex-1">
          <h4 class="text-lg font-medium text-gray-900">The Bank of England Risks Hiking Too Far Ahead</h4>
          <div class="mt-1 text-sm text-gray-400"><span>Business</span> • <time>18 Nov 2022</time></div>
        </div>
        <div>
          <img src="https://images.unsplash.com/photo-1550510537-89d5433de5cb?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1974&q=80" class="h-20 w-20 rounded-lg object-cover" alt="" />
        </div>
      </li>
      <li class="flex py-4">
        <div class="mr-4 flex-1">
          <h4 class="text-lg font-medium text-gray-900">The Bank of England Risks Hiking Too Far Ahead</h4>
          <div class="mt-1 text-sm text-gray-400"><span>Business</span> • <time>18 Nov 2022</time></div>
        </div>
        <div>
          <img src="https://images.unsplash.com/photo-1587614380862-0294308ae58b?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=2070&q=80" class="h-20 w-20 rounded-lg object-cover" alt="" />
        </div>
      </li>
    </ul>
  </div>


  <div class="mx-auto max-w-xs">
    <div>
      <div class="group relative">
        <input type="text" id="example9" class="block w-full rounded-md border-gray-300 px-10 shadow-sm transition-all hover:bg-gray-50 focus:border-primary-400 focus:ring focus:ring-primary-200 focus:ring-opacity-50 disabled:cursor-not-allowed disabled:bg-gray-50 disabled:text-gray-500" placeholder="Quick search..." />
        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center px-2.5 text-gray-500">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-5 w-5">
            <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
          </svg>
        </div>
        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2.5">
          <span class="rounded border px-1.5 text-sm text-gray-400 shadow-sm transition-all group-hover:border-primary-500 group-hover:text-primary-500"><kbd>⌘</kbd> <kbd>K</kbd></span>
        </div>
      </div>
    </div>
  </div>