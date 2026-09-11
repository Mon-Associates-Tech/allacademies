@props(['content' => '', 'template' => null, 'mockExam' => null, 'subjectExam' => null, 'fontSize' => 11, 'isPdf' => false])
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
         ║  TEMPLATE CONTENT (Rich Text)                            ║
         ╚══════════════════════════════════════════════════════════╝ --}}
    @if($content)
        <div style="margin-bottom: 16px; text-align: left; color: #111; line-height: 1.6;">
            {!! $content !!}
        </div>
    @endif
</div>