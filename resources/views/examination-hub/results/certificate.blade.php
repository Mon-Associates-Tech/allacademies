@php
    $assignment = $submission->assignment;
    $participantName = $submission->participant_name ?? $submission->participant_email;
    $date = $submission->submitted_at?->format('F d, Y');
    $percentage = number_format($submission->percentage ?? 0, 1);
    $grade = $submission->grade ?? 'N/A';
@endphp

<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Certificate - {{ $assignment->title }}</title>
    <link href="{{ url('css/app.css') }}" rel="stylesheet">
    <style>
        body { font-family: Inter, system-ui, -apple-system, Arial, sans-serif; }
        .print-button { position: fixed; right: 20px; top: 20px; z-index: 20; }
        .certificate-box { max-width: 1000px; margin: 32px auto; padding: 40px; border: 12px solid #0f172a; }
        .certificate-inner { border: 6px solid #10b981; padding: 32px; }
        .certificate-title { font-family: Georgia, serif; letter-spacing: -0.03em; }
        .certificate-name { line-height: 1.05; }
        .certificate-meta { color: #475569; }
        .certificate-footer { margin-top: 36px; display: flex; justify-content: space-between; align-items: flex-end; }
        @media print { .print-button { display: none; } }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 dark:bg-slate-950 dark:text-slate-100">

<button class="print-button bg-emerald-500 text-white py-3 px-4 rounded-lg font-semibold shadow-lg hover:bg-emerald-600" onclick="window.print()">Print / Save PDF</button>

<div class="certificate-box bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 shadow-2xl">
    <div class="certificate-inner bg-slate-50 dark:bg-slate-950">
        <h2 class="certificate-title text-3xl sm:text-4xl font-semibold text-slate-900 dark:text-white">Certificate of Completion</h2>
        <p class="mt-4 text-lg certificate-meta">This certifies that</p>

        <div class="certificate-name mt-6 text-4xl sm:text-5xl font-bold tracking-tight text-slate-900 dark:text-white">{{ $participantName }}</div>

        <p class="mt-4 text-xl certificate-meta">has successfully completed the examination</p>
        <h3 class="mt-3 text-2xl font-semibold text-slate-800 dark:text-slate-200">{{ $assignment->title }}</h3>

        <div class="mt-10 rounded-3xl border border-slate-200 bg-slate-100 p-8 dark:border-slate-700 dark:bg-slate-900">
            <p class="text-lg text-slate-700 dark:text-slate-300">Score: <span class="font-semibold text-slate-900 dark:text-white">{{ $submission->score ?? 0 }} / {{ $submission->total_marks ?? 0 }}</span></p>
            <p class="mt-2 text-lg text-slate-700 dark:text-slate-300">Percentage: <span class="font-semibold text-slate-900 dark:text-white">{{ $percentage }}%</span></p>
            <p class="mt-2 text-lg text-slate-700 dark:text-slate-300">Grade: <span class="font-semibold text-slate-900 dark:text-white">{{ $grade }}</span></p>
            <p class="mt-3 text-base text-slate-500 dark:text-slate-400">Date: {{ $date }}</p>
        </div>

        <div class="certificate-footer">
            <div>
                <p class="text-sm uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Examiner</p>
                <p class="mt-2 text-lg font-semibold text-slate-900 dark:text-white">{{ config('app.name') }}</p>
            </div>

            <div class="text-right">
                <p class="text-sm uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Signature</p>
                <p class="mt-2 text-lg font-semibold text-slate-900 dark:text-white">______________________</p>
            </div>
        </div>
    </div>
</div>

</body>
</html>
