@php
    $totalMarks    = $mockExam->subjectExams->sum(fn($se) => $se->sections->sum(fn($s) => $s->getTotalMarks()));
    $totalDuration = $mockExam->subjectExams->sum('duration_in_minutes');
    $subjectNames  = $mockExam->subjectExams->map(fn($se) => $se->getDisplayTitle())->implode(', ');
@endphp

<div class="fp-wrap">
@foreach($blocks as $block)
    @switch($block['type'])

        @case('heading')
            @php
                $level = $block['level'] ?? 'h2';
                $sizes = ['h1' => $fontSize + 7, 'h2' => $fontSize + 4, 'h3' => $fontSize + 1];
                $size  = $sizes[$level] ?? ($fontSize + 4);
            @endphp
            <div style="text-align:center; font-weight:bold; text-transform:uppercase; letter-spacing:0.04em; color:#111; font-size:{{ $size }}pt; margin-bottom:8px;">
                {{ $block['content'] ?? '' }}
            </div>
            @break

        @case('richtext')
            <div style="font-size:{{ $fontSize - 1 }}pt; color:#222; line-height:1.6; margin-bottom:10px;">
                {!! $block['content'] ?? '' !!}
            </div>
            @break

        @case('image')
            @php
                $align = $block['alignment'] ?? 'center';
                $cellAlign = match($align) { 'left' => 'left', 'right' => 'right', default => 'center' };
                $width = (int) ($block['width'] ?? 300);
            @endphp
            @if(!empty($block['src']))
                <table style="width:100%; border-collapse:collapse; margin-bottom:10px;">
                    <tr>
                        <td style="text-align:{{ $cellAlign }};">
                            <img src="{{ $block['src'] }}" alt="{{ $block['alt'] ?? '' }}" style="max-width:{{ $width }}px;">
                        </td>
                    </tr>
                </table>
            @endif
            @break

        @case('divider')
            <div style="border-top:1px solid #333; margin:10px 0;"></div>
            @break

        @case('info_table')
            @php
                $fieldLabels = [
                    'candidate_name' => 'Full Name',
                    'index_number'   => 'Index No.',
                    'date'           => 'Date',
                    'duration'       => 'Duration',
                    'subject'        => 'Subject',
                    'grade'          => 'Class / Form',
                    'signature'      => 'Signature',
                    'score'          => 'Total Score',
                ];
                $fieldValues = [
                    'date'     => $mockExam->starts_at ? $mockExam->starts_at->format('d M Y') : now()->format('d M Y'),
                    'duration' => $totalDuration > 0 ? ($totalDuration >= 60 ? floor($totalDuration / 60).'hr '.($totalDuration % 60).'min' : $totalDuration.' mins') : null,
                    'subject'  => $subjectNames,
                ];
                $activeFields = $block['fields'] ?? [];
            @endphp
            <table style="width:100%; border-collapse:collapse; border:1.5px solid #444; margin-bottom:14px;">
                @foreach(array_chunk($activeFields, 2) as $row)
                    <tr>
                        @foreach($row as $fieldKey)
                            <td style="padding:6px 10px; border:1px solid #bbb; width:50%;">
                                <span style="display:block; font-size:7pt; text-transform:uppercase; letter-spacing:0.08em; color:#999;">{{ $fieldLabels[$fieldKey] ?? $fieldKey }}</span>
                                @if(isset($fieldValues[$fieldKey]) && $fieldValues[$fieldKey])
                                    <span style="font-size:{{ $fontSize }}pt; font-weight:bold; color:#111;">{{ $fieldValues[$fieldKey] }}</span>
                                @else
                                    <span style="display:inline-block; width:100%; border-bottom:1px solid #444; height:14px;"></span>
                                @endif
                            </td>
                        @endforeach
                        @if(count($row) === 1)<td style="border:1px solid #bbb; width:50%;"></td>@endif
                    </tr>
                @endforeach
            </table>
            @break
    @endswitch
@endforeach
</div>
