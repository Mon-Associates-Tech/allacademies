@php
    
    $logo_path = URL::to('/img/logo.png');
    $heading_type = $examination->heading['heading_type'];
    
    if ($heading_type != 'individual') {
        $metaData = $examination->metaData->meta[count($examination->metaData->meta) - 1];
        $logo_path = is_null($metaData['logo']) ? null : $metaData['logo'];
        if ($logo_path) {
            $logo_path = asset('storage/' . $logo_path);
        }
    
        $type = $metaData['type'];
        $institution = Str::upper($metaData['name']);
        $school = is_null($metaData) ? '' : Str::ucfirst($metaData['school']);
        $faculty = is_null($metaData) ? '' : Str::ucfirst($metaData['faculty']);
        $college = is_null($metaData) ? '' : Str::ucfirst($metaData['college']);
        $department = is_null($metaData) ? '' : Str::ucfirst($metaData['department']);
    }
    
    $academicGroup = Str::ucfirst($academicSubject->academicLevel->academicGroup->name);
    $academicLevel = Str::ucfirst($academicSubject->academicLevel->name);
    $subjectCode = $academicSubject->code;
    $subjectName = Str::ucfirst($academicSubject->name);
    
    $heading = $examination->heading;
    $date = Carbon\Carbon::parse($heading['date'])->format('d F, Y');
    $title = Str::ucfirst($examination->title);
    $start = Carbon\Carbon::parse($heading['start'])->format('g:i A');
    $end = Carbon\Carbon::parse($heading['end'])->format('g:i A');
    $examiners = is_null($examination->examiners) ? '' : 'Examiner(s): ' . $examination->examiners;
    $instructions = Str::ucfirst($heading['instructions']);
@endphp

@if ($heading_type == 'institutional_advanced')
    {{-- heading 3 - institution with logo --}}
    <div style="text-align: center; margin-top:1rem;">
        <img src="{{ $logo_path }}" style="width:10%;" alt="" onerror="this.style.display='none'" />
        <h3 style="font-weight: bold;">{{ $institution }}</h3>
        @if ($type == 'faculty_based')
            <h3>{{ $faculty }}</h3>
        @elseif($type == 'college_based')
            <h3>{{ $college }} <span style="margin-right:1rem;"></span> {{ $school }}</h3>
        @endif
        @if ($type != 'institution_only')
            <h3>{{ $department }}</h3>
        @endif
        <h3>{{ $academicGroup }} <span style="margin-right:1rem;"></span> {{ $academicLevel }}</h3>
        <h3>{{ $title }}</h3>
        <h3>{{ $subjectCode . ' ' . $subjectName }} <span style="margin-right:1rem;"></span> {{ $date }}</h3>
        <h3>{{ $start }} <span style="margin-right:0.5rem; margin-left:0.5rem;"> — </span> {{ $end }}
        </h3>
    </div>
    <p style="font-weight: bold; margin-bottom:0.5rem; margin-top:1rem;">{{ $examiners }}</p>
    <p style="font-weight: bold;"><u>Exams Instructions</u></p>
    <P style="margin-bottom: 1rem;">{{ $instructions }}</P>
@elseif($heading_type == 'institutional')
    {{-- heading 2 for institutions, without examiners --}}
    <div style="text-align: center; margin-top:1rem;">
        <img src="{{ $logo_path }}" style="width:10%;" alt="" onerror="this.style.display='none'" />
        <h3 style="font-weight: bold;">{{ $institution }}</h3>
        <h3>{{ $academicGroup }} <span style="margin-right:1rem;"></span> {{ $academicLevel }}</h3>
        <h3>{{ $subjectCode . ' ' . $subjectName }}</h3>
        <h3>{{ $title }}</h3>
    </div>
    <div style="font-weight: bold; margin-top: 0.5rem;">
        <p>Date: {{ $date }}</p>
        <p>Duration: {{ $start }} <span style="margin-right:0.5rem; margin-left:0.5rem;"> — </span>
            {{ $end }}</h3>
    </div>
    <p style="font-weight: bold;"><u>Exams Instructions</u></p>
    <P style="margin-bottom: 1rem;">{{ $instructions }}</P>
@elseif($heading_type == 'basic' || $heading_type == 'individual')
    {{-- heading 1 basic heading --}}
    <div style="text-align: center; margin-top:1.5rem;">
        @if ($heading_type == 'individual')
            <img src="{{ URL::to('/img/logo.png') }}" style="width:10%;" alt=""
                onerror="this.style.display='none'" />
        @endif
        @if ($heading_type == 'basic')
            <h3 style="font-weight: bold;">{{ $institution }}</h3>
        @endif
        <h3>{{ $subjectCode }} <span style="margin-right: 1rem;"></span> {{ $subjectName }}</h3>
        <h3>{{ $title }}</h3>
        <h3>{{ $date }}</h3>
        <h3>{{ $start }} <span style="margin-left: 0.5rem; margin-right: 0.5rem;"> — </span>
            {{ $end }}</h3>
    </div>
    <p style="font-weight: bold; margin-top: 1rem;"><u>Exam Instructions</u></p>
    <P style="margin-bottom: 1rem;">{{ $instructions }}</P>
@endif
