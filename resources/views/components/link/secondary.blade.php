@props(['to'])

<a href="{{ $to }}" {{ $attributes->merge(['class' => 'inline-flex items-center text-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-primary-700 bg-primary-100 hover:bg-primary-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500']) }}>{{ $slot }}</a>