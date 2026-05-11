@props([
    'label' => null,
    'hint' => null,
    'error' => null,
    'required' => false,
    'icon' => null,
])

<div class="space-y-2">
    @if($label)
        <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider" 
               style="letter-spacing: 0.08em;">
            {{ $label }}
            @if($required) <span class="text-red-500">*</span>@endif
        </label>
    @endif
    
    <div class="relative">
        @if($icon)
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <x-dynamic-component :component="'heroicon-o-'.$icon" class="w-4 h-4 text-slate-400"/>
            </div>
        @endif
        
        <input {{ $attributes->class([
            'w-full px-4 py-2.5 text-sm border rounded-none focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 dark:bg-slate-800 dark:text-white transition-all',
            'pl-10' => $icon,
            'border-slate-200 dark:border-slate-700' => !$error,
            'border-red-300 dark:border-red-700 focus:ring-red-500/20 focus:border-red-500' => $error,
        ]) }}
        style="border-radius: var(--radius-sm);"
        />
    </div>
    
    @if($hint && !$error)
        <p class="text-xs text-slate-400 dark:text-slate-500">{{ $hint }}</p>
    @endif
    
    @if($error)
        <p class="text-xs text-red-600 dark:text-red-400">{{ $error }}</p>
    @endif
</div>