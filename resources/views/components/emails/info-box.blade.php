@php
    $colors = [
        'blue' => ['bg' => '#eff6ff', 'border' => '#3b82f6', 'text' => '#1e40af'],
        'green' => ['bg' => '#f0fdf4', 'border' => '#22c55e', 'text' => '#166534'],
        'yellow' => ['bg' => '#fefce8', 'border' => '#eab308', 'text' => '#854d0e'],
        'red' => ['bg' => '#fef2f2', 'border' => '#ef4444', 'text' => '#991b1b'],
    ];
    $color = $colors[$type ?? 'blue'];
@endphp

<table role="presentation" style="width: 100%; border: 0; cellspacing: 0; cellpadding: 0; margin: 24px 0;">
    <tr>
        <td style="background-color: {{ $color['bg'] }}; border-left: 4px solid {{ $color['border'] }}; padding: 20px; border-radius: 8px;">
            <p style="color: {{ $color['text'] }}; font-size: 14px; line-height: 1.6; margin: 0;">
                {{ $slot }}
            </p>
        </td>
    </tr>
</table>
