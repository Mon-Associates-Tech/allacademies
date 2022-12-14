{!! $examination->heading->up() !!}

@foreach ($examination->sections as $section)
    {{ $section['name'] }}
    @if ("multiple_choice_questions" === $section['type'])
        
    @elseif ("true_or_false_questions" === $section['type'])

    @elseif ("essay_questions" === $section['type'])

    @endif
@endforeach