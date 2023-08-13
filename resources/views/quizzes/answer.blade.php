@extends('layout.examination')

@section('content')

{{ $examination->heading->html }}

@foreach ($sections as $section)
    @if ($section['name'])
    {{ $section['name'] }}
    @endif
    @if ("multiple_choice_questions" === $section['type'])
        <ol>
        @foreach ($section['questions'] as $mc)
            <li>
                {{ strtoupper($mc->answer) }}
            </li>
        @endforeach
        </ol>
    @elseif ("true_or_false_questions" === $section['type'])
        <ol>
        @foreach ($section['questions'] as $tf)
            <li>
                {{ $tf->answer ? 'True' : 'False' }}
            </li>
        @endforeach
        </ol>
    @elseif ("essay_questions" === $section['type'])
    <ol>
        @foreach ($section['questions'] as $es)
            <li>
                {{ $es->answer->html }}
            </li>
        @endforeach
        </ol>
    @endif
@endforeach

@endsection