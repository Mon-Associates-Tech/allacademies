@props(['name', 'message', 'svg' => []])

<div class="bg-primary-50 border border-primary-300 text-sm text-gray-600 rounded-md p-4 mb-2 mt-2 col-span-2"
    role="alert">
    <div class="flex items-center">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
            class="w-8 h-8 text-primary-600 hover:text-primary-900 mr-1">
            @foreach ($svg as $d)
                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $d }}" />
            @endforeach
        </svg>
        <span class="font-bold mr-2">{{ $name }}</span> {{ $message }}
    </div>
</div>
