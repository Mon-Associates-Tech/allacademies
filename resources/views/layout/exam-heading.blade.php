@if($examination->heading['heading_type'] == "3")
{{-- heading 3 advanced with logo for institutions --}}
    <div style="text-align: center; margin-top:1rem;">
        <img src="{{ asset('storage/' . $examination->metaData->meta['logo']) }}" style="width:7%;" alt="" onerror="this.style.display='none'" />
        <h2>{{ Str::upper(is_null($examination->metaData) ? null : $examination->metaData->meta['school'] ?? '') }}</h2>
        <h3 style="font-weight: normal;">{{ Str::upper(is_null($examination->metaData) ? null : $examination->metaData->meta['department'] ?? '') }}</h3>   
        <h3 style="font-weight: normal;">{{ Str::ucfirst($academicSubject->academicLevel->academicGroup->name) }} {{ Str::ucfirst($academicSubject->academicLevel->name) }}</h3>   
        <h3 style="font-weight: normal;">{{ $academicSubject->code }} <span style="margin-right: 1rem;"></span> {{ $academicSubject->name }} <span style="margin-right: 1rem;"></span> {{ 'Date: ' . Carbon\Carbon::parse($examination->heading['date'])->format('d F, Y') }}</h3>
        <h3 style="font-weight: normal;">{{ $examination->title }}</h3>
        <h3 style="font-weight: normal;"> Examiner(s): {{ $examination->examiners }}</h3>
        <h3 style="font-weight: normal;"> Duration : {{  Carbon\Carbon::parse($examination->heading['start'])->format('g:i A') }} <span style="margin-right: 1; margin-left:1;"> — </span> {{ Carbon\Carbon::parse($examination->heading['end'])->format('g:i A') }}</h3>                        
    </div>
    <p style="font-weight: bold;"><u>Exam Instructions</u></p>
    <P style="margin-bottom: 1rem;">{{$examination->heading['instructions']}}</P> 
@elseif($examination->heading['heading_type'] == "2")
    {{-- heading 2 for institutions, without logo and examiners--}}
    <div style="text-align: center; margin-top:1rem;">
        <h2>{{ Str::upper(is_null($examination->metaData) ? null : $examination->metaData->meta['school'] ?? '') }}</h2>
        <h3>{{ Str::ucfirst($academicSubject->academicLevel->academicGroup->name) }} {{ Str::ucfirst($academicSubject->academicLevel->name) }}</h3>  
        <h3>{{ $academicSubject->code }} <span style="margin-right: 1rem;"></span> {{ $academicSubject->name }}</h3> 
        <h3>{{ $examination->title }}</h3>
    </div>
    <h3>Date: {{ Carbon\Carbon::parse($examination->heading['date'])->format('d F, Y') }}</h3>
    <h3 style="margin-bottom: 1rem;">Duration : {{  Carbon\Carbon::parse($examination->heading['start'])->format('g:i A') }} <span style="margin-right: 1; margin-left:1;"> — </span> {{ Carbon\Carbon::parse($examination->heading['end'])->format('g:i A') }}</h3> 
    <p style="font-weight: bold;"><u>Exam Instructions</u></p>
    <P style="margin-bottom: 1rem;">{{$examination->heading['instructions']}}</P>
@elseif($examination->heading['heading_type'] == "1")
    {{-- heading 1 for individuals, basic heading--}}
    <div style="text-align: center; margin-top:1rem;">
        <h3>{{ $academicSubject->code }} <span style="margin-right: 1rem;"></span> {{ $academicSubject->name }}</h3> 
        <h3>{{ $examination->title }}</h3>
        <h3>{{ Carbon\Carbon::parse($examination->heading['date'])->format('d F, Y') }}</h3>
        <h3>{{  Carbon\Carbon::parse($examination->heading['start'])->format('g:i A') }} <span style="margin-right: 1; margin-left:1;"> — </span> {{ Carbon\Carbon::parse($examination->heading['end'])->format('g:i A') }}</h3> 
    </div>
    <p style="font-weight: bold;"><u>Exam Instructions</u></p>
    <P style="margin-bottom: 1rem;">{{$examination->heading['instructions']}}</P>
@endif