@props(['to'])

<a href="{{ $to }}" {{ $attributes->merge(['class' => 'inline-flex items-center text-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500']) }}>{{ $slot }}</a>