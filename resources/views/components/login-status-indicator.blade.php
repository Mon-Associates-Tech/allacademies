@props(['isActive', 'activity'])

<span class="relative inline-flex">
    <span class="w-2 h-2 rounded-full {{
        is_null($activity->logout_at) && $isActive ? 'bg-green-500' : 'bg-gray-300'
    }}"></span>
    @if(is_null($activity->logout_at) && $isActive)
        <span class="animate-ping absolute inline-flex h-2 w-2 rounded-full bg-green-500 opacity-75"></span>
    @endif
</span>
