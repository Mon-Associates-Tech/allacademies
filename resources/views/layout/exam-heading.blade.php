@php
    $logo_path = is_null($examination->metaData) ? null : $examination->metaData->meta['logo'] ?? '';
    if($logo_path){
        $logo_path = asset('storage/' . $logo_path);
    }else{
        $logo_path = URL::to('/img/logo.png');
    }
    $heading_type = $examination->heading['heading_type'];
    $institution_type = is_null($examination->metaData) ? '' : $examination->metaData->meta['institution_type'] ?? '';
    $institution_name = is_null($examination->metaData) ? '' : Str::upper($examination->metaData->meta['institution_name']) ?? '';
    $school = is_null($examination->metaData) ? '' : Str::ucfirst($examination->metaData->meta['school']) ?? '';
    $faculty = is_null($examination->metaData) ? '' : Str::ucfirst($examination->metaData->meta['faculty']) ?? '';
    $college = is_null($examination->metaData) ? '' : Str::ucfirst($examination->metaData->meta['college']) ?? '';
    $department = is_null($examination->metaData) ? '' : Str::ucfirst($examination->metaData->meta['department']) ?? '';

    $academic_group = Str::ucfirst($academicSubject->academicLevel->academicGroup->name);
    $academic_level = Str::ucfirst($academicSubject->academicLevel->name);
    $subject_code = $academicSubject->code;
    $subject_name = Str::ucfirst($academicSubject->name);

    $exam_date = Carbon\Carbon::parse($examination->heading['date'])->format('d F, Y');
    $exam_title = Str::ucfirst($examination->title);
    $start_time = Carbon\Carbon::parse($examination->heading['start'])->format('g:i A');
    $end_time =  Carbon\Carbon::parse($examination->heading['end'])->format('g:i A'); 
    $examiners = is_null($examination->examiners) ? "" : "Examiner(s): " . $examination->examiners;
    $instructions  = Str::ucfirst($examination->heading['instructions']);
@endphp

@if($heading_type == "institutional_advanced")
{{-- heading 3 - institution_name with logo --}}
<div style="text-align: center; margin-top:1rem;">
    <img src="{{$logo_path}}" style="width:10%;" alt="" onerror="this.style.display='none'" />
    <h3 style="font-weight: bold;">{{ $institution_name }}</h3>
    @if($institution_type == "faculty_based")
        <h3>{{ $faculty }}</h3>
    @elseif($institution_type == "college_based")
        <h3>{{ $college}} <span style="margin-right:1rem;"></span> {{$school }}</h3>
    @endif
    @if($institution_type != "institution_only")
        <h3>{{ $department }}</h3>
    @endif   
    <h3>{{ $academic_group }} <span style="margin-right:1rem;"></span> {{$academic_level }}</h3> 
    <h3>{{ $exam_title }}</h3>  
    <h3>{{ $subject_code . " " . $subject_name }} <span style="margin-right:1rem;"></span> {{ $exam_date }}</h3>
    <h3>{{ $start_time }} <span style="margin-right:0.5rem; margin-left:0.5rem;"> — </span> {{ $end_time }}</h3>   
</div>
<p style="font-weight: bold; margin-bottom:0.5rem; margin-top:1rem;">{{ $examiners }}</p>
<p style="font-weight: bold;"><u>Exams Instructions</u></p>
<P style="margin-bottom: 1rem;">{{ $instructions }}</P> 

@elseif($heading_type == "institutional")
    {{-- heading 2 for institutions, without examiners--}}
    <div style="text-align: center; margin-top:1rem;">
        <img src="{{$logo_path}}" style="width:10%;" alt="" onerror="this.style.display='none'" />
        <h3 style="font-weight: bold;">{{ $institution_name }}</h3>  
        <h3>{{ $academic_group }} <span style="margin-right:1rem;"></span> {{$academic_level }}</h3> 
        <h3>{{ $subject_code . " " . $subject_name }}</h3>
        <h3>{{ $exam_title }}</h3>  
    </div>
    <div style="font-weight: bold; margin-top: 0.5rem;">
        <p>Date: {{ $exam_date }}</p>
        <p>Duration: {{ $start_time }} <span style="margin-right:0.5rem; margin-left:0.5rem;"> — </span> {{ $end_time }}</h3> 
    </div>
    <p style="font-weight: bold;"><u>Exams Instructions</u></p>
    <P style="margin-bottom: 1rem;">{{ $instructions }}</P> 

@elseif($heading_type == "basic" || $heading_type == "individual")
    {{-- heading 1 basic heading--}}
    <div style="text-align: center; margin-top:1.5rem;">
        @if($heading_type == "individual")
            <img src="{{URL::to('/img/logo.png')}}" style="width:10%;" alt="" onerror="this.style.display='none'" />
        @endif
        @if($heading_type == "basic")
            <h3 style="font-weight: bold;">{{ $institution_name }}</h3>
        @endif
        <h3>{{ $subject_code }} <span style="margin-right: 1rem;"></span> {{ $subject_name }}</h3>
        <h3>{{ $exam_title }}</h3>
        <h3>{{ $exam_date }}</h3>
        <h3>{{ $start_time }} <span style="margin-left: 0.5rem; margin-right: 0.5rem;"> — </span> {{ $end_time }}</h3> 
    </div>
    <p style="font-weight: bold; margin-top: 1rem;"><u>Exam Instructions</u></p>
    <P style="margin-bottom: 1rem;">{{ $instructions}}</P>
@endif