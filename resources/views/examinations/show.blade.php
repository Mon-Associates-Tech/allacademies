{!! $examination->heading->up() !!}

@foreach ($sections as $section)
    {{ $section['name'] }}
    @if ("multiple_choice_questions" === $section['type'])
        <ol>
        @foreach ($section['questions'] as $mc)
            <li>
                {!! $mc->question->up() !!}
                @foreach (['a', 'b', 'c', 'd', 'e'] as $o)
                    @if ($mc->{"option_{$o}"}->up())
                    <div style="display: flex; align-items: center; column-gap: 1rem;">
                        <div style="flex: 0 1 auto;">({{ $o }})</div>
                        <div style="flex: 1 1 0%;">{!! $mc->{"option_{$o}"}->up() !!}</div>
                    </div>
                    @endif
                @endforeach
                <p style="text-align: right;">[{{ $mc->score }} mark(s)]</p>
            </li>
        @endforeach
        </ol>
    @elseif ("true_or_false_questions" === $section['type'])
        <ol>
        @foreach ($section['questions'] as $tf)
            <li>
                {!! $tf->question->up() !!}
                @foreach (['a', 'b'] as $o)
                    <div style="display: flex; align-items: center; column-gap: 1rem;">
                        <div style="flex: 0 1 auto;">({{ $o }})</div>
                        <div style="flex: 1 1 0%;">{{ 'a' === $o ? 'True' : 'False' }}</div>
                    </div>
                @endforeach
                <p style="text-align: right;">[{{ $tf->score }} mark(s)]</p>
            </li>
        @endforeach
        </ol>
    @elseif ("essay_questions" === $section['type'])
    <ol>
        @foreach ($section['questions'] as $es)
            <li>
                {!! $es->question->up() !!}
                <p style="text-align: right;">[{{ $es->score }} mark(s)]</p>
            </li>
        @endforeach
        </ol>
    @endif
@endforeach