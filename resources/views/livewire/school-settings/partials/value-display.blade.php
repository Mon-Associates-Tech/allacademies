@switch($setting->type)
    @case('image')
        @if($setting->value)
            <img src="{{ $setting->value }}" alt="{{ $setting->label }}" class="h-16 w-16 object-cover rounded">
        @else
            <span class="text-gray-500 italic">No image uploaded</span>
        @endif
        @break

    @case('pdf')
        @if($setting->value)
            <a href="{{ $setting->value }}" target="_blank" class="text-indigo-600 hover:text-indigo-900 flex items-center">
                <svg class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                View PDF
            </a>
        @else
            <span class="text-gray-500 italic">No PDF uploaded</span>
        @endif
        @break

    @case('boolean')
        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $setting->value ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
            {{ $setting->value ? 'Yes' : 'No' }}
        </span>
        @break

    @case('json')
        @if($setting->value)
            <pre class="bg-gray-50 p-2 rounded text-sm font-mono max-w-xs overflow-x-auto">{{ json_encode($setting->value, JSON_PRETTY_PRINT) }}</pre>
        @else
            <span class="text-gray-500 italic">No data</span>
        @endif
        @break

    @case('longtext')
        <div class="text-sm text-gray-900 max-w-xs">
            {{ Str::limit($setting->value ?: 'No content', 100) }}
        </div>
        @break

    @default
        <span class="text-sm text-gray-900">{{ $setting->value ?: 'Not set' }}</span>
@endswitch
