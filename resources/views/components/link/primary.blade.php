@props(['to'])

<a href="{{ $to }}" {{ $attributes->merge(['class' => 'inline-flex items-center text-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500']) }}>{{ $slot }}</a>