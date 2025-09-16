@props(['name' => '', 'avatar' => '', 'textSize' => '', 'maxInitials' => 2, 'radius' => null])

@php
    // --- Initials Generation ---
    $words = array_values(array_filter(explode(' ', trim($name))));
    $initials = '';

    // Validate maxInitials prop (between 1 and 3)
    $maxInitials = max(1, min(3, (int) $maxInitials));

    if (count($words) >= 2) {
        if ($maxInitials === 3 && count($words) >= 3) {
            // Use first letter of first three words for 3-character display
            $initials = strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1) . substr($words[2], 0, 1));
        } else {
            // Use the first letter of the first and last words for names with multiple words.
            $initials = strtoupper(substr($words[0], 0, 1) . substr(end($words), 0, 1));
        }
    } elseif (count($words) === 1) {
        // Use the specified number of letters for single-word names.
        $initials = strtoupper(substr($words[0], 0, $maxInitials));
    }

    // --- Background Gradient Generation ---
    // A palette of subtle, muted gradients that contrast well with white text.
    $gradients = [
        ['#64748b', '#475569'], // Slate
        ['#6b7280', '#4b5563'], // Gray
        ['#78716c', '#57534e'], // Stone
        ['#71717a', '#52525b'], // Zinc
        ['#737373', '#525252'], // Neutral
        ['#7c2d12', '#991b1b'], // Muted Red
        ['#a16207', '#ca8a04'], // Muted Yellow
        ['#166534', '#15803d'], // Muted Green
        ['#0f4c75', '#1e40af'], // Muted Blue
        ['#581c87', '#7c3aed'], // Muted Purple
        ['#be185d', '#db2777'], // Muted Pink
        ['#0f766e', '#0d9488']  // Muted Teal
    ];

    $backgroundStyle = 'background-color: #6b7280;'; // Default fallback
    $radiusClass = $radius ?: 'rounded-full';

    // Generate a deterministic gradient based on the name's hash.
    if (!empty($name)) {
        $hash = crc32($name);
        $gradientIndex = abs($hash) % count($gradients);
        $selectedGradient = $gradients[$gradientIndex];
        $backgroundStyle = "background-image: linear-gradient(to bottom right, {$selectedGradient[0]}, {$selectedGradient[1]});";
    }
@endphp

<div {{ $attributes->merge(['class' => "flex items-center $radiusClass justify-center text-white font-bold"]) }}

     style="{{ $backgroundStyle }}">
    @if($avatar)
        <img class="w-full h-full  object-cover border-2 border-gray-200 dark:border-gray-600 {{ $radiusClass }}"
             src="{{ Storage::url($avatar) }}"
             alt="{{ $name }}">
    @else
        <span class="{{$textSize}}">{{ $initials }}</span>
    @endif
</div>
