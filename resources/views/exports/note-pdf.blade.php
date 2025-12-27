<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $note->title }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            line-height: 1.6;
            color: #1f2937;
            padding: 40px;
            font-size: 12pt;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #3b82f6;
        }

        .title {
            font-size: 24pt;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 15px;
        }

        .metadata {
            font-size: 10pt;
            color: #6b7280;
            margin-bottom: 8px;
        }

        .metadata-label {
            font-weight: 600;
            color: #374151;
        }

        .badges {
            margin-top: 15px;
            text-align: center;
        }

        .badge {
            display: inline-block;
            padding: 4px 12px;
            margin: 0 5px;
            border-radius: 12px;
            font-size: 9pt;
            font-weight: 600;
            background-color: #eff6ff;
            color: #1e40af;
            border: 1px solid #93c5fd;
        }

        .content {
            margin-top: 30px;
            text-align: justify;
        }

        .content h1 {
            font-size: 18pt;
            margin-top: 20px;
            margin-bottom: 10px;
            color: #1f2937;
        }

        .content h2 {
            font-size: 16pt;
            margin-top: 18px;
            margin-bottom: 8px;
            color: #374151;
        }

        .content h3 {
            font-size: 14pt;
            margin-top: 15px;
            margin-bottom: 7px;
            color: #4b5563;
        }

        .content p {
            margin-bottom: 12px;
        }

        .content ul,
        .content ol {
            margin-left: 25px;
            margin-bottom: 12px;
        }

        .content li {
            margin-bottom: 5px;
        }

        .content blockquote {
            border-left: 4px solid #3b82f6;
            padding-left: 15px;
            margin: 15px 0;
            color: #4b5563;
            font-style: italic;
        }

        .content code {
            background-color: #f3f4f6;
            padding: 2px 6px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
            font-size: 10pt;
        }

        .content pre {
            background-color: #f3f4f6;
            padding: 15px;
            border-radius: 6px;
            overflow-x: auto;
            margin: 15px 0;
            font-family: 'Courier New', monospace;
            font-size: 9pt;
        }

        .content table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }

        .content table th,
        .content table td {
            border: 1px solid #e5e7eb;
            padding: 8px;
            text-align: left;
        }

        .content table th {
            background-color: #f9fafb;
            font-weight: 600;
        }

        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #e5e7eb;
            text-align: center;
            font-size: 9pt;
            color: #9ca3af;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
<div class="header">
    <div class="title">{{ $note->title }}</div>

    <div class="metadata">
        @if($note->user)
            <span class="metadata-label">Author:</span> {{ $note->user->name }}
        @endif
    </div>

    <div class="metadata">
        <span class="metadata-label">Created:</span> {{ $note->created_at->format('F d, Y') }}
        @if($note->created_at != $note->updated_at)
            &nbsp;•&nbsp;
            <span class="metadata-label">Updated:</span> {{ $note->updated_at->format('F d, Y') }}
        @endif
    </div>

    @if($note->academicSubject || $note->book || $note->is_public)
        <div class="badges">
            @if($note->academicSubject)
                <span class="badge">📚 {{ $note->academicSubject->name }}</span>
            @endif

            @if($note->book)
                <span class="badge">📖 {{ $note->book->title }}</span>
            @endif

            @if($note->is_public)
                <span class="badge">🌐 Public</span>
            @else
                <span class="badge">🔒 Private</span>
            @endif
        </div>
    @endif
</div>

<div class="content">
    {!! $note->content !!}
</div>

<div class="footer">
    <p>Exported from {{ config('app.name') }}</p>
    <p>Export Date: {{ $exportDate }}</p>
</div>
</body>
</html>
