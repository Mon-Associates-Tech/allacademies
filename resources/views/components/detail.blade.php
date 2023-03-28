@props(['action'])

<div class="bg-white shadow overflow-hidden sm:rounded-lg">
    <div class="px-4 py-5 sm:px-6">
      <dl class="grid grid-cols-1 gap-x-4 gap-y-8 sm:grid-cols-2">
        {{ $slot }}
      </dl>
    </div>
    @isset($action)
    <div class="bg-gray-50 px-4 py-4 sm:px-6 flex justify-end space-x-2">
        {{ $action }}
    </div>
    @endisset
</div>