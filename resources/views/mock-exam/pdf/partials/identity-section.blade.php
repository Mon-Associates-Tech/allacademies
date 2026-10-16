@props([
    'identity' => null,
    'fontSize' => 11,
    'imageUrlStyle' => 'asset',
    'escapePretext' => false,
])

@php
    $hasRows = $identity && !empty($identity['rows']);
@endphp

@if($hasRows)
    <style>
        .identity-section {
            text-align: center;
            padding-bottom: 8mm;
            margin-bottom: 8mm;
        }

        .identity-row {
            display: table;
            margin: 0 auto 2mm auto;
        }

        .identity-row:last-child {
            margin-bottom: 0;
        }

        .identity-row-cell {
            display: table-cell;
            vertical-align: middle;
            padding: 0 3mm;
        }

        .identity-logo {
            max-height: 18mm;
        }

        .identity-line {
            font-size: {{ ($fontSize ?? 11) + 1 }}pt;
            font-weight: 600;
            margin-bottom: 1.5mm;
        }

        .identity-pretext {
            font-weight: 400;
            color: #333;
        }
    </style>

    <div class="identity-section">
        @foreach ($identity['rows'] as $row)
            <div class="identity-row">
                @foreach ($row as $item)
                    <div class="identity-row-cell">
                        @if ($item['type'] === 'image')
                            @if ($imageUrlStyle === 'path')
                                <img alt="logo" src="{{ \Illuminate\Support\Facades\Storage::disk('public')->path($item['value']) }}"
                                    class="identity-logo">
                            @else
                                <img alt="logo" src="{{ asset('storage/' . $item['value']) }}"
                                    class="identity-logo">
                            @endif
                        @else
                            <div class="identity-line" style="font-size: {{ $item['font_size'] ?? 14 }}pt;">
                                @if ($item['pretext'])
                                    <span class="identity-pretext">
                                        @if ($escapePretext)
                                            {{ $item['pretext'] }}
                                        @else
                                            {!! $item['pretext'] !!}
                                        @endif
                                    </span>
                                @endif{{ $item['value'] }}
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>
@endif
