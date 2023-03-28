@props(['paths' => []])

@php
  $previous = count($paths) ? [array_key_last($paths) => end($paths)] : ['Dashboard' => route('dashboard')];
  $previousKey = array_key_first($previous);
@endphp

<div>
  <nav class="sm:hidden" aria-label="Back">
      <a href="{{ $previous[$previousKey] }}" class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700">
      <!-- Heroicon name: solid/chevron-left -->
      <svg class="flex-shrink-0 -ml-1 mr-1 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
      </svg>

      Back
      </a>
  </nav>
  <nav class="hidden sm:flex" aria-label="Breadcrumb">
      <ol class="flex items-center space-x-4">
        <li>
            <div>
            <a href="{{ route('dashboard') }}" class="text-sm font-medium text-gray-500 hover:text-gray-700">
              Dashboard
            </a>
            </div>
        </li>
        @foreach ($paths as $name => $to)
        <li>
            <div class="flex items-center">
              <svg class="flex-shrink-0 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
              </svg>

              <a href="{{ $to }}" class="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700">{{ $name }}</a>
            </div>
        </li>
        @endforeach
      </ol>
  </nav>
</div>
