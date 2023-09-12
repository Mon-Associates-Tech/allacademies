{{-- heading 1 for universities --}}
<div style="text-align: center;">
    <img src="{{ asset('storage/' . $examination->metaData->meta['logo']) }}" style="width:7%;" alt="" onerror="this.style.display='none'" />
    <h2>{{ Str::upper(is_null($examination->metaData) ? null : $examination->metaData->meta['school'] ?? '') }}</h2>
    <h3>{{ Str::upper(is_null($examination->metaData) ? null : $examination->metaData->meta['department'] ?? '') }}</h3>   
    <h3>{{ Str::ucfirst($academicSubject->academicLevel->academicGroup->name) }} {{ Str::ucfirst($academicSubject->academicLevel->name) }}</h3>   
    <h3>{{ $academicSubject->code }} {{ $academicSubject->name }} <span style="margin-left: 2rem;">{{ now()->format('l jS \\of F Y') }}</span></h3>
    <h3>{{ $examination->heading->html }}</h3>
    <h3>{{ 'Duration: ' . $academicSubject->code }}</h3> 
</div>
<p style="font-weight: bold;"><u>EXAM INSTRUCTIONS</u></p>
<P style="margin-bottom: 1rem;">Many companies prefer using Laravel because it is a powerful framework that able us to build web applications of any complexity. And so many tools and packages that support by small groups of developers. Laravel also offers a Blade templating engine that is fast in rendering views that are produced HTML based. So if you're new to this framework and want to learn with Laravel 9 Blade Layout Templating then this might can help you. To start with this tutorial we need a fresh installation of Laravel.</P> 


{{-- heading for individual subscription --}}
{{-- <div style="text-align: center;">
    <h3>{{ $academicSubject->code }} {{ $academicSubject->name }}</h3>
    <h3>{{ $examination->heading->html }}</h3>
    <h3>{{ now()->format('l jS \\of F Y') }}</h3>
    <h3>{{ 'Duration: ' . $academicSubject->code }}</h3> 
</div>
<p style="font-weight: bold;"><u>EXAM INSTRUCTIONS</u></p>
<P style="margin-bottom: 1rem;">Many companies prefer using Laravel because it is a powerful framework that able us to build web applications of any complexity. And so many tools and packages that support by small groups of developers. Laravel also offers a Blade templating engine that is fast in rendering views that are produced HTML based. So if you're new to this framework and want to learn with Laravel 9 Blade Layout Templating then this might can help you. To start with this tutorial we need a fresh installation of Laravel.</P>  --}}


{{-- heading 3 for institutions --}}
{{-- <div style="text-align: center;">
    <h2>{{ Str::ucfirst(is_null($examination->metaData) ? null : $examination->metaData->meta['school'] ?? '') }}</h2>
    <h3>{{ Str::ucfirst(is_null($examination->metaData) ? null : $examination->metaData->meta['department'] ?? '') }}</h3>   
    <h3>{{ $academicSubject->code }} {{ $academicSubject->name }} </span></h3>
</div>

<P>{{ 'EXAM CODE:' }}</P>
<p> {{ 'EXAM TITLE:' . $examination->heading->html }}</P>
<P style="margin-bottom: 1rem;">{{ 'DURATION: ' . $academicSubject->code }}</P> 
<p style="font-weight: bold;"><u>EXAM INSTRUCTIONS</u></p>
<P style="margin-bottom: 1rem;">Many companies prefer using Laravel because it is a powerful framework that able us to build web applications of any complexity. And so many tools and packages that support by small groups of developers. Laravel also offers a Blade templating engine that is fast in rendering views that are produced HTML based. So if you're new to this framework and want to learn with Laravel 9 Blade Layout Templating then this might can help you. To start with this tutorial we need a fresh installation of Laravel.</P>  --}}
