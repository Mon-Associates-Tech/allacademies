@props(['blocks' => [], 'template' => null, 'mockExam' => null, 'subjectExam' => null, 'fontSize' => 11, 'isPdf' => false])

@php
    // 1. Intelligently resolve data based on context (Template Builder vs Final PDF)
    $isSubjectExam = !is_null($subjectExam);
    $isMockExam    = !is_null($mockExam);
    $isTemplate    = !is_null($template);

    $companyName = config('company.name', 'All Academies');
    
    if ($isSubjectExam) {
        $examTitle      = $subjectExam->mockExam->title;
        $subjectTitle   = $subjectExam->getDisplayTitle();
        $academicGroup  = $subjectExam->academicGroup;
        $academicLevel  = $subjectExam->academicLevel;
        $academicSubject= $subjectExam->academicSubject;
        $duration       = $subjectExam->duration_in_minutes;
        $totalMarks     = $subjectExam->getTotalMarks();
        $logoUrl        = $subjectExam->mockExam->logo_url ?? ($subjectExam->template->logo_url ?? null);
    } elseif ($isMockExam) {
        $examTitle      = $mockExam->title;
        $subjectTitle   = $mockExam->subjectExams->map(fn($se) => $se->getDisplayTitle())->implode(', ');
        $academicGroup  = null; 
        $academicLevel  = null;
        $academicSubject= null;
        $duration       = $mockExam->subjectExams->sum('duration_in_minutes');
        $totalMarks     = $mockExam->getTotalMarks();
        $logoUrl        = $mockExam->logo_url ?? null;
    } else {
        // Template Builder Context
        $examTitle      = $template->name;
        $subjectTitle   = $template->description; 
        $academicGroup  = $template->academicGroup;
        $academicLevel  = $template->academicLevel;
        $academicSubject= $template->academicSubject;
        $duration       = $template->default_duration_minutes;
        $totalMarks     = $template->getTotalMarks();
        $logoUrl        = $template->logo_url ?? null;
    }

    $durationText = $duration > 0 
        ? ($duration >= 60 ? floor($duration / 60) . ' hr ' . ($duration % 60) . ' min' : $duration . ' minutes')
        : 'Not specified';

    // Helper to ensure images have absolute URLs for PDF generators
    $imageUrl = fn($src) => $isPdf && $src && !Str::startsWith($src, ['http://', 'https://']) ? asset($src) : $src;
@endphp

<div class="fp-preview-container" style="font-family: 'Times New Roman', Times, serif; font-size: {{ $fontSize }}pt; line-height: 1.4; color: #000; background: #fff; padding: 15mm; width: 210mm; min-height: 297mm; box-sizing: border-box; margin: 0 auto; {{ $isPdf ? '' : 'box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);' }}">
    
    {{-- ╔══════════════════════════════════════════════════════════╗
         ║  PROFESSIONAL HEADER: Logo -> Institution -> Exam Info   ║
         ╚══════════════════════════════════════════════════════════╝ --}}
    <div style="text-align: center; margin-bottom: 20px;">
        {{-- Logo --}}
        @if($logoUrl)
            <div style="margin-bottom: 12px;">
                <img src="{{ $imageUrl($logoUrl) }}" alt="Institution Logo" style="max-height: 70px; width: auto;">
            </div>
        @endif
        
        {{-- Institution Name with Decorative Border --}}
        <div style="margin-bottom: 16px;">
            <h1 style="font-size: {{ $fontSize + 8 }}pt; font-weight: bold; text-transform: uppercase; letter-spacing: 3px; margin: 0 0 8px 0; color: #000;">
                {{ $companyName }}
            </h1>
            {{-- Double-line decorative border (thin-thick pattern) --}}
            <div style="border-top: 1px solid #000; margin: 0 20px;"></div>
            <div style="border-top: 3px solid #000; margin: 2px 20px 0 20px;"></div>
        </div>
        
        {{-- Exam Title --}}
        <div style="margin-bottom: 8px;">
            <h2 style="font-size: {{ $fontSize + 4 }}pt; font-weight: bold; text-transform: uppercase; letter-spacing: 1.5px; margin: 0; color: #000;">
                {{ $examTitle }}
            </h2>
        </div>
        
        {{-- Subject Name --}}
        @if($subjectTitle)
        <div style="margin-bottom: 12px;">
            <h3 style="font-size: {{ $fontSize + 2 }}pt; font-weight: 600; font-style: italic; margin: 0; color: #222;">
                {{ $subjectTitle }}
            </h3>
        </div>
        @endif
        
        {{-- Ornamental line --}}
        <div style="border-top: 2px solid #000; margin: 12px 40px 0 40px;"></div>
    </div>

    {{-- ╔══════════════════════════════════════════════════════════╗
         ║  PROFESSIONAL INFO GRID: Official Form Style             ║
         ╚══════════════════════════════════════════════════════════╝ --}}
    <div style="margin-bottom: 24px;">
        <table style="width: 100%; border-collapse: collapse; border: 1.5px solid #000;">
            <tr>
                @if($academicGroup)
                <td style="padding: 8px 12px; border: 1px solid #000; width: 20%; background: #f8f8f8;">
                    <div style="font-size: {{ $fontSize - 2 }}pt; text-transform: uppercase; letter-spacing: 1px; color: #555; font-weight: bold; margin-bottom: 4px;">Group</div>
                    <div style="font-size: {{ $fontSize }}pt; font-weight: bold; color: #000;">{{ $academicGroup->name }}</div>
                </td>
                @endif
                
                @if($academicLevel)
                <td style="padding: 8px 12px; border: 1px solid #000; width: 20%; background: #f8f8f8;">
                    <div style="font-size: {{ $fontSize - 2 }}pt; text-transform: uppercase; letter-spacing: 1px; color: #555; font-weight: bold; margin-bottom: 4px;">Level</div>
                    <div style="font-size: {{ $fontSize }}pt; font-weight: bold; color: #000;">{{ $academicLevel->name }}</div>
                </td>
                @endif
                
                @if($academicSubject)
                <td style="padding: 8px 12px; border: 1px solid #000; width: 20%; background: #f8f8f8;">
                    <div style="font-size: {{ $fontSize - 2 }}pt; text-transform: uppercase; letter-spacing: 1px; color: #555; font-weight: bold; margin-bottom: 4px;">Subject</div>
                    <div style="font-size: {{ $fontSize }}pt; font-weight: bold; color: #000;">{{ $academicSubject->name }}</div>
                </td>
                @endif
                
                @if($duration)
                <td style="padding: 8px 12px; border: 1px solid #000; width: 20%; background: #f8f8f8;">
                    <div style="font-size: {{ $fontSize - 2 }}pt; text-transform: uppercase; letter-spacing: 1px; color: #555; font-weight: bold; margin-bottom: 4px;">Duration</div>
                    <div style="font-size: {{ $fontSize }}pt; font-weight: bold; color: #000;">{{ $durationText }}</div>
                </td>
                @endif
                
                <td style="padding: 8px 12px; border: 1px solid #000; width: 20%; background: #f8f8f8;">
                    <div style="font-size: {{ $fontSize - 2 }}pt; text-transform: uppercase; letter-spacing: 1px; color: #555; font-weight: bold; margin-bottom: 4px;">Total Marks</div>
                    <div style="font-size: {{ $fontSize }}pt; font-weight: bold; color: #000;">{{ number_format($totalMarks, 1) }}</div>
                </td>
            </tr>
        </table>
    </div>

    {{-- ╔══════════════════════════════════════════════════════════╗
         ║  USER CONFIGURED BLOCKS (Instructions, Candidate Info)   ║
         ╚══════════════════════════════════════════════════════════╝ --}}
    @foreach($blocks as $block)
        @switch($block['type'])
            @case('heading')
                @php
                    $level = $block['level'] ?? 'h2';
                    $size = match($level) { 'h1' => $fontSize + 6, 'h2' => $fontSize + 3, 'h3' => $fontSize + 1, default => $fontSize + 2 };
                    $align = $block['alignment'] ?? 'center';
                @endphp
                <div style="text-align: {{ $align }}; font-weight: bold; text-transform: uppercase; margin: 20px 0 12px 0; font-size: {{ $size }}pt; color: #000; letter-spacing: 1px;">
                    {{ $block['content'] ?? '' }}
                </div>
                @break

            @case('richtext')
                <div style="margin-bottom: 16px; text-align: justify; color: #111; line-height: 1.6;">
                    {!! $block['content'] ?? '' !!}
                </div>
                @break

            @case('image')
                @php
                    $align = $block['alignment'] ?? 'center';
                    $width = (int) ($block['width'] ?? 200);
                @endphp
                @if(!empty($block['src']))
                    <div style="text-align: {{ $align }}; margin: 16px 0;">
                        <img src="{{ $imageUrl($block['src']) }}" alt="{{ $block['alt'] ?? '' }}" style="max-width: {{ $width }}px; height: auto; border: 1px solid #ddd;">
                    </div>
                @endif
                @break

            @case('divider')
                <div style="border-top: 1px solid #000; margin: 20px 40px;"></div>
                @break

            @case('info_table')
                @php
                    $fieldLabels = [
                        'candidate_name' => 'Candidate Name',
                        'index_number'   => 'Index Number',
                        'date'           => 'Date',
                        'duration'       => 'Duration',
                        'subject'        => 'Subject',
                        'grade'          => 'Grade / Class',
                        'signature'      => 'Invigilator Signature',
                        'score'          => 'Total Score',
                    ];
                    $fieldValues = [
                        'date'     => $isMockExam ? ($mockExam->starts_at ? $mockExam->starts_at->format('d M Y') : now()->format('d M Y')) : now()->format('d M Y'),
                        'duration' => $durationText,
                        'subject'  => $academicSubject?->name ?? 'N/A',
                    ];
                    $activeFields = $block['fields'] ?? [];
                @endphp
                @if(count($activeFields) > 0)
                    <table style="width: 100%; border-collapse: collapse; margin: 20px 0; border: 1.5px solid #000;">
                        @foreach(array_chunk($activeFields, 2) as $row)
                            <tr>
                                @foreach($row as $fieldKey)
                                    <td style="padding: 10px 12px; border: 1px solid #000; width: 50%; vertical-align: top; background: #fff;">
                                        <div style="font-size: {{ $fontSize - 1 }}pt; text-transform: uppercase; letter-spacing: 0.5px; color: #000; margin-bottom: 8px; font-weight: bold;">
                                            {{ $fieldLabels[$fieldKey] ?? $fieldKey }}
                                        </div>
                                        @if(isset($fieldValues[$fieldKey]) && $fieldValues[$fieldKey])
                                            <div style="font-size: {{ $fontSize }}pt; font-weight: bold; color: #000;">
                                                {{ $fieldValues[$fieldKey] }}
                                            </div>
                                        @else
                                            <div style="border-bottom: 1px dotted #000; height: 18px; width: 100%;"></div>
                                        @endif
                                    </td>
                                @endforeach
                                @if(count($row) === 1)
                                    <td style="border: 1px solid #000; width: 50%; background: #fff;"></td>
                                @endif
                            </tr>
                        @endforeach
                    </table>
                @endif
                @break
        @endswitch
    @endforeach
</div>