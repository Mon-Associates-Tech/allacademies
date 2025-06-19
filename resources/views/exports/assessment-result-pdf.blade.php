<!DOCTYPE html>
<html>
<head>
    <title>Assessment Results</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.5; padding: 20px; }
        h1 { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Assessment Results</h1>
    <table>
        <thead>
            <tr>
                <th>Type</th>
                <th>Question</th>
                <th>Your Answer</th>
                <th>Correct Answer</th>
                <th>Score</th>
            </tr>
        </thead>
        <tbody>
            @foreach($questions as $q)
                <tr>
                    <td>{{ ucfirst(str_replace('_', ' ', $q['question_type'])) }}</td>
                    <td>{!! nl2br(e($q['question_text'])) !!}</td>
                    <td>{{ $q['user_answer'] }}</td>
                    <td>{{ $q['correct_answer'] ?? 'N/A' }}</td>
                    <td>{{ $q['score'] }} / {{ $q['max_score'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top: 20px; font-weight: bold;">
        Total: {{ $total }} / {{ $max }} | Percentage: {{ $percent }}%
    </div>
</body>
</html>

