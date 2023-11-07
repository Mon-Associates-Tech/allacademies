@props(['score' => null, 'difficulty_level' => null, 'value' => null])
<div>
    <x-form.select name="difficulty_level" label="Difficulty Level" :options="[
        'unspecified' => 'Unspecified',
        'easy' => 'Easy',
        'medium' => 'Medium',
        'difficult' => 'Difficult',
    ]" />
</div>
<div>
    <x-form.input name="score" type="number" value="{{ $score }}" />
</div>
