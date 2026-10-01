@props(['type' => 'success', 'label' => null, 'message' => null])

@php
    $message ??= session($type === 'error' ? 'error' : 'success');
    $label   ??= $type === 'error' ? null : 'Saved.';
    $tone = $type === 'error'
        ? ['box' => 'border-accent/30 bg-accent/10', 'icon' => 'text-accent', 'label' => 'text-accent']
        : ['box' => 'border-ok/40 bg-ok/10',         'icon' => 'text-ok',     'label' => 'text-ok'];
@endphp

@if($message)
    <div {{ $attributes->merge(['class' => "mb-6 flex animate-rise items-start gap-2.5 rounded border px-3.5 py-3 text-sm {$tone['box']}", 'role' => 'status']) }}>
        <svg class="mt-0.5 h-4 w-4 shrink-0 {{ $tone['icon'] }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
            @if($type === 'error')
                <circle cx="12" cy="12" r="10"/><path d="M12 8v4m0 4h.01"/>
            @else
                <path d="M20 6L9 17l-5-5"/>
            @endif
        </svg>
        <p class="leading-snug">
            @if($label)<strong class="font-semibold {{ $tone['label'] }}">{{ $label }}</strong> @endif
            {{ $message }}
        </p>
    </div>
@endif