@props(['heading', 'current', 'incoming', 'changes'])

@php
    $organizationTypes = [
        'institution' => 'Institution Only',
        'college' => 'College Based',
        'faculty' => 'Faculty Based',
        'department' => 'Department Based',
    ];

    $getTypeName = fn($type) => $organizationTypes[$type] ?? $type;

    $hasTypeChange = $current && ($current['type'] ?? null) !== ($incoming['type'] ?? null);
@endphp

<div class="bg-white shadow rounded-lg overflow-hidden">
    <!-- Card Header -->
    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-medium text-gray-900">{{ $heading }}</h3>
    </div>

    <!-- Changes Content -->
    <div class="px-6 py-5 space-y-4">
        <!-- Institution Name -->
        <div class="space-y-2">
            <label class="text-sm font-medium text-gray-700">Institution</label>
            <div class="flex flex-col space-y-1">
                @if ($current && ($current['institution'] ?? '') !== ($incoming['institution'] ?? ''))
                    <div class="flex items-center space-x-2">
                        <span class="text-red-600 text-sm">From:</span>
                        <span class="px-2 py-1 bg-red-50 text-red-800 rounded text-sm line-through">
                            {{ $current['institution'] ?? 'N/A' }}
                        </span>
                    </div>
                @endif
                <div class="flex items-center space-x-2">
                    <span class="text-green-600 text-sm">{{ $current ? 'To:' : 'New:' }}</span>
                    <span class="px-2 py-1 bg-green-50 text-green-800 rounded text-sm font-medium">
                        {{ $incoming['institution'] ?? 'N/A' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Organizational Structure -->
        <div class="space-y-2">
            <label class="text-sm font-medium text-gray-700">Organizational Structure</label>
            <div class="space-y-2">
                @if ($hasTypeChange)
                    <div class="flex items-center space-x-2">
                        <span class="text-red-600 text-sm">From:</span>
                        <span class="px-2 py-1 bg-red-50 text-red-800 rounded text-sm line-through">
                            {{ $getTypeName($current['type'] ?? '') }}
                        </span>
                    </div>
                @endif
                <div class="flex items-center space-x-2">
                    <span class="text-green-600 text-sm">{{ $current ? 'To:' : 'New:' }}</span>
                    <span class="px-2 py-1 bg-green-50 text-green-800 rounded text-sm font-medium">
                        {{ $getTypeName($incoming['type'] ?? '') }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Specific Organizational Details -->
        @if (($incoming['type'] ?? '') !== 'institution')
            <div class="space-y-3">
                @foreach (['college', 'faculty', 'school', 'department'] as $field)
                    @if (!empty($incoming[$field]))
                        <div class="space-y-1">
                            <label class="text-sm font-medium text-gray-700 capitalize">{{ $field }}</label>
                            <div class="flex flex-col space-y-1">
                                @if ($current && !empty($current[$field]) && $current[$field] !== $incoming[$field])
                                    <div class="flex items-center space-x-2">
                                        <span class="text-red-600 text-sm">From:</span>
                                        <span class="px-2 py-1 bg-red-50 text-red-800 rounded text-sm line-through">
                                            {{ $current[$field] }}
                                        </span>
                                    </div>
                                @endif
                                <div class="flex items-center space-x-2">
                                    <span class="text-green-600 text-sm">{{ $current && !empty($current[$field]) ? 'To:' : 'New:' }}</span>
                                    <span class="px-2 py-1 bg-green-50 text-green-800 rounded text-sm font-medium">
                                        {{ $incoming[$field] }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        @endif
    </div>

    <!-- Card Footer with Stats -->
    <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4 text-sm">
                <div class="flex items-center space-x-2">
                    <span class="text-gray-500">Changes:</span>
                    @if ($changes->plus > 0)
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            +{{ $changes->plus }} additions
                        </span>
                    @endif
                    @if ($changes->minus > 0)
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            -{{ $changes->minus }} removals
                        </span>
                    @endif
                </div>
            </div>

            <!-- Visual Change Indicator -->
            <div class="flex items-center space-x-1">
                @foreach ($changes->percentage() as $percentage)
                    @for ($i = 0; $i < $percentage; $i++)
                        <div @class([
                            'w-2 h-2 rounded-full',
                            'bg-green-500' => $loop->parent->index === 0,
                            'bg-red-500' => $loop->parent->index === 1,
                            'bg-gray-400' => $loop->parent->index === 2,
                        ])></div>
                    @endfor
                @endforeach
            </div>
        </div>
    </div>
</div>
