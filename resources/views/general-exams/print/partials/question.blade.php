<div class="question">
    <span class="q-marks">{{ $question->marks }} mark{{ $question->marks != 1 ? 's' : '' }}</span>
    <span class="q-num">{{ $qNum }}.</span>
    <span class="q-text">{{ $question->question }}</span>

    @if($question->type === 'multiple_choice' && !empty($question->options))
        <div class="options">
            @foreach($question->options as $key => $value)
                <div class="opt">
                    <span class="opt-key">{{ $key }}.</span> {{ $value }}
                </div>
            @endforeach
        </div>
    @elseif($question->type === 'true_false')
        <div class="options">
            <div class="opt"><span class="opt-key">A.</span> True</div>
            <div class="opt"><span class="opt-key">B.</span> False</div>
        </div>
    @elseif($question->type === 'short_answer')
        <div class="short-answer-box"></div>
    @elseif($question->type === 'essay')
        <div class="essay-lines">
            @for($i = 0; $i < 6; $i++)
                <div class="essay-line"></div>
            @endfor
        </div>
    @endif
</div>
