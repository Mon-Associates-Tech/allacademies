<li class="mb-0.5 last:mb-0">
    <a class="block pl-4 pr-3 py-2 rounded-lg transition {{ request()->is($activePattern) ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
       href="{{ route($route) }}">
        <div class="flex items-center {{ $hasArrow ? 'justify-between' : '' }}">
            <div class="flex items-center">
                @if($icon)
                    {!! str_replace('class=""', 'class="' . $getIconClass() . '"', $icon) !!}
                @else
                    {{-- Default icon if none provided --}}
                    <svg class="{{ $getIconClass() }}" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                        <path d="M8 16A8 8 0 1 1 8 0a8 8 0 0 1 0 16zm0-2A6 6 0 1 0 8 2a6 6 0 0 0 0 12zm0-8a2 2 0 1 1 0 4 2 2 0 0 1 0-4z"/>
                    </svg>
                @endif
                <span class="text-sm ml-4 sidebar-text duration-200">{{ $label }}</span>
            </div>
            @if($hasArrow)
                <svg class="shrink-0 fill-current text-gray-400 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                    <path d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z"/>
                </svg>
            @endif
        </div>
    </a>
</li>
