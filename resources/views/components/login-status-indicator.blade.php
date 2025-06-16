@props(['isActive', 'activity'])

<span class="relative inline-flex">
    <span class="w-3 h-3 rounded-full {{
        $activity->action === 'logged_in' && $isActive ? 'bg-green-500' : 'bg-gray-300'
    }}"></span>
    @if($activity->action === 'logged_in' && $isActive)
        <span class="animate-ping absolute inline-flex h-3 w-3 rounded-full bg-green-400 opacity-75"></span>
    @endif
</span>
