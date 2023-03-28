@props(['title', 'content'])

<div class="bg-gray-200 px-10 py-6 rounded-xl text-center shadow-sm">
    <div class="flex justify-center -mt-12 mb-6">
        <div class="bg-red-500 rounded-lg text-white p-1.5 shadow-md">
            {{ $slot }}
        </div>
    </div>
    <h5 class="text-gray-900 text-lg font-medium">{{ $title }}</h5>
    <p class="text-gray-700 mt-4">{{ $content }}</p>
</div>